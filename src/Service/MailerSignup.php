<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;


class MailerSignup
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendEmailToValidateInscription($user): void
    {
        dump("Stop depuis le service");
        /**
         * how to use Mailer :
         * https://symfony.com/doc/5.4/mailer.html#creating-sending-messages
         * https://symfony.com/doc/5.4/mailer.html#twig-html-css
         */
        
        // création du mail
        $email = (new Email())
        ->from('findmearef@gmail.com')
        ->to('guillaumeg.dev@gmail.com')
        // ->cc('arnaud.joguet@gmail.com')
        ->subject('Mail envoyé depuis le service MailerSignup')
        ->html(
            $this->twig->render('api/v1/mailer/signup.html.twig', ['user' => $user]),
            'text/html'
            )
        ->context([
            'user' => [
                "id" => 100,
                "firstname" => "Guillaume",
                "lastname" => "Gentil",
                "email" => "guillaumeg.dev@gmail.com",
                "roles" => ["ROLE_REFEREE"]
            ],                
        ]);
        
        // envoie du mail
        $this->mailer->send($email);

        dd($user);
    }
}