export function toastComponent(initialMessages = {}) {
    return {
        toasts: [],
        nextId: 0,
        
        initToasts() {
            // Lê as mensagens passadas via @json() do Blade
            if (initialMessages.success) {
                this.addToast('success', 'Sucesso', initialMessages.success);
            }
            if (initialMessages.error) {
                this.addToast('error', 'Erro', initialMessages.error);
            }
            if (initialMessages.warning) {
                this.addToast('warning', 'Atenção', initialMessages.warning);
            }
            if (initialMessages.info) {
                this.addToast('info', 'Informação', initialMessages.info);
            }
            if (initialMessages.has_errors) {
                this.addToast('error', 'Atenção', 'Por favor, corrija os erros destacados no formulário.');
            }
        },
        
        addToast(type, title, message, duration = 4000) {
            const id = this.nextId++;
            this.toasts.push({ id, type, title, message, visible: true });
            
            setTimeout(() => {
                this.removeToast(id);
            }, duration);
        },
        
        removeToast(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300); // Tempo para a animação de saída terminar
            }
        },
        
        toastClasses(type) {
            const classes = {
                success: 'bg-emerald-50 border-emerald-200 text-emerald-900',
                error: 'bg-rose-50 border-rose-200 text-rose-900',
                warning: 'bg-amber-50 border-amber-200 text-amber-900',
                info: 'bg-blue-50 border-blue-200 text-blue-900',
            };
            return classes[type] || classes.info;
        }
    };
}