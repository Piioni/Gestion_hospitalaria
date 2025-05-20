document.addEventListener('DOMContentLoaded', function() {
    const hospitalSelect = document.getElementById('hospital');
    const plantaSelect = document.getElementById('planta');
    const botiquinSelect = document.getElementById('botiquin');
    const rolesSelect = document.getElementById('roles');
    
    // Función para cargar las plantas basadas en el hospital seleccionado
    function loadPlantas(hospitalId) {
        // Reiniciar selectores de planta y botiquín
        plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';
        botiquinSelect.innerHTML = '<option value="">Seleccione un botiquín</option>';
        
        // Deshabilitar selectores
        plantaSelect.disabled = hospitalId === '';
        botiquinSelect.disabled = true;
        
        if (hospitalId !== '') {
            // Solicitud AJAX para obtener plantas
            fetch(`/api.php?route=plantas&hospital_id=${hospitalId}`)
                .then(response => response.json())
                .then(data => {
                    // Llenar el selector de plantas con los datos recibidos
                    data.forEach(planta => {
                        const option = document.createElement('option');
                        option.value = planta.id;
                        option.textContent = planta.name;
                        plantaSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error al cargar plantas:', error);
                });
        }
    }
    
    // Función para cargar los botiquines basados en la planta seleccionada
    function loadBotiquines(plantaId) {
        // Reiniciar selector de botiquín
        botiquinSelect.innerHTML = '<option value="">Seleccione un botiquín</option>';
        
        // Deshabilitar selector
        botiquinSelect.disabled = plantaId === '';
        
        if (plantaId !== '') {
            // Solicitud AJAX para obtener botiquines
            fetch(`/api.php?route=botiquines&planta_id=${plantaId}`)
                .then(response => response.json())
                .then(data => {
                    // Llenar el selector de botiquines con los datos recibidos
                    data.forEach(botiquin => {
                        const option = document.createElement('option');
                        option.value = botiquin.id;
                        option.textContent = botiquin.name;
                        botiquinSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error al cargar botiquines:', error);
                });
        }
    }
    
    // Función para cargar los roles disponibles
    function loadRoles() {
        fetch('/api.php?route=roles')
            .then(response => response.json())
            .then(data => {
                // Llenar el selector de roles
                data.forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.id;
                    option.textContent = role.name;
                    rolesSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error al cargar roles:', error);
            });
    }
    
    // Eventos para los selectores
    if (hospitalSelect) {
        hospitalSelect.addEventListener('change', function() {
            loadPlantas(this.value);
        });
    }
    
    if (plantaSelect) {
        plantaSelect.addEventListener('change', function() {
            loadBotiquines(this.value);
        });
    }
    
    // Cargar roles al iniciar
    if (rolesSelect) {
        loadRoles();
    }
    
    // Si el hospital ya tiene un valor seleccionado al cargar la página
    if (hospitalSelect && hospitalSelect.value) {
        loadPlantas(hospitalSelect.value);
        
        // Si la planta ya tiene un valor seleccionado al cargar la página
        if (plantaSelect && plantaSelect.value) {
            loadBotiquines(plantaSelect.value);
        }
    }
});
