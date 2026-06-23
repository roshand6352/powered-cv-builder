@props(['section', 'settings'])

@php
    use App\Support\Cv\CvSectionIcons;
    use App\Support\Cv\CvTemplateLayout;

    $data = $section['data'] ?? [];
    $photoPosition = CvTemplateLayout::normalizePhotoPosition($settings['photo_position'] ?? null);
    $photoClass = CvTemplateLayout::photoPositionClass($photoPosition);
    $photoUrl = $data['photo_preview'] ?? $data['photo_url'] ?? null;
    $showPhoto = ! in_array($photoPosition, ['hidden', 'sidebar'], true) && filled($photoUrl);
    $showIcons = CvSectionIcons::enabled($settings);

    $contactFields = [
        'email' => $data['email'] ?? null,
        'phone' => $data['phone'] ?? null,
        'location' => $data['location'] ?? null,
        'website' => $data['website'] ?? null,
        'linkedin' => $data['linkedin'] ?? null,
        'github' => $data['github'] ?? null,
    ];
@endphp

<section class="cv-section cv-section-header" data-section="{{ $section['key'] }}">
    <header class="cv-header cv-header--photo-{{ $photoClass }}">
        @if ($showPhoto)
            <img src="{{ $photoUrl }}" alt="" class="cv-header-photo">
        @endif
        <div class="cv-header-body">
            <h1 class="cv-header-name">{{ $data['full_name'] ?: 'Your Name' }}</h1>
            @if ($data['job_title'] ?? false)
                <p class="cv-header-role">{{ $data['job_title'] }}</p>
            @endif
            <div class="cv-header-contact">
                @foreach ($contactFields as $field => $value)
                    @if ($value)
                        <span>
                            @if ($showIcons)
                                <i class="cv-contact-icon {{ CvSectionIcons::contactIcon($field) }}" aria-hidden="true"></i>
                            @endif
                            {{ $value }}
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    </header>
</section>
