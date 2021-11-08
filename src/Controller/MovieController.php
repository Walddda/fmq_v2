<?php

namespace App\Controller;

// ...

use App\Entity\Movie;
use App\Entity\Quote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends AbstractController
{
    /**
     * @Route("/movies", name="movies")
     */
    public function showAll(): Response
    {
        $res = "<h1>Results</h1><ul>";
        $movie = $this->getDoctrine()
            ->getRepository(Movie::class)
            // ->findAll();
            ->findBy(array(), array('name' => 'DESC'));

        foreach ($movie as $m) {
            $res .= "<li>" . $m->getName() . "</li>";
            $quotes = $m->getQuotes();
            foreach ($quotes as $q) {
                $res .= "<li> ----" . $q->getText() . "</li>";
            }
        }



        return new Response($res);
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
        $movie = new Movie();
        $movie->setName('abcdef');
        $movie->setReleaseYear(1234);

        $quote = new Quote();
        $quote->setText('xxbe or not to be3');
        $quote->setCharacter('xxKurt3');

        $quote->setMovie($movie);


        $entityManager = $this->getDoctrine()->getManager();

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($movie);
        $entityManager->persist($quote);

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
}
