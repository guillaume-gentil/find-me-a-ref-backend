<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\GeolocationManager;
use App\Service\MailerSignup;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/api/v1", name="api_v1_")
 */
class UserController extends AbstractController
{
    /**
     * Get a users list
     * @Route("/users", name="users_collection", methods={"GET"})
     */
    public function getUsersCollection(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        // response : return all Users
        return $this->json(['users' => $users], Response::HTTP_OK, [], [
            'groups' => 'users_collection'
        ]);
    }

    /**
     * Get user by Id
     * 
     * @Route("/users/{id}", name="user_by_id", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getUserById(User $user = null): JsonResponse
    {
        // validate the User ID sent in URL
        if(is_null($user)) {
            return $this->json(['error' => 'User\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // response : return the User
        return $this->json($user, Response::HTTP_OK, [], [
            'groups' => 'users_collection'
        ]);
    }

    /**
     * Add new user
     * @Route("/users", name="users_add", methods={"POST"})
     */
    public function addUser(
        Request $request,
        SerializerInterface $serializer,
        UserRepository $userRepository,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        GeolocationManager $geolocationManager,
        MailerSignup $mailer
        ): JsonResponse
    {
        // get the new data from the request (JSON)
        $json = $request->getContent();
        $user = $serializer->deserialize($json, User::class, 'json');

        // generate a signup token automatically for validation
        $user->setSignUpToken($this->generateSignUpToken());
        
        // setup TEMPORARY role until the user validation (via email)
        $user->setRoles(["ROLE_TEMPORARY"]);

        // hash the user password before save it in DB
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        
        if (!empty($user->getAddress())) {
            $user->setLatitude($geolocationManager->useGeocoder($user->getAddress(), $user->getZipCode(), 'lat'));
            $user->setLongitude($geolocationManager->useGeocoder($user->getAddress(), $user->getZipCode(), 'lng'));
        }
        
        // initialize the property createdAt
        $user->setCreatedAt(new \DateTimeImmutable('now'));
        
        // an admin could be create only by another admin
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Veuillez contacter l\'administrateur du site'] , Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // check the Assert (Entity's constraints)
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $cleanErrors = [];
            /**
             * @var ConstraintViolation $error
             */
            foreach($errors as $error) {
                $property = $error->getPropertyPath();
                $message = $error->getMessage();
                $cleanErrors[$property][] = $message;
            }
            return $this->json($cleanErrors , Response::HTTP_UNPROCESSABLE_ENTITY );
        }
        // if all the data are OK => save item in DB
        $userRepository->add($user, true);
        
        // send validation email to the User automatically
        $mailer->sendEmailSignup($user);

        // response : return the new User object 
        return $this->json($user, Response::HTTP_OK, [], [
            'groups' => 'users_collection'
        ]);
    }

    /**
     * Method to validate User email after signup
     *
     *@Route("/users/check-account/{signUpToken}", name="users_check_account")
     */
    public function checkAccount($signUpToken, UserRepository $userRepository)
    {
        // retrieve a user via his signup token
        $user = $userRepository->findOneBy(["signUpToken" => $signUpToken]);
        
        if($user) {

            $user->setSignUpToken('validate');
            $user->setRoles(["ROLE_REFEREE"]);
            
            dd($user);
            // if user is find and validate return to findMeARef website with ok statuts
            return $this->redirect('http://localhost:8080/authRedirect', Response::HTTP_OK);
        } else {
            return $this->redirect('http://localhost:8080/authRedirect', Response::HTTP_BAD_REQUEST);
        }
        
    }

    //TODO: the two methods : editForAdmin and edit should be refactored
    /**
     * Edit User as admin
     * @Route("/users/{id}/edit", name="users_edit_for_admin", methods={"GET", "PUT"}, requirements={"id"="\d+"})
     */
    public function editForAdmin(
        User $user,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        GeolocationManager $geolocationManager,
        ManagerRegistry $doctrine
    ): JsonResponse
    {
        // get current password
        $previousPassword = $user->getPassword();

        // get the current address from DB
        /**
         * @var User $user
         */
        $previousAddress = $user->getAddress();

        //? source : how to check HTTP method : https://stackoverflow.com/questions/22852305/how-can-i-check-if-request-was-a-post-or-get-request-in-symfony2-or-symfony3
        if ($request->isMethod('put')) {
            // get the new data from the request (JSON)
            $json = $request->getContent();

            // populate current object with new values
            $user = $serializer->deserialize($json, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

            $user->setUpdatedAt(new \DateTimeImmutable('now'));

            // retreive from API the Geocode values only if the address change
            if ($user->getAddress() != $previousAddress) {
                $user->setLatitude($geolocationManager->useGeocoder($user->getAddress(), $user->getZipCode(), 'lat'));
                $user->setLongitude($geolocationManager->useGeocoder($user->getAddress(), $user->getZipCode(), 'lng'));
            }
            
            // check modification of password
            if ($user->getPassword() != $previousPassword) {

                $errors = $validator->validate($user, null, ['users_new_password']);
                if (count($errors) > 0) {
                    $cleanErrors = [];
                    /**
                     * @var ConstraintViolation $error
                     */
                    foreach ($errors as $error) {
                        $property = $error->getPropertyPath();
                        $message = $error->getMessage();
                        $cleanErrors[$property][] = $message;
                    }
                    return $this->json($cleanErrors , Response::HTTP_UNPROCESSABLE_ENTITY );
                }
                // We need to hash password after use constraint violations
                $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            } else {
                // If user doesn't modify his password
                
                // check the Assert (Entity's constraints)
                $errors = $validator->validate($user);
                if (count($errors) > 0) {
                    $cleanErrors = [];
                    /**
                     * @var ConstraintViolation $error
                     */
                    foreach ($errors as $error) {
                        $property = $error->getPropertyPath();
                        $message = $error->getMessage();
                        $cleanErrors[$property][] = $message;
                    }
                    return $this->json($cleanErrors , Response::HTTP_UNPROCESSABLE_ENTITY );
                }
            }
            
            // if all data are OK => save changes in DB
            $doctrine
                ->getManager()
                ->flush()
                ;
        }

        // response : return the actual object ("GET") or the new object ("PUT")
        return $this->json($user, Response::HTTP_OK, [], [
            'groups' => 'users_collection'
        ]);
    }

    /**
     * Edit User
     * @Route("/users/edit", name="users_edit", methods={"GET", "PUT"})
     */
    public function edit(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        GeolocationManager $geolocationManager,
        ManagerRegistry $doctrine
    ): JsonResponse
    {
        // Get the user (from token)
        $user = $this->getUser();
        // Stock previous password for check modification
        $previousPassword = $user->getPassword();

        // get the current address from DB
        /**
         * @var User $user
         */
        $previousAddress = $user->getAddress();

        //? source : how to check HTTP method : https://stackoverflow.com/questions/22852305/how-can-i-check-if-request-was-a-post-or-get-request-in-symfony2-or-symfony3
        if ($request->isMethod('put')) {

            // get the new data from the request (JSON)
            $json = $request->getContent();

            $user = $serializer->deserialize($json, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

            // update the property updatedAt
            $user->setUpdatedAt(new \DateTimeImmutable('now'));
            
            // retreive from API the Geocode values only if the address change
            if ($user->getAddress() != $previousAddress) {
                $user->setLatitude($geolocationManager->useGeocoder($user->getAddress(), $user->getZipCode(), 'lat'));
                $user->setLongitude($geolocationManager->useGeocoder($user->getAddress(), $user->getZipCode(), 'lng'));
            }
            
            // check modification of password
            if ($user->getPassword() != $previousPassword) {

                $errors = $validator->validate($user, null, ['users_new_password']);
                if (count($errors) > 0) {
                    $cleanErrors = [];
                    /**
                     * @var ConstraintViolation $error
                     */
                    foreach ($errors as $error) {
                        $property = $error->getPropertyPath();
                        $message = $error->getMessage();
                        $cleanErrors[$property][] = $message;
                    }
                    return $this->json($cleanErrors , Response::HTTP_UNPROCESSABLE_ENTITY );
                }
                // We need to hash password after use constraint violations
                $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            } else {
                // If user don't modify his password
                
                // check the Assert (Entity's constraints)
                $errors = $validator->validate($user);
                if (count($errors) > 0) {
                    $cleanErrors = [];
                    /**
                     * @var ConstraintViolation $error
                     */
                    foreach ($errors as $error) {
                        $property = $error->getPropertyPath();
                        $message = $error->getMessage();
                        $cleanErrors[$property][] = $message;
                    }
                    return $this->json($cleanErrors , Response::HTTP_UNPROCESSABLE_ENTITY );
                }
            }
            
            // if all the data are OK => save changes in DB
            $doctrine
                ->getManager()
                ->flush()
                ;
        }

        // response : return the actual object ("GET") or the new object ("PUT")
        return $this->json($user, Response::HTTP_OK, [], [
            'groups' => 'users_collection'
        ]);
    }

    /**
     * Delete a user
     * 
     * @Route("/users/{id}", name="users_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(User $user = null, UserRepository $userRepository): JsonResponse
    {
        // validate the User ID sent in URL
        if(is_null($user)) {
            return $this->json(['error' => 'User\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // Kill the User
        $userRepository->remove($user, true);
        
        // response : return OK code without content
        return $this->json(null, Response::HTTP_NO_CONTENT); 

    }

    /**
     * Generating token for signup account
     * 
     * https://stackoverflow.com/questions/50877915/how-to-generate-a-token-in-symfony-3-4
     * https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Util/TokenGenerator.php
     * @return void
     */
    private function generateSignUpToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
