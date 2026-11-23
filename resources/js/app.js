// Importar Alpine.js
import Alpine from 'alpinejs';

// Importar componentes
import { toastComponent } from './components/toast';
import { confirmModalComponent } from './components/confirm-modal';
import { loadingStateComponent } from './components/loading-state';
import { dropdownComponent } from './components/dropdown';

// Importar utilitários
import { debounce, formatCurrency, formatDate } from './utils/helpers';

// Registrar componentes no Alpine
window.Alpine = Alpine;

Alpine.data('toastComponent', toastComponent);
Alpine.data('confirmModalComponent', confirmModalComponent);
Alpine.data('loadingStateComponent', loadingStateComponent);
Alpine.data('dropdownComponent', dropdownComponent);

// Disponibilizar utilitários globalmente
window.utils = {
    debounce,
    formatCurrency,
    formatDate
};

// Iniciar Alpine
Alpine.start();

console.log('✅ Frontend modular carregado com sucesso');