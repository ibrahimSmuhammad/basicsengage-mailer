<?php

namespace Tests;

use Ibrahim\BasicsEngageMailer\BasicsEngageTransport;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\SentMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Facade;

class BasicsEngageTransportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Boot Laravel facades for test
        Facade::setFacadeApplication(new \Illuminate\Container\Container());
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_sends_email_payload_to_basicsengage_api()
    {
        Http::fake([
            'https://api.basicsengage.com/api/v0/dev/email/send' => Http::response(['success' => true], 200),
        ]);

        $transport = new BasicsEngageTransport('test-api-key');

        $email = (new Email())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Test Subject')
            ->text('This is a test email')
            ->html('<p>This is a test email</p>');

        $sentMessage = new SentMessage($email);

        $transport->send($sentMessage);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.basicsengage.com/api/v0/dev/email/send'
                && $request['to'][0] === 'to@example.com'
                && $request['subject'] === 'Test Subject';
        });
    }

    /** @test */
    public function it_logs_error_when_api_fails()
    {
        Http::fake([
            'https://api.basicsengage.com/api/v0/dev/email/send' => Http::response('Error', 500),
        ]);

        $transport = new BasicsEngageTransport('test-api-key');

        $email = (new Email())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Fail Test')
            ->text('Will fail');

        $sentMessage = new SentMessage($email);

        $this->expectException(\Illuminate\Http\Client\RequestException::class);

        $transport->send($sentMessage);
    }
}
