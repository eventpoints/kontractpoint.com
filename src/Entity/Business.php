<?php

namespace App\Entity;

use App\Repository\BusinessRepository;
use App\Security\BusinessVariantEnum;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: BusinessRepository::class)]
class Business
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(UuidGenerator::class)]
    private null|Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $registrationNumber = null;

    #[ORM\Column(length: 255, enumType: BusinessVariantEnum::class)]
    private null|BusinessVariantEnum $variant = null;

    #[ORM\Column(length: 255)]
    private ?string $tagline = null;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'businesses')]
    private ?User $owner = null;

    public function __construct(
         null|string $name = null,
         null|User $owner = null,
         null|string $registrationNumber = null,
         null|BusinessVariantEnum $variant = null,
         null|string $tagline = null
    )
    {
        $this->name = $name;
        $this->owner = $owner;
        $this->registrationNumber = $registrationNumber;
        $this->variant = $variant;
        $this->tagline = $tagline;
        $this->createdAt = new DateTimeImmutable();
    }


    public function getId(): null|Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(string $registrationNumber): static
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }

    public function getVariant(): null|BusinessVariantEnum
    {
        return $this->variant;
    }

    public function setVariant(null|BusinessVariantEnum $variant): static
    {
        $this->variant = $variant;

        return $this;
    }

    public function getTagline(): ?string
    {
        return $this->tagline;
    }

    public function setTagline(?string $tagline): static
    {
        $this->tagline = $tagline;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
