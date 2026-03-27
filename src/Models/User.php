<?php
// /src/Models/User.php

require_once __DIR__ . '/../Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Busca um usuário pelo email (usado no Login e para evitar cadastros duplicados)
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    // Cria um novo usuário no banco (usado no Cadastro)
    public function create($name, $email, $password) {
        // Criptografa a senha com o algoritmo mais forte disponível no PHP atual (Bcrypt)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password) 
            VALUES (:name, :email, :password)
        ");

        return $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword
        ]);
    }
}