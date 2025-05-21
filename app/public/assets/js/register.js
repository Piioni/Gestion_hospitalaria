document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los elementos del formulario
    const hospitalSelect = document.getElementById('hospital');
    const plantaSelect = document.getElementById('planta');
    const botiquinSelect = document.getElementById('botiquin');

    // Evento para cuando se cambia de hospital
    hospitalSelect.addEventListener('change', function() {
        const selectedHospitalId = this.value;

        // Limpiar y deshabilitar el selector de botiquín
        botiquinSelect.innerHTML = '<option value="">Seleccione un botiquín</option>';
        botiquinSelect.disabled = true;

        // Limpiar el selector de plantas
        plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';

        // Si no hay hospital seleccionado, deshabilitar el selector de plantas
        if (!selectedHospitalId) {
            plantaSelect.disabled = true;
            return;
        }

        // Filtrar las plantas por el hospital seleccionado
        const plantasDelHospital = allPlantas.filter(planta =>
            planta.id_hospital == selectedHospitalId
        );

        // Habilitar el selector de plantas si hay plantas disponibles
        if (plantasDelHospital.length > 0) {
            plantaSelect.disabled = false;

            // Agregar las plantas filtradas al selector
            plantasDelHospital.forEach(planta => {
                const option = document.createElement('option');
                option.value = planta.id_planta;
                option.textContent = planta.nombre;
                plantaSelect.appendChild(option);
            });
        } else {
            plantaSelect.disabled = true;
        }
    });

    // Evento para cuando se cambia de planta
    plantaSelect.addEventListener('change', function() {
        const selectedPlantaId = this.value;

        // Limpiar el selector de botiquines
        botiquinSelect.innerHTML = '<option value="">Seleccione un botiquín</option>';

        // Si no hay planta seleccionada, deshabilitar el selector de botiquines
        if (!selectedPlantaId) {
            botiquinSelect.disabled = true;
            return;
        }

        // Filtrar los botiquines por la planta seleccionada
        const botiquinesDeLaPlanta = allBotiquines.filter(botiquin =>
            botiquin.id_planta == selectedPlantaId
        );

        // Habilitar el selector de botiquines si hay botiquines disponibles
        if (botiquinesDeLaPlanta.length > 0) {
            botiquinSelect.disabled = false;

            // Agregar los botiquines filtrados al selector
            botiquinesDeLaPlanta.forEach(botiquin => {
                const option = document.createElement('option');
                option.value = botiquin.id_botiquin;
                option.textContent = botiquin.nombre;
                botiquinSelect.appendChild(option);
            });
        } else {
            botiquinSelect.disabled = true;
        }
    });

    // Inicializar los selectores si hay valores preseleccionados
    if (hospitalSelect.value) {
        hospitalSelect.dispatchEvent(new Event('change'));

        // Si hay una planta preseleccionada en el formulario
        if (plantaSelect.querySelector(`option[value="${plantaSelect.value}"]`)) {
            plantaSelect.dispatchEvent(new Event('change'));
        }
    }
});
