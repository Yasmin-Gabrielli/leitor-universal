// ... código do ePub.js anterior ...
    const rendition = book.renderTo("viewer", { width: "100%", height: "100%", spread: "none" });
    // Onde você tem rendition.display(), substitua por:
const savedLocation = <?= $savedPosition !== "null" ? "'$savedPosition'" : "undefined" ?>;
rendition.display(savedLocation); // Vai abrir diretamente no CFI guardado!

    // ADICIONE ESTAS TRÊS LINHAS:
    rendition.on("relocated", function(location) {
        // location.start.cfi é a string exata de onde o utilizador está no ePub
        saveProgress(location.start.cfi); 
    });
    // ... botões next e prev ...