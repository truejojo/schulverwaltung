<?php
declare(strict_types=1);

function is_user_authenticated(): bool {
    return isset($_SESSION['email']);
}

function authenticate_user(?string $email, ?string $password): bool {
    if (!$email || !$password) {
        return false;
    }
    // Demo (ersetzen durch DB / Users):
    $validEmail = 'admin@example.com';
    // Beispiel: plain = admin123  (nur Demo – in echt Passwort hashen!)
    $validPassHash = password_hash('admin123', PASSWORD_DEFAULT);

    if (strcasecmp($email, $validEmail) !== 0) {
        return false;
    }
    return password_verify($password, $validPassHash);
}

function logout_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}