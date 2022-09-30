<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @route("/api/v1", name="api_v1")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/users", name="users", methods={"GET"})
     */
    public function getUsers(UserRepository $userRepository): JsonResponse
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
    public function getUser(User $user = null): JsonResponse
    {
        if(is_null($user)) {
            return $this->json(['error' => 'User\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($user, Response::HTTP_OK, [], [
            'groups' => 'users_collection'
        ]);
    }
}
