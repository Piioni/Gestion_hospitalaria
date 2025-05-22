document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    const dropdowns = document.querySelectorAll('.dropdown');
    const megaDropdowns = document.querySelectorAll('.mega-dropdown');
    
    // Toggle del menú móvil
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mainNav.classList.toggle('show');
        });
    }
    
    // Función para cerrar todos los menús desplegables
    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
        document.querySelectorAll('.mega-dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
        // También eliminar cualquier clase show de los elementos dropdown-content
        document.querySelectorAll('.mega-dropdown-content').forEach(content => {
            content.style.display = 'none';
        });
    }
    
    // IMPORTANTE: Al cargar la página, forzar el cierre de todos los menús desplegables
    closeAllDropdowns();
    
    // Forzar estilos iniciales correctos
    document.querySelectorAll('.mega-dropdown-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Manejar dropdowns normales
    dropdowns.forEach(dropdown => {
        // Mouse over - mostrar menú en desktop
        dropdown.addEventListener('mouseenter', function(e) {
            if (window.innerWidth > 768) {
                closeAllDropdowns();
                this.classList.add('show');
            }
        });
        
        // Mouse out - ocultar menú en desktop
        dropdown.addEventListener('mouseleave', function(e) {
            if (window.innerWidth > 768) {
                this.classList.remove('show');
            }
        });
        
        // Click en toggle para abrir/cerrar en mobile
        const toggleLink = dropdown.querySelector('.dropdown-toggle');
        if (toggleLink) {
            toggleLink.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (window.innerWidth <= 768) {
                    // En móvil, toggle el estado actual
                    const isOpen = dropdown.classList.contains('show');
                    
                    // Cerrar todos los dropdowns
                    closeAllDropdowns();
                    
                    // Si no estaba abierto, abrirlo
                    if (!isOpen) {
                        dropdown.classList.add('show');
                    }
                }
            });
        }
    });
    
    // Manejar mega-dropdowns - mismo comportamiento que dropdowns normales
    megaDropdowns.forEach(megaDropdown => {
        const content = megaDropdown.querySelector('.mega-dropdown-content');
        
        megaDropdown.addEventListener('mouseenter', function(e) {
            if (window.innerWidth > 768) {
                closeAllDropdowns();
                this.classList.add('show');
                if (content) content.style.display = 'flex';
            }
        });
        
        megaDropdown.addEventListener('mouseleave', function(e) {
            if (window.innerWidth > 768) {
                this.classList.remove('show');
                if (content) content.style.display = 'none';
            }
        });
        
        const toggleLink = megaDropdown.querySelector('.dropdown-toggle');
        if (toggleLink) {
            toggleLink.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (window.innerWidth <= 768) {
                    const isOpen = megaDropdown.classList.contains('show');
                    closeAllDropdowns();
                    
                    if (!isOpen) {
                        megaDropdown.classList.add('show');
                        if (content) content.style.display = 'flex';
                    }
                }
            });
        }
    });
    
    // Cerrar menús cuando se hace clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown') && !e.target.closest('.mega-dropdown')) {
            closeAllDropdowns();
        }
    });
    
    // Marcar el elemento activo basado en la URL actual
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-links a');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPath || (href !== '/' && currentPath.startsWith(href))) {
            link.classList.add('active');
            
            // Si el enlace está en un dropdown o mega-dropdown, también marcar el padre
            const parentDropdown = link.closest('.dropdown, .mega-dropdown');
            if (parentDropdown) {
                const parentLink = parentDropdown.querySelector('.dropdown-toggle');
                if (parentLink) {
                    parentLink.classList.add('active');
                }
            }
        }
    });
    
    // Ajustar al redimensionar la ventana
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            mainNav.classList.remove('show');
            mobileMenuToggle?.classList.remove('active');
        }
    });
    
    // Asegurar que los menús estén cerrados después de que todo esté cargado
    window.addEventListener('load', function() {
        closeAllDropdowns();
    });
});
