<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`Order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'dishes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    /**
     * @var Collection<int, Dish>
     */
    #[ORM\ManyToMany(targetEntity: Dish::class)]
    private Collection $dishes;

    /**
     * @var Collection<int, OrderFile>
     */
    #[ORM\OneToMany(targetEntity: OrderFile::class, mappedBy: 'orderRelation')]
    private Collection $orderFiles;

    public function __construct()
    {
        $this->dishes = new ArrayCollection();
        $this->orderFiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, Dish>
     */
    public function getDishes(): Collection
    {
        return $this->dishes;
    }

    public function addDish(Dish $dish): static
    {
        if (!$this->dishes->contains($dish)) {
            $this->dishes->add($dish);
        }

        return $this;
    }

    public function removeDish(Dish $dish): static
    {
        $this->dishes->removeElement($dish);

        return $this;
    }

    /**
     * @return Collection<int, OrderFile>
     */
    public function getOrderFiles(): Collection
    {
        return $this->orderFiles;
    }

    public function addOrderFile(OrderFile $orderFile): static
    {
        if (!$this->orderFiles->contains($orderFile)) {
            $this->orderFiles->add($orderFile);
            $orderFile->setOrderRelation($this);
        }

        return $this;
    }

    public function removeOrderFile(OrderFile $orderFile): static
    {
        if ($this->orderFiles->removeElement($orderFile)) {
            // set the owning side to null (unless already changed)
            if ($orderFile->getOrderRelation() === $this) {
                $orderFile->setOrderRelation(null);
            }
        }

        return $this;
    }
}
