<?php

function getBasePath() {
    static $cachedPath = null;
    
    if ($cachedPath !== null) {
        return $cachedPath;
    }
    
    if (defined('BASE_PATH')) {
        $base = BASE_PATH;
        if (strpos($base, ':') === false && strpos($base, '\\') === false && strlen($base) < 200) {
            $cachedPath = $base;
            return $cachedPath;
        }
    }
    
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    
    if (preg_match('#/Codigo/(View|Controller)/#', $script)) {
        $path = preg_replace('#/Codigo/(View|Controller)/[^/]+$#', '', $script);
        $path = dirname($path);
    } else {
        $path = dirname($script);
    }
    
    $path = str_replace('\\', '/', $path);
    $path = rtrim($path, '/');
    
    if (empty($path) || $path === '.' || $path === '/') {
        $path = '';
    }
    
    $cachedPath = $path;
    return $cachedPath;
}

function getViewUrl($view) {
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    
    if (strpos($script, '/Codigo/Controller/') !== false || strpos($script, '/index.php') !== false) {
        return 'Codigo/View/' . ltrim($view, '/');
    }
    
    if (strpos($script, '/Codigo/View/') !== false) {
        return basename($view);
    }
    
    return 'Codigo/View/' . ltrim($view, '/');
}

function getActionUrl($action) {
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    
    if (strpos($script, '/Codigo/View/') !== false) {
        return '../../index.php?action=' . $action;
    }
    
    if (strpos($script, '/Codigo/Controller/') !== false) {
        return '../../index.php?action=' . $action;
    }
    
    return 'index.php?action=' . $action;
}
