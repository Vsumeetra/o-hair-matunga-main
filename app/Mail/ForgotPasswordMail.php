<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $name;
    public $email;

    /**
     * Create a new message instance.
     */
    public function __construct($token, $name, $email)
    {
        $this->token = $token;
        $this->name = $name;
        $this->email = $email;
    }
    
    public function build()
    {
        return $this->subject('Password Reset Request')
                    ->view('emails.forgot_password')
                    ->with([
                        'token' => $this->token,
                        'name' => $this->name,
                        'email'=> $this->email
                    ]);
    }
    
}
