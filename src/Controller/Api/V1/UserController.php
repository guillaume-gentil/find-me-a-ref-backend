<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
}
