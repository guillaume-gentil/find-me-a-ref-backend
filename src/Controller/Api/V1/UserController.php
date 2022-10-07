<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\GeolocationManager;
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
 * @Route("/api/v1", name="api_v1")
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
        UserPasswordHasherInterface $passwordHasher
        ): JsonResponse
    {
        // get the new data from the request (JSON)
        $json = $request->getContent();
        $user = $serializer->deserialize($json, User::class, 'json');

        // hash the user password before save it in DB
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

        // initialize the property createdAt
        $user->setCreatedAt(new \DateTimeImmutable('now'));

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

        // response : return the new User object 
        return $this->json($user, Response::HTTP_OK, [], [
            'groups' => 'users_collection'
        ]);
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

            // hash the user password before save it in DB only if it's changed
            if ($user->getPassword() != $previousPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
            }

            $user->setUpdatedAt(new \DateTimeImmutable('now'));

            // retreive from API the Geocode values only if the address change
            if ($user->getAddress() != $previousAddress) {
                $user->setLatitude($geolocationManager->useGeocoder($user->getAddress(), 'lat'));
                $user->setLongitude($geolocationManager->useGeocoder($user->getAddress(), 'lng'));
            }

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
                $user->setLatitude($geolocationManager->useGeocoder($user->getAddress(), 'lat'));
                $user->setLongitude($geolocationManager->useGeocoder($user->getAddress(), 'lng'));
            }
            
            // check modification of password
            if ($user->getPassword() != $previousPassword) {

                //TODO: urgent vérifier la logique de mise à jour d'un mot de passe (ça fonctionne mal !)
                $userPassword = $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

                // check the Assert (Entity's constraints) using group
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
                // TODO: urgent vérifier la logique de mise à jour d'un mot de passe (ça fonctionne mal !)
                $user->setPassword($userPassword);

            } else {
                // If user don't modif his password
                
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
}
