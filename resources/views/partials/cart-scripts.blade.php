<!-- Script pour le panier -->
<script>
    // Initialiser le panier avec les données de session
    let cart = @json(session('cart', []));

    // Fonction pour afficher les notifications toast
    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        if (!toast) return;
        
        toast.innerHTML = message;
        toast.classList.remove('hidden', 'border-green-600', 'border-red-600', 'text-green-700', 'text-red-700');
        toast.classList.add(
            'border-' + (isError ? 'red' : 'green') + '-600',
            'text-' + (isError ? 'red' : 'green') + '-700'
        );
        
        setTimeout(() => toast.classList.add('hidden'), 5000);
    }

    // Fonction pour ouvrir le modal de réservation
    function openReservationModal(orderData) {
        // Injecter le résumé de la commande
        const summaryElement = document.getElementById('order-summary');
        if (!summaryElement) {
            showToast('Erreur : Modal de réservation non trouvé', true);
            return;
        }
        
        summaryElement.innerHTML = orderData.summary_html;
        
        // Mettre à jour les champs cachés
        document.getElementById('order-data').value = JSON.stringify(orderData.centers);
        document.getElementById('total-amount').value = orderData.total_amount;
        document.getElementById('to-pay-amount').value = orderData.to_pay_amount;
        
        // Afficher le montant total
        document.getElementById('modal-total').textContent = orderData.total_amount.toLocaleString('fr-FR') + ' F CFA';
        document.getElementById('modal-to-pay').textContent = orderData.to_pay_amount.toLocaleString('fr-FR') + ' F CFA';
        
        // Afficher le modal
        const reservationModal = document.getElementById('reservation-modal');
        if (!reservationModal) {
            showToast('Erreur : Modal de réservation non trouvé', true);
            return;
        }
        reservationModal.classList.remove('hidden');
    }

    // Ouvrir le modal du panier
    function openCartModal() {
        document.getElementById('cart-modal').classList.remove('hidden');
        updateCartDisplay();
    }

    // Fermer le modal du panier
    function closeCartModal() {
        document.getElementById('cart-modal').classList.add('hidden');
    }

    // Mise à jour de l'affichage du panier
    function updateCartDisplay() {
        const cartItems = document.getElementById('cart-items');
        const emptyMessage = document.getElementById('empty-cart-message');
        const cartFooter = document.getElementById('cart-footer');
        const cartTotal = document.getElementById('cart-total');

        if (cart.length === 0) {
            cartItems.innerHTML = '';
            emptyMessage.classList.remove('hidden');
            cartFooter.classList.add('hidden');
            return;
        }

        emptyMessage.classList.add('hidden');
        cartFooter.classList.remove('hidden');

        let totalQuantity = 0;
        const itemsHtml = cart.map(item => {
            totalQuantity += parseInt(item.quantity);
            return `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <div>
                        <h4 class="font-medium">${item.center_name}</h4>
                        <p class="text-sm text-gray-600">
                            Groupe ${item.blood_type} - ${item.quantity} poche(s)
                        </p>
                    </div>
                    <button onclick="removeFromCart(${item.center_id})" 
                            class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            `;
        }).join('');

        cartItems.innerHTML = itemsHtml;
        cartTotal.textContent = totalQuantity + (totalQuantity > 1 ? ' poches' : ' poche');
    }

    // Supprimer un élément du panier
    function removeFromCart(centerId) {
        fetch('/cart/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ center_id: centerId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cart = cart.filter(item => item.center_id !== centerId);
                updateCartDisplay();
                showToast('Article retiré du panier', false);
            }
        });
    }

    // Vider le panier
    function clearCart() {
        if (!confirm('Voulez-vous vraiment vider votre panier ?')) return;

        fetch('/cart/clear', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cart = [];
                updateCartDisplay();
                showToast('Panier vidé', false);
            }
        });
    }

    // Traiter le paiement
    function processPayment() {
        if (cart.length === 0) {
            showToast('Votre panier est vide', true);
            return;
        }

        try {
            // Regrouper les articles par centre
            const centerGroups = cart.reduce((groups, item) => {
                const id = item.center_id;
                if (!groups[id]) {
                    groups[id] = {
                        center_id: id,
                        center_name: item.center_name,
                        center_region: item.center_region,
                        items: [],
                        total_quantity: 0,
                        total_amount: 0
                    };
                }
                const quantity = parseInt(item.quantity) || 0;
                groups[id].items.push({...item, quantity});
                groups[id].total_quantity += quantity;
                groups[id].total_amount += quantity * 5000; // 5000 F CFA par poche
                return groups;
            }, {});

            // Calculer le total général
            const totalAmount = Object.values(centerGroups).reduce((sum, group) => sum + group.total_amount, 0);
            const totalQuantity = Object.values(centerGroups).reduce((sum, group) => sum + group.total_quantity, 0);
            const toPayAmount = Math.round(totalAmount * 0.5); // 50% du montant total

            if (totalQuantity === 0) {
                showToast('Erreur : Quantité invalide dans le panier', true);
                return;
            }

            // Créer un résumé HTML pour le modal de réservation
            let orderSummaryHtml = `
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h4 class="font-semibold text-lg mb-3">Résumé de la commande</h4>
                    ${Object.values(centerGroups).map(group => `
                        <div class="border-b last:border-0 pb-3 mb-3 last:pb-0 last:mb-0">
                            <div class="font-medium">${group.center_name} (${group.center_region})</div>
                            ${group.items.map(item => `
                                <div class="text-sm text-gray-600 ml-4">
                                    ${item.blood_type} - ${item.quantity} poche(s)
                                </div>
                            `).join('')}
                            <div class="text-sm text-gray-800 mt-1">
                                Sous-total : ${group.total_amount.toLocaleString('fr-FR')} F CFA
                            </div>
                        </div>
                    `).join('')}
                    <div class="mt-3 pt-3 border-t">
                        <div class="flex justify-between font-semibold">
                            <span>Total :</span>
                            <span>${totalAmount.toLocaleString('fr-FR')} F CFA</span>
                        </div>
                        <div class="flex justify-between text-green-700 font-medium">
                            <span>Montant à payer (50%) :</span>
                            <span>${toPayAmount.toLocaleString('fr-FR')} F CFA</span>
                        </div>
                        <div class="mt-2 text-sm text-gray-500">
                            * Le reste sera à payer au(x) centre(s) lors du retrait
                        </div>
                    </div>
                </div>
            `;

            // Préparer les données pour la réservation
            const orderData = {
                centers: Object.values(centerGroups),
                total_amount: totalAmount,
                to_pay_amount: toPayAmount,
                total_quantity: totalQuantity,
                summary_html: orderSummaryHtml
            };

            // Fermer le modal du panier
            closeCartModal();

            // Ouvrir le modal de réservation avec les données
            openReservationModal(orderData);

        } catch (error) {
            console.error('Erreur lors du traitement du panier:', error);
            showToast('Une erreur est survenue lors du traitement de votre commande', true);
        }
    }
</script>
