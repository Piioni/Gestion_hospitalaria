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
            cargarPlantasPorHospital();
        } else {
            // Si es GENERAL u otro tipo, deshabilitar el selector de plantas
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
    
    // Función para cargar las plantas según el hospital seleccionado
    function cargarPlantasPorHospital() {
        const hospitalId = hospitalSelect.value;
        
        // Limpiar el selector de plantas
        plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';
        
        // Solo cargar plantas si hay un hospital seleccionado y el tipo es PLANTA
        if (hospitalId) {
            // Verificar que allPlantas esté definido
            if (!window.allPlantas || !Array.isArray(window.allPlantas)) {
                console.error('Error: La variable allPlantas no está definida correctamente');
                return;
            }
            
            // Filtrar plantas por hospital seleccionado
            const plantasDelHospital = window.allPlantas.filter(planta => 
                planta.id_hospital == hospitalId
            );
            
            // Log para depuración
            console.log('Hospital ID:', hospitalId);
            console.log('Todas las plantas:', window.allPlantas);
            console.log('Plantas filtradas:', plantasDelHospital);
            
            // Agregar opciones de plantas al selector
            plantasDelHospital.forEach(planta => {
                const option = document.createElement('option');
                option.value = planta.id_planta || planta.id; // Manejar ambos formatos posibles
                option.textContent = planta.nombre;
                
                // Preseleccionar la planta si estamos en modo edición
                if (window.selectedPlantaId && (planta.id_planta == window.selectedPlantaId || planta.id == window.selectedPlantaId)) {
                    option.selected = true;
                }
                
                plantaSelect.appendChild(option);
            });
        }
    }
    
    // Asociar eventos a los selectores
    tipoSelect.addEventListener('change', togglePlantaSelect);
    hospitalSelect.addEventListener('change', function() {
        // Solo cargar plantas si el tipo es PLANTA
        if (tipoSelect.value === 'PLANTA') {
            cargarPlantasPorHospital();
        }
    });
    
    // Inicializar los estados al cargar la página
    togglePlantaSelect();
    
    // Si ya hay un hospital seleccionado y el tipo es PLANTA, cargar las plantas
    if (hospitalSelect.value && tipoSelect.value === 'PLANTA') {
        cargarPlantasPorHospital();
    }
});
