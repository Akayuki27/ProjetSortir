// Fonction pour détecter la largeur de l'écran
function detectScreenSize() {
    var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

    // Redirection en fonction de la largeur de l'écran
    if (width < 768) { // Exemple : rediriger si la largeur est inférieure à 768px
        window.location.href = 'http://localhost/ProjetSortir/public/sortie/mesSorties'; // Remplacez 'page_petit_ecran' par l'URL de la page de redirection pour les petits écrans
    }
}

// Appel de la fonction au chargement de la page
window.onload = detectScreenSize;