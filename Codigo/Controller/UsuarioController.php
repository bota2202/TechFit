<?php

session_start();
include_once __DIR__ . "/../Model/config.php";
include_once __DIR__ . "/../Model/Conexao.php";
include_once __DIR__ . "/../Model/UsuarioDAO.php";

function buildViewUrl($view) {
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $basePath = dirname($script);
    $basePath = str_replace('\\', '/', $basePath);
    $basePath = preg_replace('#/View/Codigo/View.*$#', '', $basePath);
    $basePath = preg_replace('#/Codigo/View.*$#', '', $basePath);
    $basePath = preg_replace('#/Codigo/Controller.*$#', '', $basePath);
    $basePath = rtrim($basePath, '/');
    
    if (empty($basePath) || $basePath === '.' || $basePath === '/') {
        return '/Codigo/View/' . $view;
    }
    
    return $basePath . '/Codigo/View/' . $view;
}

class UsuarioController
{
    private $dao;
    
    public function __construct()
    {
        $this->dao = new UsuarioDAO;
    }
    

    public function cadastrar()
    {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $estado = trim($_POST['estado'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $rua = trim($_POST['rua'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $confirmar = $_POST['confirmar'] ?? '';

        $cadastroUrl = buildViewUrl('cadastro.php');
        
        if (!$nome || !$email || !$cpf || !$telefone || !$estado || !$cidade || !$bairro || !$rua || !$senha || !$confirmar) {
            $_SESSION['erro'] = 'Preencha todos os campos!';
            header('Location: ' . $cadastroUrl);
            exit;
        }

        if ($senha != $confirmar) {
            $_SESSION['erro'] = 'Senhas não conferem';
            header('Location: ' . $cadastroUrl);
            exit;
        }

        if (strlen($senha) < 6) {
            $_SESSION['erro'] = 'A senha deve conter pelo menos 6 caracteres';
            header('Location: ' . $cadastroUrl);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro'] = 'O e-mail é inválido';
            header('Location: ' . $cadastroUrl);
            exit;
        }

        $cpfLimpo = preg_replace('/\D/', '', $cpf);
        if (strlen($cpfLimpo) !== 11 || !$this->validarCPF($cpfLimpo)) {
            $_SESSION['erro'] = 'CPF inválido';
            header('Location: ' . $cadastroUrl);
            exit;
        }

        $usuarioExistente = $this->dao->readByEmail($email);
        if ($usuarioExistente) {
            $_SESSION['erro'] = 'Esse e-mail já foi cadastrado';
            header('Location: ' . $cadastroUrl);
            exit;
        }

        $cpfExistente = $this->dao->readByCPF($cpfLimpo);
        if ($cpfExistente) {
            $_SESSION['erro'] = 'Este CPF já está cadastrado';
            header('Location: ' . $cadastroUrl);
            exit;
        }

        try {
            $usuario = new Usuario(
                null,
                $nome,
                $email,
                password_hash($senha, PASSWORD_DEFAULT),
                preg_replace('/\D/', '', $telefone),
                $cpfLimpo,
                TIPO_USUARIO_ALUNO,
                $cidade,
                $estado,
                $bairro,
                $rua
            );

            $this->dao->cadastrar($usuario);

            $loginUrl = buildViewUrl('telalogin.php');
            $_SESSION['sucesso'] = 'Usuário cadastrado com sucesso! Faça login para continuar.';
            header('Location: ' . $loginUrl);
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar usuário: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao cadastrar usuário. Tente novamente mais tarde.';
            header('Location: ' . $cadastroUrl);
            exit;
        } catch (Exception $e) {
            error_log("Erro geral ao cadastrar: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao processar cadastro. Tente novamente.';
            header('Location: ' . $cadastroUrl);
            exit;
        }
    }

    public function login()
    {
        $loginUrl = buildViewUrl('telalogin.php');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        
        if (!$email || !$senha) {
            $_SESSION['erro'] = 'Preencha todos os campos';
            header('Location: ' . $loginUrl);
            exit;
        }
        
        $usuario = $this->dao->readByEmail($email);
        if (!$usuario || !password_verify($senha, $usuario['senha_usuario_hash'])) {
            $_SESSION['erro'] = 'E-mail ou senha inválidos';
            header('Location: ' . $loginUrl);
            exit;
        }
        
        $_SESSION['usuario'] = [
            'id'    => $usuario['id_usuario'],
            'nome'  => $usuario['nome_usuario'],
            'email' => $usuario['email_usuario'],
            'tipo'  => $usuario['tipo_usuario'] ?? TIPO_USUARIO_ALUNO
        ];
        
        header('Location: ' . buildViewUrl('inicial.php'));
        exit;
    }

    public function atualizar()
    {
        require_once __DIR__ . "/../Model/Auth.php";
        require_once __DIR__ . "/../Model/helpers.php";
        require_once __DIR__ . "/../Model/Usuario.php";
        
        Auth::requireAdmin();
        
        $id = intval($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $tipo = intval($_POST['tipo'] ?? TIPO_USUARIO_ALUNO);
        $estado = trim($_POST['estado'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $rua = trim($_POST['rua'] ?? '');
        $senha = trim($_POST['senha'] ?? '');
        
        if (!$id || !$nome || !$email || !$cpf || !$telefone) {
            $_SESSION['erro'] = 'Preencha todos os campos obrigatórios!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro'] = 'E-mail inválido!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
        
        $cpfLimpo = preg_replace('/\D/', '', $cpf);
        if (strlen($cpfLimpo) !== 11 || !$this->validarCPF($cpfLimpo)) {
            $_SESSION['erro'] = 'CPF inválido!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
        
        try {
            $usuarioExistente = $this->dao->readById($id);
            if (!$usuarioExistente) {
                $_SESSION['erro'] = 'Usuário não encontrado!';
                header('Location: ' . getViewUrl('dashboard_admin.php'));
                exit;
            }
            
            $emailExistente = $this->dao->readByEmail($email);
            if ($emailExistente && $emailExistente['id_usuario'] != $id) {
                $_SESSION['erro'] = 'Este e-mail já está em uso por outro usuário!';
                header('Location: ' . getViewUrl('dashboard_admin.php'));
                exit;
            }
            
            $cpfExistente = $this->dao->readByCPF($cpfLimpo);
            if ($cpfExistente && $cpfExistente['id_usuario'] != $id) {
                $_SESSION['erro'] = 'Este CPF já está em uso por outro usuário!';
                header('Location: ' . getViewUrl('dashboard_admin.php'));
                exit;
            }
            
            $usuario = new Usuario(
                $id,
                $nome,
                $email,
                $senha,
                preg_replace('/\D/', '', $telefone),
                $cpfLimpo,
                $tipo,
                $cidade,
                $estado,
                $bairro,
                $rua
            );
            
            $this->dao->update($usuario);
            
            $_SESSION['sucesso'] = 'Usuário atualizado com sucesso!';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        } catch (Exception $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao atualizar usuário. Tente novamente.';
            header('Location: ' . getViewUrl('dashboard_admin.php'));
            exit;
        }
    }

    public function atualizarPerfil()
    {
        require_once __DIR__ . "/../Model/Auth.php";
        require_once __DIR__ . "/../Model/helpers.php";
        require_once __DIR__ . "/../Model/Usuario.php";
        
        Auth::requireAuth();
        
        $id = $_SESSION['usuario']['id'] ?? 0;
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $estado = trim($_POST['estado'] ?? '');
        $cidade = trim($_POST['cidade'] ?? '');
        $bairro = trim($_POST['bairro'] ?? '');
        $rua = trim($_POST['rua'] ?? '');
        $senha = trim($_POST['senha'] ?? '');
        
        if (!$id || !$nome || !$email || !$telefone) {
            $_SESSION['erro'] = 'Preencha todos os campos obrigatórios!';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro'] = 'E-mail inválido!';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        }
        
        try {
            $usuarioExistente = $this->dao->readById($id);
            if (!$usuarioExistente) {
                $_SESSION['erro'] = 'Usuário não encontrado!';
                header('Location: ' . getViewUrl('perfil.php'));
                exit;
            }
            
            $emailExistente = $this->dao->readByEmail($email);
            if ($emailExistente && $emailExistente['id_usuario'] != $id) {
                $_SESSION['erro'] = 'Este e-mail já está em uso por outro usuário!';
                header('Location: ' . getViewUrl('perfil.php'));
                exit;
            }
            
            $usuario = new Usuario(
                $id,
                $nome,
                $email,
                $senha,
                preg_replace('/\D/', '', $telefone),
                $usuarioExistente->getCpf(),
                $usuarioExistente->getTipo(),
                $cidade,
                $estado,
                $bairro,
                $rua
            );
            
            $this->dao->update($usuario);
            
            $_SESSION['usuario']['nome'] = $nome;
            $_SESSION['usuario']['email'] = $email;
            
            $_SESSION['sucesso'] = 'Perfil atualizado com sucesso!';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        } catch (Exception $e) {
            error_log("Erro ao atualizar perfil: " . $e->getMessage());
            $_SESSION['erro'] = 'Erro ao atualizar perfil. Tente novamente.';
            header('Location: ' . getViewUrl('perfil.php'));
            exit;
        }
    }

    private function validarCPF($cpf) {
        $cpf = preg_replace('/\D/', '', $cpf);
        
        if (strlen($cpf) != 11) {
            return false;
        }
        
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += intval($cpf[$i]) * (10 - $i);
        }
        $resto = ($soma * 10) % 11;
        if ($resto == 10 || $resto == 11) {
            $resto = 0;
        }
        if ($resto != intval($cpf[9])) {
            return false;
        }
        
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += intval($cpf[$i]) * (11 - $i);
        }
        $resto = ($soma * 10) % 11;
        if ($resto == 10 || $resto == 11) {
            $resto = 0;
        }
        if ($resto != intval($cpf[10])) {
            return false;
        }
        
        return true;
    }
}
