<?php
include(__DIR__ . '/../../../config/bootstrap.php');
$title = "Inicio - Sistema de Stock Hospitalario";
include(__DIR__ . '/../layouts/_header.php');
?>

<div class="hero-section">
    <div class="container hero-container">
        <div class="hero-background">
            <img src="/assets/img/dots.svg" alt="" class="dots-image">
        </div>
        <div class="hero-content">
            <h1 class="hero-title">Sistema de Gestión de Stock Hospitalario</h1>
            <p class="hero-description">
                Una herramienta completa y moderna diseñada para facilitar la gestión de medicamentos e insumos 
                en entornos hospitalarios, optimizando recursos y mejorando la atención al paciente.
            </p>
            <div class="hero-buttons">
                <a href="/hospitals/list" class="btn btn-primary">Ver Hospitales</a>
                <a href="/hospitals/create" class="btn btn-secondary">Crear Hospital</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="/assets/img/hospital.svg" alt="Hospital" class="hospital-image">
        </div>
    </div>
</div>

<div class="services-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Nuestros Servicios</h2>
            <p class="section-description">
                Ofrecemos un sistema integral para la gestión hospitalaria, 
                permitiendo el control de inventario en todas las plantas y botiquines
                para una administración eficiente y transparente.
            </p>
        </div>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">🏥</div>
                <h3 class="service-title">Gestión de Hospitales</h3>
                <p class="service-description">
                    Administre todos los hospitales de su red sanitaria de manera centralizada, 
                    con información completa y actualizada en tiempo real.
                </p>
            </div>
            <div class="service-card">
                <div class="service-icon">🏢</div>
                <h3 class="service-title">Control de Plantas</h3>
                <p class="service-description">
                    Organice las plantas hospitalarias según especialidades médicas, 
                    mejorando la eficiencia y el seguimiento de recursos específicos.
                </p>
            </div>
            <div class="service-card">
                <div class="service-icon">📦</div>
                <h3 class="service-title">Inventario de Stock</h3>
                <p class="service-description">
                    Lleve un control preciso del inventario de medicamentos e insumos, 
                    con alertas de stock mínimo y reportes detallados.
                </p>
            </div>
        </div>
        <div class="services-cta">
            <a href="/hospitals/list" class="btn btn-primary">Explorar Servicios</a>
        </div>
    </div>
</div>

<div class="features-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">¿Por qué elegirnos?</h2>
            <p class="section-description">
                Nuestro sistema de gestión hospitalaria ofrece una solución completa 
                para administrar eficientemente los recursos médicos, mejorando la 
                calidad de atención y optimizando los procesos internos.
            </p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <h3 class="feature-title">Interfaz Intuitiva</h3>
                <p class="feature-description">Diseñada para facilitar su uso, con navegación clara y procesos simplificados que reducen la curva de aprendizaje.</p>
            </div>
            <div class="feature-card">
                <h3 class="feature-title">Datos en Tiempo Real</h3>
                <p class="feature-description">Acceda a información actualizada sobre stocks, movimientos y recursos disponibles desde cualquier ubicación autorizada.</p>
            </div>
            <div class="feature-card">
                <h3 class="feature-title">Alertas Automatizadas</h3>
                <p class="feature-description">Reciba notificaciones sobre niveles bajos de inventario, fechas de caducidad y movimientos inusuales.</p>
            </div>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../layouts/_footer.php'); ?>
