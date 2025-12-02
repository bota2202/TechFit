<?php

session_start();
include_once __DIR__ . "/../Model/config.php";
include_once __DIR__ . "/../Model/Conexao.php";
include_once __DIR__ . "/../Model/UsuarioDAO.php";

// Helper para construir URLs corretas - sempre retorna caminho absoluto
function buildViewUrl($view) {
    // Sempre usa caminho absoluto começando com /
    // Isso evita problemas de caminhos relativos que causam loops
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $basePath = dirname($script);
    $basePath = str_replace('\\', '/', $basePath);
    
    // Remove qualquer parte duplicada que possa causar loop
    // Se contém /View/Codigo/View, remove tudo a partir daí
    $basePath = preg_replace('#/View/Codigo/View.*$#', '', $basePath);
    $basePath = preg_replace('#/Codigo/View.*$#', '', $basePath);
    $basePath = preg_replace('#/Codigo/Controller.*$#', '', $basePath);
    
    $basePath = rtrim($basePath, '/');
    
    // Se está na raiz ou vazio, retorna caminho absoluto simples
    if (empty($basePath) || $basePath === '.' || $basePath === '/') {
        return '/Codigo/View/' . $view;
    }
    
    // Retorna caminho absoluto
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

        // Validação de CPF (remove formatação)
        $cpfLimpo = preg_replace('/\D/', '', $cpf);
        if (strlen($cpfLimpo) !== 11 || !$this->validarCPF($cpfLimpo)) {
            $_SESSION['erro'] = 'CPF inválido';
            header('Location: ' . $cadastroUrl);
            exit;
        }

        // Verifica se email já existe
        $usuarioExistente = $this->dao->readByEmail($email);
        if ($usuarioExistente) {
            $_SESSION['erro'] = 'Esse e-mail já foi cadastrado';
            header('Location: ' . $cadastroUrl);
            exit;
        }

        // Verifica se CPF já existe
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
                preg_replace('/\D/', '', $telefone), // Remove formatação do telefone
                $cpfLimpo, // CPF sem formatação
                TIPO_USUARIO_ALUNO, // Tipo padrão
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
        $dashboardUrl = buildViewUrl('dashboard.php');

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        if (!$email || !$senha) {
            $_SESSION['erro'] = 'Preencha todos os campos';
            header('Location: ' . $loginUrl);
            exit;
        }
        $usuario = $this->dao->readByEmail($email);
        if (!$usuario) {
            $_SESSION['erro'] = 'E-mail ou senha inválidos';
            header('Location: ' . $loginUrl);
            exit;
        }
        if (!password_verify($senha, $usuario['senha_usuario_hash'])) {
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
        $inicialUrl = buildViewUrl('inicial.php');
        header('Location: ' . $inicialUrl);
        exit;
    }

    /**
     * Valida CPF usando algoritmo oficial
     */
    private function validarCPF($cpf) {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/\D/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        // Valida primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += intval($cpf[$i]) * (10 - $i);
        }
        $resto = ($soma * 10) % 11;
        if ($resto == 10 || $resto == 11) $resto = 0;
        if ($resto != intval($cpf[9])) {
            return false;
        }
        
        // Valida segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += intval($cpf[$i]) * (11 - $i);
        }
        $resto = ($soma * 10) % 11;
        if ($resto == 10 || $resto == 11) $resto = 0;
        if ($resto != intval($cpf[10])) {
            return false;
        }
        
        return true;
    }
}
