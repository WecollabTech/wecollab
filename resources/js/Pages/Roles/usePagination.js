import { ref } from "vue";

export function usePagination(loadDataFunction) {
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        prev_page_url: null,
        next_page_url: null,
    });

    const changePage = async (url) => {
        if (!url) return;

        try {
            const response = await axios.get(url);
            loadDataFunction(response.data.data);
            updatePagination(response.data);
        } catch (error) {
            console.error("Error al cambiar de página:", error);
        }
    };

    const updatePagination = (data) => {
        pagination.value = {
            current_page: data.current_page,
            last_page: data.last_page,
            prev_page_url: data.prev_page_url,
            next_page_url: data.next_page_url,
        };
    };

    return {
        pagination,
        changePage,
        updatePagination,
    };
}
