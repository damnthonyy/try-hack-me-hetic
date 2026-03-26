<?php
declare(strict_types=1);
$loginCSS = 'login.css';
$layoutStyles = [$loginCSS];
?>
<section class="auth">
    <header class="auth__header">
        <h1 class="title">Connexion</h1>
        <p class="lead">Connecte-toi pour accéder à ton compte.</p>
    </header>

    <form class="auth__form" action="/auth/login" method="post">
        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" autocomplete="email" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>

    <p class="auth__footer">Pas de compte ? <a href="/auth/signup">S’inscrire</a></p>
</section>
