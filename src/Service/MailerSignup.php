<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;


class MailerSignup
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send an email to the User for check his Email
     */
    public function sendEmailSignup($user): void
    {
        /**
         * how to use Mailer :
         * https://symfony.com/doc/5.4/mailer.html#creating-sending-messages
         * https://symfony.com/doc/5.4/mailer.html#twig-html-css
         */
        
        // creation of email
        $email = (new TemplatedEmail())
            ->from('no-reply@rollerhockey.fr')
            ->to($user->getUserIdentifier())
            ->subject("Bienvenue sur Find Me A Ref! La solution pour l'arbitrage du Roller Hockey")
            ->htmlTemplate('api/v1/mailer/signup.html.twig')
            ->context(['user' => $user])
        ;
        
        // send email
        $this->mailer->send($email);
    }
}