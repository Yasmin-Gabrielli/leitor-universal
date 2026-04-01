<?php
require_once '../config/config.php';
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_once '../src/Controllers/UserController.php';

$userController = new UserController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') $userController->updateProfile();
$user = $userController->getUserData($_SESSION['user_id']);
?>

<div class="max-w-2xl mx-auto mt-10 p-8 bg-gray-800 border border-gray-700 rounded-2xl shadow-xl">
    <h2 class="text-3xl font-bold text-white mb-8">O Meu Perfil</h2>
    <form action="perfil.php" method="POST" enctype="multipart/form-data" class="space-y-6">
        <div class="flex items-center gap-6 mb-8">
            <div class="w-24 h-24 rounded-full border-4 border-green-500/30 overflow-hidden bg-gray-900">
                <img src="../uploads/avatars/<?= $user['avatar'] ?? 'default.png' ?>" class="w-full h-full object-cover" id="preview">
            </div>
            <input type="file" name="avatar" accept="image/*" onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0])" class="text-sm text-gray-400">
        </div>
        <div>
            <label class="block text-gray-400 mb-2">Nome</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white">
        </div>
        <div>
            <label class="block text-gray-400 mb-2">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white">
        </div>
        <div>
            <label class="block text-gray-400 mb-2">Bio</label>
            <textarea name="bio" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="block text-gray-400 mb-2">Nova Senha (opcional)</label>
            <input type="password" name="password" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-3 text-white">
        </div>
        <button type="submit" class="w-full bg-green-500 text-gray-900 font-bold py-3 rounded-xl hover:bg-green-600 transition">Salvar Alterações</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>