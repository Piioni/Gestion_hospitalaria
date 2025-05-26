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
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
    
    // Cerrar todos los menús desplegables al cargar
    closeAllDropdowns();
    
    // Variables para gestionar el tiempo de retardo al cerrar menús
    let closeTimeout;
    const delayClose = 200; // Reducido de 500ms a 200ms para que desaparezcan más rápido
    
    // Manejar dropdowns con retraso para mejorar la usabilidad
    dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');
        
        // Mouse enter - mostrar menú
        dropdown.addEventListener('mouseenter', function() {
            if (window.innerWidth > 768) {
                // Cancelar cualquier cierre pendiente
                clearTimeout(closeTimeout);
                
                // Cerrar otros dropdowns abiertos
                dropdowns.forEach(otherDropdown => {
                    if (otherDropdown !== dropdown && otherDropdown.classList.contains('show')) {
                        otherDropdown.classList.remove('show');
                        const otherMenu = otherDropdown.querySelector('.dropdown-menu');
                        if (otherMenu) otherMenu.style.display = 'none';
                    }
                });
                
                // Mostrar este dropdown
                this.classList.add('show');
                if (menu) {
                    menu.style.display = 'block';
                }
            }
        });
        
        // Mouse leave - ocultar menú con retraso menor
        dropdown.addEventListener('mouseleave', function(event) {
            if (window.innerWidth > 768) {
                // Verificamos si el cursor se está moviendo hacia el menú desplegable
                const toElement = event.relatedTarget;
                
                // Si se mueve hacia el menú desplegable, no cerramos
                if (menu && menu.contains(toElement)) {
                    return;
                }
                
                // Configurar un retraso antes de cerrar
                closeTimeout = setTimeout(() => {
                    this.classList.remove('show');
                    if (menu) menu.style.display = 'none';
                }, delayClose);
            }
        });
        
        // También aplicamos el mismo comportamiento al menú desplegable
        if (menu) {
            menu.addEventListener('mouseenter', function() {
                // Cancelar cualquier cierre pendiente cuando el ratón está en el menú
                clearTimeout(closeTimeout);
            });
            
            menu.addEventListener('mouseleave', function(event) {
                if (window.innerWidth > 768) {
                    // Verificamos si el cursor se está moviendo hacia el elemento padre dropdown
                    const toElement = event.relatedTarget;
                    
                    // Si se mueve hacia el elemento padre, no cerramos
                    if (dropdown.contains(toElement)) {
                        return;
                    }
                    
                    // Retrasar el cierre cuando el ratón sale del menú
                    closeTimeout = setTimeout(() => {
                        dropdown.classList.remove('show');
                        this.style.display = 'none';
                    }, delayClose);
                }
            });
        }
        
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
        if (!e.target.closest('.dropdown') && window.innerWidth > 768) {
            closeAllDropdowns();
        }
    });
    
    // Marcar el elemento activo basado en la URL actual
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-links a');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPath || (href !== '/' && href !== '#' && currentPath.startsWith(href))) {
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
            if (mobileMenuToggle) mobileMenuToggle.classList.remove('active');
        }
    });
});
