<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'client')]
    private Collection $dishes;

    public function __construct()
    {
        $this->dishes = new ArrayCollection();
    }

    public function getId(): ?int
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

    /**
     * @return Collection<int, Order>
     */
    public function getDishes(): Collection
    {
        return $this->dishes;
    }

    public function addDish(Order $dish): static
    {
        if (!$this->dishes->contains($dish)) {
            $this->dishes->add($dish);
            $dish->setClient($this);
        }

        return $this;
    }

    public function removeDish(Order $dish): static
    {
        if ($this->dishes->removeElement($dish)) {
            // set the owning side to null (unless already changed)
            if ($dish->getClient() === $this) {
                $dish->setClient(null);
            }
        }

        return $this;
    }
}
