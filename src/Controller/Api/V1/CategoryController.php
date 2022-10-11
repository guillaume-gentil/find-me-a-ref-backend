<?php

namespace App\Controller\Api\V1;

use App\Entity\Category;
use App\Repository\CategoryRepository;
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
class CategoryController extends AbstractController
{
    /**
     * Get a list of all the Categories
     * @Route("/categories", name="categories", methods={"GET"} )
     */
    public function getCategories(CategoryRepository $categoryRepository): JsonResponse
    {
        $categories = $categoryRepository->findAll();

        // response : return all Categories
        return $this->json(['categories' => $categories], Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Get category by id
     *
     * @Route("/categories/{id}", name="categories_by_id", methods={"GET"} ,requirements={"id"="\d+"})
     */
    public function getCategoryById(Category $category =null): JsonResponse
    {
        if(is_null($category)) {
            return $this->json(['error' => 'Category\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // response : return the Category
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
        // get the new data from the request (JSON)
        $json = $request->getContent();
        $category = $serializer->deserialize($json, Category::class, 'json');
        
        // initialize the property createdAt
        $category->setCreatedAt(new \DateTimeImmutable('now'));

        // check the Assert (Entity's constraints)
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

        // if all the data are OK => save item in DB
        $categoryRepository->add($category, true);

        // response : return the new Category object
        return $this->json($category, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Edit a category
     * 
     * @Route("/categories/{id}/edit", name="categories_edit", methods={"GET","PUT"}, requirements={"id"="\d+"})
     */
    public function edit(
        Category $category =null,
        Request $request,
        SerializerInterface $serializer,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator
    ): JsonResponse
    {
        // validate the Category ID sent in URL
        if(is_null($category)) {
            return $this->json(['error' => 'Category\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }

        // do action only if the HTTP method of the request is PUT (= the user request an update)
        if($request->isMethod('put')) {

            // get the new data from the request (JSON)
            $json = $request->getContent();
            $category = $serializer->deserialize($json, Category::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $category]);
            
            // update the property updatedAt
            $category->setUpdatedAt(new \DateTimeImmutable('now'));

            // check the Assert (Entity's constraints)
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
            
            // if all the data are OK => save changes in DB
            $doctrine
                ->getManager()
                ->flush()
                ;
        }

        // response : return the actual object ("GET") or the new object ("PUT")
        return $this->json($category, Response::HTTP_OK, [], [
            'groups' => 'games_collection'
        ]);
    }

    /**
     * Delete a category
     *
     * @Route("/categories/{id}", name="categories-delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(Category $category =null, CategoryRepository $categoryRepository): JsonResponse
    {
        // validate the Category ID sent in URL
        if(is_null($category)) {
            return $this->json(['error' => 'Category\'s ID not found !'], Response::HTTP_NOT_FOUND);
        }
        // Delete the Category
        $categoryRepository->remove($category, true);

        // response : return OK code without content
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

}
