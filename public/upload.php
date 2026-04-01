<?php
require_once '../config/config.php';
require_once '../src/Controllers/BookController.php';

// Inicia o processo de upload se o formulário for enviado
$bookController = new BookController();
$bookController->upload();

require_once '../includes/header.php';
require_once '../includes/auth.php';
?>

<div class="max-w-xl mx-auto mt-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-600">
            Submeter Nova Obra 📤
        </h2>
        <a href="index.php" class="bg-gray-700 px-4 py-2 rounded hover:bg-gray-600 transition text-white text-sm">Voltar</a>
    </div>

    <form method="POST" enctype="multipart/form-data" class="bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-700">

        <label class="block text-gray-400 text-sm mb-2">Título da Obra</label>
        <input type="text" name="title" placeholder="Ex: O Senhor dos Anéis" required
            class="w-full p-3 mb-6 bg-gray-900 text-white rounded-lg outline-none focus:ring-2 focus:ring-green-400 border border-gray-600">

        <label class="block text-gray-400 text-sm mb-2">Arquivo do Livro (obrigatório)</label>
        <input type="file" name="file" accept=".epub, .pdf" required
            class="w-full mb-6 text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-500/20 file:text-green-400 hover:file:bg-green-500/30 cursor-pointer">

        <label class="block text-gray-400 text-sm mb-2">Capa do Livro (opcional)</label>
        <input type="file" name="cover" accept=".jpg, .jpeg, .png, .webp"
            class="w-full mb-6 text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-500/20 file:text-blue-400 hover:file:bg-blue-500/30 cursor-pointer">

        <label class="block text-gray-400 text-sm mb-2">Privacidade</label>
        <select name="visibility" class="w-full p-3 bg-gray-900 text-white rounded-lg mb-8 outline-none focus:ring-2 focus:ring-green-400 border border-gray-600">
            <option value="private">🔒 Privado (Apenas eu)</option>
            <option value="public">🌍 Público (Aparecer no Feed)</option>
        </select>

        <button type="submit" class="bg-green-500 font-bold text-gray-900 w-full p-3 rounded-xl hover:bg-green-600 transition shadow-lg text-lg">
            Enviar para a Biblioteca
        </button>

    </form>
</div>

<?php require_once '../includes/footer.php'; ?>