document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los elementos del formulario
    const hospitalSelect = document.getElementById('id_hospital');
    const plantaSelect = document.getElementById('id_planta');
    const tipoSelect = document.getElementById('tipo');
    
    /**
     * Gestiona la visibilidad del campo de planta según el tipo de almacén
     */
    function gestionarVisibilidadPlanta() {
        const plantaGroup = plantaSelect.closest('.form-group');
        
        if (tipoSelect.value === 'PLANTA') {
            // Para almacenes de tipo PLANTA, mostrar y hacer obligatorio el campo
            plantaGroup.style.display = 'block';
            plantaSelect.setAttribute('required', 'required');
            
            // Si hay un hospital seleccionado, habilitar el selector de plantas
            if (hospitalSelect.value) {
                plantaSelect.disabled = false;
            }
        } else if (tipoSelect.value === 'GENERAL') {
            // Para almacenes de tipo GENERAL, ocultar y hacer opcional
            plantaGroup.style.display = 'none';
            plantaSelect.removeAttribute('required');
            plantaSelect.value = '';
        } else {
            // Estado por defecto (ningún tipo seleccionado)
            plantaGroup.style.display = 'block';
            plantaSelect.removeAttribute('required');
        }
    }
    
    /**
     * Actualiza las opciones de plantas basadas en el hospital seleccionado
     */
    function actualizarPlantas() {
        const selectedHospitalId = hospitalSelect.value;
        
        // Limpiar selector de plantas
        plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';
        
        // Si no hay hospital seleccionado, deshabilitar el selector de plantas
        if (!selectedHospitalId) {
            plantaSelect.disabled = true;
            return;
        }
        
        // Filtrar las plantas por el hospital seleccionado
        const plantasDelHospital = window.allPlantas.filter(planta => 
            planta.id_hospital == selectedHospitalId
        );
        
        // Habilitar el selector de plantas si hay plantas disponibles y el tipo lo permite
        if (plantasDelHospital.length > 0) {
            // Solo habilitar si el tipo seleccionado es compatible
            plantaSelect.disabled = tipoSelect.value !== 'PLANTA';
            
            // Agregar las plantas filtradas al selector
            plantasDelHospital.forEach(planta => {
                const option = document.createElement('option');
                option.value = planta.id;
                option.textContent = planta.nombre;
                plantaSelect.appendChild(option);
            });
        } else {
            // Mantener deshabilitado si no hay plantas
            plantaSelect.disabled = true;
            
            // Mensaje informativo
            const noPlantasOption = document.createElement('option');
            noPlantasOption.textContent = "No hay plantas disponibles para este hospital";
            noPlantasOption.disabled = true;
            plantaSelect.appendChild(noPlantasOption);
        }
        
        // Re-aplicar la lógica de visibilidad después de actualizar las plantas
        gestionarVisibilidadPlanta();
    }
    
    // Configurar eventos
    hospitalSelect.addEventListener('change', actualizarPlantas);
    
    tipoSelect.addEventListener('change', function() {
        gestionarVisibilidadPlanta();
        actualizarPlantas();
    });
    
    // Inicialización: gestionar estados iniciales si hay valores preseleccionados
    if (tipoSelect.value) {
        gestionarVisibilidadPlanta();
    }
    
    // Inicializar lista de plantas
    actualizarPlantas();
});
