<?php
// /src/Controllers/AuthController.php

require_once __DIR__ . '/../Models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        // Verifica se o formulário foi enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                echo "<script>alert('Preencha todos os campos!');</script>";
                return;
            }

            $user = $this->userModel->findByEmail($email);

            // password_verify compara a senha digitada com o Hash salvo no banco
            if ($user && password_verify($password, $user['password'])) {
                // Sucesso! Cria a sessão
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                // Redireciona para a página inicial (minha biblioteca)
                header("Location: index.php");
                exit;
            } else {
                echo "<script>alert('Email ou senha incorretos!');</script>";
            }
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitização básica para evitar injeção de scripts no nome
            $name = htmlspecialchars(trim($_POST['name'] ?? ''));
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($name) || empty($email) || empty($password)) {
                echo "<script>alert('Preencha todos os campos!');</script>";
                return;
            }

            // Verifica se o email já existe
            if ($this->userModel->findByEmail($email)) {
                echo "<script>alert('Este email já está em uso!');</script>";
                return;
            }

            // Cria o usuário
            if ($this->userModel->create($name, $email, $password)) {
                // Loga o usuário automaticamente após criar a conta
                $newUser = $this->userModel->findByEmail($email);
                $_SESSION['user_id'] = $newUser['id'];
                $_SESSION['user_name'] = $newUser['name'];
                
                header("Location: index.php");
                exit;
            } else {
                echo "<script>alert('Erro ao criar conta. Tente novamente.');</script>";
            }
        }
    }

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }
}