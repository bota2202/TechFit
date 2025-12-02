<?php
/**
 * Funções auxiliares - TechFit
 */

/**
 * Retorna o caminho base do projeto para URLs
 * Sempre retorna caminho relativo à raiz do servidor web
 */
function getBasePath() {
    static $cachedPath = null;
    
    if ($cachedPath !== null) {
        return $cachedPath;
    }
    
    // Se BASE_PATH já foi definido e é válido para URL
    if (defined('BASE_PATH')) {
        $base = BASE_PATH;
        // Verifica se não é um caminho de arquivo
        if (strpos($base, ':') === false && strpos($base, '\\') === false && strlen($base) < 200) {
            $cachedPath = $base;
            return $cachedPath;
        }
    }
    
    // Calcula baseado no SCRIPT_NAME
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    
    // Se o script está em Codigo/View/ ou Codigo/Controller/, sobe até a raiz
    if (preg_match('#/Codigo/(View|Controller)/#', $script)) {
        // Remove /Codigo/View/ ou /Codigo/Controller/ e o nome do arquivo
        $path = preg_replace('#/Codigo/(View|Controller)/[^/]+$#', '', $script);
        $path = dirname($path);
    } else {
        // Está na raiz ou em subpasta
        $path = dirname($script);
    }
    
    // Normaliza
    $path = str_replace('\\', '/', $path);
    $path = rtrim($path, '/');
    
    // Se está na raiz, retorna vazio
    if (empty($path) || $path === '.' || $path === '/') {
        $path = '';
    }
    
    $cachedPath = $path;
    return $cachedPath;
}

/**
 * Retorna a URL completa para uma view
 */
function getViewUrl($view) {
    // Se estamos em Codigo/Controller/, sobe 1 nível e vai para View
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    
    if (strpos($script, '/Codigo/Controller/') !== false) {
        return '../View/' . ltrim($view, '/');
    }
    
    // Se estamos em Codigo/View/, é relativo à mesma pasta
    if (strpos($script, '/Codigo/View/') !== false) {
        return basename($view);
    }
    
    // Se está na raiz, usa caminho completo
    return 'Codigo/View/' . ltrim($view, '/');
}

/**
 * Retorna a URL completa para uma ação
 */
function getActionUrl($action) {
    // Se estamos em Codigo/View/, sobe 2 níveis até a raiz
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    
    if (strpos($script, '/Codigo/View/') !== false) {
        // Está em Codigo/View/, sobe 2 níveis: ../../
        return '../../index.php?action=' . $action;
    }
    
    if (strpos($script, '/Codigo/Controller/') !== false) {
        // Está em Codigo/Controller/, sobe 2 níveis: ../../
        return '../../index.php?action=' . $action;
    }
    
    // Se está na raiz, usa caminho direto
    return 'index.php?action=' . $action;
}
