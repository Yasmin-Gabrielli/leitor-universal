<?php
// /api/get_books.php
require_once '../config/config.php';
require_once '../src/Models/Book.php';

header('Content-Type: application/json');

$offset = $_GET['offset'] ?? 0;
$limit = $_GET['limit'] ?? 12;
$bookModel = new Book();
echo json_encode($bookModel->getPublicBooks($limit, $offset));