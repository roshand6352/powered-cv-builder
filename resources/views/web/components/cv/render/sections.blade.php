@props(['sections' => [], 'settings' => [], 'renderer'])

@foreach ($sections as $section)
    {!! $renderer->renderSection($section, $settings) !!}
@endforeach
