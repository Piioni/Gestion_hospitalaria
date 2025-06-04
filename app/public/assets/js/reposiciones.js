/**
 * Función para seleccionar el tipo de almacén (general o planta)
 * @param {boolean} esGeneral - Indica si se seleccionó almacén general (true) o de planta (false)
 * @param {boolean} sinActualizar - Si es true, no actualiza el almacén (usado para inicialización)
 */
function selectAlmacenTipo(esGeneral, sinActualizar = false) {
    const inputEsGeneral = document.getElementById('origen_es_general');
    const btnGeneral = document.getElementById('origen_btn_general');
    const btnPlanta = document.getElementById('origen_btn_planta');
    const plantaContainer = document.getElementById('origen_planta_container');
    
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
        document.getElementById('origen_planta').value = '';
    }
    
    // Actualizar el almacén seleccionado si es necesario
    if (!sinActualizar) {
        cargarPlantas();
    }
}

/**
 * Función para cargar plantas en un selector según el hospital seleccionado
 */
function cargarPlantas() {
    const hospitalId = document.getElementById('origen_hospital').value;
    const plantaSelect = document.getElementById('origen_planta');
    const esGeneral = document.getElementById('origen_es_general').value === '1';
    
    if (!hospitalId || !plantaSelect) {
        return;
    }
    
    // Limpiar selector de plantas
    plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';
    
    if (hospitalId && !esGeneral) {
        // Filtrar plantas por hospital
        const plantasDelHospital = window.allPlantas.filter(p => p.id_hospital == hospitalId);
        
        // Agregar opciones de plantas
        plantasDelHospital.forEach(planta => {
            const option = document.createElement('option');
            option.value = planta.id_planta;
            option.textContent = planta.nombre;
            plantaSelect.appendChild(option);
        });
    }
    
    // Actualizar el almacén seleccionado
    actualizarAlmacen();
}

/**
 * Actualiza el almacén seleccionado según el tipo (general o planta) y la planta/hospital
 */
function actualizarAlmacen() {
    const hospitalId = document.getElementById('origen_hospital').value;
    const plantaId = document.getElementById('origen_planta').value;
    const esGeneral = document.getElementById('origen_es_general').value === '1';
    const almacenIdInput = document.getElementById('id_almacen');
    const almacenNombreInput = document.getElementById('origen_almacen_nombre');
    
    // Limpiar campos
    almacenIdInput.value = '';
    almacenNombreInput.value = '';
    
    if (hospitalId) {
        let almacenSeleccionado = null;
        
        if (esGeneral) {
            // Buscar almacén general del hospital
            almacenSeleccionado = window.allAlmacenes.find(a =>
                a.id_hospital == hospitalId && a.tipo === 'GENERAL'
            );
        } else if (plantaId) {
            // Buscar almacén de la planta
            almacenSeleccionado = window.allAlmacenes.find(a =>
                a.id_planta == plantaId && a.tipo === 'PLANTA'
            );
        }
        
        if (almacenSeleccionado) {
            almacenIdInput.value = almacenSeleccionado.id_almacen;
            almacenNombreInput.value = almacenSeleccionado.nombre;
        }
    }
}

// Inicializar componentes cuando se carga el documento
document.addEventListener('DOMContentLoaded', function() {
    // Verificar el estado inicial del tipo de almacén (por defecto planta)
    const esGeneralOrigen = document.getElementById('origen_es_general').value === '1';
    
    // Inicializar el selector de tipo de almacén
    selectAlmacenTipo(esGeneralOrigen, true);
    
    // Verificar si hay valores preseleccionados
    const origenHospital = document.getElementById('origen_hospital');
    const origenPlanta = document.getElementById('origen_planta');
    const destinoHospital = document.getElementById('destino_hospital');
    const destinoPlanta = document.getElementById('destino_planta');
    
    // Si hay un hospital de origen seleccionado
    if (origenHospital && origenHospital.value) {
        // Si es almacén general, actualizamos directamente
        if (esGeneralOrigen) {
            actualizarAlmacen();
        } else {
            // Si es de planta, cargamos las plantas y luego el almacén
            cargarPlantas();
            
            // Si hay una planta seleccionada
            if (origenPlanta && origenPlanta.value) {
                actualizarAlmacen();
            }
        }
    }
    
    // Si hay un hospital de destino seleccionado, cargar sus plantas
    if (destinoHospital && destinoHospital.value) {
        cargarPlantasPorHospital(destinoHospital, destinoPlanta);
        
        // Si hay una planta de destino seleccionada, cargar sus botiquines
        if (destinoPlanta && destinoPlanta.value) {
            cargarBotiquinesPorPlanta(destinoPlanta, document.getElementById('id_botiquin'));
        }
    }
});
