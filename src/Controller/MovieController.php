<?php

namespace App\Controller;

// ...

use App\Entity\Movie;
use App\Entity\Quote;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends AbstractController
{
    /**
     * @Route("/del/{ent}", name="delete")
     */
    public function ajaxDeleteItemAction(string $ent, HttpFoundationRequest $request)
    {

        if ($request->isXmlHttpRequest()) {
            // dd($ent);
            $id = $request->get('entityId');
            $em = $this->getDoctrine()->getManager();
            switch ($ent) {
                case 'Movie':
                    $evenement = $em->getRepository(Movie::class)->find($id);
                    break;

                case 'Quote':
                    $evenement = $em->getRepository(Quote::class)->find($id);
                    break;
            }

            $em->remove($evenement);
            $em->flush();


            return new JsonResponse('good');
        }
    }

    /**
     * @Route("/", name="home")
     */
    public function showAll(): Response
    {
        $movie = $this->getDoctrine()
            ->getRepository(Movie::class)
            // ->findAll();
            ->findBy(array(), array('name' => 'DESC'));

        return $this->render('index.html.twig', [
            'data' => $movie,
        ]);
        // return new Response($res);
    }
    /**
     * @Route("/search/{term}", name="search", defaults={"term"=null})
     */
    public function showSearch($term): Response
    {
        $conn = array(
            'driver' => 'pdo_sqlite',
            'path' => $_ENV['DATABASE_URL'],
        );
        $entityManager = $this->getDoctrine()->getManager();
        $connEM = \Doctrine\DBAL\DriverManager::getConnection($conn);
        if ($term) {
            // $movie = Doctrine::getTable('User')->createQuery('u')
            //     ->where('column_name3 LIKE ?', '%search_key%')
            //     ->execute();
            $queryBuilder = new QueryBuilder($connEM);
            $queryBuilder->select('*    ')
                ->from('Movie', 'o')
                ->where('o.name LIKE :name')
                ->setParameter('name', '%' . $term . '%');
            // $queryBuilder->execute();

            $movie = $queryBuilder->execute();
            dd($movie);
        } else {
            $movie = $this->getDoctrine()
                ->getRepository(Movie::class)
                // ->findAll();
                ->findBy(array(), array('name' => 'DESC'));
        }


        return $this->render('search.html.twig', [
            'data' => $movie,
        ]);
        // return new Response($res);
    }
    /**
     * @Route("/movie", name="create_movie")
     */
    public function createMovie(): Response
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createProduct(EntityManagerInterface $entityManager)

        // $movie = $this->getDoctrine()
        //     ->getRepository(Movie::class)
        //     ->find(1);
        $entityManager = $this->getDoctrine()->getManager();

        for ($j = 0; $j < 5; $j++) {
            # code...
            $movie = new Movie();
            $movie->setName($j . 'movie' . $j);
            $movie->setReleaseYear(1234);

            $entityManager->persist($movie);
            for ($i = 0; $i < 3; $i++) {
                $quote = new Quote();
                $quote->setText($i . 'quote' . $j);
                $quote->setCharacter('char' . $j . $i);
                $quote->setMovie($movie);

                $entityManager->persist($quote);
            }
        }

        // tell Doctrine you want to (eventually) save the Product (no queries yet)

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response(
            'Saved new Movie with id: ' . $movie->getId()
                . ' and new Quote with id: ' . $quote->getId()
        );
    }

    /**
     * @Route("/movie/{id}", name="product_show")
     */
    public function show(int $id): Response
    {
        $movie = $this->getDoctrine()
            ->getRepository(Movie::class)
            ->find($id);

        if (!$movie) {
            throw $this->createNotFoundException(
                'No movie found for id ' . $id
            );
        }

        return new Response('Check out this great movie: ' . $movie->getName());

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }

    /** 
     * @Route("/edit/{ent}/{id}", name="edit")
     */
    public function edit(string $ent, int $id): Response
    {
        switch ($ent) {
            case 'Movie':
                $element = $this->getDoctrine()->getRepository(Movie::class)->find($id);
                break;

            case 'Quote':
                $element = $this->getDoctrine()->getRepository(Quote::class)->find($id);
                break;
        }

        return $this->render('edit.html.twig', [
            'elem' => $element,
            'ent' => $ent,
        ]);
    }


    /** 
     * @Route("/editSub", name="editSubmit")
     */
    public function editSubmit(HttpFoundationRequest $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ent = $request->request->get('ent');
        $id = $request->request->get('id');
        switch ($ent) {
            case 'Movie':
                $element = $this->getDoctrine()->getRepository(Movie::class)->find($id);

                $element->setName($request->request->get('MovieName'));
                $element->setReleaseYear($request->request->get('MovieYear'));
                $entityManager->persist($element);
                $entityManager->flush();
                break;

            case 'Quote':
                $element = $this->getDoctrine()->getRepository(Quote::class)->find($id);

                $element->setText($request->request->get('QuoteText'));
                $element->setCharacter($request->request->get('QuoteCharacter'));
                $entityManager->persist($element);
                $entityManager->flush();
                break;
        }

        return new Response('success');
    }

    /** 
     * @Route("/add", name="add")
     */
    public function showAdd(): Response
    {
        $movies = $this->getDoctrine()->getRepository(Movie::class)->findAll();

        return $this->render('add.html.twig', [
            'movies' => $movies,
        ]);
    }

    /** 
     * @Route("/addSub", name="addSubmit")
     */
    public function addSubmit(HttpFoundationRequest $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ent = $request->request->get('ent');
        $id = $request->request->get('movieId');
        switch ($ent) {
            case 'Movie':
                $element = new Movie;

                $element->setName($request->request->get('MovieName'));
                $element->setReleaseYear($request->request->get('MovieYear'));
                break;

            case 'Quote':
                $element = new Quote;

                $element->setText($request->request->get('QuoteText'));
                $element->setCharacter($request->request->get('QuoteCharacter'));
                $element->setMovie($this->getDoctrine()->getRepository(Movie::class)->find($id));
                $entityManager->persist($element);
                $entityManager->flush();
                break;
        }
        $entityManager->persist($element);
        $entityManager->flush();

        return new Response('successAdddd');
    }
}
