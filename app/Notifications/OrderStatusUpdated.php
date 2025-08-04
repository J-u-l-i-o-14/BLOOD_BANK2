<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Mise à jour de votre réservation de sang')
            ->greeting('Bonjour ' . $this->order->client_name)
            ->line('Le statut de votre réservation a été mis à jour.');

        // Message spécifique selon le statut
        match($this->order->status) {
            'confirmed' => $message
                ->line('Vos documents ont été validés et votre réservation est confirmée.')
                ->line('Vous pouvez maintenant procéder au paiement de votre commande.'),
            
            'cancelled' => $message
                ->line('Votre réservation a été annulée.')
                ->line('Raison : ' . ($this->order->documents_validation_comment ?? 'Non spécifiée')),
            
            'completed' => $message
                ->line('Votre réservation est maintenant terminée.')
                ->line('Merci d\'avoir utilisé nos services.'),
            
            'expired' => $message
                ->line('Votre réservation a expiré.')
                ->line('Veuillez effectuer une nouvelle réservation si nécessaire.'),
            
            default => $message
                ->line('Nouveau statut : ' . $this->order->getStatusLabel())
        };

        return $message
            ->action('Voir les détails', route('orders.show', $this->order))
            ->line('Merci de votre confiance.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'status_label' => $this->order->getStatusLabel(),
            'documents_validated' => $this->order->documents_validated,
            'validation_comment' => $this->order->documents_validation_comment,
        ];
    }
}
