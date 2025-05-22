document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los elementos del formulario
    const hospitalSelect = document.getElementById('id_hospital');
    const plantaSelect = document.getElementById('id_planta');
    const tipoSelect = document.getElementById('tipo');
    
    // Deshabilitar inicialmente el selector de plantas
    plantaSelect.disabled = true;

    // Función para actualizar las plantas basadas en el hospital seleccionado
    function actualizarPlantas() {
        const selectedHospitalId = hospitalSelect.value;
        
        // Limpiar selector de plantas
        plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';
        
        // Si no hay hospital seleccionado, deshabilitar el selector de plantas
        if (!selectedHospitalId) {
            plantaSelect.disabled = true;
            return;
        }
        
        console.log("Hospital seleccionado:", selectedHospitalId);
        
        // Filtrar las plantas por el hospital seleccionado
        // Nota: Ajustar nombres de propiedades según la estructura real
        const plantasDelHospital = allPlantas.filter(planta => {
            console.log("Comparando:", planta.id_hospital, selectedHospitalId);
            return planta.id_hospital == selectedHospitalId;
        });
        
        console.log("Plantas filtradas:", plantasDelHospital);
        
        // Habilitar el selector de plantas si hay plantas disponibles
        if (plantasDelHospital.length > 0) {
            plantaSelect.disabled = false;
            
            // Agregar las plantas filtradas al selector
            plantasDelHospital.forEach(planta => {
                const option = document.createElement('option');
                option.value = planta.id;  // Ajustar según la estructura real
                option.textContent = planta.nombre;
                plantaSelect.appendChild(option);
            });
        } else {
            // Mantener deshabilitado si no hay plantas
            plantaSelect.disabled = true;
            
            // Opcionalmente, mostrar un mensaje
            const noPlantasOption = document.createElement('option');
            noPlantasOption.textContent = "No hay plantas disponibles para este hospital";
            plantaSelect.appendChild(noPlantasOption);
        }
    }
    
    // Evento para el cambio de hospital
    hospitalSelect.addEventListener('change', actualizarPlantas);
    
    // También manejar el cambio de tipo de almacén si es necesario
    tipoSelect.addEventListener('change', function() {
        // Lógica específica para cuando cambia el tipo de almacén
        // Por ejemplo, si "GENERAL" no requiere planta, podríamos deshabilitarla
        if (this.value === "GENERAL") {
            // Lógica para almacén general
        } else if (this.value === "PLANTA") {
            // Lógica para almacén de planta
        }
        
        // Actualizar plantas basadas en el hospital seleccionado
        actualizarPlantas();
    });
    
    // Inicializar al cargar la página
    actualizarPlantas();
});
