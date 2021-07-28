<?php

namespace App\Controller;

use App\Entity\Impressions;
use App\Form\ImpressionsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface as EMI;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class ImpressionsController extends AbstractController
{

    /**
     * @return Response
     * 
     * @Route("/impression/edit/{id}", name="edit_impression")
     * 
     */
    public function edit(Impressions $impress, Request $req, EMI $emi, UserInterface $user): Response
    {
        $movie = $impress->getMovie();

        // SHOW All Impressions in twig-loop.
        $impressions = $movie->getImpressions();


        //CREATE/EDIT form
        $form = $this->createForm(ImpressionsType::class, $impress);
        $form->handleRequest($req);


        if ($form->isSubmitted() && $form->isValid()) {
            //populate obj. with data
            $impress = $form->getData();

            if ($user != $impress->getUser()) {
                return $this->redirectToRoute('movie');
            }
            $emi->persist($impress);
            $emi->flush();
            return $this->redirect("/movie/show/{$movie->getId()}");
        }

        return $this->render('movie/show.html.twig', [
            'form' => $form->createView(),
            'movie' => $movie,
            'impressions' => $impressions,
        ]);
    }

    /**
     * @return Response
     * @Route("/impression/delete/{id}", name="delete_impression")
     */
    public function delete(Impressions $impression, EMI $emi, UserInterface $user): Response
    {
        if ($user != $impression->getUser()) {
            return $this->redirectToRoute('movie');
        }
        $movie = $impression->getMovie();
        $emi->remove($impression);
        $emi->flush();

        return $this->redirect("/movie/show/{$movie->getId()}");
    }
}
