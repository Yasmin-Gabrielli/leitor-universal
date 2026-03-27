<?php
// /public/leitura.php

require_once '../config/config.php';
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_once '../src/Models/Book.php';
require_once '../src/Models/Progress.php';

$bookId = $_GET['id'] ?? null;
if (!$bookId) {
    die("<div class='p-8 text-center text-red-500'>Livro não informado na URL.</div>");
}

$bookModel = new Book();
$book = $bookModel->getById($bookId);

if (!$book) {
    die("<div class='p-8 text-center text-red-500'>Livro não encontrado.</div>");
}

if ($book['visibility'] === 'private' && $book['user_id'] !== $_SESSION['user_id']) {
    die("<div class='p-8 text-center text-red-500'>Acesso negado! Este livro é privado.</div>");
}

$progressModel = new Progress();
$savedPosition = $progressModel->get($_SESSION['user_id'], $bookId);
$savedPosition = $savedPosition ? $savedPosition : "null";

$fileUrl = '../' . $book['file_path'];
$fileType = $book['type'];
?>

<style>
    /* Estilos para o Scroll funcionar perfeitamente */
    #viewer {
        overflow-y: auto; /* Permite a rolagem vertical */
        overflow-x: hidden;
    }
    
    #viewer iframe {
        width: 100% !important;
        border: none;
    }

    /* Espaçador de cada página do PDF antes de carregar */
    .pdf-page-wrapper {
        min-height: 800px; /* Mantém a barra de scroll do tamanho certo antes da página carregar */
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .pdf-page-wrapper canvas {
        max-width: 100%;
        height: auto;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        background-color: white;
    }
</style>

<div class="flex flex-col h-[85vh] bg-gray-900 text-white rounded-xl shadow-xl p-4 mt-6">
    <div class="flex justify-between items-center mb-4 border-b border-gray-700 pb-2">
        <h2 class="text-xl font-bold text-green-400 truncate" title="<?= htmlspecialchars($book['title']) ?>">📖 <?= htmlspecialchars($book['title']) ?></h2>
        <a href="index.php" class="bg-gray-700 text-sm px-4 py-2 rounded hover:bg-gray-600 transition flex-shrink-0 ml-4">Sair da Leitura</a>
    </div>

    <div id="viewer" class="flex-grow bg-[#f5f5f5] rounded relative text-black p-4 md:p-8 shadow-inner">
        </div>
    
    <div class="text-center text-gray-500 text-xs mt-3 uppercase tracking-widest">
        ↓ Role para baixo para continuar lendo ↓
    </div>
</div>

<script>
    // Função para salvar progresso na base de dados
    function saveProgress(position) {
        fetch('../api/progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                book_id: <?= (int)$bookId ?>,
                position: position.toString()
            })
        }).catch(error => console.error('Erro de rede:', error));
    }
</script>

<?php if ($fileType === 'epub'): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/epubjs/dist/epub.min.js"></script>
    
    <script>
        const book = ePub("<?= $fileUrl ?>");
        
        // Mudei o flow para 'scrolled' e adicionei o manager 'continuous'
        const rendition = book.renderTo("viewer", { 
            width: "100%", 
            height: "100%", 
            flow: "scrolled",
            manager: "continuous",
            spread: "none"
        });

        const savedLocation = <?= $savedPosition !== "null" ? "'$savedPosition'" : "undefined" ?>;
        rendition.display(savedLocation);

        rendition.on("relocated", function(location) {
            saveProgress(location.start.cfi);
        });
    </script>

<?php elseif ($fileType === 'pdf'): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        const url = "<?= $fileUrl ?>";
        let pdfDoc = null;
        let pageNum = <?= $savedPosition !== "null" ? (int)$savedPosition : 1 ?>; 
        const viewer = document.getElementById('viewer');

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // O IntersectionObserver deteta quais páginas estão visíveis no ecrã
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    let wrapper = entry.target;
                    let num = parseInt(wrapper.dataset.page);
                    
                    // Se a página ainda não foi desenhada, desenha agora!
                    if (!wrapper.dataset.loaded) {
                        wrapper.dataset.loaded = 'true';
                        wrapper.innerHTML = '<span style="color:gray;">Carregando página...</span>'; // Texto temporário
                        renderPage(num, wrapper);
                    }
                    
                    // Salva o progresso da página que o utilizador está a ver agora
                    saveProgress(num);
                }
            });
        }, { 
            root: viewer, 
            threshold: 0.3 // Dispara quando 30% da página entra no ecrã
        });

        // Carrega o documento e cria os espaços (wrappers) para as páginas
        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;

            for (let i = 1; i <= pdfDoc.numPages; i++) {
                let wrapper = document.createElement('div');
                wrapper.id = 'page-wrapper-' + i;
                wrapper.dataset.page = i;
                wrapper.className = 'pdf-page-wrapper';
                viewer.appendChild(wrapper);
                observer.observe(wrapper); // Pede ao observador para vigiar esta div
            }

            // Vai automaticamente para a página onde a leitura parou da última vez
            if (pageNum > 1) {
                setTimeout(() => {
                    const savedWrapper = document.getElementById('page-wrapper-' + pageNum);
                    if (savedWrapper) savedWrapper.scrollIntoView({behavior: 'smooth'});
                }, 500); // Aguarda um bocado para o navegador construir as divs
            }
        });

        // Função que desenha a página do PDF dentro do wrapper
        function renderPage(num, wrapper) {
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({scale: 1.5}); // Zoom 1.5 é ótimo para scroll
                const canvas = document.createElement('canvas');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                const ctx = canvas.getContext('2d');
                
                // Limpa o texto "Carregando" e adiciona o canvas da página
                wrapper.innerHTML = ''; 
                wrapper.appendChild(canvas);
                
                page.render({ canvasContext: ctx, viewport: viewport });
            });
        }
    </script>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>