<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/comment')]
class AdminCommentController extends AbstractController
{
    #[Route('/', name: 'app_admin_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('admin_comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setCreatedAt(new DateTimeImmutable());
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le fichier audio téléversé
            $sonFile = $form['son']->getData();
            if ($sonFile) {
                $maxSize = 10 * 1024 * 1024; // 10 MB en octets
                if ($sonFile->getSize() <= $maxSize) {
                    $newFilename = uniqid().'.'.$sonFile->guessExtension();
                    $sonFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/assets/son', // Chemin correct vers le dossier de stockage
                        $newFilename
                    );
                    $comment->setSon($newFilename);
                } else {
                    $this->addFlash('danger', 'Le fichier audio est trop volumineux (maximum 10 MB).');
                    return $this->redirectToRoute('app_admin_comment_edit', ['id' => $comment->getId()]);
                }
            }

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_admin_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('admin_comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le fichier audio téléversé
            $sonFile = $form['son']->getData();
            //dd($comment->getSon());
            if ($sonFile) {
                $maxSize = 10 * 1024 * 1024; // 10 MB en octets
                if ($sonFile->getSize() <= $maxSize) {
                    $newFilename = uniqid().'.'.$sonFile->guessExtension();
                    $sonFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/assets/son', // Chemin correct vers le dossier de stockage
                        $newFilename
                    );
                    $comment->setSon($newFilename);
                } else {
                    $this->addFlash('danger', 'Le fichier audio est trop volumineux (maximum 10 MB).');
                    return $this->redirectToRoute('app_admin_comment_edit', ['id' => $comment->getId()]);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
