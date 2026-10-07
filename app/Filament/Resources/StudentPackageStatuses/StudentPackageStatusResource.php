<?php

namespace App\Filament\Resources\StudentPackageStatuses;

use App\Filament\Resources\StudentPackageStatuses\Pages\ListStudentPackageStatuses;
use App\Filament\Resources\StudentPackageStatuses\Tables\StudentPackageStatusesTable;
use App\Models\StudentPackage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StudentPackageStatusResource extends Resource
{
    protected static ?string $model = StudentPackage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إدارة المستخدمين والأدوار' : 'User Management';
    }

    public static function getNavigationParentItem(): ?string
    {
        return app()->getLocale() === 'ar' ? 'حسابات الطلاب' : 'Student Profiles';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الباقات المنتهية' : 'Student Package Statuses';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'اشتراك وباقة طالب' : 'Student Package Status';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'متابعة باقات واشتراكات الطلاب المنتهية' : 'Expired & Exhausted Student Packages';
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = StudentPackage::query()
                ->where(function ($q) {
                    $q->where('remaining_sessions', '<=', 0)
                        ->orWhere('status', 'exhausted')
                        ->orWhere(function ($sub) {
                            $sub->whereNotNull('expires_at')
                                ->where('expires_at', '<', now());
                        });
                })
                ->distinct('student_user_id')
                ->count('student_user_id');

            return $count > 0 ? (string) $count : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return app()->getLocale() === 'ar'
            ? 'عدد الطلاب المنتهية باقاتهم أو نفد رصيدهم بالكامل'
            : 'Students with expired or exhausted packages';
    }

    public static function table(Table $table): Table
    {
        return StudentPackageStatusesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentPackageStatuses::route('/'),
        ];
    }
}
