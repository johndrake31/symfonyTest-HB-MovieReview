<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Entity\Impressions;
use App\Form\ImpressionsType;
use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface as EMI;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MovieController extends AbstractController
{
    /**
     * @return Response
     * @Route("/movie", name="movie")
     */
    public function index(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAll();
        return $this->render('movie/index.html.twig', [
            'movies' => $movies,
        ]);
    }

    /**
     * @return Response
     * 
     * @Route("/movie/show/{id}", name="show_movie")
     * 
     */
    public function show(Movie $movie, Request $req, EMI $emi): Response
    {
        $user = $movie->getUser();
        $username = $user->getUsername();

        // SHOW All Impressions in twig-loop.
        $impressions = $movie->getImpressions();

        //CREATE a new impression obj.
        $impress = new Impressions();

        //CREATE/EDIT form
        $form = $this->createForm(ImpressionsType::class, $impress);
        $form->handleRequest($req);


        if ($form->isSubmitted() && $form->isValid()) {
            //populate obj. with data
            $impress = $form->getData();
            $impress->setUser($user);


            //set movie
            $impress->setMovie($movie);
            $emi->persist($impress);
            $emi->flush();

            return $this->redirect("/movie/show/{$movie->getId()}");
        }

        return $this->render('movie/show.html.twig', [
            'form' => $form->createView(),
            'movie' => $movie,
            'impressions' => $impressions,
            'username' => $username
        ]);
    }

    /**
     * @return Response
     * @Route("/movie/add", name="add_movie")
     * @Route("/movie/edit/{id}", name="edit_movie")
     */
    public function add(Movie $movie = null, Request $req, EMI $emi, UserInterface $user): Response
    {
        $createMode = null;
        if (!$movie) {
            $movie = new Movie();
            $createMode = true;
        }

        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            $movie = $form->getData();

            if ($createMode) {
                $movie->setUser($user);
            }

            if (!$createMode) {
                if ($user != $movie->getUser()) {
                    return $this->redirectToRoute('movie');
                }
            }

            $emi->persist($movie);
            $emi->flush();

            if ($createMode) {
                return $this->redirectToRoute('movie');
            } else {
                $id = $movie->getId();
                return $this->redirect("/movie/show/{$id}");
            }
        }

        return $this->render('movie/add.html.twig', [
            'form' => $form->createView(),
            'isCreate' => $createMode
        ]);
    }

    /**
     * @return Response
     * @Route("/movie/delete/{id}", name="delete_movie")
     */
    public function delete(Movie $movie, EMI $emi, UserInterface $user): Response
    {
        if ($user != $movie->getUser()) {
            return $this->redirectToRoute('movie');
        }

        $emi->remove($movie);
        $emi->flush();

        return $this->redirectToRoute('movie');
    }
}
