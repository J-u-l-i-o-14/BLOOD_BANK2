<!-- Modal du panier -->
<div id="cart-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center" onclick="closeCartModal()">
    <!-- Panel du modal -->
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 max-h-96 overflow-y-auto" onclick="event.stopPropagation()">
        <!-- En-tête -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Mon Panier</h3>
            <button type="button" onclick="closeCartModal()" class="close-modal text-gray-400 hover:text-gray-500">
                <span class="sr-only">Fermer</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Corps du modal -->
        <div class="p-6">
            <!-- Bouton vider le panier -->
            <div class="mb-4 text-right">
                <button type="button" 
                        onclick="clearCart()"
                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                    Vider le panier
                </button>
            </div>

            <!-- Liste des articles -->
            <div id="cart-items" class="space-y-4">
                <!-- Les articles seront insérés ici dynamiquement -->
            </div>

            <!-- Message panier vide -->
            <div id="empty-cart-message" class="hidden py-4 text-center text-gray-500">
                Votre panier est vide
            </div>

            <!-- Total et bouton payer -->
            <div id="cart-footer" class="mt-6 border-t pt-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-semibold text-lg">Total:</span>
                    <span id="cart-total" class="font-bold text-xl text-red-600">0 poches</span>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeCartModal()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded">
                        Fermer
                    </button>
                    <button type="button" 
                            onclick="dispatchPaymentEvent()"
                            class="btn-red">
                        Payer
                    </button>
                    
                    <script>
                        function dispatchPaymentEvent() {
                            if (typeof processPayment === 'function') {
                                // Si l'ancienne fonction existe, on la garde comme fallback
                                processPayment();
                                return;
                            }
                            
                            // Sinon, on utilise la nouvelle approche
                            try {
                                const cart = window.cart || [];
                                if (cart.length === 0) {
                                    if (typeof showToast === 'function') {
                                        showToast('Votre panier est vide', true);
                                    }
                                    return;
                                }

                                // Calculer les totaux et préparer les données
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
                                    groups[id].total_amount += quantity * 5000;
                                    return groups;
                                }, {});

                                const totalAmount = Object.values(centerGroups)
                                    .reduce((sum, group) => sum + group.total_amount, 0);
                                const toPayAmount = Math.round(totalAmount * 0.5);

                                // Créer le résumé HTML
                                const summaryHtml = `
                                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                                        ${Object.values(centerGroups).map(group => `
                                            <div class="border-b last:border-0 pb-3 mb-3">
                                                <div class="font-medium">${group.center_name} (${group.center_region})</div>
                                                ${group.items.map(item => `
                                                    <div class="text-sm text-gray-600 ml-4">
                                                        ${item.blood_type} - ${item.quantity} poche(s)
                                                    </div>
                                                `).join('')}
                                            </div>
                                        `).join('')}
                                        <div class="mt-3 pt-3 border-t">
                                            <div class="flex justify-between font-semibold">
                                                <span>Total :</span>
                                                <span>${totalAmount.toLocaleString('fr-FR')} F CFA</span>
                                            </div>
                                            <div class="flex justify-between text-green-700">
                                                <span>À payer (50%) :</span>
                                                <span>${toPayAmount.toLocaleString('fr-FR')} F CFA</span>
                                            </div>
                                        </div>
                                    </div>
                                `;

                                // Créer et dispatcher l'événement
                                const event = new CustomEvent('processPaymentEvent', {
                                    detail: {
                                        centers: Object.values(centerGroups),
                                        total_amount: totalAmount,
                                        to_pay_amount: toPayAmount,
                                        summary_html: summaryHtml
                                    }
                                });
                                document.dispatchEvent(event);
                            } catch (error) {
                                console.error('Erreur lors du traitement:', error);
                                if (typeof showToast === 'function') {
                                    showToast('Une erreur est survenue', true);
                                }
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
