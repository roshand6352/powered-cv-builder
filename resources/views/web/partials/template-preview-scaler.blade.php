{{-- Scales each server-rendered A4 template preview to fully fit its card width. Pure presentation. --}}
<script>
    (function () {
        var BASE_W = 794;
        var BASE_H = 1123;

        function scale(wrap) {
            var frame = wrap.querySelector('[data-doc-frame]') || wrap.querySelector('iframe');
            if (! frame) return;

            var w = wrap.clientWidth;
            if (! w) return;

            var s = w / BASE_W;
            frame.style.transformOrigin = 'top left';
            frame.style.transform = 'scale(' + s + ')';
            // Explicit height for browsers without aspect-ratio support; matches the A4 ratio.
            wrap.style.height = (BASE_H * s) + 'px';
        }

        function scaleAll() {
            document.querySelectorAll('[data-doc-scale]').forEach(scale);
        }

        function init() {
            document.querySelectorAll('[data-doc-scale]').forEach(function (wrap) {
                scale(wrap);
                if (typeof ResizeObserver === 'function') {
                    new ResizeObserver(function () { scale(wrap); }).observe(wrap);
                }
                var frame = wrap.querySelector('[data-doc-frame]') || wrap.querySelector('iframe');
                if (frame) {
                    frame.addEventListener('load', function () { scale(wrap); });
                }
            });

            // A couple of deferred passes in case fonts/layout settle after first paint.
            requestAnimationFrame(scaleAll);
            setTimeout(scaleAll, 300);
        }

        if (document.readyState !== 'loading') init();
        else document.addEventListener('DOMContentLoaded', init);

        window.addEventListener('load', scaleAll);
        window.addEventListener('resize', scaleAll);
    })();
</script>
