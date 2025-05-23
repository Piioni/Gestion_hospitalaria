document.addEventListener('DOMContentLoaded', function() {
    // Variables para almacenar las selecciones
    let selectedHospitales = {};
    let selectedPlantas = {};
    let selectedBotiquines = {};

    // Elementos de mensajes vacíos
    const noHospitalesMsg = document.getElementById('no-hospitals-msg');
    const noPlantasMsg = document.getElementById('no-plantas-msg');
    const noBotiquinesMsg = document.getElementById('no-botiquines-msg');

    // Selector de tipo de ubicación
    const locationTypeSelect = document.getElementById('location-type-select');
    
    // Secciones de ubicación
    const hospitalesSection = document.getElementById('hospitales-section');
    const plantasSection = document.getElementById('plantas-section');
    const botiquinesSection = document.getElementById('botiquines-section');
    
    // Contenedor de secciones
    const sectionsContainer = document.getElementById('location-sections-container');

    // Escuchar cambios en el selector de tipo de ubicación
    locationTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        
        // Ocultar todas las secciones
        hospitalesSection.style.display = 'none';
        plantasSection.style.display = 'none';
        botiquinesSection.style.display = 'none';
        
        // Ajustar clase del contenedor para manejar espaciado
        if (selectedType) {
            sectionsContainer.classList.add('has-selection');
        } else {
            sectionsContainer.classList.remove('has-selection');
        }
        
        // Mostrar la sección seleccionada
        if (selectedType === 'hospitales') {
            hospitalesSection.style.display = 'block';
            
            // Si se ha seleccionado hospitales, limpiar selecciones de plantas y botiquines
            if (Object.keys(selectedHospitales).length > 0) {
                selectedPlantas = {};
                selectedBotiquines = {};
                document.getElementById('selected-plantas').innerHTML = '';
                document.getElementById('selected-botiquines').innerHTML = '';
                noPlantasMsg.style.display = 'block';
                noBotiquinesMsg.style.display = 'block';
            }
        } else if (selectedType === 'plantas') {
            plantasSection.style.display = 'block';
            
            // Si se ha seleccionado plantas, limpiar selecciones de hospitales y botiquines
            if (Object.keys(selectedPlantas).length > 0) {
                selectedHospitales = {};
                selectedBotiquines = {};
                document.getElementById('selected-hospitals').innerHTML = '';
                document.getElementById('selected-botiquines').innerHTML = '';
                noHospitalesMsg.style.display = 'block';
                noBotiquinesMsg.style.display = 'block';
            }
        } else if (selectedType === 'botiquines') {
            botiquinesSection.style.display = 'block';
            
            // Si se ha seleccionado botiquines, limpiar selecciones de hospitales y plantas
            if (Object.keys(selectedBotiquines).length > 0) {
                selectedHospitales = {};
                selectedPlantas = {};
                document.getElementById('selected-hospitals').innerHTML = '';
                document.getElementById('selected-plantas').innerHTML = '';
                noHospitalesMsg.style.display = 'block';
                noPlantasMsg.style.display = 'block';
            }
        }
        
        // Actualizar campos ocultos
        updateHiddenFields();
    });

    // Función para actualizar los campos ocultos
    function updateHiddenFields() {
        const container = document.getElementById('hidden-fields-container');
        container.innerHTML = '';

        // Agregar campos para hospitales
        Object.keys(selectedHospitales).forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'hospitales[]';
            input.value = id;
            container.appendChild(input);
        });

        // Agregar campos para plantas
        Object.keys(selectedPlantas).forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'plantas[]';
            input.value = id;
            container.appendChild(input);
        });

        // Agregar campos para botiquines
        Object.keys(selectedBotiquines).forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'botiquines[]';
            input.value = id;
            container.appendChild(input);
        });

        // Actualizar visibilidad de mensajes vacíos
        if (noHospitalesMsg) {
            noHospitalesMsg.style.display = Object.keys(selectedHospitales).length > 0 ? 'none' : 'block';
        }
        
        if (noPlantasMsg) {
            noPlantasMsg.style.display = Object.keys(selectedPlantas).length > 0 ? 'none' : 'block';
        }
        
        if (noBotiquinesMsg) {
            noBotiquinesMsg.style.display = Object.keys(selectedBotiquines).length > 0 ? 'none' : 'block';
        }
    }

    // Agregar hospitales
    document.getElementById('add-hospital').addEventListener('click', function() {
        const select = document.getElementById('hospital-select');
        const id = select.value;
        const name = select.options[select.selectedIndex].text;

        if (id && !selectedHospitales[id]) {
            // Limpiar otras selecciones si existen
            if (Object.keys(selectedPlantas).length > 0 || Object.keys(selectedBotiquines).length > 0) {
                selectedPlantas = {};
                selectedBotiquines = {};
                document.getElementById('selected-plantas').innerHTML = '';
                document.getElementById('selected-botiquines').innerHTML = '';
                updateHiddenFields();
            }
            
            selectedHospitales[id] = name;

            const list = document.getElementById('selected-hospitals');
            const item = document.createElement('li');
            item.className = 'selection-item';
            item.innerHTML = `
                <span class="selection-item-text">
                    <i class="bi bi-hospital text-primary"></i>
                    ${name}
                </span>
                <button type="button" class="btn-remove remove-hospital" data-id="${id}" title="Eliminar">
                    <i class="bi bi-x-circle"></i>
                </button>
            `;
            list.appendChild(item);

            updateHiddenFields();
            select.value = '';
        }
    });

    // Agregar plantas
    document.getElementById('add-planta').addEventListener('click', function() {
        const select = document.getElementById('planta-select');
        const id = select.value;
        const name = select.options[select.selectedIndex].text;

        if (id && !selectedPlantas[id]) {
            // Limpiar otras selecciones si existen
            if (Object.keys(selectedHospitales).length > 0 || Object.keys(selectedBotiquines).length > 0) {
                selectedHospitales = {};
                selectedBotiquines = {};
                document.getElementById('selected-hospitals').innerHTML = '';
                document.getElementById('selected-botiquines').innerHTML = '';
                updateHiddenFields();
            }
            
            selectedPlantas[id] = name;

            const list = document.getElementById('selected-plantas');
            const item = document.createElement('li');
            item.className = 'selection-item';
            item.innerHTML = `
                <span class="selection-item-text">
                    <i class="bi bi-building text-success"></i>
                    ${name}
                </span>
                <button type="button" class="btn-remove remove-planta" data-id="${id}" title="Eliminar">
                    <i class="bi bi-x-circle"></i>
                </button>
            `;
            list.appendChild(item);

            updateHiddenFields();
            select.value = '';
        }
    });

    // Agregar botiquines
    document.getElementById('add-botiquin').addEventListener('click', function() {
        const select = document.getElementById('botiquin-select');
        const id = select.value;
        const name = select.options[select.selectedIndex].text;

        if (id && !selectedBotiquines[id]) {
            // Limpiar otras selecciones si existen
            if (Object.keys(selectedHospitales).length > 0 || Object.keys(selectedPlantas).length > 0) {
                selectedHospitales = {};
                selectedPlantas = {};
                document.getElementById('selected-hospitals').innerHTML = '';
                document.getElementById('selected-plantas').innerHTML = '';
                updateHiddenFields();
            }
            
            selectedBotiquines[id] = name;

            const list = document.getElementById('selected-botiquines');
            const item = document.createElement('li');
            item.className = 'selection-item';
            item.innerHTML = `
                <span class="selection-item-text">
                    <i class="bi bi-box-seam text-info"></i>
                    ${name}
                </span>
                <button type="button" class="btn-remove remove-botiquin" data-id="${id}" title="Eliminar">
                    <i class="bi bi-x-circle"></i>
                </button>
            `;
            list.appendChild(item);

            updateHiddenFields();
            select.value = '';
        }
    });

    // Eliminar elementos (delegación de eventos)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-hospital')) {
            const button = e.target.closest('.remove-hospital');
            const id = button.dataset.id;
            delete selectedHospitales[id];
            button.closest('li').remove();
            updateHiddenFields();
        }

        if (e.target.closest('.remove-planta')) {
            const button = e.target.closest('.remove-planta');
            const id = button.dataset.id;
            delete selectedPlantas[id];
            button.closest('li').remove();
            updateHiddenFields();
        }

        if (e.target.closest('.remove-botiquin')) {
            const button = e.target.closest('.remove-botiquin');
            const id = button.dataset.id;
            delete selectedBotiquines[id];
            button.closest('li').remove();
            updateHiddenFields();
        }
    });

    // Inicializar los mensajes vacíos
    updateHiddenFields();
});
