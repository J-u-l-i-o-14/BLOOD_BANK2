<!-- Modal de validation des documents -->
<div id="validate-docs-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 relative">
        <button onclick="closeValidateDocsModal()" class="absolute top-3 right-4 text-gray-400 hover:text-gray-600">
            <span class="sr-only">Fermer</span>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h3 class="text-xl font-bold mb-4">Validation des documents</h3>
        
        <!-- Détails de la commande -->
        <div class="bg-gray-50 p-4 rounded-lg mb-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Client :</span>
                    <span class="font-medium" id="modal-client-name"></span>
                </div>
                <div>
                    <span class="text-gray-600">Numéro d'ordonnance :</span>
                    <span class="font-medium" id="modal-prescription-number"></span>
                </div>
            </div>
        </div>

        <!-- Liste des documents -->
        <div class="mb-4">
            <h4 class="font-medium mb-2">Documents fournis :</h4>
            <div id="modal-documents" class="space-y-2">
                <!-- Les documents seront injectés ici -->
            </div>
        </div>

        <!-- Formulaire de validation -->
        <form id="docs-validation-form" class="space-y-4">
            <input type="hidden" id="modal-order-id" name="order_id">
            
            <div>
                <label class="block text-gray-700 font-medium mb-1" for="validation-comment">
                    Commentaire de validation
                </label>
                <textarea id="validation-comment" name="comment" rows="3"
                    class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-red-500"
                    placeholder="Commentaire optionnel pour la validation, obligatoire en cas de rejet"></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" 
                        onclick="rejectDocuments()"
                        class="px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 font-medium">
                    Rejeter
                </button>
                <button type="button"
                        onclick="validateDocuments()"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
                    Valider
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentOrder = null;

function openValidateDocsModal(order) {
    currentOrder = order;
    
    // Mettre à jour les informations du modal
    document.getElementById('modal-client-name').textContent = order.client_name;
    document.getElementById('modal-prescription-number').textContent = order.prescription_number;
    document.getElementById('modal-order-id').value = order.id;
    
    // Afficher les documents
    const docsContainer = document.getElementById('modal-documents');
    docsContainer.innerHTML = order.documents.map(doc => `
        <div class="flex items-center justify-between bg-white p-3 rounded border">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span class="text-sm">${doc.name}</span>
            </div>
            <a href="${doc.url}" target="_blank" 
               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Voir
            </a>
        </div>
    `).join('');
    
    // Afficher le modal
    document.getElementById('validate-docs-modal').classList.remove('hidden');
}

function closeValidateDocsModal() {
    document.getElementById('validate-docs-modal').classList.add('hidden');
    document.getElementById('docs-validation-form').reset();
    currentOrder = null;
}

async function validateDocuments() {
    if (!currentOrder) return;
    
    const comment = document.getElementById('validation-comment').value;
    
    try {
        const response = await fetch(`/api/orders/${currentOrder.id}/validate-documents`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ comment })
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showToast('Documents validés avec succès', false);
            closeValidateDocsModal();
            // Recharger la page pour mettre à jour la liste
            window.location.reload();
        } else {
            throw new Error(result.message || 'Erreur lors de la validation');
        }
    } catch (error) {
        showToast(error.message || 'Une erreur est survenue', true);
    }
}

async function rejectDocuments() {
    if (!currentOrder) return;
    
    const comment = document.getElementById('validation-comment').value;
    if (!comment) {
        showToast('Un commentaire est requis pour le rejet des documents', true);
        return;
    }
    
    try {
        const response = await fetch(`/api/orders/${currentOrder.id}/reject-documents`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ comment })
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showToast('Documents rejetés', false);
            closeValidateDocsModal();
            // Recharger la page pour mettre à jour la liste
            window.location.reload();
        } else {
            throw new Error(result.message || 'Erreur lors du rejet');
        }
    } catch (error) {
        showToast(error.message || 'Une erreur est survenue', true);
    }
}
</script>
