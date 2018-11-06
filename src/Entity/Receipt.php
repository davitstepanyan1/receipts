<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReceiptRepository")
 */
class Receipt
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $finished = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReceiptRow", mappedBy="receipt", cascade={"persist", "remove"})
     */
    private $rows;

    public function __construct()
    {
        $this->rows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * @return Collection|ReceiptRow[]
     */
    public function getRows(): Collection
    {
        return $this->rows;
    }

    public function addRow(ReceiptRow $row): self
    {
        if (!$this->rows->contains($row)) {
            $this->rows[] = $row;
            $row->setReceipt($this);
        }

        return $this;
    }

    public function removeRow(ReceiptRow $row): self
    {
        if ($this->rows->contains($row)) {
            $this->rows->removeElement($row);
            // set the owning side to null (unless already changed)
            if ($row->getReceipt() === $this) {
                $row->setReceipt(null);
            }
        }

        return $this;
    }
}
