<?php
require_once '../config/config.php';
require_once '../src/Controllers/AuthController.php';

$auth = new AuthController();
$auth->login();
?>

<body class="bg-gray-900 flex items-center justify-center h-screen">

<form method="POST" class="bg-gray-800 p-8 rounded-2xl shadow-xl w-80">

    <h2 class="text-2xl text-center mb-6 text-green-400">Login 😏</h2>

    <input type="email" name="email" placeholder="Email"
        class="w-full p-3 mb-4 bg-gray-700 rounded outline-none focus:ring-2 focus:ring-green-400">

    <input type="password" name="password" placeholder="Senha"
        class="w-full p-3 mb-6 bg-gray-700 rounded outline-none focus:ring-2 focus:ring-green-400">

    <button class="w-full bg-green-500 p-3 rounded hover:bg-green-600 transition">
        Entrar
    </button>

    <p class="text-sm mt-4 text-center">
        Não tem conta? <a href="register.php" class="text-green-400">Criar</a>
    </p>

</form>

</body>