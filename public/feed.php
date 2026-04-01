<?php
require_once '../config/config.php';
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_once '../src/Models/Book.php';
require_once '../src/Models/Favorite.php';

$bookModel = new Book();
$publicBooks = $bookModel->getPublicBooks(12, 0); // Carrega os primeiros 12

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
    <div id="book-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 justify-items-center">
        <?php foreach ($publicBooks as $book): ?>
            
            <div class="book-card w-full max-w-sm bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-green-500/20 hover:-translate-y-1 transition-all duration-300 border border-gray-700 flex flex-col group">
                
                <div class="h-64 sm:h-80 relative flex items-center justify-center border-b border-gray-700 bg-gray-900 overflow-hidden">
                    <a href="detalhes_livro.php?id=<?= $book['id'] ?>" class="absolute inset-0 z-10"></a>

                    <?php if(!empty($book['cover_path']) && $book['cover_path'] !== 'default_cover.png'): ?>
                        <img src="../<?= $book['cover_path'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Capa">
                    <?php else: ?>
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-700 to-gray-900"></div>
                        <div class="z-10 text-gray-600 group-hover:text-green-500/50 transition-colors duration-300">
                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M21 4H7a2 2 0 00-2 2v12a2 2 0 002 2h14a1 1 0 001-1V5a1 1 0 00-1-1zm-1 13H7V6h13v11zM7 2h14v2H7z"/></svg>
                        </div>
                    <?php endif; ?>

                    <span class="absolute top-2 right-2 sm:top-3 sm:right-3 z-20 text-[8px] sm:text-[10px] font-bold bg-black/60 backdrop-blur-md text-green-400 px-2 py-1 rounded-md uppercase border border-green-500/30">
                        <?= $book['type'] ?>
                    </span>
                </div>
                <div class="p-3 sm:p-5 flex flex-col flex-grow">
                    <h3 class="text-white font-bold text-sm sm:text-lg leading-tight mb-2 line-clamp-2" title="<?= htmlspecialchars($book['title']) ?>">
                        <?= htmlspecialchars($book['title']) ?>
                    </h3>
                    
                    <div class="flex items-center mt-auto pt-2 sm:pt-4">
                        <?php if (!empty($book['avatar']) && $book['avatar'] !== 'default.png'): ?>
                            <img src="../uploads/avatars/<?= $book['avatar'] ?>" class="w-6 h-6 sm:w-8 sm:h-8 rounded-full object-cover mr-2 sm:mr-3 border border-gray-600">
                        <?php else: ?>
                            <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-gradient-to-tr from-green-500 to-emerald-700 flex items-center justify-center text-[10px] sm:text-xs font-bold text-white mr-2 sm:mr-3 shadow-inner">
                                <?= strtoupper(substr($book['uploader_name'] ?? 'U', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div class="text-sm">
                            <p class="text-gray-300 font-medium truncate text-xs sm:text-sm w-20 sm:w-32"><?= htmlspecialchars($book['uploader_name'] ?? 'Usuário') ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php endforeach; ?>
    </div>

    <!-- Sentinela para o Infinite Scroll -->
    <div id="scroll-sentinel" class="h-20 flex items-center justify-center mt-8">
        <div id="loader" class="hidden animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
    </div>
<?php endif; ?>

<script>
let offset = 12;
const limit = 12;
let loading = false;
let endOfData = false;

const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && !loading && !endOfData) {
        loadMoreBooks();
    }
}, { threshold: 0.1 });

if (document.getElementById('scroll-sentinel')) {
    observer.observe(document.getElementById('scroll-sentinel'));
}

function loadMoreBooks() {
    loading = true;
    document.getElementById('loader').classList.remove('hidden');

    fetch(`../api/get_books.php?offset=${offset}&limit=${limit}`)
        .then(res => res.json())
        .then(data => {
            if (data.length < limit) endOfData = true;
            
            const container = document.getElementById('book-container');
            data.forEach(book => {
                const card = createBookCard(book);
                container.appendChild(card);
            });

            offset += limit;
            loading = false;
            document.getElementById('loader').classList.add('hidden');
        });
}

function createBookCard(book) {
    // Aqui clonaríamos a estrutura do PHP ou usaríamos um template literal
    // Por brevidade, vamos usar um div simples ou podes criar uma função que gera o HTML idêntico
    const div = document.createElement('div');
    div.className = "book-card w-full max-w-sm bg-gray-800 rounded-2xl overflow-hidden border border-gray-700 flex flex-col group";
    div.innerHTML = `
        <div class="h-64 sm:h-80 relative flex items-center justify-center border-b border-gray-700 bg-gray-900 overflow-hidden">
            <a href="detalhes_livro.php?id=${book.id}" class="absolute inset-0 z-10"></a>
            <img src="../${book.cover_path}" class="w-full h-full object-cover" alt="Capa">
            <span class="absolute top-3 right-3 z-20 text-[10px] font-bold bg-black/60 text-green-400 px-2 py-1 rounded-md uppercase border border-green-500/30">${book.type}</span>
        </div>
        <div class="p-5 flex flex-col flex-grow">
            <h3 class="text-white font-bold text-lg mb-2 line-clamp-2">${book.title}</h3>
        </div>`;
    return div;
}

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