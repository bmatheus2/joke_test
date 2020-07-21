<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{ Request, Response };
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Entity\Joke;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   title="Joke API",
 *   version="0.0.1"
 * )
 */

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
    * @OA\Get(
    *     path="/joke/list",
    *     @OA\Response(response="200", description="Returns a joke object by id"),
    *     @OA\Parameter(
    *         name="search",
    *         in="query",
    *         description="Search string for querying jokes",
    *         required=false,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="page",
    *         in="query",
    *         description="Query results page number",
    *         required=false,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\Parameter(
    *         name="per-page",
    *         in="query",
    *         description="Number of jokes returned per page",
    *         required=false,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     )
    *)
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
                                 ->orderBy('j.id', 'DESC')
                                 ->getQuery();

        $paginator = new Paginator($jokesQuery);
        $count = count($paginator);

        $result = $jokesQuery->getResult();

        return $this->json([
            'count' => $count,
            'page' => $page,
            'total_pages' => ceil($count/$limit),
            'jokes' => $paginator
        ]);
    }

    /**
    * @Route("/joke/random", name="joke_random", methods="GET")
    * @OA\Get(
    *     path="/joke/random",
    *     @OA\Response(response="200", description="Returns a random joke object")
    * )
    */
    public function random()
    {
        $joke = $this->getDoctrine()->getRepository(Joke::class)->random();
        return $this->json($joke);
    }

    /**
    * @Route("/joke/{id}", name="joke_show", methods="GET")
    * @OA\Get(
    *     path="/joke/{id}",
    *     @OA\Response(response="200", description="Returns a joke object by id"),
    *     @OA\Response(response="404", description="Returns an array containing an error message"),
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="The id of the joke",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     )
    *)
    */
    public function show(int $id)
    {
        $joke = $this->getDoctrine()
                     ->getRepository(Joke::class)
                     ->find($id);
        if(!$joke) {
            return $this->json(['errors' => "Joke ID: {$id} not found"], Response::HTTP_NOT_FOUND);
        }

        return $this->json($joke);
    }

    /**
    * @Route("/joke", name="joke_new", methods="POST")
    * @OA\Post(
    *     path="/joke",
    *     @OA\Response(response="200", description="Creates a new joke object"),
    *     @OA\Response(response="400", description="Returns an array containing an error message"),
    *     @OA\Parameter(
    *         name="content",
    *         in="query",
    *         description="Content for new joke",
    *         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     )
    * )
    */
    public function new(Request $request, ValidatorInterface $validator)
    {
        $content = $this->getDataByKey($request, 'content');

        $joke = new Joke;
        $joke->setContent($content);

        $errors = $validator->validate($joke);;
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json(['errors' => $errorsString], Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($joke);
        $em->flush();

        return $this->json($joke, 200);
    }

    /**
    * @Route("/joke/{id}", name="joke_edit", methods="POST")
    * @OA\Post(
    *     path="/joke/{id}",
    *     @OA\Response(response="200", description="Creates a new joke object"),
    *     @OA\Response(response="400", description="Returns an array containing an error message"),
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="The id of the joke",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *    ),
    *    @OA\Parameter(
    *         name="content",
    *         in="query",
    *         description="Content for new joke",
    *         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     )
    * )
    */
    public function edit(Request $request, ValidatorInterface $validator, int $id)
    {
        $joke = $this->getDoctrine()
                             ->getRepository(Joke::class)
                             ->find($id);

        $content = $this->getDataByKey($request, 'content');

        $joke->setContent($content);

        $errors = $validator->validate($joke);;
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json(['errors' => $errorsString], Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($joke);
        $em->flush();

        return $this->json($joke, 200);
    }

    /**
    * @Route("/joke/{id}", name="joke_delete", methods="DELETE")
    * @OA\Delete(
    *     path="/joke{id}",
    *     @OA\Response(response="200", description="Deletes joke by id, returns array with deleted id"),
    *     @OA\Response(response="404", description="Returns an array containing an error message"),
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="The id of the joke",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *    ),
    *    @OA\Parameter(
    *         name="content",
    *         in="query",
    *         description="Content for new joke",
    *         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     )
    * )
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

    private function getDataByKey(Request $request, $key = 'content')
    {
        $data = json_decode($request->getContent(), true);
        return (array_key_exists($key, $data) && $data[$key] != '') ? $data[$key] : null;
    }
}
