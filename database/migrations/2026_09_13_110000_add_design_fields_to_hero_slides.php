<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            // Button label overrides (fallback to generic "Explore Now" / "Learn More" if null)
            $table->string('cta_primary_text', 100)->nullable()->after('cta_primary_url');
            $table->string('cta_secondary_text', 100)->nullable()->after('cta_secondary_url');

            // Overlay darkness 0-100 (stored as tinyint percentage)
            $table->unsignedTinyInteger('overlay_opacity')->default(55)->after('cta_secondary_text');

            // Accent palette: controls badge + primary button color family
            $table->string('accent_color', 20)->default('teal')->after('overlay_opacity');
            // values: teal | purple | orange | rose | sky | amber

            // Text horizontal alignment
            $table->string('text_align', 10)->default('left')->after('accent_color');
            // values: left | center | right

            // Content block position on slide (9-cell grid)
            $table->string('text_position', 20)->default('mid-left')->after('text_align');
            // values: top-left | top-center | top-right
            //         mid-left | mid-center | mid-right
            //         bot-left | bot-center | bot-right

            // Optional FontAwesome icon class for the badge dot
            $table->string('badge_icon', 60)->nullable()->after('text_position');
            // e.g. "fa-solid fa-rocket"
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn([
                'cta_primary_text',
                'cta_secondary_text',
                'overlay_opacity',
                'accent_color',
                'text_align',
                'text_position',
                'badge_icon',
            ]);
        });
    }
};
