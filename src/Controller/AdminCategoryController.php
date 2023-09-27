<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/admin/category')]
class AdminCategoryController extends AbstractController
{
    #[Route('/', name: 'app_admin_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin_category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, ParameterBagInterface $parameterBag): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form['picture']->getData();

            if ($pictureFile instanceof UploadedFile) {
                $newFileName = $slugger->slug($category->getName()) . '.' . $pictureFile->getClientOriginalExtension();

                if ($pictureFile->getClientMimeType() === 'image/jpeg' || $pictureFile->getClientMimeType() === 'image/jpg') {
                    try {
                        $pictureFile->move($parameterBag->get('kernel.project_dir') . '/public/uploads/images/products', $newFileName);
                    } catch (FileException $e) {
                        $this->addFlash('error', "Une erreur s'est produite lors de l'envoi de l'image.");
                    }

                    $category->setPicture($newFileName);
                } else {
                    $this->addFlash('error', "Le type de fichier n'est pas valide.");
                }
            }

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été ajoutée avec succès.');

            return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_admin_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('admin_category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form['picture']->getData();

            if ($pictureFile) {
                $newFileName = uniqid() . '.' . $pictureFile->getClientOriginalExtension();

                try {
                    $pictureFile->move($this->getParameter('kernel.project_dir') . '/public/uploads/images/products', $newFileName);
                } catch (FileException $e) {
                    $this->addFlash('error', "Une erreur s'est produite lors de l'envoi de l'image.");
                }

                if ($category->getPicture() && file_exists($this->getParameter('kernel.project_dir') . '/public/uploads/images/products/' . $category->getPicture())) {
                    unlink($this->getParameter('kernel.project_dir') . '/public/uploads/images/products/' . $category->getPicture());
                }

                $category->setPicture($newFileName);
            }

            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été modifiée avec succès.');


            return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            // Delete associated picture if exists
            $picturePath = $this->getParameter('kernel.project_dir') . '/public/uploads/images/products/' . $category->getPicture();
            if ($category->getPicture() && file_exists($picturePath)) {
                unlink($picturePath);
            }

            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
