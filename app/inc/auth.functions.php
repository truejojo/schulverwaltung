<?php
declare(strict_types=1);

function is_user_authenticated(): bool {
    return isset($_SESSION['email']);
}

function authenticateUser(?string $email, ?string $password): bool {
     if (!$email || !$password) {
        return false;
    }

    $sql = 'SELECT u.id,
               u.email,
               u.password_hash,
               u.role_id,
               COALESCE(r.status, "") AS role_name
        FROM users u
        LEFT JOIN roles r ON r.id = u.role_id
        WHERE u.email = :email
        LIMIT 1
    ';
    $stmt = db()->prepare($sql);
    $stmt->execute([':email' => $email]);
    $row = $stmt->fetch();

    if (!$row || empty($row['password_hash'])) {
        return false;
    }
    if (!password_verify($password, $row['password_hash'])) {
        return false;
    }

    if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) {
        $new = password_hash($password, PASSWORD_DEFAULT);
        $u = db()->prepare('UPDATE users SET password_hash = :h WHERE id = :id');
        $u->execute([':h' => $new, ':id' => $row['id']]);
    }

    $_SESSION['user_id']  = (int)$row['id'];
    $_SESSION['email']    = $row['email'];
    $_SESSION['role_id']  = (int)$row['role_id'];
    $_SESSION['role']     = $row['role_name']; // optional lesbarer Name
    return true;
}

function logout_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/*************************************************************/
/*************************************************************/
/*************************************************************/

/**
 * Admin-Token aus ENV oder Konstante lesen.
 * ENV: APP_ADMIN_TOKEN
 * Fallback: define('APP_ADMIN_TOKEN', '...') z. B. in app/app.php
 */
function admin_token_value(): string {
    $env = getenv('APP_ADMIN_TOKEN');
    if ($env !== false && $env !== '') {
        return (string)$env;
    }
    if (defined('APP_ADMIN_TOKEN') && APP_ADMIN_TOKEN !== '') {
        return (string)APP_ADMIN_TOKEN;
    }
    return '';
}

/**
 * Prüft, ob die Session einen gültigen Admin-Token trägt.
 * Wenn kein Token konfiguriert ist, ist die Bedingung automatisch erfüllt.
 */
function has_admin_token(): bool {
    $required = admin_token_value();
    if ($required === '') {
        return true; // kein Token gefordert
    }
    return !empty($_SESSION['admin_token']) && $_SESSION['admin_token'] === true;
}

/**
 * Setzt Admin-Token in der Session, wenn er mit dem konfigurierten Token übereinstimmt.
 * Ist kein Token konfiguriert, wird true gesetzt (Bedingung gilt als erfüllt).
 */
function set_admin_token(?string $token): bool {
    $required = admin_token_value();
    if ($required === '') {
        $_SESSION['admin_token'] = true;
        return true;
    }
    if (!$token) {
        return false;
    }
    if (hash_equals($required, $token)) {
        $_SESSION['admin_token'] = true;
        return true;
    }
    return false;
}

/** Admin-Token aus der Session entfernen. */
function clear_admin_token(): void {
    unset($_SESSION['admin_token']);
}

/** Rolle direkt aus der Session prüfen (users.role_id). */
function user_has_role_id(int $expected = 3): bool {
    return isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === $expected;
}

/**
 * verwaltungs_rolle_id ermitteln (und in der Session cachen).
 * Erwartet Tabelle 'verwaltung' mit Spalte (user_id, verwaltungs_rolle_id).
 */
function session_verwaltungs_rolle_id(): ?int {
    if (array_key_exists('verwaltungs_rolle_id', $_SESSION)) {
        $v = $_SESSION['verwaltungs_rolle_id'];
        return $v === null ? null : (int)$v;
    }
    $uid = $_SESSION['user_id'] ?? null;
    if (!$uid) {
        $_SESSION['verwaltungs_rolle_id'] = null;
        return null;
    }
    $stmt = db()->prepare('SELECT verwaltungs_rolle_id FROM verwaltung WHERE user_id = :uid LIMIT 1');
    $stmt->execute([':uid' => (int)$uid]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $val = $row ? (int)$row['verwaltungs_rolle_id'] : null;
    $_SESSION['verwaltungs_rolle_id'] = $val;
    return $val;
}

/** Verwaltungs-Admin? (verwaltungs_rolle_id === 3) */
function user_is_verwaltungs_admin(): bool {
    return session_verwaltungs_rolle_id() === 3;
}

/**
 * Zentrale Freigabe für CRUD in der UI und auf Endpunkten.
 * Bedingungen:
 *  - User eingeloggt
 *  - gültiger Admin-Token (oder keiner konfiguriert)
 *  - role_id === 3 (users)
 *  - verwaltungs_rolle_id === 3 (verwaltung)
 */
function can_manage(): bool {
    return is_user_authenticated()
        && has_admin_token()
        && user_has_role_id(3)
        && user_is_verwaltungs_admin();
}