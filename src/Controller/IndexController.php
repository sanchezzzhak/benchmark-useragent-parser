<?php

namespace App\Controller;

use App\Entity\BenchmarkResult;
use App\Form\FormBenchmarkFinderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(FormBenchmarkFinderType::class, null, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);
        $repo = $this->getDoctrine()->getRepository(BenchmarkResult::class);


        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'form' => $form->createView(),
        ]);
    }
}
