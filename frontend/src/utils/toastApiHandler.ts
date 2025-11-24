import { useToast } from "../composables/useToast";

const { addToast } = useToast();

export function toastSuccess(message: string = "Operação realizada com sucesso!") {
    addToast("success", message);
}

export function toastError(message: string = "Erro inesperado.") {
    addToast("error", message);
}

export function toastValidation(errors: Record<string, string[]>) {
    const shown = new Set<string>();

    Object.values(errors).forEach((msgs) => {
        msgs.forEach(msg => {
            if (!shown.has(msg)) {
                addToast("error", msg);
                shown.add(msg);
            }
        });
    });
}

