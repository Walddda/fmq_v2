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
            ->findBy(array(), array('name' => 'ASC'));

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
