@props(['src'])

{{--
    Accurate, scaled mini-preview of a real template (rendered server-side with dummy data).
    The transform-critical styles are inline (not utility classes) so scaling/positioning
    never breaks regardless of the compiled CSS. Aspect ratio is locked to A4 so the
    document fills the full width of the card and stays fully visible (no crop/overflow).
--}}
<div
    data-doc-scale
    {{ $attributes->merge(['class' => 'relative w-full overflow-hidden bg-white']) }}
    style="aspect-ratio: 794 / 1123;"
>
    <iframe
        data-doc-frame
        src="{{ $src }}"
        loading="lazy"
        scrolling="no"
        tabindex="-1"
        title="Template preview"
        style="position:absolute; top:0; left:0; width:794px; height:1123px; border:0; background:#ffffff; transform-origin:top left; transform:scale(0.4); pointer-events:none;"
    ></iframe>
</div>
