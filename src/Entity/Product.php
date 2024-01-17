<?php

namespace App\Entity;


use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[
    ApiResource(
        operations: [
            new Get(security: 'is_granted("ROLE_ADMIN") and object.getOwner() == user'),
            new Post(),
            new GetCollection(),
            new Put(),
            new Delete(),
            new Patch()
        ],
        /** From the application to the requester */
        normalizationContext: ['groups' => 'product.read'],
        /** From the requester to the application */
        denormalizationContext: ['groups' => 'product.write']
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'name' => SearchFilterInterface::STRATEGY_PARTIAL,
            'description' => SearchFilterInterface::STRATEGY_PARTIAL,
            'manufacturer.countryCode' => SearchFilterInterface::STRATEGY_EXACT,
            'manufacturer.id' => SearchFilterInterface::STRATEGY_EXACT,
        ]
    ),
    /** Create new api resource */
    ApiResource(
        uriTemplate: '/manufacturers/{id}/products',
        operations: [
            new GetCollection()
        ],
        uriVariables: [
            'id' => new Link(
                fromProperty: 'products',
                fromClass: Manufacturer::class
            )
        ]
    )
]
class Product
{
    /** The id of the product. */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    /** The MPN (manufacturer part number) of the product) */
    #[
        ORM\Column,
        Assert\NotNull,
        Groups(['product.read', 'product.write'])
    ]
    private ?string $mpn = null;

    /** The name of the product. */
    #[ORM\Column]
    #[
        Assert\NotBlank,
        Groups(['product.read', 'product.write'])
    ]
    private string $name = '';

    /** The description of the product.*/
    #[ORM\Column(type: Types::TEXT)]
    #[
        Assert\NotBlank,
        Groups(['product.read', 'product.write'])
    ]
    private string $description = '';

    /** The date of issue of the product.*/
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[
        Assert\NotNull,
        Groups(['product.read'])
    ]
    private ?\DateTimeInterface $issueDate = null;

    /** The manufacturer of the product. */
    #[ORM\ManyToOne(targetEntity: 'Manufacturer', inversedBy: 'products')]
    #[Groups(['product.read'])]
    private ?Manufacturer $manufacturer = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[Groups(['product.read', 'product.write'])]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMpn(): ?string
    {
        return $this->mpn;
    }

    public function setMpn(?string $mpn): void
    {
        $this->mpn = $mpn;
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

    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    public function setIssueDate(?\DateTimeInterface $issueDate): void
    {
        $this->issueDate = $issueDate;
    }

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?Manufacturer $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
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