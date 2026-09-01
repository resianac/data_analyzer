import { computed } from 'vue';
import {
    SOURCES,
    DEFAULT_SOURCE,
    getSource,
    getAllSources,
} from '../constants/sources.js';

/**
 * Composable for working with sources
 */
export function useSource(sourceKey) {
    const source = computed(() => getSource(sourceKey?.value || sourceKey));

    const label = source.value.label;
    const logo = source.value.logo;
    const website = source.value.website;
    const color = source.value.color;
    const id = source.value.id;

    const isKnown = computed(() => {
        return !!SOURCES[sourceKey?.value?.toLowerCase?.() || sourceKey?.toLowerCase?.()];
    });

    return {
        source,
        label,
        logo,
        website,
        color,
        id,
        isKnown,

        getSource,
        getAllSources,
    };
}

/**
 * Standalone helpers (for use outside components)
 */
export {
    getSource,
    getAllSources,
};
