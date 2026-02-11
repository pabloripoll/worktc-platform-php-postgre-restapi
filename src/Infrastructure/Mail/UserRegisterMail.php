<?php

namespace App\Infrastructure\Mail;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class UserRegisterMail
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromAddress = 'no-reply@example.com',
        private string $fromName = 'Your App'
    ) {
    }

    /**
     * Send the registration email to given user.
     *
     * @param array $payload  array with keys: subject, title, nickname, activation_code, message, cta_url, cta_text, email
     */
    public function send(array $payload = []): void
    {
        $subject = sprintf('Welcome to %s', $this->fromName);

        $email = (new TemplatedEmail())
            ->from(new Address($this->fromAddress, $this->fromName))
            ->to(new Address($payload['email']))
            ->subject($subject)
            ->htmlTemplate('emails/member/registration.html.twig')
            ->textTemplate('emails/member/registration.txt.twig')
            ->context([
                'payload' => $payload,
            ]);

        $this->mailer->send($email);
    }
}
