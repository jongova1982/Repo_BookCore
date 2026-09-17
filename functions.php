<?php
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function check_csrf(): void
{
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Solicitud inválida. Actualiza la página e inténtalo nuevamente.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function consume_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function require_login(): void
{
    if (empty($_SESSION['admin_id'])) {
        redirect('login.php');
    }
}

function current_admin(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function money_or_int($value): int
{
    return max(0, (int)$value);
}

function normalize_phone(string $value): string
{
    return trim(preg_replace('/[^0-9+()\-\s]/', '', $value));
}

function refresh_overdue_loans(PDO $pdo): void
{
    $pdo->exec("UPDATE prestamos SET estado='VENCIDO' WHERE estado='ACTIVO' AND fecha_vencimiento < CURDATE() AND fecha_devolucion IS NULL");
}
