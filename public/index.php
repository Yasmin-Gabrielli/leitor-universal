<?php
require_once '../config/config.php';
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_once '../src/Models/Book.php';

$bookModel = new Book();
$myBooks = $bookModel->getByUserId($_SESSION['user_id']);
?>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 mt-4 gap-4">
    <div>
        <a href="perfil.php" class="flex items-center gap-4 group">
            <div class="w-16 h-16 rounded-2xl border-2 border-green-500/30 overflow-hidden bg-gray-800 shadow-lg group-hover:scale-105 transition-transform duration-300">
                <img src="../uploads/avatars/<?= $_SESSION['avatar'] ?? 'default.png' ?>" 
                     class="w-full h-full object-cover" 
                     alt="Perfil">
            </div>
            <h2 class="text-3xl font-extrabold text-white">
                Bem-vindo(a), <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-600 group-hover:underline"><?= htmlspecialchars($_SESSION['user_name']); ?></span> 👋
            </h2>
        </a>
        <p class="text-gray-400 mt-1">O teu painel de leitura pessoal.</p>
    </div>
    <a href="upload.php" class="bg-green-500 text-gray-900 font-bold px-6 py-3 rounded-xl hover:bg-green-600 transition shadow-lg hover:shadow-green-500/30 flex items-center gap-2 flex-shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Novo Livro
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
    <a href="favoritos.php" class="bg-gray-800 p-6 rounded-2xl shadow-lg hover:-translate-y-1 transition duration-300 hover:shadow-red-500/20 flex items-center border border-gray-700 hover:border-red-500/50 group">
        <div class="w-16 h-16 bg-red-500/10 rounded-xl flex items-center justify-center text-3xl group-hover:scale-110 transition mr-4">❤️</div>
        <div>
            <h3 class="font-bold text-white text-xl">Meus Favoritos</h3>
            <p class="text-gray-400 text-sm">Livros que guardaste</p>
        </div>
    </a>
    
    
</div>

<div class="mb-6 flex items-center">
    <h3 class="text-2xl font-bold text-white border-l-4 border-green-500 pl-3">📚 A Minha Biblioteca</h3>
</div>

<?php if (empty($myBooks)): ?>
    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-12 text-center shadow-lg">
        <span class="text-4xl block mb-4">📂</span>
        <h3 class="text-xl text-white font-bold mb-2">A tua biblioteca está vazia</h3>
        <p class="text-gray-400">Ainda não tens nenhum livro na tua conta. Faz o upload do teu primeiro ficheiro para começares a ler!</p>
        <a href="upload.php" class="inline-block mt-6 bg-gray-700 text-white font-bold px-6 py-2 rounded-lg hover:bg-gray-600 transition border border-gray-600">Fazer Upload Agora</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 justify-items-center">
        <?php foreach ($myBooks as $book): ?>
            
            <div class="w-full max-w-sm bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-green-500/20 hover:-translate-y-1 transition-all duration-300 border border-gray-700 flex flex-col group">
                
                <div class="h-64 sm:h-80 relative flex items-center justify-center border-b border-gray-700 bg-gray-900 overflow-hidden group-hover:opacity-90 transition">
                    
                    <?php if($book['cover_path'] !== 'default_cover.png'): ?>
                        <img src="../<?= $book['cover_path'] ?>" class="w-full h-full object-cover" alt="Capa do Livro">
                    <?php else: ?>
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-700 to-gray-900"></div>
                        <div class="z-10 text-gray-600 group-hover:text-green-500/50 transition-colors duration-300">
                            <?php if($book['type'] === 'pdf'): ?>
                                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                            <?php else: ?>
                                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M21 4H7a2 2 0 00-2 2v12a2 2 0 002 2h14a1 1 0 001-1V5a1 1 0 00-1-1zm-1 13H7V6h13v11zM7 2h14v2H7z"/></svg>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <span class="absolute top-3 left-3 z-20 text-[10px] font-bold bg-black/50 backdrop-blur-sm text-white px-2 py-1 rounded-md uppercase tracking-wider border border-gray-600">
                        <?= $book['type'] ?>
                    </span>

                    <?php if($book['visibility'] === 'public'): ?>
                        <span class="absolute top-3 right-3 z-20 text-[10px] font-bold bg-green-500/20 backdrop-blur-sm text-green-400 px-2 py-1 rounded-md uppercase tracking-wider border border-green-500/30 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Público
                        </span>
                    <?php else: ?>
                        <span class="absolute top-3 right-3 z-20 text-[10px] font-bold bg-gray-500/20 backdrop-blur-sm text-gray-400 px-2 py-1 rounded-md uppercase tracking-wider border border-gray-500/30 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Privado
                        </span>
                    <?php endif; ?>
                </div>
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="text-white font-bold text-lg leading-tight mb-2 line-clamp-2" title="<?= htmlspecialchars($book['title']) ?>">
                        <?= htmlspecialchars($book['title']) ?>
                    </h3>
                    <p class="text-gray-500 text-xs mt-auto">Adicionado recentemente</p>
                </div>

                <div class="p-5 pt-0 mt-auto flex gap-2">
                    <a href="leitura.php?id=<?= $book['id'] ?>" class="flex-grow flex items-center justify-center bg-gray-700 hover:bg-green-500 text-white hover:text-gray-900 font-bold py-2.5 rounded-xl transition-all duration-300">
                        Ler
                    </a>
                    
                    <a href="editar_livro.php?id=<?= $book['id'] ?>" class="w-12 flex items-center justify-center bg-gray-700 hover:bg-yellow-500 text-white border border-gray-600 rounded-xl transition-all duration-300 group cursor-pointer" title="Editar Livro">
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>

                    <form action="excluir_livro.php" method="POST" onsubmit="return confirm('Tens a certeza que queres eliminar este livro? Todos os ficheiros serão apagados permanentemente.')" class="inline">
                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                        <button type="submit" class="w-12 h-full flex items-center justify-center bg-gray-700 hover:bg-red-600 text-white border border-gray-600 rounded-xl transition-all duration-300 group" title="Excluir Livro">
                            <svg class="w-5 h-5 text-gray-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
                </div>
            
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>