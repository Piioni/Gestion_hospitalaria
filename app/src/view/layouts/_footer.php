    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-main">
                <div class="footer-logo">
                    <span class="logo-icon">🏥</span>
                    <span class="logo-text">Stock Hospitalario</span>
                </div>
                <div class="footer-description">
                    Sistema de gestión de stock hospitalario para control eficiente de medicamentos e insumos,
                    optimizando recursos y mejorando la calidad de atención.
                </div>
                <div class="footer-copyright">
                    &copy; <?= date("Y") ?> Stock Hospitalario. Todos los derechos reservados.
                </div>
            </div>
            <div class="footer-links">
                <h3 class="footer-title">Enlaces</h3>
                <ul class="footer-menu">
                    <li><a href="/" class="footer-link">Inicio</a></li>
                    <li><a href="/hospitals/list" class="footer-link">Hospitales</a></li>
                    <li><a href="/plants/list" class="footer-link">Plantas</a></li>
                    <li><a href="/stock/list" class="footer-link">Stock</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h3 class="footer-title">Servicios</h3>
                <ul class="footer-menu">
                    <li><a href="/hospitals/create" class="footer-link">Crear Hospital</a></li>
                    <li><a href="/plants/create" class="footer-link">Añadir Planta</a></li>
                    <li><a href="/stock/create" class="footer-link">Gestionar Stock</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>
