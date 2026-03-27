<?php
// /src/Models/Progress.php

require_once __DIR__ . '/../Database.php';

class Progress {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Guarda ou atualiza o progresso
    public function save($userId, $bookId, $position) {
        // A magia do "ON DUPLICATE KEY UPDATE": 
        // Se já existir um registo para este utilizador+livro, ele apenas atualiza a posição.
        // Se não existir, ele insere um novo. Tudo numa só query!
        $stmt = $this->db->prepare("
            INSERT INTO reading_progress (user_id, book_id, current_position) 
            VALUES (:user_id, :book_id, :position)
            ON DUPLICATE KEY UPDATE 
                current_position = :position_update, 
                updated_at = CURRENT_TIMESTAMP
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'book_id' => $bookId,
            'position' => $position,
            'position_update' => $position
        ]);
    }

    // Busca a última posição guardada para carregar quando o utilizador voltar
    public function get($userId, $bookId) {
        $stmt = $this->db->prepare("
            SELECT current_position 
            FROM reading_progress 
            WHERE user_id = :user_id AND book_id = :book_id 
            LIMIT 1
        ");
        $stmt->execute([
            'user_id' => $userId,
            'book_id' => $bookId
        ]);
        
        $result = $stmt->fetch();
        return $result ? $result['current_position'] : null;
    }
}