function toggleMovimientoFields() {
    const esTraslado = document.getElementById('tipo_traslado').checked;
    const esDevolucion = document.getElementById('tipo_devolucion').checked;
    const esEntrada = document.getElementById('tipo_entrada').checked;
    const origenSection = document.getElementById('origen-section');
    const botiquinSection = document.getElementById('botiquin-section');
    const destinoSection = document.getElementById('destino-section');
    const almacenesContainer = document.getElementById('almacenes-container');

    if (esTraslado) {
        origenSection.style.display = 'block';
        botiquinSection.style.display = 'none';
        destinoSection.style.display = 'block';
        almacenesContainer.classList.add('two-columns');
    } else if (esDevolucion) {
        origenSection.style.display = 'none';
        botiquinSection.style.display = 'block';
        destinoSection.style.display = 'block';
        almacenesContainer.classList.add('two-columns');
    } else if (esEntrada) {
        origenSection.style.display = 'none';
        botiquinSection.style.display = 'none';
        destinoSection.style.display = 'block';
        almacenesContainer.classList.remove('two-columns');
    } else {
        origenSection.style.display = 'none';
        botiquinSection.style.display = 'none';
        destinoSection.style.display = 'none';
        almacenesContainer.classList.remove('two-columns');
    }
}
