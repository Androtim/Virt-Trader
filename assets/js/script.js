// Fermeture automatique des alertes après 5 secondes
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer toutes les alertes
    const alerts = document.querySelectorAll('.alert');
    
    // Fermer automatiquement après 5 secondes
    if (alerts.length > 0) {
        setTimeout(function() {
            alerts.forEach(function(alert) {
                // Créer un nouvel élément qui correspond au bouton de fermeture Bootstrap
                var close = new bootstrap.Alert(alert);
                close.close();
            });
        }, 5000);
    }
    
    // Tooltip pour les boutons d'action
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Format de prix en temps réel pour l'achat/vente
function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price);
}

// Fonction pour confirmer les actions importantes
function confirmAction(message) {
    return confirm(message);
}
