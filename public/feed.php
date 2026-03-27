<?php
require_once '../config/config.php';
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_once '../src/Models/Book.php';
require_once '../src/Models/Favorite.php';

$bookModel = new Book();
$publicBooks = $bookModel->getPublicBooks();

// Pega os favoritos do usuário logado
$favoriteModel = new Favorite();
$myFavorites = $favoriteModel->getUserFavoriteBookIds($_SESSION['user_id']);
?>

<div class="flex items-center justify-between mb-8 mt-4">
    <div>
        <h2 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-600">
            Feed Global 🌍
        </h2>
        <p class="text-gray-400 mt-1">Descubra obras partilhadas pela comunidade.</p>
    </div>
</div>

<?php if (empty($publicBooks)): ?>
    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-12 text-center shadow-lg">
        <span class="text-4xl block mb-4">📭</span>
        <h3 class="text-xl text-white font-bold mb-2">O feed está vazio</h3>
        <p class="text-gray-400">Ainda não existem livros públicos.</p>
        <a href="upload.php" class="inline-block mt-6 bg-green-500 text-gray-900 font-bold px-6 py-2 rounded-lg hover:bg-green-600 transition">Fazer Upload</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($publicBooks as $book): ?>
            
            <div class="bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-green-500/20 hover:-translate-y-1 transition-all duration-300 border border-gray-700 flex flex-col group">
                
                <div class="h-48 relative flex items-center justify-center border-b border-gray-700 bg-gray-900 overflow-hidden">
                    
                    <?php if(!empty($book['cover_path']) && $book['cover_path'] !== 'default_cover.png'): ?>
                        <img src="../<?= $book['cover_path'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Capa">
                    <?php else: ?>
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-700 to-gray-900"></div>
                        <div class="z-10 text-gray-600 group-hover:text-green-500/50 transition-colors duration-300">
                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M21 4H7a2 2 0 00-2 2v12a2 2 0 002 2h14a1 1 0 001-1V5a1 1 0 00-1-1zm-1 13H7V6h13v11zM7 2h14v2H7z"/></svg>
                        </div>
                    <?php endif; ?>

                    <span class="absolute top-3 right-3 z-20 text-[10px] font-bold bg-black/60 backdrop-blur-md text-green-400 px-2 py-1 rounded-md uppercase tracking-wider border border-green-500/30">
                        <?= $book['type'] ?>
                    </span>
                </div>
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="text-white font-bold text-lg leading-tight mb-2 line-clamp-2" title="<?= htmlspecialchars($book['title']) ?>">
                        <?= htmlspecialchars($book['title']) ?>
                    </h3>
                    
                    <div class="flex items-center mt-auto pt-4">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-green-500 to-emerald-700 flex items-center justify-center text-xs font-bold text-white mr-3 shadow-inner">
                            <?= strtoupper(substr($book['uploader_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="text-sm">
                            <p class="text-gray-500 text-xs">Partilhado por</p>
                            <p class="text-gray-300 font-medium truncate w-32"><?= htmlspecialchars($book['uploader_name'] ?? 'Usuário') ?></p>
                        </div>
                    </div>
                </div>

                <div class="p-5 pt-0 mt-auto flex gap-2">
                    <a href="leitura.php?id=<?= $book['id'] ?>" class="flex-grow flex items-center justify-center bg-gray-700 hover:bg-green-500 text-white hover:text-gray-900 font-bold py-2.5 rounded-xl transition-all duration-300">
                        Ler
                    </a>
                    
                    <?php $isFav = in_array($book['id'], $myFavorites); ?>
                    <button onclick="toggleFavorite(<?= $book['id'] ?>, this)" class="w-12 flex items-center justify-center bg-gray-700 hover:bg-red-500/20 text-white border border-gray-600 rounded-xl transition-all duration-300 group cursor-pointer">
                        <svg class="w-5 h-5 transition-colors duration-300 <?= $isFav ? 'text-red-500 fill-current' : 'text-gray-400 group-hover:text-red-500' ?>" 
                             fill="<?= $isFav ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                </div>

            </div>
            
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function toggleFavorite(bookId, buttonElement) {
    const svg = buttonElement.querySelector('svg');
    
    fetch('../api/favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ book_id: bookId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.action === 'added') {
                svg.classList.add('text-red-500', 'fill-current');
                svg.classList.remove('text-gray-400');
                svg.setAttribute('fill', 'currentColor');
            } else {
                svg.classList.remove('text-red-500', 'fill-current');
                svg.classList.add('text-gray-400');
                svg.setAttribute('fill', 'none');
            }
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>