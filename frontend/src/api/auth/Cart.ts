import axios from "axios";
import { API_BASE_URL } from "../config";
import {useAuthStore} from "@/stores/auth.ts";

export interface addItemData {
    product_id: string;
    quantity: number;
}

export interface Cart {
    id: string;
    name: string;
    user_id: string;
    session_id: string;
    created_at: string;
    updated_at: string;
    items: any[];
    items_count: number;
    total_quantity: number;
    subtotal: number;
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

export interface CartResponse {
    success: boolean;
    data: Cart[];
    meta: PaginationMeta;
    links: PaginationLinks;
}

export interface AddItem {
    cart_id: string | null;
    quantity: number;
    product_id: string;
}

export interface createCartData {
    name: string;
}

export const getOne = async (id: string) => {
    const auth = useAuthStore();
    try {
        const response = await axios.get(`${API_BASE_URL}cart/${id}`, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro ao carregar carrinhos:", error.response?.data || error.message);
        throw error;
    }
}

export const getAll = async () => {
    const auth = useAuthStore();
    try {
        const response = await axios.get(`${API_BASE_URL}cart/getAll`, {
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro ao carregar carrinhos:", error.response?.data || error.message);
        throw error;
    }
}

export const fetchCarts = async (
    page: number = 1,
    filters?: { search?: string }
) => {
    const auth = useAuthStore();
    try {
        const params: any = { page };

        if (filters?.search) params.search = filters.search;

        const response = await axios.get(`${API_BASE_URL}cart`, {
            params,
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro ao carregar carrinhos:", error.response?.data || error.message);
        throw error;
    }
};

export const addItem = async (data: AddItem) => {
    const auth = useAuthStore();

    try {
        const payload = {
            cart_id: data.cart_id ?? null,
            quantity: data.quantity,
            product_id: data.product_id,
        };

        const response = await axios.post(
            `${API_BASE_URL}cart/items`,
            payload,
            {
                headers: {
                    Authorization: `Bearer ${auth.token}`,
                },
            }
        );

        return response.data;
    } catch (error: any) {
        console.error("Erro ao registrar item no carrinho:", error.response?.data || error.message);
        throw error;
    }
};


export const deleteItem = async (cart_id: string, product_id: string) => {
    const auth = useAuthStore();
    try {
        const response = await axios.delete(
            `${API_BASE_URL}cart/${cart_id}/items/${product_id}`,
            {
                headers: {
                    Authorization: `Bearer ${auth.token}`,
                },
            }
        );

        return response.data;
    } catch (error: any) {
        console.error("Erro ao registrar item no carrinho:", error.response?.data || error.message);
        throw error;
    }
}

export const updateItem = async (cart_id: string, product_id: string, quantity: number) => {
    const auth = useAuthStore();
    try {
        const response = await axios.put(
            `${API_BASE_URL}cart/${cart_id}/items/${product_id}`,
            {
                "quantity": quantity,
            }
            ,
            {
                headers: {
                    Authorization: `Bearer ${auth.token}`,
                },
            }
        );

        return response.data;
    } catch (error: any) {
        console.error("Erro ao registrar item no carrinho:", error.response?.data || error.message);
        throw error;
    }
}

export const createCart = async (data: createCartData) => {
    const auth = useAuthStore();

    try {
        const response = await axios.post(
            `${API_BASE_URL}cart`, data,
            {
                headers: {
                    Authorization: `Bearer ${auth.token}`,
                },
            }
        );

        return response.data;
    } catch (error: any) {
        console.error("Erro no registro:", error.response?.data || error.message);
        throw error;
    }
}

export const clearCart = async (cart_id: string) => {
    const auth = useAuthStore();
    try {
        const response = await axios.delete(
            `${API_BASE_URL}cart/${cart_id}/clear`,
            {
                headers: {
                    Authorization: `Bearer ${auth.token}`,
                },
            }
        );

        return response.data;
    } catch (error: any) {
        console.error("Erro ao registrar item no carrinho:", error.response?.data || error.message);
        throw error;
    }
}

export const deleteCart = async (cart_id: string) => {
    const auth = useAuthStore();
    try {
        const response = await axios.delete(
            `${API_BASE_URL}cart/${cart_id}/delete`,
            {
                headers: {
                    Authorization: `Bearer ${auth.token}`,
                },
            }
        );

        return response.data;
    } catch (error: any) {
        console.error("Erro ao registrar item no carrinho:", error.response?.data || error.message);
        throw error;
    }
}