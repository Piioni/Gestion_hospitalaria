/**
 * Función para seleccionar el tipo de almacén (general o planta)
 * @param {string} prefijo - Prefijo para los IDs de los elementos ('origen' o 'destino')
 * @param {boolean} esGeneral - Indica si se seleccionó almacén general (true) o de planta (false)
 * @param {boolean} sinActualizar - Si es true, no actualiza el almacén (usado para inicialización)
 */
function selectAlmacenTipo(prefijo, esGeneral, sinActualizar = false) {
    const inputEsGeneral = document.getElementById(`${prefijo}_es_general`);
    const btnGeneral = document.getElementById(`${prefijo}_btn_general`);
    const btnPlanta = document.getElementById(`${prefijo}_btn_planta`);
    const plantaContainer = document.getElementById(`${prefijo}_planta_container`);
    
    // Actualizar el valor del input hidden
    inputEsGeneral.value = esGeneral ? '1' : '0';
    
    // Actualizar clases de botones
    if (esGeneral) {
        btnGeneral.classList.add('active', 'btn-primary');
        btnGeneral.classList.remove('btn-secondary');
        
        btnPlanta.classList.remove('active', 'btn-primary');
        btnPlanta.classList.add('btn-secondary');
        
        plantaContainer.style.display = 'none';
        document.getElementById(`${prefijo}_planta`).value = '';
    } else {
        btnPlanta.classList.add('active', 'btn-primary');
        btnPlanta.classList.remove('btn-secondary');
        
        btnGeneral.classList.remove('active', 'btn-primary');
        btnGeneral.classList.add('btn-secondary');
        
        plantaContainer.style.display = 'block';
    }
    
    // Actualizar el almacén seleccionado si es necesario
    if (!sinActualizar) {
        cargarPlantasAlmacen(prefijo);
    } else if (esGeneral) {
        actualizarAlmacen(prefijo);
    }
}

/**
 * Función genérica para cargar plantas en un selector según el hospital seleccionado
 * @param {string} prefijo - Prefijo para los IDs de los elementos ('origen' o 'destino')
 */
function cargarPlantasAlmacen(prefijo) {
    const hospitalId = document.getElementById(`${prefijo}_hospital`).value;
    const plantaSelect = document.getElementById(`${prefijo}_planta`);
    const esGeneral = document.getElementById(`${prefijo}_es_general`).value === '1';
    
    // Limpiar selector de plantas
    plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';
    
    if (hospitalId && !esGeneral) {
        // Filtrar plantas por hospital y agregar opciones
        window.plantas
            .filter(p => p.id_hospital == hospitalId)
            .forEach(planta => {
                const option = document.createElement('option');
                option.value = planta.id_planta || planta.id;
                option.textContent = planta.nombre;
                plantaSelect.appendChild(option);
            });
    }
    
    // Actualizar el almacén seleccionado
    actualizarAlmacen(prefijo);
}

/**
 * Alias para cargarPlantasAlmacen para mantener compatibilidad con código existente
 * @param {string} prefijo - Prefijo para los IDs de los elementos ('origen' o 'destino')
 */
function cargarPlantas(prefijo = 'origen') {
    cargarPlantasAlmacen(prefijo);
}

/**
 * Función genérica para actualizar el almacén seleccionado
 * @param {string} prefijo - Prefijo para los IDs de los elementos ('origen' o 'destino')
 */
function actualizarAlmacen(prefijo = 'origen') {
    const hospitalId = document.getElementById(`${prefijo}_hospital`).value;
    const plantaId = document.getElementById(`${prefijo}_planta`).value;
    const esGeneral = document.getElementById(`${prefijo}_es_general`).value === '1';
    
    // Identificar el input correcto según el prefijo
    const almacenIdInput = (prefijo === 'origen') 
        ? document.getElementById(`id_${prefijo}`) || document.getElementById('id_almacen')
        : document.getElementById(`id_${prefijo}`);
        
    const almacenNombreInput = document.getElementById(`${prefijo}_almacen_nombre`);
    
    // Limpiar campos
    if (almacenIdInput) almacenIdInput.value = '';
    if (almacenNombreInput) almacenNombreInput.value = '';
    
    if (!hospitalId) return;
    
    const almacenes = window.almacenes || [];
    let almacenSeleccionado = null;
    
    if (esGeneral) {
        // Buscar almacén general del hospital
        almacenSeleccionado = almacenes.find(a => 
            (a.id_hospital == hospitalId) && (a.tipo === 'GENERAL')
        );
    } else if (plantaId) {
        // Buscar almacén de la planta
        almacenSeleccionado = almacenes.find(a => 
            (a.id_planta == plantaId) && (a.tipo === 'PLANTA')
        );
    }
    
    if (almacenSeleccionado) {
        const almacenId = almacenSeleccionado.id_almacen || almacenSeleccionado.id;
        
        if (almacenIdInput) almacenIdInput.value = almacenId;
        if (almacenNombreInput) almacenNombreInput.value = almacenSeleccionado.nombre;
    }
}
