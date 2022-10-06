<?php

namespace App\Controller\Api\V1;

use App\Entity\Type;
use App\Repository\TypeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @route("/api/v1", name="api_v1")
 */
class TypeController extends AbstractController
{
    /**
     * @Route("/types", name="types", methods={"GET"})
     */
    public function getTypes(TypeRepository $typeRepository): JsonResponse
    {
        $types = $typeRepository->findAll();

        return $this->json(['types' => $types], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * get type of game by id
     * 
     * @Route("/types/{id}", name="types_by_id", methods={"GET"} ,requirements={"id"="\d+"})
     */
    public function getTypeById(Type $type = null ): JsonResponse
    {
        if(is_null($type)) {
            return $this->json(['error' => 'Type\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($type, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Add new type of game
     *
     * @Route("/types", name="types_add", methods={"POST"})
     */
    public function add(
        Request $request,
        SerializerInterface $serializer,
        TypeRepository $typeRepository,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $json = $request->getContent();
        $type = $serializer->deserialize($json, Type::class, 'json');

        $errors = $validator->validate($type);
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
        $type->setCreatedAt(new \DateTimeImmutable('now'));
        $typeRepository->add($type, true);

        return $this->json($type, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Edit a type of game
     *
     * @Route("/types/{id}/edit", name="types_edit", methods={"GET","PUT"}, requirements={"id"="\d+"})
     */
    public function edit(
        Type $type= null,
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator
    ): JsonResponse
    {
        if(is_null($type)) {
            return $this->json(['error' => 'Type\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }
        /*
            {
                "name":"new name"
            }
         */
        if ($request->isMethod('put')) {

            $json = $request->getContent();
            $type = $serializer->deserialize($json, Type::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $type]);
            $type->setUpdatedAt(new \DateTimeImmutable('now'));

            $errors = $validator->validate($type);
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
            
            $manager = $doctrine->getManager();
            $manager->flush();
        }

        return $this->json($type, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Delete a type
     * @Route("/types/{id}", name="types_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     *
     * @return JsonResponse
     */
    public function delete(Type $type = null, TypeRepository $typeRepository): JsonResponse
    {
        if(is_null($type)) {
            return $this->json(['error' => 'Type\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        //TODO: check if it's necessary to control the user's ROLE (may be the lexik's component do it automatically)
        $user = $this->getUser();
        $userRole = $user->getRoles();
        if (in_array("ROLE_ADMIN", $userRole)) {

            $typeRepository->remove($type, true);
            return $this->json(null, Response::HTTP_NO_CONTENT); 
        } else {
            return $this->json(['you don\'t have the rights to do this action'], Response::HTTP_FORBIDDEN);
        }
    }

}
