<?php

namespace App\Entity;

use App\Entity\Trait\EntityLifecycleTrait;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
#[ORM\Index(columns: ['latitude', 'longitude'])]
class Restaurant {

    use EntityLifecycleTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['restaurant:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['restaurant:read'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['restaurant:read'])]
    private ?float $latitude = null;

    #[ORM\Column]
    #[Groups(['restaurant:read'])]
    private ?float $longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['restaurant:read'])]
    private ?string $street = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Groups(['restaurant:read'])]
    private ?string $houseNumber = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Groups(['restaurant:read'])]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['restaurant:read'])]
    private ?string $city = null;

    #[ORM\ManyToOne(inversedBy: 'restaurants')]
    #[Groups(['restaurant:read'])]
    private ?Country $country = null;

    #[ORM\Column(length: 40, unique: true, nullable: true)]
    private ?string $osmId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $osmCuisine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }

    public function getLatitude(): ?float {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static {
        $this->longitude = $longitude;

        return $this;
    }

    public function getStreet(): ?string {
        return $this->street;
    }

    public function setStreet(?string $street): static {
        $this->street = $street;

        return $this;
    }

    public function getHouseNumber(): ?string {
        return $this->houseNumber;
    }

    public function setHouseNumber(?string $houseNumber): static {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    public function getPostalCode(): ?string {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string {
        return $this->city;
    }

    public function setCity(?string $city): static {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getOsmId(): ?string
    {
        return $this->osmId;
    }

    public function setOsmId(?string $osmId): static
    {
        $this->osmId = $osmId;

        return $this;
    }

    public function getOsmCuisine(): ?string
    {
        return $this->osmCuisine;
    }

    public function setOsmCuisine(?string $osmCuisine): static
    {
        $this->osmCuisine = $osmCuisine;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }
}
