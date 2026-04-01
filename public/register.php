<?php
require_once '../config/config.php';
require_once '../src/Controllers/AuthController.php';

$auth = new AuthController();
$auth->register();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo - Leitura Universal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen relative overflow-hidden font-sans">

    <div class="absolute top-[-10%] right-[-10%] w-96 h-96 bg-emerald-600/20 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-10%] w-96 h-96 bg-green-500/20 rounded-full blur-[100px] pointer-events-none"></div>

    <form method="POST" enctype="multipart/form-data" class="bg-gray-800 p-8 sm:p-10 rounded-3xl shadow-2xl shadow-black/50 w-full max-w-sm relative z-10 border border-gray-700">
        
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-gradient-to-tr from-emerald-500 to-green-400 rounded-2xl flex items-center justify-center shadow-lg transform rotate-6 hover:rotate-0 transition-all duration-300">
                <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
        </div>

        <h2 class="text-3xl font-extrabold text-center mb-2 text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-green-500">
            Criar Conta
        </h2>
        <p class="text-center text-gray-400 text-sm mb-8">Junta-te à nossa comunidade de leitores.</p>

        <div class="mb-5 relative group">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 group-focus-within:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <input type="text" name="name" placeholder="O teu Nome" required
                class="w-full pl-11 p-3.5 bg-gray-900/50 text-white rounded-xl border border-gray-700 outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all placeholder-gray-500">
        </div>

        <div class="mb-5 relative group">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 group-focus-within:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                </svg>
            </div>
            <input type="email" name="email" placeholder="O teu Email" required
                class="w-full pl-11 p-3.5 bg-gray-900/50 text-white rounded-xl border border-gray-700 outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all placeholder-gray-500">
        </div>

        <div class="mb-6 relative group">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 group-focus-within:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <input type="password" name="password" placeholder="A tua Senha" required
                class="w-full pl-11 p-3.5 bg-gray-900/50 text-white rounded-xl border border-gray-700 outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all placeholder-gray-500">
        </div>

        <div class="mb-6">
            <label class="block text-gray-400 text-sm mb-2">Foto de Perfil (opcional)</label>
            <input type="file" name="avatar" accept=".jpg, .jpeg, .png, .webp" class="w-full text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-500/20 file:text-green-400 hover:file:bg-green-500/30 cursor-pointer">
        </div>

        <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-green-600 text-gray-900 font-bold p-3.5 rounded-xl hover:from-emerald-400 hover:to-green-500 transition-all shadow-lg hover:shadow-green-500/30 transform hover:-translate-y-0.5">
            Criar a minha Biblioteca
        </button>

        <p class="text-sm mt-6 text-center text-gray-400">
            Já tens uma conta? <a href="login.php" class="text-green-400 font-semibold hover:text-green-300 hover:underline transition-all">Entrar agora</a>
        </p>

    </form>
</body>
</html>