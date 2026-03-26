<?php

declare(strict_types=1);

$pageCss = 'pages/register';
?>
<section class="auth">
    <header class="auth__header">
        <h1 class="title">Inscription</h1>
        <p class="lead">Crée un compte pour continuer.</p>
    </header>

    <form class="auth__form" action="/auth/signup" method="post">
        <div class="form-group">
            <label class="form-label" for="reg-email">Email</label>
            <input type="email" class="form-control" id="reg-email" name="email" autocomplete="email" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="reg-password">Mot de passe</label>
            <input type="password" class="form-control" id="reg-password" name="password" autocomplete="new-password" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirmation du mot de passe</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
        </div>
        <button type="submit" class="btn btn-primary">S’inscrire</button>
    </form>

    <p class="auth__footer">Déjà un compte ? <a href="/auth/login">Se connecter</a></p>
</section>
