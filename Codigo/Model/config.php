<?php
/**
 * Arquivo de Configuração TechFit
 * IMPORTANTE: Não versionar este arquivo com credenciais reais em produção
 */

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'TechFit');
define('DB_USER', 'root');
define('DB_PASS', '@Plast..2024');
define('DB_CHARSET', 'utf8');

// Configurações da Aplicação
define('APP_NAME', 'TechFit');

// BASE_PATH será definido no index.php ou calculado aqui se não existir
if (!defined('BASE_PATH')) {
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $basePath = dirname($script);
    $basePath = rtrim($basePath, '/');
    if (empty($basePath) || $basePath === '.' || $basePath === '/') {
        $basePath = '';
    }
    define('BASE_PATH', $basePath);
}

// Tipos de Usuário
define('TIPO_USUARIO_ADMIN', 1);
define('TIPO_USUARIO_INSTRUTOR', 2);
define('TIPO_USUARIO_ALUNO', 3);

// Configurações de Sessão
define('SESSION_LIFETIME', 3600); // 1 hora em segundos

// Configurações de Segurança
define('PASSWORD_MIN_LENGTH', 6);
define('PASSWORD_REQUIRE_UPPERCASE', false);
define('PASSWORD_REQUIRE_NUMBER', false);

