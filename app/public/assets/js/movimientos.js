document.addEventListener('DOMContentLoaded', function() {
    toggleMovimientoFields();
    
    // Para origen, verificamos el estado inicial del input hidden
    const esGeneralOrigen = document.getElementById('origen_es_general').value === '1';
    selectAlmacenTipo('origen', esGeneralOrigen, true);
    
    // Para destino, verificamos el estado inicial del input hidden
    const esGeneralDestino = document.getElementById('destino_es_general').value === '1';
    selectAlmacenTipo('destino', esGeneralDestino, true);
});

function toggleMovimientoFields() {
    const esTraslado = document.getElementById('tipo_traslado').checked;
    const origenSection = document.getElementById('origen-section');
    const almacenesContainer = document.getElementById('almacenes-container');

    if (esTraslado) {
        origenSection.style.display = 'block';
        almacenesContainer.classList.add('two-columns');
    } else {
        origenSection.style.display = 'none';
        almacenesContainer.classList.remove('two-columns');
        // Limpiar campos de origen
        document.getElementById('id_origen').value = '';
        document.getElementById('origen_almacen_nombre').value = '';
    }
}

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
        btnGeneral.classList.add('active');
        btnGeneral.classList.remove('btn-secondary');
        btnGeneral.classList.add('btn-primary');
        
        btnPlanta.classList.remove('active');
        btnPlanta.classList.add('btn-secondary');
        btnPlanta.classList.remove('btn-primary');
        
        plantaContainer.style.display = 'none';
    } else {
        btnPlanta.classList.add('active');
        btnPlanta.classList.remove('btn-secondary');
        btnPlanta.classList.add('btn-primary');
        
        btnGeneral.classList.remove('active');
        btnGeneral.classList.add('btn-secondary');
        btnGeneral.classList.remove('btn-primary');
        
        plantaContainer.style.display = 'block';
    }
    
    // Limpiar el selector de planta si cambiamos a general
    if (esGeneral) {
        document.getElementById(`${prefijo}_planta`).value = '';
    }
    
    // Actualizar el almacén seleccionado si es necesario
    if (!sinActualizar) {
        cargarPlantas(prefijo);
    }
}

/**
 * Función genérica para cargar plantas en un selector según el hospital seleccionado
 * @param {string} prefijo - Prefijo para los IDs de los elementos ('origen' o 'destino')
 */
function cargarPlantas(prefijo) {
    const hospitalId = document.getElementById(`${prefijo}_hospital`).value;
    const plantaSelect = document.getElementById(`${prefijo}_planta`);
    const esGeneral = document.getElementById(`${prefijo}_es_general`).value === '1';

    // Limpiar selector de plantas
    plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';

    if (hospitalId && !esGeneral) {
        // Filtrar plantas por hospital
        const plantasDelHospital = plantas.filter(p => p.id_hospital == hospitalId);

        // Agregar opciones de plantas
        plantasDelHospital.forEach(planta => {
            const option = document.createElement('option');
            option.value = planta.id;
            option.textContent = planta.nombre;
            plantaSelect.appendChild(option);
        });
    }

    // Actualizar el almacén seleccionado
    actualizarAlmacen(prefijo);
}

/**
 * Función genérica para actualizar el almacén seleccionado
 * @param {string} prefijo - Prefijo para los IDs de los elementos ('origen' o 'destino')
 */
function actualizarAlmacen(prefijo) {
    const hospitalId = document.getElementById(`${prefijo}_hospital`).value;
    const plantaId = document.getElementById(`${prefijo}_planta`).value;
    const esGeneral = document.getElementById(`${prefijo}_es_general`).value === '1';

    let almacenSeleccionado = null;

    if (hospitalId) {
        if (esGeneral) {
            // Buscar almacén general del hospital
            almacenSeleccionado = almacenes.find(a =>
                a.id_hospital == hospitalId && a.tipo === 'GENERAL'
            );
        } else if (plantaId) {
            // Buscar almacén de la planta
            almacenSeleccionado = almacenes.find(a =>
                a.id_planta == plantaId && a.tipo === 'PLANTA'
            );
        }
    }

    if (almacenSeleccionado) {
        document.getElementById(`id_${prefijo}`).value = almacenSeleccionado.id;
        document.getElementById(`${prefijo}_almacen_nombre`).value = almacenSeleccionado.nombre;
    } else {
        document.getElementById(`id_${prefijo}`).value = '';
        document.getElementById(`${prefijo}_almacen_nombre`).value = '';
    }
}
