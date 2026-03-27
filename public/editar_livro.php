<?php
// /public/editar_livro.php
require_once '../config/config.php';
require_once '../src/Controllers/BookController.php';

// Inicia o processo se o formulário for enviado
$bookController = new BookController();
$bookController->edit();

require_once '../includes/header.php';
require_once '../includes/auth.php';
require_once '../src/Models/Book.php';

$bookId = $_GET['id'] ?? null;
if (!$bookId) {
    die("<div class='p-8 text-center text-red-500'>ID do livro não informado.</div>");
}

$bookModel = new Book();
$book = $bookModel->getByIdAndUser($bookId, $_SESSION['user_id']);

if (!$book) {
    die("<div class='p-8 text-center text-red-500'>Livro não encontrado ou não tens permissão para editá-lo.</div>");
}
?>

<div class="max-w-xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl text-green-400 font-bold">Editar Obra ✏️</h2>
        <a href="index.php" class="bg-gray-700 px-4 py-2 rounded hover:bg-gray-600 transition text-white text-sm">Voltar</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-700">
        
        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">

        <label class="block text-gray-400 text-sm mb-2">Título do Livro</label>
        <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required
            class="w-full p-3 mb-6 bg-gray-900 text-white rounded-lg outline-none focus:ring-2 focus:ring-green-400 border border-gray-600">

        <label class="block text-gray-400 text-sm mb-2">Visibilidade</label>
        <select name="visibility" class="w-full p-3 bg-gray-900 text-white rounded-lg mb-6 outline-none focus:ring-2 focus:ring-green-400 border border-gray-600">
            <option value="private" <?= $book['visibility'] === 'private' ? 'selected' : '' ?>>🔒 Privado (Apenas eu)</option>
            <option value="public" <?= $book['visibility'] === 'public' ? 'selected' : '' ?>>🌍 Público (Aparecer no Feed)</option>
        </select>

        <label class="block text-gray-400 text-sm mb-2">Capa do Livro (Opcional)</label>
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-24 bg-gray-900 rounded border border-gray-600 flex-shrink-0 overflow-hidden flex items-center justify-center">
                <?php if ($book['cover_path'] !== 'default_cover.png'): ?>
                    <img src="../<?= $book['cover_path'] ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="text-xs text-gray-500 text-center">Sem Capa</span>
                <?php endif; ?>
            </div>
            
            <input type="file" name="cover" accept=".jpg, .jpeg, .png, .webp"
                class="w-full text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-500/20 file:text-green-400 hover:file:bg-green-500/30 cursor-pointer">
        </div>

        <button type="submit" class="bg-green-500 font-bold text-gray-900 w-full p-3 rounded-xl hover:bg-green-600 transition shadow-lg mt-2">
            Salvar Alterações
        </button>

    </form>
</div>

<?php require_once '../includes/footer.php'; ?>