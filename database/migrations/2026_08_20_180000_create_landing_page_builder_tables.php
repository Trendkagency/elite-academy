<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Main Landing Page');
            $table->string('slug')->default('main');
            $table->unsignedBigInteger('published_version_id')->nullable();
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->timestamps();
        });

        Schema::create('landing_page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('landing_pages')->onDelete('cascade');
            $table->string('section_key')->index();
            $table->string('type')->default('hero'); // hero, counters, about, features, courses, teachers, testimonials, certificates, faq, cta, custom
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('subtitle_en')->nullable();
            $table->text('subtitle_ar')->nullable();
            $table->string('badge_en')->nullable();
            $table->string('badge_ar')->nullable();
            $table->string('image_url')->nullable();
            $table->json('settings_json')->nullable(); // padding, margin, bg_color, bg_image, 3d tilt, depth, blur, animation, responsive visibility
            $table->boolean('is_enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_page_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->nullable()->constrained('landing_page_sections')->onDelete('cascade');
            $table->enum('type', ['manual', 'dynamic'])->default('manual');
            $table->string('data_source')->nullable(); // students_count, courses_count, teachers_count, parents_satisfaction, certificates_count
            $table->string('target_value')->default('100');
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->string('label_ar')->nullable();
            $table->string('label_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('color')->default('teal');
            $table->boolean('is_enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_page_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('landing_pages')->onDelete('cascade');
            $table->integer('version_number')->default(1);
            $table->longText('snapshot_json');
            $table->string('created_by')->nullable();
            $table->string('status')->default('published'); // published, draft, archived
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_versions');
        Schema::dropIfExists('landing_page_counters');
        Schema::dropIfExists('landing_page_sections');
        Schema::dropIfExists('landing_pages');
    }
};
