/**
 * Source configuration
 * Each source has: label, logo path, website URL, and color
 */
export const SOURCES = {
    ultra: {
        id: 'ultra',
        label: 'Ultra',
        logo: '/assets/images/sources/logo/ultra_logo.png',
        website: 'https://www.ultra.md',
        color: '#0055A4',
    },
    enter: {
        id: 'enter',
        label: 'Enter',
        logo: '/assets/images/sources/logo/enter_logo.ico',
        website: 'https://www.enter.md',
        color: '#00A3E0',
    },
};

/**
 * Default source (fallback)
 */
export const DEFAULT_SOURCE = {
    id: 'unknown',
    label: 'Unknown',
    logo: '/assets/images/sources/unknown.png',
    website: '#',
    color: '#6B7280',
};

/**
 * Get source by key
 */
export const getSource = (key) => {
    if (!key) return DEFAULT_SOURCE;
    const normalizedKey = key.toLowerCase().trim();
    return SOURCES[normalizedKey] || {
        ...DEFAULT_SOURCE,
        id: normalizedKey,
        label: normalizedKey.charAt(0).toUpperCase() + normalizedKey.slice(1),
    };
};

/**
 * Get all sources as array
 */
export const getAllSources = () => {
    return Object.values(SOURCES);
};
