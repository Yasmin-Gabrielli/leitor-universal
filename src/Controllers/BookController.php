<?php
// /src/Controllers/BookController.php

require_once __DIR__ . '/../Models/Book.php';

class BookController {
    private $bookModel;

    public function __construct() {
        $this->bookModel = new Book();
    }

    // =========================================================
    // FUNÇÃO ISOLADA: TRATA APENAS DO UPLOAD DA CAPA
    // =========================================================
    private function processCoverUpload($fileArray) {
        // Se não enviou ficheiro ou deu erro, retorna nulo
        if (!isset($fileArray) || $fileArray['error'] !== UPLOAD_ERR_OK) {
            return null; 
        }

        $ext = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];

        // Se o formato for inválido, retorna false
        if (!in_array($ext, $allowedExts)) {
            return false; 
        }

        // Prepara o caminho e a pasta
        $newName = uniqid('cover_') . '-' . time() . '.' . $ext;
        $coverUploadDir = __DIR__ . '/../../uploads/covers/';
        
        // Cria a pasta se ela não existir
        if (!is_dir($coverUploadDir)) {
            mkdir($coverUploadDir, 0777, true);
        }

        $uploadPath = $coverUploadDir . $newName;

        // Tenta mover o ficheiro. Se conseguir, devolve o caminho relativo.
        if (move_uploaded_file($fileArray['tmp_name'], $uploadPath)) {
            return 'uploads/covers/' . $newName;
        }

        return null;
    }

    // =========================================================
    // FUNÇÃO DE UPLOAD (Criar Novo Livro)
    // =========================================================
    public function upload() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $title = htmlspecialchars(trim($_POST['title'] ?? 'Livro Sem Título'));
            $visibility = $_POST['visibility'] === 'public' ? 'public' : 'private';
            $userId = $_SESSION['user_id'];
            
            // 1. PROCESSAR O FICHEIRO DO LIVRO (PDF/EPUB)
            $file = $_FILES['file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            $allowedExts = ['epub', 'pdf'];
            if (!in_array($ext, $allowedExts)) {
                echo "<script>alert('Erro: Apenas ficheiros ePub e PDF são permitidos!'); window.history.back();</script>";
                return;
            }

            $newName = uniqid() . '-' . time() . '.' . $ext;
            $ebookUploadDir = __DIR__ . '/../../uploads/ebooks/';

            // Garante que a pasta de ebooks existe
            if (!is_dir($ebookUploadDir)) {
                mkdir($ebookUploadDir, 0777, true);
            }
            $uploadPath = $ebookUploadDir . $newName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $type = $ext; 
                $relativePath = 'uploads/ebooks/' . $newName;
                
                // 2. PROCESSAR A CAPA USANDO A FUNÇÃO ISOLADA
                $coverRelativePath = 'default_cover.png'; // Capa padrão
                
                if (isset($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $uploadedCover = $this->processCoverUpload($_FILES['cover']);
                    
                    if ($uploadedCover === false) {
                        echo "<script>alert('Erro: Formato de capa inválido! Use JPG, PNG ou WEBP.'); window.history.back();</script>";
                        return; // Pára tudo se a capa tiver o formato errado
                    } elseif ($uploadedCover !== null) {
                        $coverRelativePath = $uploadedCover; // Capa upada com sucesso!
                    }
                }

                // 3. GUARDAR TUDO NA BASE DE DADOS
                if ($this->bookModel->create($userId, $title, $relativePath, $type, $visibility, $coverRelativePath)) {
                    echo "<script>alert('Livro carregado com sucesso! 🔥'); window.location.href='index.php';</script>";
                    exit;
                } else {
                    echo "<script>alert('Erro ao guardar na base de dados.'); window.history.back();</script>";
                }
            } else {
                echo "<script>alert('Falha ao processar o upload do livro no servidor.'); window.history.back();</script>";
            }
        }
    }

    // =========================================================
    // FUNÇÃO DE EDIÇÃO (Atualizar Livro Existente)
    // =========================================================
    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
            $bookId = (int)$_POST['book_id'];
            $userId = $_SESSION['user_id'];
            $title = htmlspecialchars(trim($_POST['title']));
            $visibility = $_POST['visibility'] === 'public' ? 'public' : 'private';
            
            $book = $this->bookModel->getByIdAndUser($bookId, $userId);
            if (!$book) {
                echo "<script>alert('Erro: Não tens permissão para editar este livro.'); window.location.href='index.php';</script>";
                exit;
            }

            $newCoverPath = null;

            // PROCESSAR A NOVA CAPA USANDO A MESMA FUNÇÃO ISOLADA
            if (isset($_FILES['cover']) && $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadedCover = $this->processCoverUpload($_FILES['cover']);
                
                if ($uploadedCover === false) {
                    echo "<script>alert('Erro: Formato de capa inválido! Use JPG, PNG ou WEBP.'); window.history.back();</script>";
                    return;
                } elseif ($uploadedCover !== null) {
                    $newCoverPath = $uploadedCover;
                    
                    // Apaga a capa antiga para poupar espaço (se não for a padrão)
                    if ($book['cover_path'] !== 'default_cover.png' && file_exists(__DIR__ . '/../../' . $book['cover_path'])) {
                        unlink(__DIR__ . '/../../' . $book['cover_path']);
                    }
                }
            }

            // ATUALIZA NA BASE DE DADOS
            if ($this->bookModel->update($bookId, $userId, $title, $visibility, $newCoverPath)) {
                echo "<script>alert('Livro atualizado com sucesso! 🎨'); window.location.href='index.php';</script>";
                exit;
            } else {
                echo "<script>alert('Erro ao atualizar o livro.'); window.history.back();</script>";
            }
        }
    }

    // =========================================================
    // FUNÇÃO DE EXCLUSÃO (Apagar Livro e Ficheiros)
    // =========================================================
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
            $bookId = (int)$_POST['book_id'];
            $userId = $_SESSION['user_id'];

            // Verifica se o livro pertence ao utilizador
            $book = $this->bookModel->getByIdAndUser($bookId, $userId);
            if (!$book) {
                echo "<script>alert('Erro: Livro não encontrado ou sem permissão.'); window.location.href='index.php';</script>";
                exit;
            }

            // 1. Caminhos dos ficheiros
            $ebookFile = __DIR__ . '/../../' . $book['file_path'];
            $coverFile = __DIR__ . '/../../' . $book['cover_path'];

            // 2. Apagar ficheiro do ebook
            if (file_exists($ebookFile)) unlink($ebookFile);

            // 3. Apagar capa (se não for a padrão)
            if ($book['cover_path'] !== 'default_cover.png' && file_exists($coverFile)) {
                unlink($coverFile);
            }

            // 4. Remover do banco de dados
            if ($this->bookModel->delete($bookId, $userId)) {
                echo "<script>alert('Livro removido com sucesso!'); window.location.href='index.php';</script>";
                exit;
            }
        }
    }
}