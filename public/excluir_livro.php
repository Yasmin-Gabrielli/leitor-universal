<?php
// /public/excluir_livro.php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../src/Controllers/BookController.php';

// Processa a exclusão
$bookController = new BookController();
$bookController->delete();
?>