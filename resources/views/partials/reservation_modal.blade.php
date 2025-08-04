<!-- Modal de réservation -->
<div id="reservation-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden" x-data="reservationModalHandler()">
    <div class="bg-gradient-to-br from-red-50 to-white rounded-xl shadow-2xl max-w-4xl w-full px-12 py-4 relative flex flex-col items-center max-h-[90vh] overflow-y-auto">
        <button id="close-modal" class="absolute top-3 right-4 text-gray-400 hover:text-red-600 text-3xl font-bold">&times;</button>
        <h2 class="text-3xl font-bold text-red-700 mb-6 text-center">Réserver des poches de sang</h2>

        <!-- Résumé de la commande -->
        <div id="order-summary" class="mb-6 w-full max-w-2xl">
            <!-- Le résumé sera injecté ici dynamiquement -->
        </div>

        <!-- Script pour gérer l'ouverture alternative du modal -->
        <script>
            // Créer un événement personnalisé pour l'ouverture du modal
            const OPEN_RESERVATION_EVENT = 'openReservationModalEvent';
            
            document.addEventListener('DOMContentLoaded', function() {
                const reservationModal = document.getElementById('reservation-modal');
                const cartModal = document.getElementById('cart-modal');
                
                // Écouter l'événement de paiement depuis le panier
                document.addEventListener('processPaymentEvent', function(e) {
                    if (!e.detail) return;
                    
                    try {
                        // Fermer le modal du panier
                        if (cartModal) {
                            cartModal.classList.add('hidden');
                        }
                        
                        // Préparer les données pour le modal de réservation
                        const orderData = e.detail;
                        
                        // Mettre à jour le résumé
                        document.getElementById('order-summary').innerHTML = orderData.summary_html;
                        
                        // Mettre à jour les champs cachés
                        document.getElementById('order-data').value = JSON.stringify(orderData.centers);
                        document.getElementById('total-amount').value = orderData.total_amount;
                        document.getElementById('to-pay-amount').value = orderData.to_pay_amount;
                        
                        // Afficher les montants
                        document.getElementById('modal-total').textContent = orderData.total_amount.toLocaleString('fr-FR') + ' F CFA';
                        document.getElementById('modal-to-pay').textContent = orderData.to_pay_amount.toLocaleString('fr-FR') + ' F CFA';
                        
                        // Ouvrir le modal
                        reservationModal.classList.remove('hidden');
                    } catch (error) {
                        console.error('Erreur lors de l\'ouverture du modal de réservation:', error);
                        // Utiliser le toast s'il existe
                        if (typeof showToast === 'function') {
                            showToast('Une erreur est survenue lors de l\'ouverture du modal de réservation', true);
                        }
                    }
                });
            });
        </script>

        <!-- Formulaire de réservation -->
        <form id="reservation-form" class="w-full max-w-lg space-y-6">
            @csrf
            <input type="hidden" name="order_data" id="order-data">
            <input type="hidden" name="total_amount" id="total-amount">
            <input type="hidden" name="to_pay_amount" id="to-pay-amount">

            <!-- Informations personnelles -->
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="client-name">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="client_name" id="client-name" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-red-500" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="client-email">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="client_email" id="client-email" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-red-500" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="client-phone">Téléphone <span class="text-red-500">*</span></label>
                    <input type="tel" name="client_phone" id="client-phone" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-red-500" 
                           pattern="^(9|7|2)[0-9]{7}$" maxlength="8" minlength="8" placeholder="Ex: 90123456" required>
                    <div class="text-xs text-gray-500">Format : 8 chiffres, commence par 9, 7 ou 2</div>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="prescription-number">Numéro d'ordonnance <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" 
                               name="prescription_number" 
                               id="prescription-number" 
                               class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-red-500" 
                               required
                               onblur="validatePrescriptionNumber(this.value)">
                        <div id="prescription-status" class="absolute right-3 top-2 hidden">
                            <svg class="prescription-valid hidden w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <svg class="prescription-invalid hidden w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div id="prescription-message" class="text-sm mt-1"></div>
                    </div>
                    
                    <!-- Historique des commandes -->
                    <div id="prescription-history" class="mt-4 hidden">
                        <h4 class="font-medium text-gray-700 mb-2">Historique des commandes pour cette ordonnance</h4>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3 max-h-48 overflow-y-auto">
                            <!-- L'historique sera injecté ici -->
                        </div>
                    </div>

                    <!-- Statistiques de l'ordonnance -->
                    <div id="prescription-stats" class="mt-3 hidden">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-blue-50 rounded p-2 text-center">
                                <div class="text-sm text-blue-600">Total commandes</div>
                                <div id="stats-total" class="font-bold text-blue-700">-</div>
                            </div>
                            <div class="bg-green-50 rounded p-2 text-center">
                                <div class="text-sm text-green-600">Commandes complétées</div>
                                <div id="stats-completed" class="font-bold text-green-700">-</div>
                            </div>
                            <div class="bg-gray-50 rounded p-2 text-center">
                                <div class="text-sm text-gray-600">Dernière commande</div>
                                <div id="stats-last-date" class="font-bold text-gray-700">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="client-docs">Documents justificatifs <span class="text-red-500">*</span></label>
                    <input type="file" name="client_docs[]" id="client-docs" 
                           class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-red-500" 
                           accept=".pdf,.jpg,.jpeg,.png" multiple required>
                    <div class="text-xs text-gray-500">Ordonnance médicale, pièce d'identité, etc.</div>
                    <div id="docs-list" class="text-xs text-gray-600 mt-1 flex flex-wrap gap-1"></div>
                </div>
            </div>

            <!-- Montant et bouton de réservation -->
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Montant total :</span>
                        <span id="modal-total" class="text-lg font-semibold">0 F CFA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-green-600">Montant à payer (50%) :</span>
                        <span id="modal-to-pay" class="text-lg font-semibold text-green-700">0 F CFA</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        * Le reste sera à payer dans chaque centre lors du retrait des poches
                    </p>
                </div>

                <!-- Modes de paiement -->
                <div class="space-y-3">
                    <h4 class="font-medium">Mode de paiement</h4>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="payment_method" value="orange_money" class="text-orange-500 focus:ring-orange-500" required>
                            <span class="text-orange-600 font-medium">Orange Money</span>
                        </label>
                        <label class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="payment_method" value="mtn_money" class="text-yellow-500 focus:ring-yellow-500" required>
                            <span class="text-yellow-600 font-medium">MTN Money</span>
                        </label>
                        <label class="flex items-center space-x-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="payment_method" value="moov_money" class="text-blue-500 focus:ring-blue-500" required>
                            <span class="text-blue-600 font-medium">Moov Money</span>
                        </label>
                    </div>
                </div>

                <!-- Bouton de réservation -->
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transform transition-all duration-200 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Payer et réserver maintenant
                </button>
            </div>
        </form>

    </div>
</div>

<!-- Toast notifications -->
<div id="toast" class="fixed top-8 left-1/2 z-[9999] -translate-x-1/2 bg-white/80 backdrop-blur-md border text-gray-700 font-bold px-8 py-5 rounded-xl shadow-2xl hidden transition-all duration-300 text-center max-w-lg w-full"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reservationModal = document.getElementById('reservation-modal');
    const form = document.getElementById('reservation-form');
    const toast = document.getElementById('toast');

    // Fonction pour ouvrir le modal
    window.openReservationModal = function(orderData) {
        // Injecter le résumé de la commande
        document.getElementById('order-summary').innerHTML = orderData.summary_html;
        
        // Mettre à jour les champs cachés avec les données de la commande
        document.getElementById('order-data').value = JSON.stringify(orderData.centers);
        document.getElementById('total-amount').value = orderData.total_amount;
        document.getElementById('to-pay-amount').value = orderData.to_pay_amount;
        
        // Afficher le montant total
        document.getElementById('modal-total').textContent = orderData.total_amount.toLocaleString('fr-FR') + ' F CFA';
        document.getElementById('modal-to-pay').textContent = orderData.to_pay_amount.toLocaleString('fr-FR') + ' F CFA';
        
        reservationModal.classList.remove('hidden');
    };

    // Fermeture du modal
    document.getElementById('close-modal').onclick = () => reservationModal.classList.add('hidden');
    window.onclick = (e) => {
        if (e.target === reservationModal) reservationModal.classList.add('hidden');
    };

    // Affichage des fichiers sélectionnés
    document.getElementById('client-docs').onchange = function() {
        const fileList = Array.from(this.files)
            .map(file => `<span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded">${file.name}</span>`)
            .join('');
        document.getElementById('docs-list').innerHTML = fileList;
    };

    // Fonction de validation du numéro d'ordonnance
    async function validatePrescriptionNumber(number) {
        if (!number) return;

        const statusDiv = document.getElementById('prescription-status');
        const messageDiv = document.getElementById('prescription-message');
        const historyDiv = document.getElementById('prescription-history');
        const statsDiv = document.getElementById('prescription-stats');
        const validIcon = statusDiv.querySelector('.prescription-valid');
        const invalidIcon = statusDiv.querySelector('.prescription-invalid');
        const submitBtn = form.querySelector('button[type="submit"]');

        // Réinitialiser l'affichage
        statusDiv.classList.remove('hidden');
        messageDiv.className = 'text-sm mt-1';
        messageDiv.textContent = 'Vérification...';
        historyDiv.classList.add('hidden');
        statsDiv.classList.add('hidden');
        
        try {
            // Valider l'ordonnance
            const response = await fetch(`/api/validate-prescription/${number}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            validIcon.classList.add('hidden');
            invalidIcon.classList.add('hidden');
            
            if (response.ok) {
                if (data.status === 'completed') {
                    // Ordonnance complétée
                    invalidIcon.classList.remove('hidden');
                    messageDiv.className = 'text-sm mt-1 text-red-600';
                    messageDiv.textContent = 'Cette ordonnance a déjà été complètement traitée. Veuillez fournir une nouvelle ordonnance.';
                    submitBtn.disabled = true;
                    
                    // Afficher les statistiques
                    updateStats(data.stats);
                    statsDiv.classList.remove('hidden');
                    
                } else if (data.status === 'in_progress') {
                    // Ordonnance en cours
                    validIcon.classList.remove('hidden');
                    messageDiv.className = 'text-sm mt-1 text-green-600';
                    messageDiv.textContent = 'Ordonnance valide avec des commandes en cours. Vous pouvez ajouter d\'autres commandes.';
                    submitBtn.disabled = false;
                    
                    // Charger et afficher l'historique
                    await loadPrescriptionHistory(number);
                    historyDiv.classList.remove('hidden');
                    
                    // Afficher les statistiques
                    updateStats(data.stats);
                    statsDiv.classList.remove('hidden');
                    
                } else {
                    // Nouvelle ordonnance
                    validIcon.classList.remove('hidden');
                    messageDiv.className = 'text-sm mt-1 text-green-600';
                    messageDiv.textContent = 'Nouvelle ordonnance valide.';
                    submitBtn.disabled = false;
                }
            } else {
                throw new Error(data.message || 'Erreur de validation');
            }
        } catch (error) {
            invalidIcon.classList.remove('hidden');
            messageDiv.className = 'text-sm mt-1 text-red-600';
            messageDiv.textContent = 'Erreur lors de la validation de l\'ordonnance.';
            submitBtn.disabled = true;
        }
    }

    // Fonction pour charger l'historique des commandes
    async function loadPrescriptionHistory(number) {
        try {
            const response = await fetch(`/api/prescription-history/${number}`);
            const data = await response.json();
            
            if (response.ok && data.success) {
                const historyHtml = data.orders.map(order => `
                    <div class="border-b border-gray-200 pb-2 last:border-0 last:pb-0">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">${order.date}</span>
                        </div>
                        <div class="mt-1">
                            ${order.centers.map(center => `
                                <div class="flex items-center justify-between text-sm">
                                    <span>${center.name}</span>
                                    <span class="flex items-center">
                                        <span class="mr-2">${center.quantity} poche(s)</span>
                                        <span class="px-2 py-1 rounded text-xs ${
                                            center.status === 'completed' 
                                                ? 'bg-green-100 text-green-700' 
                                                : 'bg-yellow-100 text-yellow-700'
                                        }">
                                            ${center.status === 'completed' ? 'Complété' : 'En cours'}
                                        </span>
                                    </span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `).join('');
                
                document.getElementById('prescription-history').querySelector('div').innerHTML = historyHtml;
            }
        } catch (error) {
            console.error('Erreur lors du chargement de l\'historique:', error);
        }
    }

    // Fonction pour mettre à jour les statistiques
    function updateStats(stats) {
        if (!stats) return;
        
        document.getElementById('stats-total').textContent = stats.total_orders;
        document.getElementById('stats-completed').textContent = stats.completed_orders;
        document.getElementById('stats-last-date').textContent = stats.last_order_date;
    }

    // Soumission du formulaire
    form.onsubmit = async function(e) {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Vérifier le numéro d'ordonnance une dernière fois
        const prescriptionNumber = document.getElementById('prescription-number').value;
        await validatePrescriptionNumber(prescriptionNumber);
        
        if (submitBtn.disabled) {
            showToast('Veuillez corriger les erreurs avant de continuer', true);
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Traitement de la réservation...';

        // Vérifier le mode de paiement
        const paymentMethod = form.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            showToast('❌ Veuillez choisir un mode de paiement', true);
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Payer et réserver maintenant';
            return;
        }

        try {
            const formData = new FormData(form);
            const response = await fetch('{{ route("orders.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const result = await response.json();
            
            if (response.ok) {
                // Vider le panier après une commande réussie
                if (typeof cart !== 'undefined') {
                    cart = [];
                    if (typeof updateCartDisplay === 'function') {
                        updateCartDisplay();
                    }
                }
                
                showToast('✅ Réservation enregistrée et paiement validé ! Les centres vont traiter votre demande.', false);
                setTimeout(() => {
                    reservationModal.classList.add('hidden');
                    form.reset();
                    document.getElementById('docs-list').innerHTML = '';
                }, 2000);
            } else {
                showToast('❌ ' + (result.message || 'Une erreur est survenue lors de la réservation'), true);
            }
        } catch (error) {
            console.error('Erreur lors de la réservation:', error);
            showToast('❌ Une erreur est survenue lors de la réservation', true);
        }

        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Payer et réserver maintenant';
    };

    // Fonction pour afficher les notifications toast
    function showToast(message, isError = false) {
        toast.innerHTML = message;
        toast.classList.remove('hidden', 'border-green-600', 'border-red-600', 'text-green-700', 'text-red-700');
        toast.classList.add(
            'border-' + (isError ? 'red' : 'green') + '-600',
            'text-' + (isError ? 'red' : 'green') + '-700'
        );
        
        setTimeout(() => toast.classList.add('hidden'), 5000);
    }
});
</script>
