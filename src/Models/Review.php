<?php
// /src/Models/Review.php
require_once __DIR__ . '/../Database.php';

class Review {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function addReview($bookId, $userId, $rating, $comment) {
        $stmt = $this->db->prepare("
            INSERT INTO reviews (book_id, user_id, rating, comment) 
            VALUES (:book_id, :user_id, :rating, :comment)
        ");
        return $stmt->execute([
            'book_id' => $bookId,
            'user_id' => $userId,
            'rating' => $rating,
            'comment' => htmlspecialchars($comment)
        ]);
    }

    public function getByBookId($bookId) {
        $stmt = $this->db->prepare("
            SELECT r.*, u.name as user_name, u.avatar 
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.book_id = :book_id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute(['book_id' => $bookId]);
        return $stmt->fetchAll();
    }

    public function getAverageRating($bookId) {
        $stmt = $this->db->prepare("SELECT AVG(rating) as average FROM reviews WHERE book_id = :book_id");
        $stmt->execute(['book_id' => $bookId]);
        return $stmt->fetch()['average'] ?? 0;
    }

    // Atualiza um comentário existente
    public function update($id, $userId, $rating, $comment) {
        $stmt = $this->db->prepare("UPDATE reviews SET rating = :rating, comment = :comment WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['rating' => $rating, 'comment' => htmlspecialchars($comment), 'id' => $id, 'user_id' => $userId]);
    }

    // Remove um comentário
    public function delete($id, $userId) {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
}