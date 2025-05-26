document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    const dropdowns = document.querySelectorAll('.dropdown');
    
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
        // Asegurar que todos los menús desplegables estén ocultos
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
    
    // IMPORTANTE: Al cargar la página, forzar el cierre de todos los menús desplegables
    closeAllDropdowns();
    
    // Manejar dropdowns
    dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');
        
        // Mouse over - mostrar menú en desktop
        dropdown.addEventListener('mouseenter', function(e) {
            if (window.innerWidth > 768) {
                closeAllDropdowns();
                this.classList.add('show');
                if (menu) menu.style.display = 'block';
            }
        });
        
        // Mouse out - ocultar menú en desktop
        dropdown.addEventListener('mouseleave', function(e) {
            if (window.innerWidth > 768) {
                this.classList.remove('show');
                if (menu) menu.style.display = 'none';
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
                        if (menu) menu.style.display = 'block';
                    }
                }
            });
        }
    });
    
    // Cerrar menús cuando se hace clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
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
            
            // Si el enlace está en un dropdown, también marcar el padre
            const parentDropdown = link.closest('.dropdown');
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
