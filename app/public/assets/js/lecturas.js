document.addEventListener('DOMContentLoaded', function () {
    const hospitalSelect = document.getElementById('id_hospital');
    const plantaSelect = document.getElementById('id_planta');
    const botiquinSelect = document.getElementById('id_botiquin');

    const plantaHelp = plantaSelect.parentElement.querySelector('.field-help');
    const botiquinHelp = botiquinSelect.parentElement.querySelector('.field-help');

    plantaSelect.disabled = true; // Deshabilitar inicialmente el selector de plantas
    botiquinSelect.disabled = true; // Deshabilitar inicialmente el selector de botiquines

    // Asociar evento al selector de hospitales para cargar las plantas de ese hospital
    if (hospitalSelect && plantaSelect) {
        hospitalSelect.addEventListener('change', function () {
            plantaSelect.disabled = false; // Habilitar el selector de plantas
            cargarPlantasPorHospital(hospitalSelect, plantaSelect);
        });

        // Si hay un hospital seleccionado al cargar la página, cargamos sus plantas
        if (hospitalSelect.value) {
            cargarPlantasPorHospital(hospitalSelect, plantaSelect);
        }
    }

    // Asociar evento al selector de plantas para cargar los botiquines de esa planta
    if (plantaSelect && botiquinSelect) {
        plantaSelect.addEventListener('change', function () {
            botiquinSelect.disabled = false; // Habilitar el selector de botiquines
            cargarBotiquinesPorPlanta(plantaSelect, botiquinSelect);
        });

        // Si hay una planta seleccionada al cargar la página, cargamos sus botiquines
        if (plantaSelect.value) {
            cargarBotiquinesPorPlanta(plantaSelect, botiquinSelect);
        }
    }

    // Actualizar mensajes de ayuda cuando cambian las selecciones
    hospitalSelect.addEventListener('change', function () {
        if (this.value) {
            plantaHelp.innerHTML = '<i class="fas fa-info-circle"></i> Seleccione una planta del hospital';
        } else {
            plantaHelp.innerHTML = '<i class="fas fa-info-circle"></i> Primero seleccione un hospital';
        }
    });

    plantaSelect.addEventListener('change', function () {
        if (this.value) {
            botiquinHelp.innerHTML = '<i class="fas fa-info-circle"></i> Seleccione un botiquín de la planta';
        } else {
            botiquinHelp.innerHTML = '<i class="fas fa-info-circle"></i> Primero seleccione una planta';
        }
    });

    // Establecer mensajes iniciales según valores seleccionados
    if (hospitalSelect.value) {
        plantaHelp.innerHTML = '<i class="fas fa-info-circle"></i> Seleccione una planta del hospital';
    }

    if (plantaSelect.value) {
        botiquinHelp.innerHTML = '<i class="fas fa-info-circle"></i> Seleccione un botiquín de la planta';
    }

});
