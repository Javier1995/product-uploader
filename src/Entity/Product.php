<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(options: ['comment' => 'Stores product data', 'charset' => 'latin1'])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: TYPES::INTEGER, name:'intProductDataId', length: 10, options:["unsigned" => true])]
    private ?int $intProductDataId = null;

    #[ORM\Column(length: 50, nullable: false)]
    private ?string $strtProductName = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $strProductDesc = null;

    #[ORM\Column(length: 10, nullable: false, unique:true)]
    private ?string $strProductCode = null;

    #[ORM\Column(nullable: true)]
    private ?int $stockLevel = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 2, scale: 2, nullable: true)]
    private ?string $price = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => NULL])]
    private ?\DateTimeInterface $dtmAdded = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => NULL])]
    private ?\DateTimeInterface $dtmDiscontinued = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"], nullable: false)]
    private \DateTimeInterface $stmTimestamp;

    public function getIntProductDataId(): ?int
    {
        return $this->intProductDataId;
    }

    public function getStrtProductName(): ?string
    {
        return $this->strtProductName;
    }

    public function setStrtProductName(?string $strtProductName): static
    {
        $this->strtProductName = $strtProductName;

        return $this;
    }

    public function getStrProductDesc(): ?string
    {
        return $this->strProductDesc;
    }

    public function setStrProductDesc(?string $strProductDesc): static
    {
        $this->strProductDesc = $strProductDesc;

        return $this;
    }

    public function getStrProductCode(): ?string
    {
        return $this->strProductCode;
    }

    public function setStrProductCode(string $strProductCode): static
    {
        $this->strProductCode = $strProductCode;

        return $this;
    }

    public function getStockLevel(): ?int
    {
        return $this->stockLevel;
    }

    public function setStockLevel(?int $stockLevel): static
    {
        $this->stockLevel = $stockLevel;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDtmAdded(): ?\DateTimeInterface
    {
        return $this->dtmAdded;
    }

    public function setDtmAdded(?\DateTimeInterface $dtmAdded): static
    {
        $this->dtmAdded = $dtmAdded;

        return $this;
    }

    public function getDtmDiscontinued(): ?\DateTimeInterface
    {
        return $this->dtmDiscontinued;
    }

    public function setDtmDiscontinued(?\DateTimeInterface $dtmDiscontinued): static
    {
        $this->dtmDiscontinued = $dtmDiscontinued;

        return $this;
    }


    public function getStmTimestamp(): ?\DateTimeInterface
    {
        return $this->stmTimestamp;
    }

    public function setStmTimestamp(?\DateTimeInterface $stmTimestamp): static
    {
        $this->stmTimestamp = $stmTimestamp;

        return $this;
    }
}
