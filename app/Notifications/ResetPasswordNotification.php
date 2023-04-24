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

        $appName = config('app.name');
        $appUrl = config('app.url');


        // return (new MailMessage)
        // ->from('no-reply@laravel.com', 'Laravel')
        // ->subject('Reset Password')
        // ->greeting("Hello, $notifiable->name!")
        // ->line("You are receiving this email because we received a password reset request for your account.")
        // ->action('Reset Password', new ResetPassword($this->token))
        // ->line('This password reset link will expire in 60 minutes.')
        // ->line('If you did not request a password reset, no further action is required.')
        // ->salutation('Thank you for using '.$appName.'!');

        return (new ResetPassword($this->token))->buildMailMessage($notifiable);
    }

        /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function buildMailMessage($notifiable)
    {
        $appName = config('app.name');
        $appUrl = config('app.url');
        return (new MailMessage)
        ->from('inventario-trycatchtv@outlook.com', 'No Reply - Inventario Trycatch')
        ->subject('Reset Password')
        ->greeting("Hello, $notifiable->name!")
        ->line("You are receiving this email because we received a password reset request for your account.")
        ->action('Reset Password', url(config('app.url_front').route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false)))
        ->line('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')])
        ->line('If you did not request a password reset, no further action is required.')
        ->salutation('Thank you for using '.$appName.'!');





        return (new MailMessage)
            ->subject('¡Restablece tu contraseña!')
            ->greeting('Hola,')
            ->line('Recibes este correo electrónico porque recibimos una solicitud de restablecimiento de contraseña para tu cuenta.')
            ->action('Restablecer contraseña', url(config('app.url').route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false)))
            ->line('Si no solicitaste un restablecimiento de contraseña, no se requiere ninguna otra acción de tu parte.');
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
