<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\PurchaseItem;
use App\Repository\ProductRepository;
use Stripe\Checkout\Session;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    protected $productRepository;

    // Création des services du panier et du repo 
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[Route('/checkout', name: 'app_payment_checkout')]
    public function checkout($stripe_SK, SessionInterface $session_panier, ManagerRegistry $doctrine): Response
    {
        Stripe::setApiKey($stripe_SK);

        $panier = $session_panier->get('panier', []);

        $panier_data = [];
        foreach ($panier as $id => $quantity) {
            $product = $doctrine->getRepository(Product::class)->find($id);
            if ($product) {
                $panier_data[] = [
                    "product" => $product,
                    "quantity" => $quantity
                ];
            }
        }

        $line_items = [];
        foreach ($panier_data as $id => $value) {
            $line_items[] = [
                "price_data" => [
                    "currency" => "eur",
                    "product_data" => [
                        'name' => $value['product']->getName(),
                    ],
                    "unit_amount" => $value['product']->getPrice() * 100,
                ],
                "quantity" => $value['quantity']
            ];
        }

        $session = Session::create([
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_success_url', [
                'panier_data' => $panier_data
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/checkout/success', name: 'app_success_url')]
    public function success(SessionInterface $session, MailerInterface $mailer): Response
    {
        $user = $this->getUser();


        if ($user instanceof UserInterface) {
            $userEmail = $user->getEmail();
        }

        $items = [];
        $cart_data = $session->get('panier', []);
        
        foreach ($cart_data as $id => $qty) {
            $oneproduct = $this->productRepository->find($id);
            
            $items[]= [
                "id" => $oneproduct->getId(),
                "name" => $oneproduct->getName(),
                "description" => $oneproduct->getDescription(),
                "price" => $oneproduct->getPrice(),
                "quantity" => $qty
            ];
        }
        
        $panier_data = $session->get('panier', []);

       
        
        // Envoie d'un e-mail de confirmation
        $email = (new TemplatedEmail())
            ->from('admin@alexandre-devperso.fr')
            ->to($userEmail)
            ->subject('Confirmation de commande')
            ->htmlTemplate('emails/payment_success.html.twig')
            ->context([
                'items' => $items
            ]);

        $mailer->send($email);

        $session->remove('panier');

        return $this->render('payment/success.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/checkout/cancel', name: 'app_cancel_url')]
    public function cancel()
    {
        return $this->render('payment/cancel.html.twig');
    }
}
