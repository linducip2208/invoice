{!! '/*! Invocely Embed Widget v1 — drop-in invoice viewer for any site */' !!}
(function() {
    const SCRIPT_TAG = document.currentScript;
    const TOKEN = SCRIPT_TAG.getAttribute('data-token');
    const HEIGHT = SCRIPT_TAG.getAttribute('data-height') || '600px';
    const WIDTH = SCRIPT_TAG.getAttribute('data-width') || '100%';

    if (!TOKEN) {
        console.warn('[Invocely] data-token attribute required');
        return;
    }

    const embedUrl = '{{ url("/embed/invoice/") }}/' + encodeURIComponent(TOKEN);

    const iframe = document.createElement('iframe');
    iframe.src = embedUrl;
    iframe.style.width = WIDTH;
    iframe.style.height = HEIGHT;
    iframe.style.border = 'none';
    iframe.style.borderRadius = '16px';
    iframe.style.boxShadow = '0 4px 20px rgba(167,139,250,0.15)';
    iframe.setAttribute('frameborder', '0');
    iframe.setAttribute('allow', 'clipboard-write');
    iframe.title = 'Invoice from Invocely';

    SCRIPT_TAG.parentNode.insertBefore(iframe, SCRIPT_TAG.nextSibling);
})();
