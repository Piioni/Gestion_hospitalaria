document.addEventListener('DOMContentLoaded', function() {
    // Inicializar la gestión de ubicaciones basada en el locationType
    initLocationManagement();
});

function initLocationManagement() {
    // Configurar manejo de ubicaciones según el tipo de rol
    if (locationType === 'admin') {
        // No se necesitan configuraciones para roles administrativos
        return;
    }

    // Configurar selector según el tipo de ubicación
    const config = {
        hospitales: {
            selectId: 'hospital-select',
            btnId: 'add-hospital',
            listId: 'selected-hospitals',
            inputName: 'hospital_ids',
            emptyMsgId: 'no-hospitals-msg',
            entityName: 'hospital'
        },
        plantas: {
            selectId: 'planta-select',
            btnId: 'add-planta',
            listId: 'selected-plantas',
            inputName: 'planta_ids',
            emptyMsgId: 'no-plantas-msg',
            entityName: 'planta'
        },
        botiquines: {
            selectId: 'botiquin-select',
            btnId: 'add-botiquin',
            listId: 'selected-botiquines',
            inputName: 'botiquin_ids',
            emptyMsgId: 'no-botiquines-msg',
            entityName: 'botiquín'
        }
    };

    // Configurar manejo de ubicaciones si existe el tipo
    if (config[locationType]) {
        setupLocationSelector(config[locationType]);
    } else {
        console.warn("Tipo de ubicación desconocido:", locationType);
    }

    // Configurar validación del formulario
    setupFormValidation();
}

// Función unificada para configurar selectores de ubicación
function setupLocationSelector(config) {
    const selectElement = document.getElementById(config.selectId);
    const addBtn = document.getElementById(config.btnId);
    const listElement = document.getElementById(config.listId);

    if (!selectElement || !addBtn || !listElement) {
        console.error(`No se encontraron los elementos necesarios para el selector ${config.selectId}`);
        return;
    }

    // Evento para agregar una ubicación
    addBtn.addEventListener('click', function() {
        addItemToSelection(
            selectElement,
            listElement,
            config.inputName,
            config.emptyMsgId,
            config.entityName
        );
    });

    // Configurar eventos de eliminación para elementos existentes
    setupRemoveEvents(listElement, config.emptyMsgId);
}

// Función para agregar un ítem a la lista de selección
function addItemToSelection(selectElement, listElement, inputNamePrefix, emptyMessageId, entityName = 'elemento') {
    // Verificar que se haya seleccionado un valor
    if (!selectElement.value) {
        Toast.warning(`Por favor, seleccione un ${entityName} antes de añadirlo.`);
        return;
    }

    const selectedValue = selectElement.value;
    const selectedText = selectElement.options[selectElement.selectedIndex].text;

    // Verificar si ya existe en la lista
    if (itemExistsInList(listElement, selectedValue)) {
        Toast.info(`Este ${entityName} ya está en la lista.`);
        return;
    }

    // Eliminar mensaje de "no hay elementos" si existe
    const emptyMessage = document.getElementById(emptyMessageId);
    if (emptyMessage) {
        emptyMessage.remove();
    }

    // Crear elemento de lista
    const listItem = document.createElement('li');
    listItem.className = 'selection-item';
    listItem.dataset.id = selectedValue;

    // Crear contenido del elemento
    listItem.innerHTML = `
        <span class="item-name">${selectedText}</span>
        <span class="remove-item" title="Eliminar"><i class="bi bi-trash"></i></span>
        <input type="hidden" name="${inputNamePrefix}[]" value="${selectedValue}">
    `;

    // Agregar evento para eliminar el elemento
    listItem.querySelector('.remove-item').addEventListener('click', function() {
        removeItemFromList(listItem, listElement, emptyMessageId, entityName);
    });

    // Agregar elemento a la lista
    listElement.appendChild(listItem);

    // Mostrar notificación de éxito
    Toast.success(`Se ha añadido "${selectedText}" a la lista.`);

    // Restablecer el select al valor predeterminado
    selectElement.value = '';
}

// Función para eliminar un elemento de la lista
function removeItemFromList(listItem, listElement, emptyMessageId, entityName = 'elemento') {
    const itemName = listItem.querySelector('.item-name').textContent;
    listItem.remove();

    // Si no quedan elementos, mostrar mensaje de "no hay elementos"
    if (listElement.children.length === 0) {
        addEmptyMessage(listElement, emptyMessageId);
    }

    // Mostrar notificación
    Toast.info(`Se ha eliminado "${itemName}" de la lista.`);
}

// Función para verificar si un ítem existe en la lista de selección
function itemExistsInList(listElement, value) {
    return Array.from(listElement.querySelectorAll('.selection-item'))
        .some(item => item.dataset.id === value);
}

// Función para agregar mensaje de "no hay elementos"
function addEmptyMessage(listElement, emptyMessageId) {
    const iconMap = {
        'hospitales': 'hospital',
        'plantas': 'building',
        'botiquines': 'box-seam'
    };

    const messageMap = {
        'hospitales': 'No hay hospitales seleccionados',
        'plantas': 'No hay plantas seleccionadas',
        'botiquines': 'No hay botiquines seleccionados'
    };

    // Determinar tipo de contenido basado en el ID
    const type = emptyMessageId.includes('hospital') ? 'hospitales' :
                 emptyMessageId.includes('planta') ? 'plantas' : 'botiquines';

    const emptyMessage = document.createElement('li');
    emptyMessage.className = 'empty-selection-message';
    emptyMessage.id = emptyMessageId;
    emptyMessage.innerHTML = `<i class="bi bi-info-circle me-2"></i>${messageMap[type]}`;

    listElement.appendChild(emptyMessage);
}

// Configurar eventos de eliminación para elementos existentes
function setupRemoveEvents(listElement, emptyMessageId) {
    const removeButtons = listElement.querySelectorAll('.remove-item');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const listItem = this.closest('.selection-item');
            const itemName = listItem.querySelector('.item-name').textContent;

            // Determinar el tipo de elemento basado en el ID del listElement
            let entityName = 'elemento';
            if (listElement.id.includes('hospital')) {
                entityName = 'hospital';
            } else if (listElement.id.includes('planta')) {
                entityName = 'planta';
            } else if (listElement.id.includes('botiquin')) {
                entityName = 'botiquín';
            }

            removeItemFromList(listItem, listElement, emptyMessageId, entityName);
        });
    });
}

// Configurar validación del formulario
function setupFormValidation() {
    const form = document.getElementById('locationForm');
    if (!form) return;

    form.addEventListener('submit', function(event) {
        // Verificar si hay elementos seleccionados según el tipo de ubicación
        const listId = locationType === 'hospitales' ? 'selected-hospitals' :
                      locationType === 'plantas' ? 'selected-plantas' : 'selected-botiquines';

        const list = document.getElementById(listId);
        if (!list) return;

        const items = list.querySelectorAll('.selection-item');

        // Advertir pero permitir guardar sin ubicaciones asignadas
        if (items.length === 0) {
            const confirmation = confirm("¿Está seguro que desea guardar sin asignar ninguna ubicación? El usuario no tendrá acceso a ningún dato.");
            if (!confirmation) {
                event.preventDefault();
                return;
            }

            // Permitir continuar con la advertencia
            Toast.warning("Se guardarán los cambios sin ubicaciones asignadas.");
        } else {
            // Mostrar la cantidad de ubicaciones que se van a guardar
            Toast.info(`Se guardarán ${items.length} ubicaciones.`);
        }
    });
}
