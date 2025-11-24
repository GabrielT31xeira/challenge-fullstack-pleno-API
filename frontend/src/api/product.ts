import axios from "axios";
import { API_BASE_URL } from "./config";

export interface ProductCategory {
    id: string;
    name: string;
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

export interface ProductResponse {
    success: boolean;
    data: Product[];
    meta: PaginationMeta;
    links: PaginationLinks;
}

export const fetchProducts = async (
    page: number = 1,
    filters?: { categoryId?: string; name?: string; price?: number }
): Promise<ProductResponse> => {
    try {
        const params: any = { page };

        if (filters) {
            if (filters.categoryId) params.category_id = filters.categoryId;
            if (filters.name) params.search = filters.name;
            if (filters.price) params.max_price = filters.price;
        }

        const response = await axios.get<ProductResponse>(`${API_BASE_URL}products`,
            { params }
        );
        return response.data;
    } catch (error) {
        console.error("Erro ao buscar produtos:", error);
        throw error;
    }
};
