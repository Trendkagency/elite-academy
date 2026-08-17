<?php
/**
 * One-time HTML → Blade converter for Elite Academy static pages.
 * Run: php scripts/convert-html-to-blade.php
 */

$baseDir = dirname(__DIR__);

$pages = [
    'index.html'            => 'home',
    'about.html'            => 'about',
    'subjects.html'         => 'subjects',
    'subject-details.html'  => 'subject-details',
    'teachers.html'         => 'teachers',
    'teacher-profile.html'  => 'teacher-profile',
    'courses.html'          => 'courses',
    'course-details.html'   => 'course-details',
    'events.html'           => 'events',
    'event-details.html'    => 'event-details',
    'blog.html'             => 'blog',
    'contact.html'          => 'contact',
    'faq.html'              => 'faq',
    'login.html'            => 'login',
    'register.html'         => 'register',
    'student-portal.html'   => 'student-portal',
];

$routeMap = [
    'index.html'            => "{{ route('home') }}",
    'about.html'            => "{{ route('about') }}",
    'subjects.html'         => "{{ route('subjects') }}",
    'subject-details.html'  => "{{ route('subject-details') }}",
    'teachers.html'         => "{{ route('teachers') }}",
    'teacher-profile.html'  => "{{ route('teacher-profile') }}",
    'courses.html'          => "{{ route('courses') }}",
    'course-details.html'   => "{{ route('course-details') }}",
    'events.html'           => "{{ route('events') }}",
    'event-details.html'    => "{{ route('event-details') }}",
    'blog.html'             => "{{ route('blog') }}",
    'contact.html'          => "{{ route('contact') }}",
    'faq.html'              => "{{ route('faq') }}",
    'login.html'            => "{{ route('login') }}",
    'register.html'         => "{{ route('register') }}",
    'student-portal.html'   => "{{ route('student-portal') }}",
];

$viewsDir = $baseDir . '/resources/views/pages';
if (! is_dir($viewsDir)) {
    mkdir($viewsDir, 0755, true);
}

function extractMainContent(string $html): string
{
    // Remove DOCTYPE through body opening
    $html = preg_replace('/^[\s\S]*?<body[^>]*>/i', '', $html) ?? $html;

    // Remove ambient blobs block at top
    $html = preg_replace('/<!-- BACKGROUND DEPTH[\s\S]*?(?=<!--|<header|<main|<section)/i', '', $html, 1) ?? $html;

    // Remove header block
    $html = preg_replace('/<!-- Header[\s\S]*?<\/header>/i', '', $html, 1) ?? $html;
    $html = preg_replace('/<!-- HEADER[\s\S]*?<\/header>/i', '', $html, 1) ?? $html;
    $html = preg_replace('/<!-- ==========================================\s*-->\s*<!-- HEADER[\s\S]*?<\/header>/i', '', $html, 1) ?? $html;

    // Remove mobile drawer (checkbox + overlay)
    $html = preg_replace('/<!-- Premium Mobile[\s\S]*?<!-- ==========================================/i', '<!-- CONTENT_START -->', $html, 1) ?? $html;
    $html = preg_replace('/<input type="checkbox" id="nav-toggle"[\s\S]*?(?=<main|<section)/i', '', $html, 1) ?? $html;

    // Remove footer and everything after
    $html = preg_replace('/<!-- 3\. MAIN FOOTER -->[\s\S]*$/i', '', $html) ?? $html;
    $html = preg_replace('/<footer[\s\S]*$/i', '', $html) ?? $html;

    // Remove trailing scripts (moved to @push)
    $html = preg_replace('/<script[\s\S]*?<\/script>\s*$/i', '', $html) ?? $html;
    $html = preg_replace('/<script src="js\/scroll-reveal\.js"><\/script>\s*/i', '', $html) ?? $html;
    $html = preg_replace('/<script src="js\/i18n\.js"><\/script>\s*/i', '', $html) ?? $html;

    // Trim closing body/html if present
    $html = preg_replace('/<\/body>\s*<\/html>\s*$/i', '', $html) ?? $html;

    return trim($html);
}

function extractScripts(string $html): string
{
    preg_match_all('/<script(?:\s[^>]*)?>[\s\S]*?<\/script>/i', $html, $matches);
    $scripts = $matches[0] ?? [];
    $filtered = array_filter($scripts, fn ($s) => ! str_contains($s, 'i18n.js') && ! str_contains($s, 'scroll-reveal.js'));

    return implode("\n\n", $filtered);
}

function convertHtml(string $html, array $routeMap): string
{
    // Asset paths
    $html = str_replace('href="dist/output.css"', 'href="{{ asset(\'dist/output.css\') }}"', $html);
    $html = preg_replace('/src="images\//', 'src="{{ asset(\'images/', $html) ?? $html;
    $html = preg_replace('/src="\{\{ asset\(\'images\/([^"]+)"/', 'src="{{ asset(\'images/$1\') }}"', $html) ?? $html;

    // data-i18n → Blade translations
    $html = preg_replace_callback(
        '/<(\w+)([^>]*)\sdata-i18n="([^"]+)"([^>]*)>([^<]*)<\/\1>/',
        function (array $m) {
            $tag = $m[1];
            $before = trim($m[2] . $m[4]);
            $key = $m[3];

            return '<' . $tag . ($before ? ' ' . $before : '') . '>{{ __(\'' . $key . '\') }}</' . $tag . '>';
        },
        $html
    );

    $html = preg_replace('/\s*data-i18n="[^"]+"/', '', $html) ?? $html;

    foreach ($routeMap as $file => $route) {
        $html = preg_replace('/href="' . preg_quote($file, '/') . '(#[^"]*)?"/', 'href="' . $route . '$1"', $html);
        $html = preg_replace('/action="' . preg_quote($file, '/') . '(#[^"]*)?"/', 'action="' . $route . '$1"', $html);
        $html = preg_replace("/href='" . preg_quote($file, '/') . "(#[^']*)?'/", "href='" . $route . "$1'", $html);
        $html = preg_replace("/action='" . preg_quote($file, '/') . "(#[^']*)?'/", "action='" . $route . "$1'", $html);
    }

    // Cleanup conversion artifacts
    $html = str_replace('<!-- CONTENT_START --> -->', '', $html);
    $html = str_replace('<!-- CONTENT_START -->', '', $html);
    $html = preg_replace('/<!-- Global Footer -->/', '', $html) ?? $html;

    return trim($html);
}

foreach ($pages as $htmlFile => $bladeName) {
    $sourcePath = $baseDir . '/' . $htmlFile;
    if (! file_exists($sourcePath)) {
        echo "SKIP (missing): {$htmlFile}\n";
        continue;
    }

    $raw = file_get_contents($sourcePath);
    $scripts = extractScripts($raw);
    $content = extractMainContent($raw);
    $content = convertHtml($content, $routeMap);

    $blade = "@extends('layouts.app')\n\n@section('content')\n{$content}\n@endsection\n";

    if ($scripts !== '') {
        $blade .= "\n@push('scripts')\n{$scripts}\n@endpush\n";
    }

    $outPath = $viewsDir . '/' . $bladeName . '.blade.php';
    file_put_contents($outPath, $blade);
    echo "Converted: {$htmlFile} → pages/{$bladeName}.blade.php\n";
}

echo "\nDone. " . count($pages) . " pages processed.\n";
