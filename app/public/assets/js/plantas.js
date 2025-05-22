document.addEventListener('DOMContentLoaded', function() {
    // Inicializar los colapsables si están presentes
    const collapsibles = document.querySelectorAll('.collapsible-header');
    if (collapsibles.length > 0) {
        collapsibles.forEach(collapsible => {
            collapsible.addEventListener('click', function() {
                // La función toggleCollapsible ya está definida en la página
            });
        });
    }

    // Agregar funcionalidad de filtrado si hay un campo de búsqueda
    const searchField = document.getElementById('planta-search');
    if (searchField) {
        searchField.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const plantaCards = document.querySelectorAll('.planta-card');
            
            plantaCards.forEach(card => {
                const plantaName = card.querySelector('.planta-name').textContent.toLowerCase();
                const hospitalInfo = card.querySelector('.planta-info').textContent.toLowerCase();
                
                if (plantaName.includes(searchTerm) || hospitalInfo.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

});
