<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cms\SubmitContactFormRequest;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        $contactSettings = [
            'hero_badge' => SiteSetting::get('contact_hero_badge', 'STUDENT & PARENT SUPPORT'),
            'hero_title' => SiteSetting::get('contact_hero_title', 'We Are Always Here To Help'),
            'hero_subtitle' => SiteSetting::get('contact_hero_subtitle', 'Have questions regarding curriculum enrollment, parent progress dashboards, or scheduling a campus visit? Reach out to our dedicated support advisors.'),
            'hero_image' => SiteSetting::get('contact_hero_image', 'images/academy_campus.png'),
            'card_title' => SiteSetting::get('contact_card_title', 'Support Desk 24/7'),
            'card_subtitle' => SiteSetting::get('contact_card_subtitle', 'Direct Academic Assistance'),
            'card_icon' => SiteSetting::get('contact_card_icon', '🎧'),
            'phone' => SiteSetting::get('contact_phone', '+20 100 123 4567'),
            'whatsapp' => SiteSetting::get('contact_whatsapp', '+20 100 123 4568'),
            'email' => SiteSetting::get('contact_email', 'support@elite-academy.edu.eg'),
            'address' => SiteSetting::get('contact_address', 'New Cairo Hub, Egypt'),
            'form_title' => SiteSetting::get('contact_form_title', 'Send Us a Message'),
            'form_subtitle' => SiteSetting::get('contact_form_subtitle', 'Our student advisors will respond within 24 hours.'),
            'map_url' => SiteSetting::get('contact_map_iframe_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d55251.33660578643!2d31.470000000000002!3d30.030000000000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145822ee00000001%3A0x1000000000000000!2sNew%20Cairo%2C%20Cairo%20Governorate%2C%20Egypt!5e0!3m2!1sen!2seg!4v1700000000000!5m2!1sen!2seg'),
        ];

        return view('pages.contact', [
            'pageTitle' => __('Contact Us — Elite Academy'),
            'activeNav' => 'contact',
            'contactSettings' => $contactSettings,
        ]);
    }

    public function submitAjax(SubmitContactFormRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $message = ContactMessage::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your message has been sent to our academic support team. We will get back to you shortly.',
            'message_id' => $message->id,
        ], 201);
    }
}
