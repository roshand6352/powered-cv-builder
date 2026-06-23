<script>
(function () {
    var toggle = document.querySelector('[data-nav-toggle]');
    var mobile = document.querySelector('[data-nav-mobile]');

    if (toggle && mobile) {
        toggle.addEventListener('click', function () {
            var open = mobile.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        });

        mobile.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                mobile.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Open menu');
            });
        });
    }

    window.toggleFaq = function (btn) {
        var item = btn.closest('.faq-item');
        var isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item.open').forEach(function (el) {
            el.classList.remove('open');
        });
        if (!isOpen) {
            item.classList.add('open');
        }
    };
})();
</script>
