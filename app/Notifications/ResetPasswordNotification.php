<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return $this->buildMailMessage($notifiable, $this->resetUrl($notifiable));

        // return (new ResetPassword($this->token))->buildMailMessage($this->resetUrl($notifiable));
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function buildMailMessage($notifiable, $url)
    {
        $appName = config('app.name');
        return (new MailMessage)
            ->from('inventario-trycatchtv@outlook.com', 'No Reply - Inventario Trycatch')
            ->subject(Lang::get('Reset Password'))
            ->greeting("Hello, $notifiable->name!")
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), $url)
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'))
            ->salutation('Thank you for using ' . $appName . '!');
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

    public function resetUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        return url(config('app.url_front') . route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
