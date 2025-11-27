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