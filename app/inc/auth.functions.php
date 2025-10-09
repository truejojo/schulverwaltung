<?php
declare(strict_types=1);

function is_user_authenticated(): bool {
    return isset($_SESSION['email']);
}

function authenticateUser(?string $email, ?string $password): bool {
     if (!$email || !$password) {
        return false;
    }

    $sql = '
        SELECT u.id,
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