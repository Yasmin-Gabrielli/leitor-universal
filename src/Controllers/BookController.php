<?php
// /src/Controllers/BookController.php

require_once __DIR__ . '/../Models/Book.php';

class BookController {
    private $bookModel;

    public function __construct() {
        $this->bookModel = new Book();
    }

    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $title = htmlspecialchars(trim($_POST['title'] ?? 'Livro Sem Título'));
            $visibility = $_POST['visibility'] === 'public' ? 'public' : 'private';
            $userId = $_SESSION['user_id'];
            
            $file = $_FILES['file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Validação pesada: só aceita ePub e PDF
            $allowedExts = ['epub', 'pdf'];
            if (!in_array($ext, $allowedExts)) {
                echo "<script>alert('Erro: Apenas arquivos ePub e PDF são permitidos!');</script>";
                return;
            }

            // Gera um nome de arquivo único para segurança e organização
            $newName = uniqid() . '-' . time() . '.' . $ext;
            // Caminho absoluto para salvar o arquivo no servidor
            $uploadPath = __DIR__ . '/../../uploads/ebooks/' . $newName;

            // Move o arquivo da pasta temporária do PHP para a nossa pasta oficial
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $type = $ext; 
                // Salvamos apenas o caminho relativo no banco para facilitar na hora de puxar
                $relativePath = 'uploads/ebooks/' . $newName;
                
                if ($this->bookModel->create($userId, $title, $relativePath, $type, $visibility)) {
                    echo "<script>alert('Livro upado com sucesso! 🔥'); window.location.href='index.php';</script>";
                    exit;
                }
            } else {
                echo "<script>alert('Falha ao processar o upload do arquivo no servidor.');</script>";
            }
        }
    }
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
            $bookId = (int)$_POST['book_id'];
            $userId = $_SESSION['user_id'];
            $title = htmlspecialchars(trim($_POST['title']));
            $visibility = $_POST['visibility'] === 'public' ? 'public' : 'private';
            
            // Verifica se o livro pertence mesmo ao utilizador
            $book = $this->bookModel->getByIdAndUser($bookId, $userId);
            if (!$book) {
                echo "<script>alert('Erro: Não tens permissão para editar este livro.'); window.location.href='index.php';</script>";
                exit;
            }

            $relativePath = null; // Caminho da nova capa, se houver

            // Processa o upload da NOVA CAPA (se o utilizador tiver escolhido uma)
            if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['cover'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (!in_array($ext, $allowedExts)) {
                    echo "<script>alert('Erro: Apenas imagens JPG, PNG ou WEBP são permitidas para a capa!');</script>";
                    return;
                }

                // Gera nome único para a imagem
                $newName = uniqid('cover_') . '-' . time() . '.' . $ext;
                $uploadPath = __DIR__ . '/../../uploads/covers/' . $newName;

                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $relativePath = 'uploads/covers/' . $newName;
                    
                    // Opcional: Apagar a capa antiga do servidor para poupar espaço
                    if ($book['cover_path'] !== 'default_cover.png' && file_exists(__DIR__ . '/../../' . $book['cover_path'])) {
                        unlink(__DIR__ . '/../../' . $book['cover_path']);
                    }
                }
            }

            // Atualiza na Base de Dados
            if ($this->bookModel->update($bookId, $userId, $title, $visibility, $relativePath)) {
                echo "<script>alert('Livro atualizado com sucesso! 🎨'); window.location.href='index.php';</script>";
                exit;
            } else {
                echo "<script>alert('Erro ao atualizar o livro.');</script>";
            }
        }
    }
}