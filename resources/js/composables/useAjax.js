import axios from 'axios';
import {reactive} from "vue";

export default function useAjax() {
    const state = reactive({
        errors: null,
        data: null,
        loading: false,
    });

    const instance = axios.create({
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        timeout: 5000,
    });

    const setCsrfToken = () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            instance.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        }
    };
    setCsrfToken();

    const handleError = (error) => {
        if (error.response) {
            return error.response.data?.errors || 'Server error';
        } else if (error.request) {
            return 'Network error - no response received';
        } else {
            return 'Request setup error';
        }
    };

    const makeRequest = async (
        method = 'GET',
        url,
        data = null,
        params = null,
        headers = {}
    ) => {
        state.loading = true;
        state.errors = null;
        state.data = null;

        try {
            const response = await instance({
                method: String(method).toUpperCase(),
                url,
                data: data,
                params: params,
                headers: {
                    ...instance.defaults.headers.common,
                    ...headers
                },
            });

            state.data = response.data;

            return response;
        } catch (error) {
            state.errors = handleError(error);
        } finally {
            state.loading = false
        }
    }

    return {
        state,
        call: makeRequest,
        get: (url, params, headers) => makeRequest('GET', url, null, params, headers),
        post: (url, data, headers) => makeRequest('POST', url, data, null, headers),
        put: (url, data, headers) => makeRequest('PUT', url, data, null, headers),
        patch: (url, data, headers) => makeRequest('PATCH', url, data, null, headers),
        delete: (url, headers) => makeRequest('DELETE', url, null, null, headers),
    };
}
