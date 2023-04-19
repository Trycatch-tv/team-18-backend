<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;
    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        // $verificationUrl = $this->verificationUrl($notifiable);

        // $appName = config('app.name');
        // $appUrl = config('app.url');


        // return (new MailMessage)
        // ->from('no-reply@laravel.com', 'Laravel')
        // ->subject('Verify Email Address')
        // ->greeting("Hello, $notifiable->name!")
        // ->line("Please click the button below to verify your email address for $appName account.")
        // ->action('Verify Email Address', $this->verificationUrl($notifiable))
        // ->line('If you did not create an account, no further action is required.')
        // ->salutation('Thank you for using '.$appName.'!');

        return (new ResetPassword($this->token))->toMail($notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
