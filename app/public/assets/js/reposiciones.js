document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tipo de almacén
    const esGeneralOrigen = document.getElementById('origen_es_general').value === '1';
    selectAlmacenTipo('origen', esGeneralOrigen, true);

    // Cargar datos si hay selecciones previas
    const origenHospital = document.getElementById('origen_hospital');
    const destinoHospital = document.getElementById('destino_hospital');
    const destinoPlanta = document.getElementById('destino_planta');

    // Inicializar almacén de origen si hay hospital seleccionado
    if (origenHospital.value) {
        esGeneralOrigen ? actualizarAlmacen('origen') : cargarPlantas('origen');
    }

    // Inicializar plantas y botiquines de destino
    if (destinoHospital.value) {
        cargarPlantasPorHospital(destinoHospital, destinoPlanta);
        
        if (destinoPlanta.value) {
            cargarBotiquinesPorPlanta(destinoPlanta, document.getElementById('id_botiquin'));
        }
    }
});
