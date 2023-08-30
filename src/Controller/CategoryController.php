<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Category1Type;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{    
    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category, CategoryRepository $categoryRepository): Response
    {
        $products = $category->getProducts();
        $categories = $categoryRepository->findAll();
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
        ]);
    }

}
