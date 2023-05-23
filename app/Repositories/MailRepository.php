<?php

namespace App\Repositories;

use App\Exceptions\EmailServiceNotAvailableException;
use App\Mail\PasswordUpdateMail;
use App\Mail\PasswordCreateMail;
use App\Models\User;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class MailRepository
{
    /**
     * Sends an email to a User containing his new password
     *
     * @param Authenticatable $authenticable
     *
     * @return bool
     * @throws EmailServiceNotAvailableException
     */
    public static function sendPasswordUpdateMail(Authenticatable $authenticable)
    {
        return self::sendMail(new PasswordUpdateMail($authenticable));
    }

    /**
     * Sends an email to a newly created User containing email and password
     *
     * @param User $authenticable
     * @param string $password
     *
     * @return bool
     * @throws EmailServiceNotAvailableException
     */
    public static function sendPasswordCreateMail($authenticable, $password)
    {
        if (!($authenticable instanceof User)) {
            throw new \InvalidArgumentException('$authenticable must be either an instance of ' . User::class);
        }
        return self::sendMail(new PasswordCreateMail($authenticable, $password));
    }

    /**
     * Sends an email
     *
     * @param Mailable $mail
     *
     * @return bool
     * @throws EmailServiceNotAvailableException
     */
    public static function sendMail(Mailable $mail)
    {
        try {
            Mail::send($mail);
        } catch (\Exception $exception) {
            report($exception);
            throw new EmailServiceNotAvailableException($exception->getMessage());
        }
        return true;
    }
}
