<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_sponsor_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('representative_name')->nullable();
            $table->timestamps();
            $table->index('name', 'ae_sponsor_masters_name_idx');
        });

        Schema::create('academic_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('legacy_post_id')->nullable()->unique();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('season', 20)->nullable();
            $table->string('folder_name', 120)->unique();
            $table->string('title');
            $table->string('event_material_path')->nullable();
            $table->text('event_material_description')->nullable();
            $table->string('event_type', 20)->default('offline');
            $table->string('online_url', 500)->nullable();
            $table->string('is_public', 1)->default('Y');
            $table->string('main_exposure', 1)->default('N');
            $table->text('venue')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->boolean('start_time_omit')->default(false);
            $table->boolean('end_time_omit')->default(false);
            $table->string('greeting_title', 200)->nullable();
            $table->longText('greeting_content')->nullable();
            $table->string('greeting_image_path')->nullable();
            $table->longText('committee_content')->nullable();
            $table->string('pc_banner_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('exhibition_image_path')->nullable();
            $table->string('address', 500)->nullable();
            $table->string('address_detail', 300)->nullable();
            $table->decimal('address_lat', 10, 7)->nullable();
            $table->decimal('address_lng', 10, 7)->nullable();
            $table->longText('walking_guide')->nullable();
            $table->longText('shuttle_guide')->nullable();
            $table->date('pre_reg_start')->nullable();
            $table->date('pre_reg_end')->nullable();
            $table->date('onsite_reg_start')->nullable();
            $table->date('onsite_reg_end')->nullable();
            $table->string('onsite_reg_allowed', 20)->default('allowed');
            $table->longText('pre_reg_guide')->nullable();
            $table->longText('reg_fee_guide')->nullable();
            $table->longText('cert_doc_guide')->nullable();
            $table->longText('reg_info_guide')->nullable();
            $table->date('abstract_start')->nullable();
            $table->date('abstract_end')->nullable();
            $table->date('abstract_revision_end')->nullable();
            $table->date('abstract_judging_end')->nullable();
            $table->date('abstract_result_date')->nullable();
            $table->string('abstract_book_path')->nullable();
            $table->json('presentation_types')->nullable();
            $table->longText('submission_guide')->nullable();
            $table->longText('abstract_notes')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();

            $table->index(['year', 'season'], 'ae_year_season_idx');
            $table->index(['is_public', 'start_at'], 'ae_pub_start_idx');
        });

        Schema::create('academic_event_venue_floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_event_id')
                ->constrained('academic_events', 'id', 'ae_venue_floors_evt_fk')
                ->cascadeOnDelete();
            $table->string('floor_name', 100);
            $table->string('file_path')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('academic_event_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_event_id')
                ->constrained('academic_events', 'id', 'ae_fields_evt_fk')
                ->cascadeOnDelete();
            $table->string('name', 150);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('academic_event_sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_event_id')
                ->constrained('academic_events', 'id', 'ae_sponsors_evt_fk')
                ->cascadeOnDelete();
            $table->foreignId('academic_sponsor_master_id')
                ->nullable()
                ->constrained('academic_sponsor_masters', 'id', 'ae_sponsors_mst_fk')
                ->nullOnDelete();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('level', 30);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['academic_event_id', 'sort_order'], 'ae_sponsors_evt_sort_idx');
        });

        Schema::create('academic_event_main_sponsor_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_event_id')
                ->constrained('academic_events', 'id', 'ae_mss_evt_fk')
                ->cascadeOnDelete();
            $table->foreignId('academic_event_sponsor_id')
                ->nullable()
                ->constrained('academic_event_sponsors', 'id', 'ae_mss_spon_fk')
                ->cascadeOnDelete();
            $table->string('placement', 30);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['academic_event_id', 'sort_order'], 'ae_mss_evt_sort_idx');
        });

        Schema::create('academic_event_speakers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_event_id')
                ->constrained('academic_events', 'id', 'ae_spk_evt_fk')
                ->cascadeOnDelete();
            $table->string('source', 20)->default('manual');
            $table->foreignId('member_id')
                ->nullable()
                ->constrained('users', 'id', 'ae_spk_mbr_fk')
                ->nullOnDelete();
            $table->string('name');
            $table->string('affiliation')->nullable();
            $table->string('position')->nullable();
            $table->string('image_path')->nullable();
            $table->string('abstract_title')->nullable();
            $table->longText('bio')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['academic_event_id', 'sort_order'], 'ae_spk_evt_sort_idx');
        });

        Schema::create('academic_event_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_event_id')
                ->constrained('academic_events', 'id', 'ae_sess_evt_fk')
                ->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('category', 30)->nullable();
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->longText('description')->nullable();
            $table->string('chair_speakers', 500)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['academic_event_id', 'session_date'], 'ae_sess_evt_date_idx');
        });

        Schema::table('edu_courses', function (Blueprint $table) {
            $table->foreign('linked_event_id', 'edu_courses_linked_evt_fk')
                ->references('id')
                ->on('academic_events')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('edu_courses', function (Blueprint $table) {
            $table->dropForeign(['linked_event_id']);
        });

        Schema::dropIfExists('academic_event_sessions');
        Schema::dropIfExists('academic_event_speakers');
        Schema::dropIfExists('academic_event_main_sponsor_slots');
        Schema::dropIfExists('academic_event_sponsors');
        Schema::dropIfExists('academic_event_fields');
        Schema::dropIfExists('academic_event_venue_floors');
        Schema::dropIfExists('academic_events');
        Schema::dropIfExists('academic_sponsor_masters');
    }
};
