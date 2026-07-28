/**
 * CV date helpers — storage stays YYYY-MM (or YYYY-MM-DD);
 * display uses "January 2023".
 */

const MONTH_NAME_TO_INDEX = {
    jan: 1,
    january: 1,
    feb: 2,
    february: 2,
    mar: 3,
    march: 3,
    apr: 4,
    april: 4,
    may: 5,
    jun: 6,
    june: 6,
    jul: 7,
    july: 7,
    aug: 8,
    august: 8,
    sep: 9,
    sept: 9,
    september: 9,
    oct: 10,
    october: 10,
    nov: 11,
    november: 11,
    dec: 12,
    december: 12,
};

const MONTH_LONG = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December',
];

/**
 * Parse a CV date string into { year, month } (month 1–12).
 * Accepts YYYY-MM, YYYY-MM-DD, YYYY, "Jan 2020", "January 2020".
 */
export function parseCvDateParts(value) {
    if (value === null || value === undefined) {
        return null;
    }

    const raw = String(value).trim();

    if (! raw || /^present$/i.test(raw)) {
        return null;
    }

    let match = raw.match(/^(\d{4})-(\d{2})(?:-(\d{2}))?$/);

    if (match) {
        const year = Number(match[1]);
        const month = Number(match[2]);

        if (month >= 1 && month <= 12) {
            return { year, month };
        }

        return null;
    }

    if (/^\d{4}$/.test(raw)) {
        return { year: Number(raw), month: 1 };
    }

    match = raw.match(/^([A-Za-z]+)\s+(\d{4})$/);

    if (match) {
        const month = MONTH_NAME_TO_INDEX[match[1].toLowerCase()];
        const year = Number(match[2]);

        if (month) {
            return { year, month };
        }
    }

    match = raw.match(/^(\d{1,2})[\/\-](\d{4})$/);

    if (match) {
        const month = Number(match[1]);
        const year = Number(match[2]);

        if (month >= 1 && month <= 12) {
            return { year, month };
        }
    }

    return null;
}

/** Value suitable for <input type="month"> (YYYY-MM), or empty string. */
export function toMonthInputValue(value) {
    const parts = parseCvDateParts(value);

    if (! parts) {
        return '';
    }

    return `${parts.year}-${String(parts.month).padStart(2, '0')}`;
}

/** Normalize to YYYY-MM when parseable; otherwise return original trimmed string. */
export function toStorageMonth(value) {
    if (value === null || value === undefined) {
        return '';
    }

    const raw = String(value).trim();

    if (! raw) {
        return '';
    }

    const monthValue = toMonthInputValue(raw);

    return monthValue || raw;
}

/** Display format: "January 2023". Unparseable legacy values returned as-is. */
export function formatCvDate(value) {
    if (value === null || value === undefined) {
        return '';
    }

    const raw = String(value).trim();

    if (! raw) {
        return '';
    }

    if (/^present$/i.test(raw)) {
        return 'Present';
    }

    const parts = parseCvDateParts(raw);

    if (! parts) {
        return raw;
    }

    // Year-only stored values keep year-only display.
    if (/^\d{4}$/.test(raw)) {
        return String(parts.year);
    }

    return `${MONTH_LONG[parts.month - 1]} ${parts.year}`;
}

export function formatCvDateRange(start, end, { present = false } = {}) {
    const startLabel = formatCvDate(start);
    const endLabel = present ? 'Present' : formatCvDate(end);
    const hasRange = Boolean(startLabel) && Boolean(endLabel || present);

    return `${startLabel}${hasRange ? ' – ' : ''}${endLabel}`;
}

/** Compare YYYY-MM (or parseable) values. Returns negative / 0 / positive, or null. */
export function compareCvMonths(a, b) {
    const left = parseCvDateParts(a);
    const right = parseCvDateParts(b);

    if (! left || ! right) {
        return null;
    }

    return (left.year * 12 + left.month) - (right.year * 12 + right.month);
}

export function isStartAfterEnd(start, end) {
    const cmp = compareCvMonths(start, end);

    return cmp !== null && cmp > 0;
}

const DATE_FIELDS_BY_SECTION = {
    experience: ['start_date', 'end_date'],
    education: ['start_date', 'end_date'],
    projects: ['start_date', 'end_date'],
    certifications: ['date'],
};

/** Normalize parseable date fields on a CV document to YYYY-MM (in place). */
export function normalizeDocumentDates(document) {
    if (! document || typeof document !== 'object') {
        return document;
    }

    Object.entries(DATE_FIELDS_BY_SECTION).forEach(([section, fields]) => {
        const items = document[section];

        if (! Array.isArray(items)) {
            return;
        }

        items.forEach((item) => {
            if (! item || typeof item !== 'object') {
                return;
            }

            fields.forEach((field) => {
                if (item[field] !== undefined && item[field] !== null && item[field] !== '') {
                    item[field] = toStorageMonth(item[field]);
                }
            });
        });
    });

    return document;
}
