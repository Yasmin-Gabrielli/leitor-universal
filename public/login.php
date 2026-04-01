<?php
require_once '../config/config.php';
require_once '../src/Controllers/AuthController.php';

$auth = new AuthController();
$auth->login();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Leitura Universal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen relative overflow-hidden font-sans">

    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-green-500/20 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-emerald-600/20 rounded-full blur-[100px] pointer-events-none"></div>

    <form method="POST" class="bg-gray-800 p-8 sm:p-10 rounded-3xl shadow-2xl shadow-black/50 w-full max-w-sm relative z-10 border border-gray-700">
        
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-gradient-to-tr from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg transform -rotate-6 hover:rotate-0 transition-all duration-300">
                <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
        </div>

        <h2 class="text-3xl font-extrabold text-center mb-2 text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-600">
            Bem-vindo(a)!
        </h2>
        <p class="text-center text-gray-400 text-sm mb-8">Acede à tua biblioteca pessoal.</p>

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

        <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-gray-900 font-bold p-3.5 rounded-xl hover:from-green-400 hover:to-emerald-500 transition-all shadow-lg hover:shadow-green-500/30 transform hover:-translate-y-0.5">
            Entrar na Biblioteca
        </button>

        <p class="text-sm mt-6 text-center text-gray-400">
            Ainda não tens conta? <a href="register.php" class="text-green-400 font-semibold hover:text-green-300 hover:underline transition-all">Criar agora</a>
        </p>

    </form>
</body>
</html>