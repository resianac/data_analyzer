import { router, usePage } from '@inertiajs/vue3';

/**
 *
 * @param routeUrl
 * @param options
 */
export default function useNavigation(routeUrl, options = {}) {
    const {
        query = {},
        page = null,
        pageSize = null,
        sort = null,
        filters = {},
        preserveState = true,
        preserveScroll = true,
        replace = true,
        only = [],
        onStart = null,
        onFinish = null,
        onError = null,
    } = options;


    const currentQuery = { ...usePage().props.query };
    const cleanedQuery = {};
    const newQuery = {
        ...currentQuery,
        ...filters,
        ...(page !== null && { page }),
        ...(pageSize !== null && { pageSize }),
        ...(sort && { ...sort }),
    };

    Object.keys(newQuery).forEach((key) => {
        const value = newQuery[key];

        if (value === null || value === undefined || value === '' || (Array.isArray(value) && value.length === 0)) {
            return;
        }

        cleanedQuery[key] = Array.isArray(value) ? value.join(',') : value;
    });

    router.get(routeUrl, cleanedQuery, {
        preserveState,
        preserveScroll,
        replace,
        only,
        onStart,
        onFinish,
        onError,
    });
};
