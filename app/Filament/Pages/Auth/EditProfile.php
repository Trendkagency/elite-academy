<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class EditProfile extends BaseEditProfile
{
    protected Width|string|null $maxWidth = Width::SevenExtraLarge;

    public function getHeading(): string|Htmlable
    {
        return app()->getLocale() === 'ar'
            ? 'الملف الشخصي وإعدادات الحساب'
            : 'My Profile & Account Settings';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return app()->getLocale() === 'ar'
            ? 'إدارة الصورة الشخصية، بيانات الاتصال، كلمة المرور، وصلاحيات النظام'
            : 'Manage your avatar photo, personal credentials, security password, and administrative identity';
    }

    public static function getLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'My Profile';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'My Profile';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-user-circle';
    }

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'إعدادات النظام العامة' : 'System Settings';
    }

    public static function getNavigationSort(): ?int
    {
        return 99;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->inlineLabel(false)
            ->components([
                // 0. Hero Profile Card Banner
                ViewField::make('profile_hero')
                    ->view('filament.components.profile-hero')
                    ->dehydrated(false)
                    ->columnSpanFull(),

                // 1. Identity & Avatar Information
                Section::make(app()->getLocale() === 'ar' ? 'الصورة الشخصية والبيانات الأساسية' : 'Personal Identity & Avatar')
                    ->description(app()->getLocale() === 'ar' ? 'قم بتحديث صورتك الشخصية واسمك الرسمي وبيانات التواصل المباشرة' : 'Update your personal profile picture, display name, and direct contact details')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'lg' => 3,
                        ])->schema([
                            // Avatar upload column
                            FileUpload::make('avatar_url')
                                ->label(app()->getLocale() === 'ar' ? 'الصورة الشخصية' : 'Profile Picture')
                                ->avatar()
                                ->image()
                                ->disk('public')
                                ->directory('avatars')
                                ->visibility('public')
                                ->maxSize(3072)
                                ->imageEditor()
                                ->circleCropper()
                                ->helperText(app()->getLocale() === 'ar' ? 'صيغ PNG, JPG, WebP بحد أقصى 3 ميجابايت' : 'PNG, JPG, WebP max 3MB')
                                ->columnSpan([
                                    'default' => 1,
                                    'lg' => 1,
                                ]),

                            // Info inputs column
                            Grid::make([
                                'default' => 1,
                                'sm' => 2,
                            ])->schema([
                                $this->getNameFormComponent()
                                    ->label(app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full Name')
                                    ->placeholder(app()->getLocale() === 'ar' ? 'أدخل اسمك الكامل' : 'Enter your full name'),

                                $this->getEmailFormComponent()
                                    ->label(app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address'),

                                TextInput::make('phone')
                                    ->label(app()->getLocale() === 'ar' ? 'رقم الهاتف / الواتساب' : 'Phone / WhatsApp')
                                    ->tel()
                                    ->maxLength(30)
                                    ->placeholder('+20 100 000 0000'),

                                TextInput::make('job_title')
                                    ->label(app()->getLocale() === 'ar' ? 'المسمى الوظيفي / الدور' : 'Job Title / Role')
                                    ->maxLength(120)
                                    ->placeholder(app()->getLocale() === 'ar' ? 'مدير النظام' : 'Lead Administrator'),

                                Textarea::make('bio')
                                    ->label(app()->getLocale() === 'ar' ? 'نبذة مختصرة' : 'Short Bio / Notes')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->placeholder(app()->getLocale() === 'ar' ? 'نبذة مختصرة عن مهامك أو تخصصك...' : 'Brief summary of your responsibilities or profile notes...')
                                    ->columnSpanFull(),
                            ])->columnSpan([
                                'default' => 1,
                                'lg' => 2,
                            ]),
                        ]),
                    ]),

                // 2. Security & Password Section
                Section::make(app()->getLocale() === 'ar' ? 'أمان الحساب وكلمة المرور' : 'Account Security & Password')
                    ->description(app()->getLocale() === 'ar' ? 'تحديث كلمة المرور لحماية حسابك (اترك الحقول فارغة إذا كنت لا ترغب في التغيير)' : 'Ensure your account is protected with a secure password (leave empty to keep current)')
                    ->icon('heroicon-o-lock-closed')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 3,
                        ])->schema([
                            $this->getCurrentPasswordFormComponent()
                                ->label(app()->getLocale() === 'ar' ? 'كلمة المرور الحالية' : 'Current Password')
                                ->placeholder('••••••••')
                                ->columnSpan(1),

                            $this->getPasswordFormComponent()
                                ->label(app()->getLocale() === 'ar' ? 'كلمة المرور الجديدة' : 'New Password')
                                ->placeholder('••••••••')
                                ->columnSpan(1),

                            $this->getPasswordConfirmationFormComponent()
                                ->label(app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور الجديدة' : 'Confirm New Password')
                                ->placeholder('••••••••')
                                ->columnSpan(1),
                        ]),
                    ]),

                // 3. System Access & Telemetry Section
                Section::make(app()->getLocale() === 'ar' ? 'صلاحيات الحساب ومعلومات النظام' : 'Account Permissions & System Info')
                    ->description(app()->getLocale() === 'ar' ? 'نظرة عامة على حالة حسابك وصلاحياته داخل المنظومة' : 'Overview of administrative permissions and account status')
                    ->icon('heroicon-o-shield-check')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        ViewField::make('account_telemetry')
                            ->view('filament.components.profile-telemetry')
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
