<?php

namespace App\Services\Frontend;

use App\Models\DoctorCategory;
use App\Models\LocalDoctor;
use App\Models\User;
use App\Services\Backoffice\LocalDoctorRegionNormalizer;
use App\Services\LocalDoctorGeocoder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MypageLocalDoctorService
{
    public function __construct(
        private readonly PublicLocalDoctorService $publicLocalDoctorService,
        private readonly LocalDoctorGeocoder $geocoder,
    ) {}

    public function findForMember(User $user): ?LocalDoctor
    {
        return LocalDoctor::query()
            ->with('doctorCategories')
            ->where('member_id', $user->id)
            ->first();
    }

    public function canMemberEdit(?LocalDoctor $doctor): bool
    {
        return $doctor !== null && $doctor->allow_member_edit;
    }

    /**
     * @return array{
     *     categories: Collection<int, DoctorCategory>,
     *     functional_tests: list<array{id: string, label: string}>,
     *     treatment_areas: list<array{id: string, label: string}>,
     *     sigungu_by_sido: array<string, list<string>>,
     *     sidos: list<string>,
     *     status_labels: array<string, string>
     * }
     */
    public function formContext(?LocalDoctor $doctor): array
    {
        $sigunguBySido = config('local_doctor_regions.sigungu_by_sido', []);

        return [
            'categories' => DoctorCategory::query()
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'functional_tests' => config('local_doctor_form_options.functional_tests', []),
            'treatment_areas' => config('local_doctor_form_options.treatment_areas', []),
            'sigungu_by_sido' => is_array($sigunguBySido) ? $sigunguBySido : [],
            'sidos' => config('local_doctor_regions.sidos', []),
            'status_labels' => [
                'active' => '운영중',
                'inactive' => '미운영',
            ],
            'photo_url' => $doctor ? $this->publicLocalDoctorService->photoUrl($doctor) : asset('images/img_sample_profile_human.jpg'),
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function updateForMember(User $user, Request $request, array $validated): LocalDoctor
    {
        $doctor = $this->findForMember($user);
        if ($doctor === null || ! $this->canMemberEdit($doctor)) {
            throw new AccessDeniedHttpException();
        }

        $doctor = DB::transaction(function () use ($request, $validated, $doctor): LocalDoctor {
            $doctor->doctor_name = (string) $validated['doctor_name'];
            $doctor->license_no = (string) $validated['license_no'];
            $doctor->introduction = $validated['introduction'] ?? null;
            $doctor->hospital_name = (string) $validated['hospital_name'];
            $sidoRaw = trim((string) ($validated['sido'] ?? ''));
            $norm = LocalDoctorRegionNormalizer::normalizeSido($sidoRaw);
            $doctor->sido = $norm['sido'] !== '' ? $norm['sido'] : $sidoRaw;
            $doctor->sigungu = (string) $validated['sigungu'];
            $doctor->address = (string) $validated['address'];
            $doctor->address_detail = $validated['address_detail'] ?? null;
            $doctor->homepage = $validated['homepage'] ?? null;
            $doctor->phone = (string) $validated['phone'];
            $doctor->status = (string) $validated['status'];
            $doctor->functional_tests_selected = $validated['functional_tests_selected'] ?? [];
            $doctor->treatment_areas_selected = $validated['treatment_areas_selected'] ?? [];
            $doctor->other_symptoms = $validated['other_symptoms'] ?? null;
            $doctor->diseases_text = $validated['diseases_text'] ?? null;

            if ($request->hasFile('photo')) {
                $this->replacePhoto($doctor, $request->file('photo'));
            } elseif ($request->boolean('delete_photo')) {
                $this->deletePhoto($doctor);
            }

            $doctor->save();
            $doctor->doctorCategories()->sync($validated['category_ids'] ?? []);
            return $doctor->fresh(['doctorCategories']);
        });

        if ($this->geocoder->hasApiKey()) {
            $this->geocoder->syncForDoctor($doctor);
            $doctor = $doctor->fresh(['doctorCategories']);
        }

        return $doctor;
    }

    protected function replacePhoto(LocalDoctor $doctor, UploadedFile $file): void
    {
        $this->deleteStoredPhoto($doctor->photo_path);
        $doctor->photo_path = $file->store('local_doctors/photos', 'public');
    }

    protected function deletePhoto(LocalDoctor $doctor): void
    {
        $this->deleteStoredPhoto($doctor->photo_path);
        $doctor->photo_path = null;
    }

    protected function deleteStoredPhoto(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
