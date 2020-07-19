<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
    * @Route("/joke/list", name="joke_list", methods="GET")
    */
    public function list(Request $request)
    {
        $jokesQuery = $this->getDoctrine()
                        ->getRepository(Joke::class)
                        ->createQueryBuilder('j');

        if($request->query->has('search')) {
            $jokesQuery = $jokesQuery->where('j.content LIKE :content')
                                     ->setParameter('content', '%'.$request->query->get('search').'%');
        }

        $limit = (int) $request->query->get('per-page', 10);
        $page = (int) $request->query->get('page', 1);
        $offset = (int) ($limit * ($page - 1));

        $jokesQuery = $jokesQuery->setMaxResults($limit)
                                 ->setFirstResult($offset)
                                 ->getQuery();

        $paginator = new Paginator($jokesQuery);
        $count = count($paginator);

        $result = $jokesQuery->getResult();

        return $this->json([
            'data' => [
                'count' => $count,
                'page' => $page,
                'total_pages' => ceil($count/$limit),
                'jokes' => $paginator
            ]
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
