<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $messageBody;
    public ?int $alerteId;

    public function __construct(string $messageBody, ?int $alerteId = null)
    {
        $this->messageBody = $messageBody;
        $this->alerteId = $alerteId;
    }

    public function build(): self
    {
        return $this->subject('Alerte Citoyen - Notification')
            ->view('emails.alert_notification')
            ->with([
                'messageBody' => $this->messageBody,
                'alerteId' => $this->alerteId,
            ]);
    }
}


