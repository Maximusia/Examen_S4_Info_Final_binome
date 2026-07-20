    </div>
    <!-- FIN CONTENU PRINCIPAL -->

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> Mobile Money - Tous droits réservés</p>
            <small>Application de transfert d'argent mobile sécurisée</small>
        </div>
    </footer>

    <!-- Bootstrap 5 JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script personnalisé pour les interactions dynamiques -->
    <script>
        // Fermer automatiquement les alertes après 5 secondes
        if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
            document.querySelectorAll('.alert').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                setTimeout(() => {
                    bsAlert.close();
                }, 5000);
            });
        }

        // Fonction pour formater les montants en monnaie
        function formatMoney(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'MGA', // Ariary malgache
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        // Validation de formulaire client
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                const forms = document.querySelectorAll('.needs-validation');
                Array.prototype.slice.call(forms).forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>
