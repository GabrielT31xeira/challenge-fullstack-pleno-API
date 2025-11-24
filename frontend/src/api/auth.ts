import axios from "axios";
import { API_BASE_URL } from "./config";
import {useAuthStore} from "@/stores/auth.ts";

export interface LoginData {
    email: string;
    password: string;
}

export interface RegisterData {
    name: string;
    email: string;
    password: string;
}

export const loginUser = async (data: LoginData) => {
    try {
        const response = await axios.post(`${API_BASE_URL}login`, data);
        return response.data;
    } catch (error: any) {
        console.error("Erro no login:", error.response?.data || error.message);
        throw error;
    }
};

export const registerUser = async (data: RegisterData) => {
    try {
        const response = await axios.post(`${API_BASE_URL}register`, data);
        return response.data;
    } catch (error: any) {
        console.error("Erro no registro:", error.response?.data || error.message);
        throw error;
    }
};

export const logoutUser = async () => {
    const auth = useAuthStore();

    if (!auth.token) return;

    try {
        await axios.post(
            `${API_BASE_URL}logout`,
            {},
            {
                headers: {
                    Authorization: `Bearer ${auth.token}`,
                },
            }
        );

        auth.logout();

    } catch (err: any) {
        console.error("Erro ao deslogar:", err);
    }
};
