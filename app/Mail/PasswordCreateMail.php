<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordCreateMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User $authenticable
     */
    protected $authenticable;

    /**
     * @var string $password
     */
    protected $password;

    /**
     * Create a new message instance
     *
     * @param User $authenticable
     * @param string $password
     *
     * @return void
     */
    public function __construct($authenticable, $password)
    {
        $this->authenticable = $authenticable;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->authenticable->email)
            ->subject('Sartex - Bienvenue dans votre espace pro')
            ->view('mail.password_create')
            ->with([
                'authenticable' => $this->authenticable,
                'password' => $this->password,
            ]);
    }
}
