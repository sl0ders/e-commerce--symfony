<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrdersRepository")
 */
class Orders
{

    const STATE_IN_COURSE = "orders.states.in_course";
    const STATE_VALIDATE = "orders.states.validate";
    const STATE_COMPLETED = "orders.states.completed";
    const STATE_HONORED = "orders.states.honored";
    const STATE_ABORDED = "orders.states.aborded";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $validation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nCmd;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user;

    /**
     * @ORM\OneToMany(targetEntity=LinkOrderProduct::class, mappedBy="orders")
     */
    private $linkOrderProducts;

    public function __construct()
    {
        $this->linkOrderProducts = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getValidation(): ?string
    {
        return $this->validation;
    }

    public function setValidation(string $validation): self
    {
        $this->validation = $validation;

        return $this;
    }

    public function getNCmd(): ?string
    {
        return $this->nCmd;
    }

    public function setNCmd(string $nCmd): self
    {
        $this->nCmd = $nCmd;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNCmd();
    }

    /**
     * @return Collection|LinkOrderProduct[]
     */
    public function getLinkOrderProducts(): Collection
    {
        return $this->linkOrderProducts;
    }

    public function addLinkOrderProduct(LinkOrderProduct $linkOrderProduct): self
    {
        if (!$this->linkOrderProducts->contains($linkOrderProduct)) {
            $this->linkOrderProducts[] = $linkOrderProduct;
            $linkOrderProduct->setOrders($this);
        }

        return $this;
    }

    public function removeLinkOrderProduct(LinkOrderProduct $linkOrderProduct): self
    {
        if ($this->linkOrderProducts->removeElement($linkOrderProduct)) {
            // set the owning side to null (unless already changed)
            if ($linkOrderProduct->getOrders() === $this) {
                $linkOrderProduct->setOrders(null);
            }
        }

        return $this;
    }
}
