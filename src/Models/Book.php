<?php
// /src/Models/Book.php

require_once __DIR__ . '/../Database.php';

class Book {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Salva o registro do livro no banco
    public function create($userId, $title, $filePath, $type, $visibility, $coverPath = 'default_cover.png') {
        $stmt = $this->db->prepare("
            INSERT INTO books (user_id, title, file_path, type, visibility, cover_path) 
            VALUES (:user_id, :title, :file_path, :type, :visibility, :cover_path)
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'file_path' => $filePath,
            'type' => $type,
            'visibility' => $visibility,
            'cover_path' => $coverPath
        ]);
    }

    // Busca um livro específico pelo ID (usaremos isso no Leitor)
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    // Adicione isto dentro da classe Book:

    // Busca todos os livros de um utilizador específico (Para a Minha Biblioteca)
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    // Busca todos os livros públicos de todos os utilizadores (Para o Feed)
    public function getPublicBooks($limit = 12, $offset = 0) {
        // Usamos JOIN para ir buscar o nome do autor (utilizador) que fez o upload
        $stmt = $this->db->prepare("
            SELECT books.*, users.name as uploader_name, users.avatar 
            FROM books 
            JOIN users ON books.user_id = users.id 
            WHERE books.visibility = 'public' 
            ORDER BY books.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // Busca um livro garantindo que ele pertence ao utilizador logado (Segurança!)
    public function getByIdAndUser($id, $userId) {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id = :id AND user_id = :user_id LIMIT 1");
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->fetch();
    }

    // Atualiza os dados do livro
    public function update($id, $userId, $title, $visibility, $coverPath = null) {
        if ($coverPath) {
            // Se enviou uma nova capa, atualiza tudo
            $stmt = $this->db->prepare("UPDATE books SET title = :title, visibility = :visibility, cover_path = :cover_path WHERE id = :id AND user_id = :user_id");
            return $stmt->execute([
                'title' => $title,
                'visibility' => $visibility,
                'cover_path' => $coverPath,
                'id' => $id,
                'user_id' => $userId
            ]);
        } else {
            // Se NÃO enviou capa, atualiza só o título e visibilidade
            $stmt = $this->db->prepare("UPDATE books SET title = :title, visibility = :visibility WHERE id = :id AND user_id = :user_id");
            return $stmt->execute([
                'title' => $title,
                'visibility' => $visibility,
                'id' => $id,
                'user_id' => $userId
            ]);
        }
    }

    // Remove o livro do banco de dados
    public function delete($id, $userId) {
        $stmt = $this->db->prepare("DELETE FROM books WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
}