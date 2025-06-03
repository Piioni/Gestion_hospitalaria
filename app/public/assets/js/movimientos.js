document.addEventListener('DOMContentLoaded', function() {
    toggleMovimientoFields();
    toggleAlmacenTipo('origen');
    toggleAlmacenTipo('destino');

});

// Función para alternar el tipo de movimiento
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
 * Función genérica para cargar plantas en un selector según el hospital seleccionado
 * @param {string} prefijo - Prefijo para los IDs de los elementos ('origen' o 'destino')
 */
function cargarPlantas(prefijo) {
    const hospitalId = document.getElementById(`${prefijo}_hospital`).value;
    const plantaSelect = document.getElementById(`${prefijo}_planta`);
    const esGeneral = document.getElementById(`${prefijo}_es_general`).checked;

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
    const esGeneral = document.getElementById(`${prefijo}_es_general`).checked;

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

/**
 * Función genérica para alternar entre almacén general y de planta
 * @param {string} prefijo - Prefijo para los IDs de los elementos ('origen' o 'destino')
 */
function toggleAlmacenTipo(prefijo) {
    const esGeneral = document.getElementById(`${prefijo}_es_general`).checked;
    const plantaContainer = document.getElementById(`${prefijo}_planta_container`);

    if (esGeneral) {
        plantaContainer.style.display = 'none';
        document.getElementById(`${prefijo}_planta`).value = '';

        // Buscar almacén general del hospital seleccionado
        const hospitalId = document.getElementById(`${prefijo}_hospital`).value;
        if (hospitalId) {
            actualizarAlmacen(prefijo);
        }
    } else {
        plantaContainer.style.display = 'block';
        cargarPlantas(prefijo);
    }
}
