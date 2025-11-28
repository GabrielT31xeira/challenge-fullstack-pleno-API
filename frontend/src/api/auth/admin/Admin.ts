import axios from "axios";
import { API_BASE_URL } from "@/api/config.ts";
import {useAuthStore} from "@/stores/auth.ts";
import type {ProductCategory} from "@/api/product.ts";

export interface Category {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    active: boolean;
    parent_id: string | null;
}

export interface PaginationMeta {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
}

export interface PaginationLinks {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
}

export interface CategoryResponse {
    success: boolean;
    data: Category[];
    meta: PaginationMeta;
    links: PaginationLinks;
}

export interface Product {
    id: string;
    name: string;
    description: string;
    price: string;
    quantity: number;
    min_quantity: number;
    active: number;
    category: ProductCategory;
    tags: { name: string; slug: string }[];
}

export const dashboard = async () => {
    const auth = useAuthStore();
    try {
        const response = await axios.get(`${API_BASE_URL}admin/dashboard`, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro os dados da dashboard:", error.response?.data || error.message);
        throw error;
    }
}

export const lowStock = async () => {
    const auth = useAuthStore();
    try {
        const response = await axios.get(`${API_BASE_URL}admin/products/lowstock`, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro os dados da dashboard:", error.response?.data || error.message);
        throw error;
    }
}

export const getAllTags = async () => {
    const auth = useAuthStore();
    try {
        const response = await axios.get(`${API_BASE_URL}admin/tags`, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro ao buscar todas as tags:", error.response?.data || error.message);
        throw error;
    }
}

export const createCategory = async (category: Category) => {
    const auth = useAuthStore();

    try {
        const response = await axios.post(`${API_BASE_URL}admin/categories/create`, category, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro ao buscar todas as tags:", error.response?.data || error.message);
        throw error;
    }
}

export const updateCategory = async (category: Category, id: string) => {
    const auth = useAuthStore();

    try {
        const response = await axios.put(`${API_BASE_URL}admin/categories/update/${id}`, category, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro ao buscar todas as tags:", error.response?.data || error.message);
        throw error;
    }
}

export const removeCategory = async (id: string) => {
    const auth = useAuthStore();

    try {
        const response = await axios.delete(`${API_BASE_URL}admin/categories/delete/${id}`, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro ao buscar todas as tags:", error.response?.data || error.message);
        throw error;
    }
}

export const fetchCategory = async (
    page: number = 1,
    filters?: { name?: string; }
): Promise<CategoryResponse> => {
    const auth = useAuthStore();
    try {
        const params: any = { page };

        if (filters) {
            if (filters.name) params.search = filters.name;
        }

        const response = await axios.get<CategoryResponse>(`${API_BASE_URL}admin/categories/paginated`,
            {
                params,
                headers: {
                    Authorization: `Bearer ${auth.token}`,
                },
            }
        );
        return response.data;
    } catch (error) {
        console.error("Erro ao buscar categoria:", error);
        throw error;
    }
};

export const createProduct = async (data: any) => {
    const auth = useAuthStore();
    try {
        const response = await axios.post(`${API_BASE_URL}products`, data, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro os dados da dashboard:", error.response?.data || error.message);
        throw error;
    }
}

export const updateProduct = async (product_id: string, data: any) => {
    const auth = useAuthStore();
    try {
        const response = await axios.put(`${API_BASE_URL}products/${product_id}`, data, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro os dados da dashboard:", error.response?.data || error.message);
        throw error;
    }
}

export const removeProduct = async (product_id: string) => {
    const auth = useAuthStore();
    try {
        const response = await axios.delete(`${API_BASE_URL}products/${product_id}`, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro os dados da dashboard:", error.response?.data || error.message);
        throw error;
    }
}