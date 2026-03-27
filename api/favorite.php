<?php
// api/favorite.php

session_start();
require_once '../src/Models/Favorite.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Não autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$bookId = $data['book_id'] ?? null;

if (!$bookId) {
    echo json_encode(['success' => false, 'error' => 'ID do livro ausente']);
    exit;
}

$favoriteModel = new Favorite();
$result = $favoriteModel->toggle($_SESSION['user_id'], $bookId);

echo json_encode(['success' => true, 'action' => $result['status']]);