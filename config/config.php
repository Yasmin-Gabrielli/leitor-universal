<?php
// /config/config.php

// Inicia a sessão globalmente para toda a aplicação
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Credenciais do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'leitura_universal');
define('DB_USER', 'root'); // Mude se usar outro usuário
define('DB_PASS', '');     // Coloque sua senha do MySQL aqui

// URL Base do sistema
define('BASE_URL', 'http://localhost/leitura-universal/public');