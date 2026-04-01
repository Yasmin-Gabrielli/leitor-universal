<?php
// /src/Models/User.php
require_once __DIR__ . '/../Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Adicionado: Cria um novo utilizador
    public function create($name, $email, $password, $avatar = 'default.png') {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, avatar) 
            VALUES (:name, :email, :password, :avatar)
        ");
        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT), // Hash da senha
            'avatar' => $avatar
        ]);
    }

    // Adicionado: Busca um utilizador pelo email
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $name, $email, $bio, $avatar = null, $password = null) {
        $sql = "UPDATE users SET name = :name, email = :email, bio = :bio";
        $params = ['name' => $name, 'email' => $email, 'bio' => $bio, 'id' => $id];

        if ($avatar) {
            $sql .= ", avatar = :avatar";
            $params['avatar'] = $avatar;
        }
        if ($password) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}