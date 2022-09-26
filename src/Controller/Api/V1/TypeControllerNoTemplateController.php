<?php

namespace App\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TypeControllerNoTemplateController extends AbstractController
{
    /**
     * @Route("/api/v1/type/controller/no/template", name="app_api_v1_type_controller_no_template")
     */
    public function index(): Response
    {
        return $this->render('api/v1/type_controller_no_template/index.html.twig', [
            'controller_name' => 'TypeControllerNoTemplateController',
        ]);
    }
}
