<?php
// /api/progress.php

require_once '../config/config.php';
require_once '../src/Models/Progress.php';

// Define que a resposta desta página será sempre em formato JSON
header('Content-Type: application/json');

// Segurança: Bloqueia quem não estiver com sessão iniciada (Retorna erro 401 - Unauthorized)
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Acesso negado. Por favor, inicie sessão.']);
    exit;
}

// Aceitamos apenas requisições do tipo POST para salvar dados
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido.']);
    exit;
}

// Lê os dados JSON enviados pelo JavaScript (fetch)
$data = json_decode(file_get_contents('php://input'), true);

$bookId = $data['book_id'] ?? null;
$position = $data['position'] ?? null;

// Validação simples
if (!$bookId || !$position) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados incompletos (book_id ou position em falta).']);
    exit;
}

// Guarda no banco de dados
$progressModel = new Progress();
$saved = $progressModel->save($_SESSION['user_id'], $bookId, $position);

if ($saved) {
    echo json_encode(['success' => true, 'message' => 'Progresso guardado com sucesso!']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno ao guardar progresso.']);
}