<?php

class Auth
{
    public static function isAuthenticated()
    {
        return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
    }

    public static function getUser()
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function isAdmin()
    {
        if (!self::isAuthenticated()) {
            return false;
        }
        return ($_SESSION['usuario']['tipo'] ?? TIPO_USUARIO_ALUNO) == TIPO_USUARIO_ADMIN;
    }

    public static function isInstrutor()
    {
        if (!self::isAuthenticated()) {
            return false;
        }
        return ($_SESSION['usuario']['tipo'] ?? TIPO_USUARIO_ALUNO) == TIPO_USUARIO_INSTRUTOR;
    }

    public static function isAluno()
    {
        if (!self::isAuthenticated()) {
            return false;
        }
        return ($_SESSION['usuario']['tipo'] ?? TIPO_USUARIO_ALUNO) == TIPO_USUARIO_ALUNO;
    }

    public static function requireAuth()
    {
        if (!self::isAuthenticated()) {
            $_SESSION['erro'] = 'Você precisa estar logado para acessar esta página';
            require_once __DIR__ . '/helpers.php';
            header('Location: ' . getViewUrl('telalogin.php'));
            exit;
        }
    }

    public static function requireAdmin()
    {
        self::requireAuth();
        if (!self::isAdmin()) {
            $_SESSION['erro'] = 'Acesso negado. Esta área é restrita a administradores.';
            require_once __DIR__ . '/helpers.php';
            $usuario = self::getUser();
            if ($usuario && $usuario['tipo'] == TIPO_USUARIO_ALUNO) {
                header('Location: ' . getViewUrl('dashboard.php'));
            } else {
                header('Location: ' . getViewUrl('inicial.php'));
            }
            exit;
        }
    }

    public static function logout()
    {
        session_destroy();
        session_start();
        $_SESSION['sucesso'] = 'Logout realizado com sucesso';
    }
}

