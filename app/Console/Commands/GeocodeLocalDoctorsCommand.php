<?php

namespace App\Console\Commands;

use App\Models\LocalDoctor;
use App\Services\LocalDoctorGeocoder;
use Illuminate\Console\Command;

class GeocodeLocalDoctorsCommand extends Command
{
    protected $signature = 'local-doctors:geocode {--id= : 특정 주치의 ID만 처리}';

    protected $description = '주치의 병원 주소를 카카오 지오코딩하여 map_lat/map_lng 저장';

    public function handle(LocalDoctorGeocoder $geocoder): int
    {
        $query = LocalDoctor::query();
        if ($this->option('id')) {
            $query->whereKey((int) $this->option('id'));
        }

        $doctors = $query->get();
        $ok = 0;
        $fail = 0;

        foreach ($doctors as $doctor) {
            if ($geocoder->syncForDoctor($doctor)) {
                $ok++;
                $this->line("OK #{$doctor->id} {$doctor->hospital_name}");
            } else {
                $fail++;
                $this->warn("FAIL #{$doctor->id} {$doctor->hospital_name}");
            }
        }

        $this->info("완료: 성공 {$ok}건, 실패 {$fail}건");

        return self::SUCCESS;
    }
}
