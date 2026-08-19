<?php

namespace App\Enums;

enum LiveSessionState: string
{
    case BEFORE_JOINABLE = 'before_joinable';
    case PREREQUISITE_REQUIRED = 'prerequisite_required';
    case PACKAGE_REQUIRED = 'package_required';
    case LIVE = 'live';
    case ENDED = 'ended';
    case CANCELLED = 'cancelled';

    public function label(?string $locale = null): string
    {
        $isAr = ($locale ?: app()->getLocale()) === 'ar';

        return match ($this) {
            self::BEFORE_JOINABLE => $isAr ? 'رابط الحصة غير متاح الآن (يتفعل قبل البث بـ 30 دقيقة)' : 'LOCKED — Available 30 minutes before session',
            self::PREREQUISITE_REQUIRED => $isAr ? 'مطلوب تسليم واجب الجلسة السابقة أو تقديم عذر مقبول' : 'PREREQUISITE — Complete required assignment or submit exception',
            self::PACKAGE_REQUIRED => $isAr ? 'مغلق 🔒 — الكورس لا يتضمن حصة مجانية (يلزم الاشتراك في باقة حصص)' : 'LOCKED 🔒 — Course Has No Free Demo (Active Package Required)',
            self::LIVE => $isAr ? 'الانضمام للبث المباشر الآن 🟢' : 'LIVE — Join Live Session Now 🟢',
            self::ENDED => $isAr ? 'انتهت الحصة ⏹️' : 'ENDED — Session Ended',
            self::CANCELLED => $isAr ? 'ملغاة ❌' : 'CANCELLED — Session Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::BEFORE_JOINABLE => 'bg-slate-100 text-slate-700 border-slate-200',
            self::PREREQUISITE_REQUIRED => 'bg-amber-50 text-amber-800 border-amber-200',
            self::PACKAGE_REQUIRED => 'bg-rose-50 text-rose-800 border-rose-200',
            self::LIVE => 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 animate-pulse',
            self::ENDED => 'bg-slate-100 text-slate-600 border-slate-300',
            self::CANCELLED => 'bg-gray-100 text-gray-600 border-gray-300',
        };
    }

    public function canJoin(): bool
    {
        return $this === self::LIVE;
    }
}
