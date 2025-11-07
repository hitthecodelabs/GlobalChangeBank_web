<?php include 'header.php'; ?>

<main class="main-wrap" id="main">
    <section class="auth-hero auth-hero--register">
        <div class="container-fluid">
            <div class="auth-layout">
                <div class="auth-intro">
                    <span class="auth-intro__eyebrow">Join the GCB Network</span>
                    <h1 class="auth-intro__title">Register and activate purpose-driven financing</h1>
                    <p class="auth-intro__lead">
                        Create an account to drive climate, social, and governance projects with partners
                        around the world.
                    </p>
                    <ul class="auth-intro__list">
                        <li>Access to exclusive calls and programs from the Global Change Council.</li>
                        <li>Tools to document impact and report results to your stakeholders.</li>
                        <li>Mentorship, resources, and ongoing support from the Global Change Bank team.</li>
                    </ul>
                </div>

                <!-- ============================================================================== -->
                <!-- IMPORTANT CHANGE HERE -->
                <!-- The 'action' now points to Laravel's entry point and the register route. -->
                <!-- ============================================================================== -->
                <div class="auth-card" aria-labelledby="registerCardTitle">
                    <div class="auth-card__header">
                        <h2 id="registerCardTitle" class="auth-card__title">Create your profile</h2>
                        <p class="auth-card__subtitle">Connect with a community funding global transformation.</p>
                    </div>

                    <form class="auth-card__form" method="POST" action="index_laravel.php/register">

                        <!--
                            LATER, WHEN WE INTEGRATE WITH LARAVEL, WE'LL ADD:
                            @csrf
                            For now we skip it to avoid PHP errors.
                        -->

                        <!-- Name Field -->
                        <div class="form-group">
                            <label for="name">Full name</label>
                            <input id="name" type="text" name="name" required>
                        </div>

                        <!-- Email Field -->
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input id="email" type="email" name="email" required>
                        </div>

                        <!-- Password Field -->
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password" required>
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="form-group">
                            <label for="password_confirmation">Confirm password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required>
                        </div>

                        <button class="btn btn-primary auth-card__submit" type="submit">
                            Create account
                        </button>
                    </form>

                    <p class="auth-card__footer">
                        Already have an account? <a href="/login.php">Sign in</a>.
                    </p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
