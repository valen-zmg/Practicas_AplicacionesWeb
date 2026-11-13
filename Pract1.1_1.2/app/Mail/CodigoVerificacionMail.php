<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CodigoVerificacionMail extends Mailable
{
    public function __construct(public User $usuario, public string $codigo)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Código de verificación de acceso');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.codigo');
    }
}
