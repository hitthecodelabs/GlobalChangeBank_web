<?php include 'header.php'; ?>

<main class="main-wrap" id="main">
    <section class="auth-hero">
        <div class="container-fluid">
            <div class="auth-layout">
                <div class="auth-intro">
                    <span class="auth-intro__eyebrow">Global Change Bank</span>
                    <h1 class="auth-intro__title">Inicia sesión y potencia tus proyectos de impacto</h1>
                    <p class="auth-intro__lead">
                        Gestiona alianzas, conecta con inversionistas responsables y mantén el control de tus
                        iniciativas sociales desde un solo lugar.
                    </p>
                    <ul class="auth-intro__list">
                        <li>Acceso seguro a tableros de colaboración global.</li>
                        <li>Seguimiento en tiempo real de fondos, métricas y comunidades.</li>
                        <li>Soporte dedicado del equipo GCB a tus misiones de cambio.</li>
                    </ul>
                </div>

                <!-- ============================================================================== -->
                <!-- CAMBIO IMPORTANTE AQUÍ -->
                <!-- El 'action' ahora apunta al punto de entrada de Laravel y la ruta de login. -->
                <!-- ============================================================================== -->
                <div class="auth-card" aria-labelledby="authCardTitle">
                    <div class="auth-card__header">
                        <h2 id="authCardTitle" class="auth-card__title">Ingresa a tu cuenta</h2>
                        <p class="auth-card__subtitle">Bienvenido de vuelta a la comunidad financiera con propósito.</p>
                    </div>

                    <form class="auth-card__form" method="POST" action="index_laravel.php/login">

                        <!--
                            MÁS ADELANTE, CUANDO INTEGREMOS EN LARAVEL, AÑADIREMOS LA LÍNEA MÁGICA:
                            @csrf
                            Por ahora no la ponemos para evitar errores de PHP.
                        -->

                        <!-- Campo de Email -->
                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input id="email" type="email" name="email" required autofocus>
                        </div>

                        <!-- Campo de Contraseña -->
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input id="password" type="password" name="password" required>
                        </div>

                        <button class="btn btn-primary auth-card__submit" type="submit">
                            Iniciar sesión
                        </button>
                    </form>

                    <p class="auth-card__footer">
                        ¿Aún no formas parte de GCB? <a href="/register.php">Crea tu cuenta</a>.
                    </p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
