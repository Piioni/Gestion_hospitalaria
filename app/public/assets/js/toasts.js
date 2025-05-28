/**
 * Sistema de notificaciones toast
 * Permite crear y mostrar notificaciones en formato toast
 */
let ToastSystem = {
    /**
     * Crea y muestra una notificación toast
     * @param {string} type - Tipo de toast: 'success', 'warning', 'danger', 'info'
     * @param {string} title - Título del toast
     * @param {string} message - Mensaje del toast
     * @param {string|null} actions - HTML con botones o acciones (opcional)
     * @param {object} options - Opciones adicionales (opcional)
     * @returns {HTMLElement} - El elemento toast creado
     */
    show: function(type, title, message, actions = null, options = {}) {
        // Crear contenedor si no existe
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        
        // Crear el toast
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Obtener icono según el tipo
        const iconClass = this.getIconForType(type);
        
        // Crear contenido del toast
        let toastContent = `
            <div class="toast-header">
                <div class="toast-icon"><i class="fas fa-${iconClass}"></i></div>
                <h4 class="toast-title">${title}</h4>
                <button type="button" class="toast-close">&times;</button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;
        
        // Agregar acciones si existen
        if (actions) {
            toastContent += `<div class="toast-actions">${actions}</div>`;
        }
        
        toast.innerHTML = toastContent;
        
        // Añadir el toast al contenedor
        container.appendChild(toast);
        
        // Mostrar el toast con un pequeño retraso para la animación
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);
        
        // Configurar el botón de cerrar
        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                this.hideToast(toast);
            });
        }
        
        // Auto-eliminar después de un tiempo si se especifica
        if (options.autoClose) {
            const closeDelay = options.closeDelay || 5000;
            setTimeout(() => {
                this.hideToast(toast);
            }, closeDelay);
        }
        
        return toast;
    },
    
    /**
     * Oculta una notificación toast con animación
     * @param {HTMLElement} toast - El elemento toast a ocultar
     */
    hideToast: function(toast) {
        if (toast && toast.parentNode) {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }
    },
    
    /**
     * Elimina todos los toast actuales
     */
    clearAll: function() {
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach(toast => {
            this.hideToast(toast);
        });
    },

    /**
     * Obtiene el icono según el tipo de toast
     * @param {string} type - Tipo de toast
     * @returns {string} - Clase del icono
     */
    getIconForType: function(type) {
        switch (type) {
            case 'warning': return 'exclamation-triangle';
            case 'danger': return 'exclamation-circle';
            case 'success': return 'check-circle';
            default: return 'info-circle';
        }
    },
    
    // Shortcuts para tipos comunes de toast
    success: function(title, message, actions = null, options = {}) {
        return this.show('success', title, message, actions, options);
    },
    
    warning: function(title, message, actions = null, options = {}) {
        return this.show('warning', title, message, actions, options);
    },
    
    danger: function(title, message, actions = null, options = {}) {
        return this.show('danger', title, message, actions, options);
    },
    
    info: function(title, message, actions = null, options = {}) {
        return this.show('info', title, message, actions, options);
    }
};

// Añadir al objeto window para acceso global
window.ToastSystem = ToastSystem;
