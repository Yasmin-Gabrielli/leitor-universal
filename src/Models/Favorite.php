<?php
// src/Models/Favorite.php

// Mudamos aqui para puxar o nosso arquivo Database correto
require_once __DIR__ . '/../Database.php';

class Favorite {
    private $db;

    public function __construct() {
        // Agora sim, usando a nossa conexão super segura em Singleton!
        $this->db = Database::getConnection();
    }

    // Função "Interruptor": Adiciona o favorito se não existir, ou remove se já existir
    public function toggle($userId, $bookId) {
        $stmt = $this->db->prepare("SELECT id FROM favorites WHERE user_id = ? AND book_id = ?");
        $stmt->execute([$userId, $bookId]);
        
        if ($stmt->fetch()) {
            // Já favoritou -> Remove
            $stmt = $this->db->prepare("DELETE FROM favorites WHERE user_id = ? AND book_id = ?");
            $stmt->execute([$userId, $bookId]);
            return ['status' => 'removed'];
        } else {
            // Não favoritou -> Adiciona
            $stmt = $this->db->prepare("INSERT INTO favorites (user_id, book_id) VALUES (?, ?)");
            $stmt->execute([$userId, $bookId]);
            return ['status' => 'added'];
        }
    }

    // Pega só os IDs dos livros favoritados (ex: [1, 3, 5])
    public function getUserFavoriteBookIds($userId) {
        $stmt = $this->db->prepare("SELECT book_id FROM favorites WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); 
    }
    // Traz todos os detalhes dos livros que o utilizador favoritou
    public function getFavoritedBooksDetails($userId) {
        // Fazemos um JOIN para juntar os Favoritos + Livros + Nome de quem fez upload
        $sql = "SELECT b.*, u.name as uploader_name 
                FROM books b
                INNER JOIN favorites f ON b.id = f.book_id
                INNER JOIN users u ON b.user_id = u.id
                WHERE f.user_id = ?
                ORDER BY f.created_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}