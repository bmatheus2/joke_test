<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class JokeController extends AbstractController
{
    /**
     * @Route("/joke", name="joke")
     */
    public function index()
    {
        return $this->render('joke/index.html.twig', [
            'controller_name' => 'JokeController',
        ]);
    }

    /**
     * @Route("/joke/{id}", name="joke_show")
     */
    public function show(int $id)
    {
        return $this->json([
          'data' => $id
        ]);
    }

    /**
     * @Route("/joke/random", name="joke_random")
     */
    public function random()
    {
        return $this->json([
          'data' => $data
        ], 200);
    }

    /**
     * @Route("/joke/new", name="joke_new")
     */
    public function new(array $data)
    {
        return $this->json([
          'data' => $data
        ], 200);
    }

    /**
     * @Route("/joke/delete", name="joke_delete")
     */
    public function delete(int $id)
    {

    }
}
