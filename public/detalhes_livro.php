<?php
require_once '../config/config.php';
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_once '../src/Models/Book.php';
require_once '../src/Models/Review.php';

$bookId = $_GET['id'] ?? null;
$bookModel = new Book();
$reviewModel = new Review();

$book = $bookModel->getById($bookId);
if (!$book) die("Livro não encontrado.");

// Processar Novo Comentário ou Edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    if (isset($_POST['review_id']) && !empty($_POST['review_id'])) {
        $reviewModel->update($_POST['review_id'], $_SESSION['user_id'], $_POST['rating'], $_POST['comment']);
    } else {
        $reviewModel->addReview($bookId, $_SESSION['user_id'], $_POST['rating'], $_POST['comment']);
    }
    header("Location: detalhes_livro.php?id=$bookId");
    exit;
}

// Processar Exclusão
if (isset($_GET['delete_review'])) {
    $reviewModel->delete($_GET['delete_review'], $_SESSION['user_id']);
    header("Location: detalhes_livro.php?id=$bookId");
    exit;
}

$reviews = $reviewModel->getByBookId($bookId);
$avg = $reviewModel->getAverageRating($bookId);
?>

<div class="max-w-4xl mx-auto mt-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="col-span-1">
            <img src="../<?= $book['cover_path'] ?>" class="w-full rounded-2xl shadow-2xl border border-gray-700">
            <a href="leitura.php?id=<?= $book['id'] ?>" class="mt-6 block text-center bg-green-500 text-gray-900 font-bold py-3 rounded-xl hover:bg-green-600 transition">Começar Leitura</a>
        </div>
        
        <div class="col-span-2">
            <h1 class="text-4xl font-bold text-white mb-2"><?= htmlspecialchars($book['title']) ?></h1>
            <div class="flex items-center gap-2 mb-6">
                <span class="text-yellow-400 text-xl">★</span>
                <span class="text-white font-bold text-lg"><?= number_format($avg, 1) ?></span>
                <span class="text-gray-500">(<?= count($reviews) ?> avaliações)</span>
            </div>

            <!-- Formulário de Comentário -->
            <div class="bg-gray-800 p-6 rounded-2xl border border-gray-700 mb-8">
                <h3 id="form-title" class="text-white font-bold mb-4 text-lg">Deixa a tua opinião</h3>
                <form method="POST" id="review-form" class="space-y-4">
                    <input type="hidden" name="review_id" id="review_id" value="">
                    <div class="flex gap-4">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="rating" value="<?= $i ?>" required class="hidden peer">
                                <span class="text-2xl text-gray-600 peer-checked:text-yellow-400 hover:text-yellow-300 transition">★</span>
                            </label>
                        <?php endfor; ?>
                    </div>
                    <textarea name="comment" id="comment-text" placeholder="O que achaste deste livro?" class="w-full bg-gray-900 border border-gray-700 rounded-xl p-4 text-white outline-none focus:border-green-500" required></textarea>
                    <button type="submit" id="submit-btn" class="bg-green-500 hover:bg-green-600 text-gray-900 px-6 py-2 rounded-lg transition font-bold">Publicar</button>
                </form>
            </div>

            <!-- Lista de Comentários -->
            <div class="space-y-6">
                <h3 class="text-white font-bold text-xl mb-4">Comentários da Comunidade</h3>
                <?php foreach ($reviews as $rev): ?>
                    <div class="bg-gray-800/50 p-6 rounded-2xl border border-gray-700">
                        <div class="flex items-center gap-4 mb-3">
                            <img src="../uploads/avatars/<?= $rev['avatar'] ?>" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <p class="text-white font-bold text-sm"><?= htmlspecialchars($rev['user_name']) ?></p>
                                <p class="text-yellow-400 text-xs"><?= str_repeat('★', $rev['rating']) ?></p>
                            </div>
                            <span class="ml-auto text-gray-500 text-xs"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></span>
                        </div>
                        <p class="text-gray-300 italic">"<?= htmlspecialchars($rev['comment']) ?>"</p>
                        
                        <?php if ($rev['user_id'] == $_SESSION['user_id']): ?>
                            <div class="mt-4 flex gap-4 text-xs font-bold uppercase tracking-widest">
                                <button onclick="editReview(<?= $rev['id'] ?>, <?= $rev['rating'] ?>, '<?= addslashes($rev['comment']) ?>')" class="text-blue-400 hover:text-blue-300 transition">Editar</button>
                                <a href="?id=<?= $bookId ?>&delete_review=<?= $rev['id'] ?>" onclick="return confirm('Apagar este comentário?')" class="text-red-500 hover:text-red-400 transition">Excluir</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function editReview(id, rating, comment) {
    document.getElementById('review_id').value = id;
    document.getElementById('comment-text').value = comment;
    document.getElementById('form-title').innerText = "Editar o teu comentário";
    document.getElementById('submit-btn').innerText = "Salvar Alterações";
    
    const radios = document.getElementsByName('rating');
    radios[rating-1].checked = true;
    
    window.scrollTo({ top: document.getElementById('review-form').offsetTop - 100, behavior: 'smooth' });
}
</script>
<?php require_once '../includes/footer.php'; ?>