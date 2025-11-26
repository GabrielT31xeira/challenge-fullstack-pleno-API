import axios from "axios";
import { API_BASE_URL } from "../config";
import {useAuthStore} from "@/stores/auth.ts";

export interface createOrderData {
    cart_id: string;
    shipping_address: Array<string>,
    billing_address: Array<string>,
    notes: string,
}

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