<?php

namespace App\Controller;

use App\Entity\Multimedia;
use App\Form\MultimediaType;
use App\Repository\MultimediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/multimedia')]
class MultimediaController extends AbstractController
{
    #[Route('/', name: 'app_multimedia_index', methods: ['GET'])]
    public function index(MultimediaRepository $multimediaRepository): Response
    {
        return $this->render('multimedia/index.html.twig', [
            'multimedia' => $multimediaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_multimedia_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $multimedia = new Multimedia();
        $form = $this->createForm(MultimediaType::class, $multimedia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($multimedia);
            $entityManager->flush();

            return $this->redirectToRoute('app_multimedia_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('multimedia/new.html.twig', [
            'multimedia' => $multimedia,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_multimedia_show', methods: ['GET'])]
    public function show(Multimedia $multimedia): Response
    {
        return $this->render('multimedia/show.html.twig', [
            'multimedia' => $multimedia,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_multimedia_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Multimedia $multimedia, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MultimediaType::class, $multimedia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_multimedia_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('multimedia/edit.html.twig', [
            'multimedia' => $multimedia,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_multimedia_delete', methods: ['POST'])]
    public function delete(Request $request, Multimedia $multimedia, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$multimedia->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($multimedia);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_multimedia_index', [], Response::HTTP_SEE_OTHER);
    }
}
