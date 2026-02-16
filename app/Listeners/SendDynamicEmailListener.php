<?php

namespace App\Listeners;

use App\Mail\DynamicMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendDynamicEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        if (!$user->hasVerifiedEmail()) {
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
            );

            Mail::to($user->email)->send(new DynamicMail('verification_email', [
                'username' => $user->name,
                'app_name' => config('app.name'),
                'verification_url' => $verificationUrl,
            ]));
        }
    }
}
