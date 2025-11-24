import { defineStore } from "pinia";

interface User {
    id: string;
    name: string;
    email: string;
    role: "user" | "admin";
}

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null as User | null,
        token: null as string | null,
    }),

    actions: {
        setAuth(user: User, token: string) {
            this.user = user;
            this.token = token;

            localStorage.setItem("auth_user", JSON.stringify(user));
            localStorage.setItem("auth_token", token);
        },

        loadAuthFromStorage() {
            const user = localStorage.getItem("auth_user");
            const token = localStorage.getItem("auth_token");

            if (user && token) {
                this.user = JSON.parse(user);
                this.token = token;
            }
        },

        logout() {
            this.user = null;
            this.token = null;

            localStorage.removeItem("auth_user");
            localStorage.removeItem("auth_token");
        }
    },

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.role === "admin",
    },
});
