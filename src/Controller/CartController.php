<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        #ETAPE 1 : On récupere la session 'panier' si elle existe - sinon elle est créée avec un tableau vide
        $panier = $session->get('panier', []);

        #Variable tableau : on va construire un nouveau tableau 'panierData' à partir de notre 1er tableau 'panier'
        $panierData = [];

        $totalQuantity = 0;
        $total = 0;

        #On boucle sur la session 'panier' pour récuperer proprement l'objet (au lieu de l'id) et la quantité
        foreach($panier as $id => $quantity)
        {
            $panierData[] = [
                "product" => $doctrine->getRepository(Product::class)->find($id),
                "quantity" => $quantity
            ];

            #On calcule le totale des quantités
            $totalQuantity += $quantity;
        }

        #On boucle le totale du panier ici, afin de ne pas le faire dans la vue Twig
        foreach($panierData as $id => $value)
        {
            $total += $value['product']->getPrice() * $value['quantity'];
        }


        return $this->render('cart/index.html.twig', [
            'items' => $panierData,
            'totalQuantity' => $totalQuantity,
            'total' => $total
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, SessionInterface $session)
    {
        #ETAPE 1 : On récupere la session 'panier' si elle existe - sinon elle est créée avec un tableau vide
        $panier = $session->get('panier', []);

        // $panier[2] = 1;
        // $panier[8] = 3;

        #ETAPE 2 : On ajoute la quantité au produit de l'id $id
        if(!empty($panier[$id]))
        {
            $panier[$id]++;
        }
        else
        {
            $panier[$id] = 1;
        }
        
        #ETAPE 3 : On remplace la variable de session panier par le nouveau tableau $panier
        $session->set('panier', $panier);

        $this->addFlash('add_cart', "Le produit a bien été ajouté a votre panier");

        return $this->redirectToRoute('app_home');
    }

    #[Route('/cart/delete/{id}', name: 'cart_delete')]
    public function delete($id, SessionInterface $session)
    {
        #ETAPE 1 : On récupere la session 'panier' si elle existe - sinon elle est créée avec un tableau vide
        $panier = $session->get('panier', []);

        #On supprime de la session celui dont on a passé l'id
        if(!empty($panier[$id]))
        {
            $panier[$id]--;

            if($panier[$id] <= 0)
            {
                unset($panier[$id]);
            }
        }

        #On réaffecte le nouveau panier à la session
        $session->set('panier', $panier);

        #On redirige vers le panier
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'cart_clear')]
    public function clear(SessionInterface $session)
    {
        #On vide la session 'panier'
        $session->remove('panier');

        #On redirige vers le panier
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/{id}', name: 'cart_add_cart')]
    public function addcart($id, SessionInterface $session)
    {
        #ETAPE 1 : On récupere la session 'panier' si elle existe - sinon elle est créée avec un tableau vide
        $panier = $session->get('panier', []);

        // $panier[2] = 1;
        // $panier[8] = 3;

        #ETAPE 2 : On ajoute la quantité au produit de l'id $id
        if(!empty($panier[$id]))
        {
            $panier[$id]++;
        }
        else
        {
            $panier[$id] = 1;
        }
        
        #ETAPE 3 : On remplace la variable de session panier par le nouveau tableau $panier
        $session->set('panier', $panier);
        
        $this->addFlash('add_cart', "Le produit a bien été ajouté a votre panier");

        return $this->redirectToRoute('app_cart');
    }
}
