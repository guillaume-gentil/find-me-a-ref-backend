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
 * @route("/api/v1", name="api_v1")
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

        return $this->json(['users' => $users], Response::HTTP_OK, [], [
            'groups' => 'users_collection'
        ]);
    }

    /**
     * Get user by Id
     * @Route("/users/{id}", name="user_by_id", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getUserById(User $user = null): JsonResponse
    {
        if(is_null($user)) {
            return $this->json(['error' => 'User\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

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
        $json = $request->getContent();
        $user = $serializer->deserialize($json, User::class, 'json');
        //DEV: password for developpement : 'admin'
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $user->setCreatedAt(new \DateTimeImmutable('now'));

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
        
        //dd($user);
        $userRepository->add($user, true);

        return $this->json($user, Response::HTTP_OK, [], [
            'groups' => 'users_collection'
        ]);
    }


    //TODO: the two methods : editForAdmin and edit should be refactored
    /**
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
        // on récupère son mot de passe avant modification éventuelle
        $previousPassword = $user->getPassword();

        // on récupère l'adresse avant modification éventuelle
        /**
         * @var User $user
         */
        $previousAddress = $user->getAddress();
        //dd($previousAddress);

        //? source : how to check HTTP method : https://stackoverflow.com/questions/22852305/how-can-i-check-if-request-was-a-post-or-get-request-in-symfony2-or-symfony3
        if ($request->isMethod('put')) {
            //! requête PUT
            //* 2. récupère les nouvelles données de l'utilisateurs (transmises via le formulaire Front)
            $json = $request->getContent();

            //* on remplce les anciennes données par les nouvelles
            $user = $serializer->deserialize($json, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

            //* vérification du mot de passe
            if ($user->getPassword() != $previousPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
            }

            //* set les valeurs non envoyée (updatedAt, longitudes, latitudes)
            $user->setUpdatedAt(new \DateTimeImmutable('now'));

            if ($user->getAddress() != $previousAddress) {
                $user->setLatitude($geolocationManager->useGeocoder($user->getAddress(), 'lat'));
                $user->setLongitude($geolocationManager->useGeocoder($user->getAddress(), 'lng'));
            }

            //* vérification des erreurs
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
            
            //* puis flush()
            $manager = $doctrine->getManager();
            $manager->flush();
        }

        //* puis envoie de la réponse
        return $this->json($user, Response::HTTP_OK, [], [
            'groups' => 'users_collection'
        ]);
    }

    /**
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
        // On récupère l'utilisateur connecté
        $user = $this->getUser();
        // on récupère son mot de passe avant modification éventuelle
        $previousPassword = $user->getPassword();

        // on récupère l'adresse avant modification éventuelle
        /**
         * @var User $user
         */
        $previousAddress = $user->getAddress();
        //dd($previousAddress);

        //? source : how to check HTTP method : https://stackoverflow.com/questions/22852305/how-can-i-check-if-request-was-a-post-or-get-request-in-symfony2-or-symfony3
        if ($request->isMethod('put')) {
            //! requête PUT
            //* 2. récupère les nouvelles données de l'utilisateurs (transmises via le formulaire Front)
            $json = $request->getContent();

            //* on remplce les anciennes données par les nouvelles
            $user = $serializer->deserialize($json, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

            //* vérification du mot de passe
            if ($user->getPassword() != $previousPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
            }

            //* set les valeurs non envoyée (updatedAt, longitudes, latitudes)
            $user->setUpdatedAt(new \DateTimeImmutable('now'));

            if ($user->getAddress() != $previousAddress) {
                $user->setLatitude($geolocationManager->useGeocoder($user->getAddress(), 'lat'));
                $user->setLongitude($geolocationManager->useGeocoder($user->getAddress(), 'lng'));
            }

            //* vérification des erreurs
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
            
            //* puis flush()
            $manager = $doctrine->getManager();
            $manager->flush();
        }

        //* puis envoie de la réponse
        return $this->json($user, Response::HTTP_OK, [], [
            'groups' => 'users_collection'
        ]);
    }

    /**
     * Delete a user
     * @Route("/users/{id}", name="users_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(User $user = null, UserRepository $userRepository)
    {
        // manage 404 error
        if(is_null($user)) {
            return $this->json(['error' => 'User\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        $userAdmin = $this->getUser();
        $userRole = $userAdmin->getRoles();

        //TODO: check if it's necessary to control the user's ROLE (may be the lexik's component do it automatically) 
        if (in_array("ROLE_ADMIN", $userRole)) {

            $userRepository->remove($user, true);
            return $this->json(null, Response::HTTP_NO_CONTENT); 
        } else {
            return $this->json(['you don\'t have the rights to do this action'], Response::HTTP_FORBIDDEN);
        }
    }
}
