<?php
// verifica_login.php - COLOCAR EM TODAS AS PÁGINAS RESTRITAS

// 1. Controle de output rigoroso
if (headers_sent($file, $line)) {
    die("Erro fatal: Output iniciado em {$file} na linha {$line}. Sessions requerem ZERO output antes.");
}

// 2. Sessão super-reforçada
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_lifetime' => 86400,
        'use_strict_mode' => 1,
        'cookie_httponly' => 1,
        'cookie_samesite' => 'Strict'
    ]);
}

// 3. Verificação em 3 níveis
$usuarioValido = (
    !empty($_SESSION['usuario_cpf']) &&
    is_numeric($_SESSION['usuario_cpf']) &&
    $_SESSION['ip'] === $_SERVER['REMOTE_ADDR'] &&
    $_SESSION['user_agent'] === $_SERVER['HTTP_USER_AGENT']
);

if (!$usuarioValido) {
    // Destrói a sessão comprometida
    session_unset();
    session_destroy();

    // Armazena a URL de destino com segurança
    $_SESSION = []; // Limpa tudo
    $_SESSION['redirect_to'] = htmlspecialchars((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], ENT_QUOTES);

    // Redirecionamento à prova de falhas
    header("HTTP/1.1 401 Unauthorized");
    header("Location: login.php", true, 302);
    exit();
}

// 4. Renova ID da sessão periodicamente
if (rand(1, 100) > 70) { // 30% de chance a cada acesso
    session_regenerate_id(true);
}
?>