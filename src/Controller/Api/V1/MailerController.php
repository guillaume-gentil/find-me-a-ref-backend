<?php

namespace App\Controller\Api\V1;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/v1", name="api_v1_")
 */
class MailerController extends AbstractController
{
    /**
     * @Route("/mailer", name="send_mail")
     */
    public function sendEmail(MailerInterface $mailer): void
    {
        /**
         * how to use Mailer :
         * https://symfony.com/doc/5.4/mailer.html#creating-sending-messages
         * https://symfony.com/doc/5.4/mailer.html#twig-html-css
         */

        $email = (new TemplatedEmail())
            ->from('findmearef@gmail.com')
            ->to('guillaumeg.dev@gmail.com')
            ->cc('arnaud.joguet@gmail.com')
            ->subject('Premier mail envoyé depuis le composant Mailer')
            ->text('Hello dev !')
            ->htmlTemplate('api/v1/mailer/signup.html.twig')
            ->context([
                'user' => [
                    "id" => 1,
                    "firstname" => "Guillaume",
                    "lastname" => "Gentil",
                    "email" => "guillaumeg.dev@gmail.com",
                    "roles" => ["ROLE_REFEREE"]
                ],                
            ]);

        $mailer->send($email);
    }
}
