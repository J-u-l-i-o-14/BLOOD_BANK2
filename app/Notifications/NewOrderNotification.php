<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Order $order,
        public OrderItem $orderItem
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
        $amount = number_format($this->orderItem->total_price, 0, ',', ' ');
        $paidAmount = number_format($this->orderItem->paid_amount, 0, ',', ' ');
        $remainingAmount = number_format($this->orderItem->remaining_amount, 0, ',', ' ');

        return (new MailMessage)
            ->subject('Nouvelle commande de poches de sang')
            ->greeting('Bonjour,')
            ->line("Une nouvelle commande a été passée pour votre centre.")
            ->line('Détails de la commande :')
            ->line("- Client : {$this->order->client_name}")
            ->line("- Téléphone : {$this->order->client_phone}")
            ->line("- Groupe sanguin : {$this->orderItem->blood_type}")
            ->line("- Quantité : {$this->orderItem->quantity} poche(s)")
            ->line("- Montant total : {$amount} F CFA")
            ->line("- Montant payé : {$paidAmount} F CFA")
            ->line("- Reste à payer : {$remainingAmount} F CFA")
            ->action('Voir la commande', route('orders.show', $this->order))
            ->line('Merci de traiter cette commande rapidement.');
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
            'order_item_id' => $this->orderItem->id,
            'client_name' => $this->order->client_name,
            'blood_type' => $this->orderItem->blood_type,
            'quantity' => $this->orderItem->quantity,
            'amount' => $this->orderItem->total_price,
            'paid_amount' => $this->orderItem->paid_amount
        ];
    }
}
