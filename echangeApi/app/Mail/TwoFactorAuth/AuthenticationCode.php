<?php

namespace App\Mail\TwoFactorAuth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Message;
use Illuminate\Queue\SerializesModels;

class AuthenticationCode extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var User
     */
    public $user;

    public $verificationCode;

    /**
     * Create a new message instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct($user, $verificationCode)
    {
        $this->user = $user;
        $this->verificationCode = $verificationCode;
    }

    /**
     * Set the subject for the message.
     *
     * @param  Message  $message
     * @return $this
     */
    protected function buildSubject($message)
    {
        $message->subject('['.config('app.name').'] '.__('notifications.verification_code_label', []).$this->verificationCode);

        return $this;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.2fa_authentication.verification-code', [
            'user' => $this->user,
            'code' => $this->verificationCode,
        ]);
    }
}
