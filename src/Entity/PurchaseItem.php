<?php

namespace App\Entity;

use App\Repository\PurchaseItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseItemRepository::class)]
class PurchaseItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $productname = null;

    #[ORM\Column]
    private ?int $productprice = null;

    #[ORM\Column(length: 255)]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseItems')]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'purchaseItems')]
    private ?Purchase $purchase = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductname(): ?string
    {
        return $this->productname;
    }

    public function setProductname(string $productname): static
    {
        $this->productname = $productname;

        return $this;
    }

    public function getProductprice(): ?int
    {
        return $this->productprice;
    }

    public function setProductprice(int $productprice): static
    {
        $this->productprice = $productprice;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getPurchase(): ?Purchase
    {
        return $this->purchase;
    }

    public function setPurchase(?Purchase $purchase): static
    {
        $this->purchase = $purchase;

        return $this;
    }
}
