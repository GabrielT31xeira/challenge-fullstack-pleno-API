import axios from "axios";
import { API_BASE_URL } from "../config";
import {useAuthStore} from "@/stores/auth.ts";

export interface createOrderData {
    cart_id: string;
    shipping_address: Array<string>,
    billing_address: Array<string>,
    notes: string,
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

export interface OrderResponse {
    id: string;
    status: OrderStatus;
    subtotal: number;
    tax: number;
    shipping_cost: number;
    total: number;
    notes: string | null;

    shipping_address: string | null;
    billing_address: string | null;

    items: OrderItem[];
    cart: Cart | null;

    created_at: string;
}

export type OrderStatus =
    | "pending"
    | "processing"
    | "shipped"
    | "delivered"
    | "cancelled";

// === Order Items ===
export interface OrderItem {
    id: string;
    product_id: string;
    quantity: number;
    price: number;
    total: number;

    product: Product | null;
}

// === Cart ===
export interface Cart {
    id: string;
    name: string;
    user_id: string;
    subtotal: number;
    total: number;
    created_at: string;
    updated_at: string;

    items: CartItem[];
}

// === Cart Items ===
export interface CartItem {
    id: string;
    product_id: string;
    quantity: number;
    total: number;

    product: Product;
}

// === Product ===
export interface Product {
    id: string;
    name: string;
    price: number;
    image_url?: string | null;
}


export const fetchOrders = async (
    page: number = 1,
    filters?: { search?: string },
) => {
    const auth = useAuthStore();
    try {
        const params: any = { page };

        if (filters) {
            if (filters.name) params.search = filters.name;
        }

        const response = await axios.get(`${API_BASE_URL}orders`, {
            params,
            headers: {
                Authorization: `Bearer ${auth.token}`,
            },
        });

        return response.data;
    } catch (error: any) {
        console.error("Erro ao carregar pedidos:", error.response?.data || error.message);
        throw error;
    }
};

export const createOrder = async (data: createOrderData) => {
    const auth = useAuthStore();

    try {
        const payload = {
            cart_id: data.cart_id ?? null,
            shipping_address: data.shipping_address,
            billing_address: data.billing_address,
            notes: data.notes,
        };

        const response = await axios.post(
            `${API_BASE_URL}orders`,
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