<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/v1", name="api_v1_")
 */
class MailerController extends AbstractController
{
    /**
     * @Route("/mailer", name="send_mail")
     */
    public function sendEmailToValidateInscription(Request $request, MailerInterface $mailer): Response
    {
        // récupère les données JSON envoyée par le formulaire d'inscription (via le Front)
        $json = $request->getContent();
        // dd($json);

        // $user = $serializer->deserialize($json, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);
        /**
         * how to use Mailer :
         * https://symfony.com/doc/5.4/mailer.html#creating-sending-messages
         * https://symfony.com/doc/5.4/mailer.html#twig-html-css
         */

        // création du mail
        $email = (new TemplatedEmail())
            ->from('findmearef@gmail.com')
            ->to('guillaumeg.dev@gmail.com')
            // ->cc('arnaud.joguet@gmail.com')
            ->subject('Mail envoyé depuis le composant Mailer')
            ->htmlTemplate('api/v1/mailer/signup.html.twig')
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
        $mailer->send($email);

        return $this->redirectToRoute("api_v1_wait_for_email_validation");
    }

    /**
     * @Route("/mailer/twilight", name="wait_for_email_validation")
     */
    public function waitForEmailValidation()
    {
        dd("Email envoyé !");
    }
}
