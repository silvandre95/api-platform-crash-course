<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\State\DeleteManufacturerProcessor;
use App\Validator\AssertCanDeleteManufacturer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity]
#[ApiResource(
        /** Available Operations */
        operations: [
            new Get(security: 'is_granted("ROLE_ADMIN")'),
            new GetCollection(security: 'is_granted("ROLE_ADMIN")'),
            new Delete(validate: true)
        ]
)]
#[AssertCanDeleteManufacturer]
#[IsGranted('ROLE_USER')]
class Manufacturer
{
    /** The id of manufacturer */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    /** The name of manufacturer */
    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(message: 'Please fill up the name.')]
    #[Groups(['product.read'])]
    private string $name = '';

    /** The description of manufacturer */
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['product.read'])]
    private string $description = '';

    /** The country code of manufacturer */
    #[ORM\Column(length: 3)]
    #[Assert\NotBlank]
    #[Groups(['product.read'])]
    private string $countryCode = '';

    /** The listed date of manufacturer */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    #[Groups(['product.read'])]
    private ?\DateTimeInterface $listedDate = null;

    /** @var Product[] Available products from this manufacturer */
    #[ORM\OneToMany(
        mappedBy: 'manufacturer',
        targetEntity: 'Product'
    )]
    private iterable $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getListedDate(): ?\DateTimeInterface
    {
        return $this->listedDate;
    }

    public function setListedDate(?\DateTimeInterface $listedDate): void
    {
        $this->listedDate = $listedDate;
    }

    public function getProducts(): iterable
    {
        return $this->products;
    }

}