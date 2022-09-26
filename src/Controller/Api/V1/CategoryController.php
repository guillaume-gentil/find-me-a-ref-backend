<?php

namespace App\Controller\Api\V1;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
            'groups' => 'games_get_collection'
        ]);
    }

    /**
     * Get category by Id
     * @Route("/categories/{id}", name="categories_by_id", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getCategoryItem(Category $category = null): JsonResponse
    {
        if(is_null($category)) {
            return $this->json(['error' => 'Category not found !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($category, Response::HTTP_OK, [], [
            'groups' => 'categories_get_item'
        ]); 
    }
}
