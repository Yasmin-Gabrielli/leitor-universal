<?php
require_once '../config/config.php';
require_once '../src/Controllers/BookController.php';

// Inicia o processo de upload se o formulário for enviado
$bookController = new BookController();
$bookController->upload();

require_once '../includes/header.php';
require_once '../includes/auth.php';
?>

<h2 class="text-2xl mb-6 text-green-400 font-bold">Upload de Livro 📤</h2>

<form method="POST" enctype="multipart/form-data" class="bg-gray-800 p-6 rounded-xl w-full max-w-md shadow-lg">

    <input type="text" name="title" placeholder="Título da Obra" required
        class="w-full p-3 mb-4 bg-gray-700 text-white rounded outline-none focus:ring-2 focus:ring-green-400">

    <input type="file" name="file" accept=".epub, .pdf" required
        class="w-full mb-4 text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer">

    <select name="visibility" class="w-full p-3 bg-gray-700 text-white rounded mb-6 outline-none focus:ring-2 focus:ring-green-400">
        <option value="private">Privado (Apenas eu)</option>
        <option value="public">Público (Aparecer no Feed)</option>
    </select>

    <button type="submit" class="bg-green-500 font-bold text-gray-900 w-full p-3 rounded hover:bg-green-600 transition shadow">
        Enviar para a Biblioteca
    </button>

</form>

<?php require_once '../includes/footer.php'; ?>