document.addEventListener('DOMContentLoaded', function() {
    const hospitalSelect = document.getElementById('hospital');
    const plantaSelect = document.getElementById('planta');
    const botiquinSelect = document.getElementById('botiquin');

    // Cuando cambia el hospital seleccionado
    hospitalSelect.addEventListener('change', function() {
        const hospitalId = this.value;

        // Resetear las plantas y botiquines
        plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';
        botiquinSelect.innerHTML = '<option value="">Seleccione un botiquín</option>';

        if (hospitalId) {
            // Solicitar las plantas del hospital seleccionado
            fetch(`/api/plantas-by-hospital/${hospitalId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(planta => {
                        const option = document.createElement('option');
                        option.value = planta.id;
                        option.textContent = planta.name;
                        plantaSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error cargando plantas:', error));
        }
    });

    // Cuando cambia la planta seleccionada
    plantaSelect.addEventListener('change', function() {
        const plantaId = this.value;

        // Resetear los botiquines
        botiquinSelect.innerHTML = '<option value="">Seleccione un botiquín</option>';

        if (plantaId) {
            // Solicitar los botiquines de la planta seleccionada
            fetch(`/api/botiquines-by-planta/${plantaId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(botiquin => {
                        const option = document.createElement('option');
                        option.value = botiquin.id;
                        option.textContent = botiquin.name;
                        botiquinSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error cargando botiquines:', error));
        }
    });
});