
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
            document.querySelector('.field-help').style.display = 'none';
        } else {
            // Si es GENERAL u otro tipo, deshabilitar el selector de plantas
            plantaSelect.disabled = true;
            plantaSelect.required = false;
            plantaSelect.value = '';
            document.querySelector('label[for="id_planta"]').classList.remove('field-required');
            document.querySelector('label[for="id_planta"]').classList.add('field-optional');
            document.querySelector('.field-help').style.display = 'block';
        }
    }
    
    // Función para cargar las plantas según el hospital seleccionado
    function cargarPlantasPorHospital() {
        const hospitalId = hospitalSelect.value;
        
        // Limpiar el selector de plantas
        plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';
        
        if (hospitalId) {
            // Filtrar plantas por hospital seleccionado
            const plantasDelHospital = window.allPlantas.filter(planta => 
                planta.id_hospital == hospitalId
            );
            
            // Agregar opciones de plantas al selector
            plantasDelHospital.forEach(planta => {
                const option = document.createElement('option');
                option.value = planta.id_planta;
                option.textContent = planta.nombre;
                plantaSelect.appendChild(option);
            });
        }
    }
    
    // Asociar eventos a los selectores
    tipoSelect.addEventListener('change', togglePlantaSelect);
    hospitalSelect.addEventListener('change', cargarPlantasPorHospital);
    
    // Inicializar los estados al cargar la página
    togglePlantaSelect();
    cargarPlantasPorHospital();
});
