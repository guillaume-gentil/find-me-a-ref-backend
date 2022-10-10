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
 * @Route("/api/v1", name="api_v1")
 */
class TypeController extends AbstractController
{
    /**
     * Get a list of all the Types
     * @Route("/types", name="types", methods={"GET"})
     */
    public function getTypes(TypeRepository $typeRepository): JsonResponse
    {
        $types = $typeRepository->findAll();

        // response : return all Types
        return $this->json(['types' => $types], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Get type of game by id
     * 
     * @Route("/types/{id}", name="types_by_id", methods={"GET"} ,requirements={"id"="\d+"})
     */
    public function getTypeById(Type $type = null ): JsonResponse
    {
        // validate the Type ID sent in URL
        if(is_null($type)) {
            return $this->json(['error' => 'Type\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // response : return the Type
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
        // get the new data from the request (JSON)
        $json = $request->getContent();
        $type = $serializer->deserialize($json, Type::class, 'json');

        // initialize the property createdAt
        $type->setCreatedAt(new \DateTimeImmutable('now'));

        // check the Assert (Entity's constraints)
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

         // if all the data are OK => save item in DB
        $typeRepository->add($type, true);

        // response : return the new Type object 
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
        Type $type = null,
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator
    ): JsonResponse
    {
        // validate the Type ID sent in URL
        if(is_null($type)) {
            return $this->json(['error' => 'Type\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        if ($request->isMethod('put')) {
            // get the new data from the request (JSON)
            $json = $request->getContent();

            // populate current object with new values
            $type = $serializer->deserialize($json, Type::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $type]);
            
            $type->setUpdatedAt(new \DateTimeImmutable('now'));

            // check the Assert (Entity's constraints)
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
            
            // if all the data are OK => save changes in DB
            $doctrine
                ->getManager()
                ->flush()
                ;
        }

        // response : return the actual object ("GET") or the new object ("PUT")
        return $this->json($type, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Delete a type
     * @Route("/types/{id}", name="types_delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Type $type = null, TypeRepository $typeRepository): JsonResponse
    {
        // validate the Type ID sent in URL
        if(is_null($type)) {
            return $this->json(['error' => 'Type\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // delete the Type
        $typeRepository->remove($type, true);

        // response : return OK code without content
        return $this->json(null, Response::HTTP_NO_CONTENT); 
    }

}
