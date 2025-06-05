/**
 * Carga las plantas correspondientes a un hospital seleccionado
 * @param {HTMLSelectElement} hospitalSelect - Selector de hospital
 * @param {HTMLSelectElement} plantaSelect - Selector de planta a rellenar
 */
function cargarPlantasPorHospital(hospitalSelect, plantaSelect) {
    const idHospital = hospitalSelect.value;
    const selectedPlantaId = window.selectedPlantaId;
    
    // Limpiar el selector de plantas
    plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';
    
    if (!idHospital) return;
    
    // Filtrar y agregar plantas
    window.plantas
        .filter(planta => planta.id_hospital == idHospital)
        .forEach(planta => {
            const option = document.createElement('option');
            option.value = planta.id_planta;
            option.textContent = planta.nombre;
            
            if (selectedPlantaId && planta.id_planta == selectedPlantaId) {
                option.selected = true;
            }
            
            plantaSelect.appendChild(option);
        });
}

/**
 * Carga los botiquines correspondientes a una planta seleccionada
 * @param {HTMLSelectElement} plantaSelect - Selector de planta
 * @param {HTMLSelectElement} botiquinSelect - Selector de botiquin a rellenar
 */
function cargarBotiquinesPorPlanta(plantaSelect, botiquinSelect) {
    const plantaId = plantaSelect.value;
    const selectedBotiquinId = window.selectedBotiquinId;
    
    // Limpiar el selector de botiquines
    botiquinSelect.innerHTML = '<option value="">Seleccione un botiquín</option>';
    
    if (!plantaId) return;
    
    // Filtrar y agregar botiquines
    window.botiquines
        .filter(botiquin => botiquin.id_planta == plantaId)
        .forEach(botiquin => {
            const option = document.createElement('option');
            option.value = botiquin.id_botiquin;
            option.textContent = botiquin.nombre;
            
            if (selectedBotiquinId && botiquin.id_botiquin == selectedBotiquinId) {
                option.selected = true;
            }
            
            botiquinSelect.appendChild(option);
        });
}
