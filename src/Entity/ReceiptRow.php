<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReceiptRowRepository")
 */
class ReceiptRow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount = 1;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", inversedBy="receiptRows", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="products_rows")
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Receipt", inversedBy="rows", cascade={"persist"})
     */
    private $receipt;

    public function __construct() {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param ArrayCollection $products
     * @return self
     */
    public function setProducts($products): self
    {
        $this->products = $products;
        return $this;
    }

    public function addProduct(?Product $product): self
    {
        $this->products[] = $product;

        return $this;
    }

    public function getReceipt(): ?Receipt
    {
        return $this->receipt;
    }

    public function setReceipt(?Receipt $receipt): self
    {
        $this->receipt = $receipt;

        return $this;
    }
}
