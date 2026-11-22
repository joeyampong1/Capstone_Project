<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class BrevoTransport extends AbstractTransport
{
    public function __construct(
        protected string $apiKey
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var Email $email */
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $from = $email->getFrom()[0] ?? null;
        if (!$from instanceof Address) {
            $from = new Address(
                config('mail.from.address'),
                config('mail.from.name')
            );
        }

        $to = [];
        foreach ($email->getTo() as $recipient) {
            $to[] = array_filter([
                'email' => $recipient->getAddress(),
                'name' => $recipient->getName() ?: null,
            ]);
        }

        $payload = [
            'sender' => array_filter([
                'email' => $from->getAddress(),
                'name' => $from->getName() ?: null,
            ]),
            'to' => $to,
            'subject' => (string) $email->getSubject(),
        ];

        if ($email->getHtmlBody()) {
            $payload['htmlContent'] = $email->getHtmlBody();
        }

        if ($email->getTextBody()) {
            $payload['textContent'] = $email->getTextBody();
        }

        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'accept' => 'application/json',
        ])->timeout(30)->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            throw new \RuntimeException('Brevo API error: ' . $response->body());
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
