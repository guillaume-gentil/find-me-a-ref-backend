<?php

//App\entity
class User
{
    /**
     * Undocumented variable
     *
     * @var [string]
     */
    private $token;

    /**
     * Undocumented variable
     *
     * @var [bool]
     */
    private $enabled;
}

//App\service
class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($email)
    {
        $email = (new TemplatedEmail())
        ->from('adresse a configurer')
        ->to(new Address($email))
        ->subject('inscription')
        ->htmlTemplate('emails/signup.html.twig')
    
        ->context([
            'token' => $token
        ])

        $this->mailer->send($email);
    }
}

//App\controller
class UserController
{
    public function userAdd(MailerService $mailer)
    {
        /*... */
        $user->setToken($this->generateToken());
        /*...*/
        $manager->flush;
       

        $this->mailer->sendEmail($user->getEmail(), $user->getToken());

    }

    /**
     * @Route("/confirm-mon-compte/{token}", name="confirm_account")
     *
     * 
     */
    public function confirmAccount(string $token)
    {
        $user = $userRepository->findOneBy(["token" => $token]);

        if($user) {
            $user->setToken(null);
            $user->setEnabled(true);

            $manager->persist($user);
            $manager->flush;

            $this->addFlash(type:"success", message:"Compte validé");
            return $this->redirectToRoute(route: "home");
        } else {
            $this->addFlash(type:"error", message:"Ce compte n'existe pas");
            return $this->redirectToRoute(route: "home");
        }

        return $this->json($token);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    private function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(length:32)), '+/', '-_'), '=');
    }
}

//template twig
?>

<a href="{{url('confirm_account', {"token": token}) }}">cliquer ici pour confirmer votre compte</a>