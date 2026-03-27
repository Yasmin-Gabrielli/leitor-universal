<?php require_once '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Leitura App</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-900 text-white">

<nav class="bg-gray-800 p-4 flex justify-between items-center shadow-md">
    <h1 class="text-green-400 font-bold text-xl">📚 LeituraApp</h1>

    <div class="flex gap-4">
        <a href="index.php" class="hover:text-green-400">Home</a>
        <a href="feed.php" class="hover:text-green-400">Feed</a>
        <a href="upload.php" class="hover:text-green-400">Upload</a>
        <a href="logout.php" class="text-red-400">Sair</a>
    </div>
</nav>

<div class="p-6"></div>