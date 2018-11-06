<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @UniqueEntity("barcode")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15, unique=true)
     */
    private $barcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $cost;

    /**
     * @ORM\Column(type="integer")
     */
    private $vatClass;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ReceiptRow", mappedBy="products", cascade={"persist", "remove"})
     */
    private $receiptRows;

    function __construct()
    {
        $this->receiptRows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(string $barcode): self
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getVatClass(): ?int
    {
        return $this->vatClass;
    }

    public function setVatClass(int $vatClass): self
    {
        $this->vatClass = $vatClass;

        return $this;
    }

    public function getReceiptRows(): ?ArrayCollection
    {
        return $this->receiptRows;
    }

    public function addReceiptRow(?ReceiptRow $receiptRow): self
    {
        $this->receiptRows[] = $receiptRow;

        return $this;
    }

    /**
     * @param ArrayCollection $receiptRows
     * @return self
     */
    public function setReceiptRows($receiptRows): self
    {
        $this->receiptRows = $receiptRows;
        return $this;
    }
}
