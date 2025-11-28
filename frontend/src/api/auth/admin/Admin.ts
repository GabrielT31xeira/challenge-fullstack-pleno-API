import axios from "axios";
import { API_BASE_URL } from "@/api/config.ts";
import {useAuthStore} from "@/stores/auth.ts";

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

export const createProduct = async (data) => {
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

export const updateProduct = async (product_id: string, data) => {
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