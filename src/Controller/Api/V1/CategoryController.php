<?php

namespace App\Controller\Api\V1;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @route("/api/v1", name="api_v1")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/categories", name="categories", methods={"GET"} )
     */
    public function getCategories(CategoryRepository $categoryRepository): JsonResponse
    {
        $categories = $categoryRepository->findAll();

        return $this->json(['categories' => $categories], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * get category by id
     *
     * @Route("/categories/{id}", name="categories_by_id", methods={"GET"} ,requirements={"id"="\d+"})
     * @return JsonResponse
     */
    public function getCategoryById(Category $category =null): JsonResponse
    {
        if(is_null($category)) {
            return $this->json(['error' => 'Category\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($category, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Add new category
     * @Route("/categories", name="categories_add", methods={"POST"})
     * 
     *@return JsonResponse
     */
    public function add(
        Request $request,
        SerializerInterface $serializer,
        CategoryRepository $categoryRepository,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $json = $request->getContent();
        $category = $serializer->deserialize($json, Category::class, 'json');
        $category->setCreatedAt(new \DateTimeImmutable('now'));

        $errors = $validator->validate($category);
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

        $categoryRepository->add($category, true);

        return $this->json($category, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }



}
