<?php

namespace Ibrahim\BasicsEngageMail\Mail\Transports;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\HttpClient\HttpClient;

class BasicsEngageTransport implements TransportInterface
{
    protected HttpClientInterface $client;
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct(array $config = [])
    {
        $this->client = $config['client'] ?? HttpClient::create();
        $this->apiUrl = $config['host'] ?? env('BASICSENGAGE_API_URL', 'https://api.basicsengage.com/api/v0/dev/email/send');
        $this->apiKey = $config['api_key'] ?? env('BASICSENGAGE_API_KEY');
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(RawMessage $message, ?Envelope $envelope = null): SentMessage
    {
        if (!$message instanceof Email) {
            throw new \InvalidArgumentException('Only Email messages are supported.');
        }
        $payload = [
            'from' => $message->getFrom()[0]->getAddress() ?? null,
            //'to' => array_map(fn($a) => $a->getAddress(), $message->getTo() ?? []),
            'to' => $message->getTo()[0]->getAddress() ?? null,
            'cc' => array_map(fn($a) => $a->getAddress(), $message->getCc() ?? []),
            'bcc' => array_map(fn($a) => $a->getAddress(), $message->getBcc() ?? []),
            'reply_to' => array_map(fn($a) => $a->getAddress(), $message->getReplyTo() ?? []),
            'subject' => $message->getSubject(),
            'html' => $message->getHtmlBody(),
            'text' => $message->getTextBody(),
        ];
        $response = $this->client->request('POST', $this->apiUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);
        //    dd($response, $response->getStatusCode(), $response->getContent());
        return new SentMessage($message, $envelope);
    }

    public function __toString(): string
    {
        return 'basicsengage';
    }
}