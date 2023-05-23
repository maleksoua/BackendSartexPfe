<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User $authenticable
     */
    protected $authenticable;

    /**
     * Create a new message instance
     *
     * @param Authenticatable $authenticable
     *
     * @return void
     */
    public function __construct(Authenticatable $authenticable)
    {
        $this->authenticable = $authenticable;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->authenticable->email)
            ->subject('Modification de mot de passe')
            ->view('mail.password_update')
            ->with([
                'authenticable' => $this->authenticable,
            ]);
    }
}
