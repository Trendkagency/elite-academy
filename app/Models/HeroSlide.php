<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'track_label',
        'badge_icon',
        'cta_primary_url',
        'cta_primary_text',
        'cta_secondary_url',
        'cta_secondary_text',
        'overlay_opacity',
        'accent_color',
        'text_align',
        'text_position',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'sort_order'      => 'integer',
        'overlay_opacity' => 'integer',
    ];

    // ---------------------------------------------------------------
    // Localisation helpers
    // ---------------------------------------------------------------

    public function getLocalizedTitle(): string
    {
        return __($this->title ?? '');
    }

    public function getLocalizedSubtitle(): string
    {
        return __($this->subtitle ?? '');
    }

    public function getLocalizedTrackLabel(): string
    {
        return __($this->track_label ?? '');
    }

    public function getLocalizedCtaPrimaryText(): string
    {
        return __($this->cta_primary_text ?: 'Explore Now');
    }

    public function getLocalizedCtaSecondaryText(): string
    {
        return __($this->cta_secondary_text ?: 'Learn More');
    }

    // ---------------------------------------------------------------
    // Design helpers
    // ---------------------------------------------------------------

    /**
     * Tailwind classes for accent color palette.
     * Returns an array: [badge_bg, badge_border, badge_text, dot_bg, btn_primary, btn_shadow]
     */
    public function getAccentClasses(): array
    {
        return match ($this->accent_color ?? 'teal') {
            'purple' => [
                'badge_bg'     => 'bg-purple-500/20',
                'badge_border' => 'border-purple-400/30',
                'badge_text'   => 'text-purple-300',
                'dot_bg'       => 'bg-purple-400',
                'btn_primary'  => 'bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-500 text-white shadow-purple-600/25',
                'btn_shadow'   => 'shadow-lg shadow-purple-600/25',
            ],
            'orange' => [
                'badge_bg'     => 'bg-orange-500/20',
                'badge_border' => 'border-orange-400/30',
                'badge_text'   => 'text-orange-300',
                'dot_bg'       => 'bg-orange-400',
                'btn_primary'  => 'bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 text-white shadow-orange-500/25',
                'btn_shadow'   => 'shadow-lg shadow-orange-500/25',
            ],
            'rose' => [
                'badge_bg'     => 'bg-rose-500/20',
                'badge_border' => 'border-rose-400/30',
                'badge_text'   => 'text-rose-300',
                'dot_bg'       => 'bg-rose-400',
                'btn_primary'  => 'bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-400 text-white shadow-rose-500/25',
                'btn_shadow'   => 'shadow-lg shadow-rose-500/25',
            ],
            'sky' => [
                'badge_bg'     => 'bg-sky-500/20',
                'badge_border' => 'border-sky-400/30',
                'badge_text'   => 'text-sky-300',
                'dot_bg'       => 'bg-sky-400',
                'btn_primary'  => 'bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-400 text-white shadow-sky-500/25',
                'btn_shadow'   => 'shadow-lg shadow-sky-500/25',
            ],
            'amber' => [
                'badge_bg'     => 'bg-amber-500/20',
                'badge_border' => 'border-amber-400/30',
                'badge_text'   => 'text-amber-300',
                'dot_bg'       => 'bg-amber-400',
                'btn_primary'  => 'bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-slate-900 shadow-amber-500/25',
                'btn_shadow'   => 'shadow-lg shadow-amber-500/25',
            ],
            // teal (default)
            default => [
                'badge_bg'     => 'bg-teal-500/20',
                'badge_border' => 'border-teal-400/30',
                'badge_text'   => 'text-teal-300',
                'dot_bg'       => 'bg-teal-400',
                'btn_primary'  => 'bg-gradient-to-r from-teal-400 to-teal-500 hover:from-teal-300 hover:to-teal-400 text-slate-950 shadow-teal-500/25',
                'btn_shadow'   => 'shadow-lg shadow-teal-500/25',
            ],
        };
    }

    /**
     * Tailwind alignment class for the text container.
     */
    public function getTextAlignClass(): string
    {
        return match ($this->text_align ?? 'left') {
            'center' => 'text-center items-center',
            'right'  => 'text-right items-end',
            default  => 'text-left items-start',
        };
    }

    /**
     * Tailwind justify + items classes for the outer content wrapper based on text_position.
     * Returns [justify_class, items_class]
     */
    public function getPositionClasses(): array
    {
        return match ($this->text_position ?? 'mid-left') {
            'top-left'    => ['justify-start',  'items-start'],
            'top-center'  => ['justify-center', 'items-start'],
            'top-right'   => ['justify-end',    'items-start'],
            'mid-center'  => ['justify-center', 'items-center'],
            'mid-right'   => ['justify-end',    'items-center'],
            'bot-left'    => ['justify-start',  'items-end'],
            'bot-center'  => ['justify-center', 'items-end'],
            'bot-right'   => ['justify-end',    'items-end'],
            default       => ['justify-start',  'items-center'], // mid-left
        };
    }

    /**
     * CSS overlay rgba value built from overlay_opacity (0-100).
     */
    public function getOverlayStyle(): string
    {
        $opacity = ($this->overlay_opacity ?? 55) / 100;
        return "background: linear-gradient(to top, rgba(2,6,23,{$opacity}) 0%, rgba(2,6,23," . ($opacity * 0.6) . ") 50%, rgba(2,6,23," . ($opacity * 0.35) . ") 100%);";
    }
}