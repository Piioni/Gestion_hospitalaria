document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los elementos del DOM
    const tipoSelect = document.getElementById('tipo');
    const hospitalSelect = document.getElementById('id_hospital');
    const plantaSelect = document.getElementById('id_planta');
    
    // Función para habilitar/deshabilitar el selector de plantas según el tipo de almacén
    function togglePlantaSelect() {
        const tipoSeleccionado = tipoSelect.value;
        
        // Si el tipo es PLANTA, habilitar el selector de plantas
        if (tipoSeleccionado === 'PLANTA') {
            plantaSelect.disabled = false;
            plantaSelect.required = true;
            document.querySelector('label[for="id_planta"]').classList.add('field-required');
            document.querySelector('label[for="id_planta"]').classList.remove('field-optional');
            if (document.querySelector('.field-help')) {
                document.querySelector('.field-help').style.display = 'none';
            }
            
            // Cargar las plantas del hospital seleccionado cuando se cambia a tipo PLANTA
            cargarPlantasPorHospital(hospitalSelect, plantaSelect);
        } else {
            // Sí es GENERAL u otro tipo, deshabilitar el selector de plantas
            plantaSelect.disabled = true;
            plantaSelect.required = false;
            plantaSelect.value = '';
            document.querySelector('label[for="id_planta"]').classList.remove('field-required');
            document.querySelector('label[for="id_planta"]').classList.add('field-optional');
            if (document.querySelector('.field-help')) {
                document.querySelector('.field-help').style.display = 'block';
            }
        }
    }
    
    // Asociar eventos a los selectores
    tipoSelect.addEventListener('change', togglePlantaSelect);
    hospitalSelect.addEventListener('change', function() {
        // Solo cargar plantas si el tipo es PLANTA
        if (tipoSelect.value === 'PLANTA') {
            cargarPlantasPorHospital(hospitalSelect, plantaSelect);
        }
    });
    
    // Inicializar los estados al cargar la página
    togglePlantaSelect();
});
