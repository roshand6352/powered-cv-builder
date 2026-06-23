@props(['section', 'settings'])

@php
    use App\Support\Cv\CvTemplateLayout;

    $data = $section['data'] ?? [];
    $photoPosition = CvTemplateLayout::normalizePhotoPosition($settings['photo_position'] ?? null);
    $photoUrl = $data['photo_preview'] ?? $data['photo_url'] ?? null;
    $showPhoto = $photoPosition === 'sidebar' && filled($photoUrl);
@endphp

@if ($showPhoto)
    <div class="cv-sidebar-photo">
        <img src="{{ $photoUrl }}" alt="" class="cv-header-photo cv-header-photo--sidebar">
    </div>
@endif
