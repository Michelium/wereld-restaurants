<?php

namespace App\Entity;

use App\Entity\Trait\EntityLifecycleTrait;
use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country {

    use EntityLifecycleTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['country:read', 'restaurant:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 2)]
    #[Groups(['country:read', 'restaurant:read'])]
    private ?string $code = null;

    #[ORM\Column(length: 100)]
    #[Groups(['country:read', 'restaurant:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['country:read', 'restaurant:read'])]
    private ?string $flag = null;

    /**
     * @var Collection<int, Restaurant>
     */
    #[ORM\OneToMany(targetEntity: Restaurant::class, mappedBy: 'country')]
    private Collection $restaurants;

    public function __construct() {
        $this->restaurants = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getCode(): ?string {
        return $this->code;
    }

    public function setCode(string $code): static {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Restaurant>
     */
    public function getRestaurants(): Collection {
        return $this->restaurants;
    }

    public function addRestaurant(Restaurant $restaurant): static {
        if (!$this->restaurants->contains($restaurant)) {
            $this->restaurants->add($restaurant);
            $restaurant->setCountry($this);
        }

        return $this;
    }

    public function removeRestaurant(Restaurant $restaurant): static {
        if ($this->restaurants->removeElement($restaurant)) {
            // set the owning side to null (unless already changed)
            if ($restaurant->getCountry() === $this) {
                $restaurant->setCountry(null);
            }
        }

        return $this;
    }

    public function getFlag(): ?string {
        return $this->flag;
    }

    public function setFlag(string $flag): static {
        $this->flag = $flag;

        return $this;
    }
}
