<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'client_name',
        'client_email',
        'client_phone',
        'prescription_number',
        'total_amount',
        'paid_amount',
        'payment_method',
        'payment_reference',
        'payment_status',
        'status',
        'documents',
        'documents_validated',
        'documents_validation_comment',
        'documents_validated_at',
        'validated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'documents' => 'array',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all centers involved in this order.
     */
    public function centers()
    {
        return Center::whereIn('id', $this->items->pluck('center_id')->unique());
    }

    /**
     * Scope a query to only include orders with pending payment.
     */
    public function scopePendingPayment($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope a query to only include orders with completed payment.
     */
    public function scopePaidOrders($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Check if the order is fully paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Get the remaining amount to pay.
     */
    public function getRemainingAmount(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Get the validator of the documents.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Check if documents are validated.
     */
    public function areDocumentsValidated(): bool
    {
        return $this->documents_validated;
    }

    /**
     * Validate documents.
     */
    public function validateDocuments(User $validator, string $comment = null): bool
    {
        $this->documents_validated = true;
        $this->documents_validated_at = now();
        $this->validated_by = $validator->id;
        $this->documents_validation_comment = $comment;
        
        if ($this->status === 'pending') {
            $this->status = 'confirmed';
        }
        
        $saved = $this->save();
        
        if ($saved) {
            // Envoyer l'email de confirmation
            event(new OrderStatusChanged($this));
            
            // Créer une notification
            $this->user?->notify(new OrderStatusUpdated($this));
        }
        
        return $saved;
    }

    /**
     * Reject documents.
     */
    public function rejectDocuments(User $validator, string $comment): bool
    {
        $this->documents_validated = false;
        $this->documents_validated_at = now();
        $this->validated_by = $validator->id;
        $this->documents_validation_comment = $comment;
        $this->status = 'cancelled';
        
        $saved = $this->save();
        
        if ($saved) {
            event(new OrderStatusChanged($this));
            $this->user?->notify(new OrderStatusUpdated($this));
        }
        
        return $saved;
    }

    /**
     * Get the status label.
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'completed' => 'Terminée',
            'expired' => 'Expirée',
            default => 'Inconnu'
        };
    }

    /**
     * Get the status color class for UI.
     */
    public function getStatusColorClass(): string
    {
        return match($this->status) {
            'pending' => 'text-yellow-600 bg-yellow-100',
            'confirmed' => 'text-blue-600 bg-blue-100',
            'cancelled' => 'text-red-600 bg-red-100',
            'completed' => 'text-green-600 bg-green-100',
            'expired' => 'text-gray-600 bg-gray-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }
}
