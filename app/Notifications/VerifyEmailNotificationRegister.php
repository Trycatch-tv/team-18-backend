<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;


class VerifyEmailNotificationRegister extends VerifyEmailNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
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
        $verificationUrl = $this->verificationUrl($notifiable);

        $appName = config('app.name');
        $appUrl = config('app.url');
        // return (new MailMessage)
        //     ->subject(Lang::get('Verify Email Address'))
        //     ->line(Lang::get('Please click the button below to verify your email address.'))
        //     ->action(Lang::get('Verify Email Address'), $verificationUrl)
        //     ->line(Lang::get('If you did not create an account, no further action is required.'));

        return (new MailMessage)
        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_ADDRESS'))
        ->subject('Verify Email Address')
        ->greeting("Hello, $notifiable->name!")
        ->line("Please click the button below to verify your email address for $appName account.")
        ->action('Verify Email Address', $this->verificationUrl($notifiable))
        ->line('If you did not create an account, no further action is required.')
        ->salutation('Thank you for using '.$appName.'!');
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

    public function verificationUrl($notifiable)
    {
        $frontendUrl = env('FRONTEND_URL');
        $temporarySignedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );
        return str_replace(url('/'), $frontendUrl, $temporarySignedUrl);
    }
}
