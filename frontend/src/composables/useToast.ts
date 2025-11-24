import { ref } from "vue";

export interface Toast {
    id: number;
    type: "success" | "error";
    message: string;
}

const toasts = ref<Toast[]>([]);
let counter = 0;

export function useToast() {
    function addToast(type: "success" | "error", message: string) {
        const id = counter++;
        toasts.value.push({ id, type, message });

        setTimeout(() => {
            removeToast(id);
        }, 4000);
    }

    function removeToast(id: number) {
        toasts.value = toasts.value.filter(t => t.id !== id);
    }

    return {
        toasts,
        addToast,
    };
}
