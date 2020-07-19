<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Joke;

class JokeController extends AbstractController
{
    /**
     * @Route("/joke", name="joke", methods="GET")
     */
    public function index()
    {
        return $this->render('joke/index.html.twig', [
            'controller_name' => 'JokeController',
        ]);
    }

    /**
     * @Route("/joke/random", name="joke_random", methods="GET")
     */
    public function random()
    {
        $randomJoke = $this->getDoctrine()->getRepository(Joke::class)->random();

        return $this->json([
          'data' => $randomJoke
        ]);
    }

    /**
     * @Route("/joke/{id}", name="joke_show", methods="GET")
     */
    public function show(int $id)
    {
        $joke = $this->getDoctrine()
          ->getRepository(Joke::class)
          ->find($id);

        return $this->json([
          'data' => $joke
        ]);
    }

    /**
     * @Route("/joke", name="joke_new", methods="POST")
     */
    public function new(array $data)
    {
        $joke = new Joke;
        $joke->setContent($request->query->get('content'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($joke);
        $em->flush();

        return $this->json([
          'data' => $data
        ], 200);
    }

    /**
     * @Route("/joke/{id}", name="joke_edit", methods="POST")
     */
    public function edit(Request $request, int $id)
    {
        $joke = $joke = $this->getDoctrine()
          ->getRepository(Joke::class)
          ->find($id);
        $joke->setContent($request->query->get('content'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($joke);
        $em->flush();

        return $this->json([
          'data' => $joke
        ], 200);
    }

    /**
     * @Route("/joke/{id}", name="joke_delete", methods="DELETE")
     */
    public function delete(int $id)
    {
        $joke = $joke = $this->getDoctrine()
          ->getRepository(Joke::class)
          ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($joke);
        $em->flush();

        return $this->json([
            'deleted' => $id
        ]);
    }
}
