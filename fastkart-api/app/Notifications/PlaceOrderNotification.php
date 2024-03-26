<?php

namespace App\Notifications;

use App\Models\Order;
use App\Enums\RoleEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PlaceOrderNotification extends Notification
{
    use Queueable;

    private $order;
    private $roleName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $roleName)
    {
        $this->order = $order;
        $this->roleName = $roleName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        switch($this->roleName) {
            case RoleEnum::CONSUMER:
               return $this->toConsumerMail();
            case RoleEnum::VENDOR:
                return $this->toVendorMail();
            case RoleEnum::ADMIN:
                return $this->toAdminMail();
        }
    }

    public function toAdminMail(): MailMessage
    {
        return (new MailMessage)
            ->subject("An Order #{$this->order->order_number} has been placed")
            ->line('An order has been placed successfully.')
            ->line('Order Payment Status: '.$this->order->payment_status)
            ->line('Order Status: '.$this->order->order_status->name)
            ->line("Your prompt attention is requested.");
    }

    public function toVendorMail(): MailMessage
    {
        return (new MailMessage)
            ->subject("New Order #{$this->order->order_number} from Your Store")
            ->line('Congratulations! A new order has been received from your store.')
            ->line('Order Payment Status: '.$this->order->payment_status)
            ->line('Order Status: '.$this->order->order_status->name)
            ->line('Thank you for partnering with us!')
            ->line('If you have any questions, please contact us.');
    }

    public function toConsumerMail(): MailMessage
    {
        return (new MailMessage)
            ->subject("Your Order #{$this->order->order_number} Confirmation")
            ->greeting("Hello {$this->order->consumer->name},")
            ->line("We're excited to confirm your order with Order #{$this->order->order_number}.")
            ->line("Order Payment Status: {$this->order->payment_status}")
            ->line("Order Status: {$this->order->order_status->name}")
            ->line("Thank you for choosing us for your shopping needs.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        switch($this->roleName) {
            case RoleEnum::CONSUMER:
                $message = "Your order has been successfully placed. Order ID: #{$this->order->order_number}. Thank you for choosing us.";
                break;
            case RoleEnum::VENDOR:
                $message = "A consumer has ordered from your catalog. Order ID: #{$this->order->order_number}. Please ensure prompt fulfillment.";
                break;
            case RoleEnum::ADMIN:
                $message = "An order has been placed successfully. Order ID: #{$this->order->order_number}. Your prompt attention is requested.";
                break;
        }

        return [
            'title' => "Order has been placed",
            'message' => $message,
            'type' => "order"
        ];
    }
}