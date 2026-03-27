<?php
require_once '../config/config.php';
require_once '../src/Controllers/AuthController.php';

$auth = new AuthController();
$auth->register();
?>

<body class="bg-gray-900 flex items-center justify-center h-screen">

<form method="POST" class="bg-gray-800 p-8 rounded-2xl shadow-xl w-80">

    <h2 class="text-2xl text-center mb-6 text-green-400">Cadastro 🚀</h2>

    <input type="text" name="name" placeholder="Nome"
        class="w-full p-3 mb-4 bg-gray-700 rounded">

    <input type="email" name="email" placeholder="Email"
        class="w-full p-3 mb-4 bg-gray-700 rounded">

    <input type="password" name="password" placeholder="Senha"
        class="w-full p-3 mb-6 bg-gray-700 rounded">

    <button class="w-full bg-green-500 p-3 rounded hover:bg-green-600">
        Criar Conta
    </button>

</form>

</body>