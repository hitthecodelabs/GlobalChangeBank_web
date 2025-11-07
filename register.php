<?php include 'header.php'; ?>

<main class="main-wrap" id="main">
    <section class="auth-hero auth-hero--register">
        <div class="container-fluid">
            <div class="auth-layout">
                <div class="auth-intro">
                    <span class="auth-intro__eyebrow">Únete a la red GCB</span>
                    <h1 class="auth-intro__title">Regístrate y activa el financiamiento con propósito</h1>
                    <p class="auth-intro__lead">
                        Crea una cuenta para impulsar proyectos climáticos, sociales y de gobernanza con aliados
                        alrededor del mundo.
                    </p>
                    <ul class="auth-intro__list">
                        <li>Acceso a convocatorias y programas exclusivos del Global Change Council.</li>
                        <li>Herramientas para documentar impacto y reportar resultados a tus stakeholders.</li>
                        <li>Mentorías, recursos y acompañamiento continuo del equipo Global Change Bank.</li>
                    </ul>
                </div>

                <!-- ============================================================================== -->
                <!-- CAMBIO IMPORTANTE AQUÍ -->
                <!-- El 'action' ahora apunta al punto de entrada de Laravel y la ruta de registro. -->
                <!-- ============================================================================== -->
                <div class="auth-card" aria-labelledby="registerCardTitle">
                    <div class="auth-card__header">
                        <h2 id="registerCardTitle" class="auth-card__title">Crea tu perfil</h2>
                        <p class="auth-card__subtitle">Conecta con una comunidad que financia transformación global.</p>
                    </div>

                    <form class="auth-card__form" method="POST" action="index_laravel.php/register">

                        <!--
                            MÁS ADELANTE, CUANDO INTEGREMOS EN LARAVEL, AGREGAREMOS:
                            @csrf
                            Por ahora no la ponemos para evitar errores de PHP.
                        -->

                        <!-- Campo de Nombre -->
                        <div class="form-group">
                            <label for="name">Nombre completo</label>
                            <input id="name" type="text" name="name" required>
                        </div>

                        <!-- Campo de Email -->
                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input id="email" type="email" name="email" required>
                        </div>

                        <!-- Campo de Contrasena -->
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input id="password" type="password" name="password" required>
                        </div>

                        <!-- Campo de Confirmar Contrasena -->
                        <div class="form-group">
                            <label for="password_confirmation">Confirmar contraseña</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required>
                        </div>

                        <button class="btn btn-primary auth-card__submit" type="submit">
                            Crear cuenta
                        </button>
                    </form>

                    <p class="auth-card__footer">
                        ¿Ya tienes cuenta? <a href="/login.php">Inicia sesión</a>.
                    </p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
