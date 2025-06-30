document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tipo de almacén
    const esGeneralOrigen = document.getElementById('origen_es_general').value === '1';
    selectAlmacenTipo('origen', esGeneralOrigen, true);

    // Cargar datos si hay selecciones previas
    const origenHospital = document.getElementById('origen_hospital');
    const destinoHospital = document.getElementById('destino_hospital');
    const destinoPlanta = document.getElementById('destino_planta');
    const idBotiquin = document.getElementById('id_botiquin');

    // Inicializar almacén de origen si hay hospital seleccionado
    if (origenHospital.value) {
        esGeneralOrigen ? actualizarAlmacen('origen') : cargarPlantas('origen');
    }

    // Inicializar plantas y botiquines de destino
    if (destinoHospital.value) {
        // Cargar plantas del hospital seleccionado
        cargarPlantasPorHospital(destinoHospital, destinoPlanta);
        
        // Si hay una planta preseleccionada, cargar los botiquines correspondientes
        if (window.selectedPlantaId) {
            // Esperar un momento para que se carguen las plantas
            setTimeout(() => {
                // Asegurarse de que la planta esté seleccionada
                for (let i = 0; i < destinoPlanta.options.length; i++) {
                    if (destinoPlanta.options[i].value == window.selectedPlantaId) {
                        destinoPlanta.selectedIndex = i;
                        break;
                    }
                }
                
                // Cargar botiquines de la planta
                cargarBotiquinesPorPlanta(destinoPlanta, idBotiquin);
                
                // Si hay un botiquín preseleccionado, seleccionarlo
                if (window.selectedBotiquinId) {
                    setTimeout(() => {
                        for (let i = 0; i < idBotiquin.options.length; i++) {
                            if (idBotiquin.options[i].value == window.selectedBotiquinId) {
                                idBotiquin.selectedIndex = i;
                                break;
                            }
                        }
                    }, 100);
                }
            }, 100);
        }
    }
});
