<?php

session_start();
include_once __DIR__ . "/../Model/Conexao.php";
include_once __DIR__ . "/../Model/UsuarioDAO.php";

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

        if (!$nome || !$email || !$cpf || !$telefone || !$estado || !$cidade || !$bairro || !$rua || !$senha || !$confirmar) {
            $_SESSION['erro'] = 'Preencha todos os campos!';
            header('Location: ../View/cadastro.html');
            exit;
        }

        if ($senha != $confirmar) {
            $_SESSION['erro'] = 'Senhas não conferem';
            header('Location: ../View/cadastro.html');
            exit;
        }

        if (strlen($senha) < 6) {
            $_SESSION['erro'] = 'A senha deve conter pelo menos 6 caracteres';
            header('Location: ../View/cadastro.html');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro'] = 'O e-mail é inválido';
            header('Location: ../View/cadastro.html');
            exit;
        }

        $usuarioExistente = $this->dao->readByEmail($email);
        if ($usuarioExistente) {
            $_SESSION['erro'] = 'Esse e-mail já foi cadastrado';
            header('Location: ../View/cadastro.html');
            exit;
        }

        try {
            $usuario = new Usuario(
                null,
                $nome,
                $email,
                password_hash($senha, PASSWORD_DEFAULT),
                $telefone,
                $cpf,
                null,
                $cidade,
                $estado,
                $bairro,
                $rua
            );

            $this->dao->cadastrar($usuario);

            $_SESSION['sucesso'] = 'Usuário cadastrado';
            header('Location: ../View/telalogin.html');
            exit;
        } catch (PDOException $e) {
            $_SESSION['erro'] = 'Erro ao cadastrar usuário ' . $e->getMessage();
            header('Location: ../View/cadastro.html');
            exit;
        }
    }

    public function login()
    {

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        if (!$email || !$senha) {
            $_SESSION['erro'] = 'Preencha todos os campos';
            header('Location: ../View/telalogin.html');
            exit;
        }
        $usuario = $this->dao->readByEmail($email);
        if (!$usuario) {
            $_SESSION['erro'] = 'E-mail ou senha inválidos';
            header('Location: ../View/telalogin.html');
            exit;
        }
        if (!password_verify($senha, $usuario['senha_usuario_hash'])) {
            $_SESSION['erro'] = 'E-mail ou senha inválidos';
            header('Location: ../View/telalogin.html');
            exit;
        }
        $_SESSION['usuario'] = [
            'id'    => $usuario['id_usuario'],
            'nome'  => $usuario['nome_usuario'],
            'email' => $usuario['email_usuario']
        ];
        header('Location: ../View/dashboard.php');
        exit;
    }
}
