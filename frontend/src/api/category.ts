import axios from "axios";
import {API_BASE_URL} from "./config";

export interface Category {
    id: string;
    name: string;
}

export const fetchCategories = async (): Promise<Category[]> => {
    try {
        const response = await axios.get(`${API_BASE_URL}categories`);

        return response.data.data.map((cat: any) => ({
            id: cat.id,
            name: cat.name,
        }));
    } catch (error) {
        console.error("Erro ao buscar categorias:", error);
        throw error;
    }
};
