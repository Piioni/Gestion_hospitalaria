/**
 * Carga las plantas correspondientes a un hospital seleccionado
 * @param {HTMLSelectElement} hospitalSelect - Selector de hospital
 * @param {HTMLSelectElement} plantaSelect - Selector de planta a rellenar
 */
function cargarPlantasPorHospital(hospitalSelect, plantaSelect) {
    const hospitalId = hospitalSelect.value;
    const allPlantas = window.allPlantas;
    const selectedPlantaId = window.selectedPlantaId;

    // Limpiar el selector de plantas
    plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';

    // Solo cargar plantas si hay un hospital seleccionado
    if (hospitalId) {
        // Filtrar plantas por hospital seleccionado
        const plantasDelHospital = allPlantas.filter(
            planta => planta.id_hospital == hospitalId
        );

        // Agregar opciones de plantas al selector
        plantasDelHospital.forEach(planta => {
            const option = document.createElement('option');
            option.value = planta.id_planta
            option.textContent = planta.nombre;

            // Preseleccionar la planta si corresponde
            if (selectedPlantaId && (planta.id_planta == selectedPlantaId)) {
                option.selected = true;
            }

            plantaSelect.appendChild(option);
        });
    }
}

/**
 * Carga los botiquines correspondientes a una planta seleccionada
 * @param {HTMLSelectElement} plantaSelect - Selector de planta
 * @param {HTMLSelectElement} botiquinSelect - Selector de botiquin a rellenar
 */
function cargarBotiquinesPorPlanta(plantaSelect, botiquinSelect) {
    const plantaId = plantaSelect.value;
    const allBotiquines = window.allBotiquines
    const selectedBotiquinId = window.selectedBotiquinId;

    // Limpiar el selector de botiquines
    botiquinSelect.innerHTML = '<option value="">Seleccione un botiquín</option>';

    // Solo cargar botiquines si hay una planta seleccionada
    if (plantaId) {
        // Filtrar los botiquines por la planta seleccionada
        const botiquinesDePlanta = allBotiquines.filter(
            botiquin => botiquin.id_planta == plantaId
        );

        // Agregar opciones de botiquines al selector
        botiquinesDePlanta.forEach(botiquin => {
            const option = document.createElement('option');
            option.value = botiquin.id_botiquin
            option.textContent = botiquin.nombre;

            // Preseleccionar el botiquín si corresponde
            if (selectedBotiquinId && (botiquin.id_botiquin == window.selectedBotiquinI)) {
                option.selected = true;
            }

            botiquinSelect.appendChild(option);
        });
    }
}