document.addEventListener('DOMContentLoaded', function() {
    // Inicialización básica
    toggleMovimientoFields();
    
    // Inicializar tipo de almacén
    const esGeneralOrigen = document.getElementById('origen_es_general').value === '1';
    const esGeneralDestino = document.getElementById('destino_es_general').value === '1';
    
    selectAlmacenTipo('origen', esGeneralOrigen, true);
    selectAlmacenTipo('destino', esGeneralDestino, true);
    
    // Cargar datos si hay hospitales seleccionados
    const origenHospital = document.getElementById('origen_hospital');
    const destinoHospital = document.getElementById('destino_hospital');
    
    if (origenHospital.value) {
        esGeneralOrigen ? actualizarAlmacen('origen') : cargarPlantas('origen');
    }
    
    if (destinoHospital.value) {
        esGeneralDestino ? actualizarAlmacen('destino') : cargarPlantas('destino');
    }
});

/**
 * Alterna la visibilidad de los campos según el tipo de movimiento seleccionado
 */
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
