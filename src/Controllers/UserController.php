<?php
// /src/Controllers/UserController.php
require_once __DIR__ . '/../Models/User.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $name = htmlspecialchars(trim($_POST['name']));
            $email = htmlspecialchars(trim($_POST['email']));
            $bio = htmlspecialchars(trim($_POST['bio']));
            $password = !empty($_POST['password']) ? $_POST['password'] : null;
            
            $user = $this->userModel->getById($userId);
            $avatarName = null;

            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $avatarName = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                    $dir = __DIR__ . '/../../uploads/avatars/';
                    if (!is_dir($dir)) mkdir($dir, 0777, true);
                    
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dir . $avatarName)) {
                        if ($user['avatar'] !== 'default.png' && file_exists($dir . $user['avatar'])) {
                            unlink($dir . $user['avatar']);
                        }
                    }
                }
            }

            if ($this->userModel->update($userId, $name, $email, $bio, $avatarName, $password)) {
                $_SESSION['user_name'] = $name;
                if ($avatarName) $_SESSION['avatar'] = $avatarName; // Atualiza o avatar na sessão se foi alterado
                echo "<script>alert('Perfil atualizado!'); window.location.href='perfil.php';</script>";
                exit;
            }
        }
    }

    public function getUserData($id) {
        return $this->userModel->getById($id);
    }
}