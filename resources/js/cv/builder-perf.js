// LIVE PREVIEW FIX: performance-tuned builder orchestration helpers

export const PERF_DEBUG = false;

export function perfLog(label, startedAt) {
    if (! PERF_DEBUG) {
        return;
    }

    console.log(label, `${Math.round(performance.now() - startedAt)}ms`);
}

export function scheduleFrame(callback) {
    if (typeof requestAnimationFrame === 'function') {
        requestAnimationFrame(callback);
    } else {
        setTimeout(callback, 0);
    }
}

export function dedupeWithinMs(lastAt, windowMs = 16) {
    const now = performance.now();

    if (now - lastAt < windowMs) {
        return { skip: true, now: lastAt };
    }

    return { skip: false, now };
}
