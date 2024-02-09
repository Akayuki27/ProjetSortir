// Fonction pour détecter la largeur de l'écran
function detectScreenSize() {
    var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    let screen = false;
    // Redirection en fonction de la largeur de l'écran
    if (width < 768) { // Exemple : rediriger si la largeur est inférieure à 768px
       // window.location.href = '{{ path("mes_sorties") }}'; // Remplacez 'page_petit_ecran' par l'URL de la page de redirection pour les petits écrans
        screen = true;
    }
    return screen;
}

function changeContent(type) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            document.getElementById("foo").innerHTML =
                this.responseText;
        }
    };
    if (type === 'mobile') {
        xhttp.open("GET", "/sortie/mesSorties", true);
    } else {
        xhttp.open("GET", "/sortie/list", true);

    }
    xhttp.send();
}

// Appel de la fonction au chargement de la page
window.onload = detectScreenSize;

