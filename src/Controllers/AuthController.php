<?php
// /src/Controllers/AuthController.php

require_once __DIR__ . '/../Models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // =========================================================
    // FUNÇÃO ISOLADA: TRATA APENAS DO UPLOAD DO AVATAR
    // =========================================================
    private function processAvatarUpload($fileArray, $userId) {
        // Se não enviou ficheiro ou deu erro, retorna nulo
        if (!isset($fileArray) || $fileArray['error'] !== UPLOAD_ERR_OK) {
            return null; 
        }

        $ext = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];

        // Se o formato for inválido, retorna false
        if (!in_array($ext, $allowedExts)) {
            return false; 
        }

        // Prepara o caminho e a pasta
        $newName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
        $avatarUploadDir = __DIR__ . '/../../uploads/avatars/';
        
        // Cria a pasta se ela não existir
        if (!is_dir($avatarUploadDir)) {
            mkdir($avatarUploadDir, 0777, true);
        }

        $uploadPath = $avatarUploadDir . $newName;

        // Tenta mover o ficheiro. Se conseguir, devolve o nome do ficheiro.
        if (move_uploaded_file($fileArray['tmp_name'], $uploadPath)) {
            return $newName;
        }

        return null;
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
                $_SESSION['avatar'] = $user['avatar']; // Adiciona o avatar à sessão
                
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

            $avatarName = 'default.png'; // Valor padrão

            // Tenta criar o utilizador primeiro com o avatar padrão
            // Isso é necessário para obter um user_id para o nome do ficheiro do avatar
            if (!$this->userModel->create($name, $email, $password, $avatarName)) {
                echo "<script>alert('Erro ao criar conta. Tente novamente.'); window.history.back();</script>";
                return;
            }

            // Se o utilizador foi criado, obtém o seu ID
            $newUser = $this->userModel->findByEmail($email);
            $userId = $newUser['id'];

            // Processa o upload do avatar, se houver
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadedAvatar = $this->processAvatarUpload($_FILES['avatar'], $userId);
                
                if ($uploadedAvatar === false) {
                    echo "<script>alert('Erro: Formato de imagem de perfil inválido! Use JPG, PNG ou WEBP.'); window.history.back();</script>";
                    // Não retorna, apenas mantém o avatar padrão
                } elseif ($uploadedAvatar !== null) {
                    $avatarName = $uploadedAvatar;
                    // Atualiza o utilizador com o novo caminho do avatar
                    $this->userModel->update($userId, $name, $email, '', $avatarName, null); // Bio vazia, password null para não alterar
                }
            }

            // Loga o utilizador automaticamente após criar a conta
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['avatar'] = $avatarName; // Define o avatar na sessão
            
            header("Location: index.php");
            exit;
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