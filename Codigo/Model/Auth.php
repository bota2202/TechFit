<?php
/**
 * Classe de Autenticação - TechFit
 * Gerencia autenticação e autorização de usuários
 */

class Auth
{
    /**
     * Verifica se o usuário está autenticado
     */
    public static function isAuthenticated()
    {
        return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
    }

    /**
     * Obtém o usuário atual da sessão
     */
    public static function getUser()
    {
        return $_SESSION['usuario'] ?? null;
    }

    /**
     * Verifica se o usuário é administrador
     */
    public static function isAdmin()
    {
        if (!self::isAuthenticated()) {
            return false;
        }
        return ($_SESSION['usuario']['tipo'] ?? TIPO_USUARIO_ALUNO) == TIPO_USUARIO_ADMIN;
    }

    /**
     * Verifica se o usuário é instrutor
     */
    public static function isInstrutor()
    {
        if (!self::isAuthenticated()) {
            return false;
        }
        return ($_SESSION['usuario']['tipo'] ?? TIPO_USUARIO_ALUNO) == TIPO_USUARIO_INSTRUTOR;
    }

    /**
     * Verifica se o usuário é aluno
     */
    public static function isAluno()
    {
        if (!self::isAuthenticated()) {
            return false;
        }
        return ($_SESSION['usuario']['tipo'] ?? TIPO_USUARIO_ALUNO) == TIPO_USUARIO_ALUNO;
    }

    /**
     * Requer autenticação - redireciona se não estiver logado
     */
    public static function requireAuth()
    {
        if (!self::isAuthenticated()) {
            $_SESSION['erro'] = 'Você precisa estar logado para acessar esta página';
            require_once __DIR__ . '/helpers.php';
            header('Location: ' . getViewUrl('telalogin.php'));
            exit;
        }
    }

    /**
     * Requer que o usuário seja admin
     */
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

    /**
     * Faz logout do usuário
     */
    public static function logout()
    {
        session_destroy();
        session_start();
        $_SESSION['sucesso'] = 'Logout realizado com sucesso';
    }
}

