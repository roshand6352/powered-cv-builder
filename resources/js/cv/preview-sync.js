export function previewChannelName(cvId) {
    return `cv-preview-${cvId}`;
}

export function openPreviewChannel(cvId, onMessage) {
    if (typeof BroadcastChannel === 'undefined') {
        return null;
    }

    const channel = new BroadcastChannel(previewChannelName(cvId));
    channel.onmessage = (event) => onMessage?.(event.data);

    return channel;
}

export function broadcastPreviewState(channel, payload) {
    if (! channel) {
        return;
    }

    channel.postMessage({
        type: 'document-update',
        updatedAt: Date.now(),
        ...payload,
    });
}
