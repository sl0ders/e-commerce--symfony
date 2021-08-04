<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isEnabled;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="readAt", type="datetime", nullable=true)
     */
    private $readAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="expirationDate", type="datetime", nullable=true)
     */
    private $expirationDate;

    private $sender = "teraneo";

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idPath;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="notifications")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="notification")
     */
    private $company;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getSender(): string
    {
        return $this->sender;
    }

    public function setSender(?String $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getReadAt(): ?DateTime
    {
        return $this->readAt;
    }

    /**
     * @param DateTime $readAt
     * @return Notification
     */
    public function setReadAt(DateTime $readAt): ?Notification
    {
        $this->readAt = $readAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpirationDate(): DateTime
    {
        return $this->expirationDate;
    }

    /**
     * @param DateTime $expirationDate
     * @return Notification
     */
    public function setExpirationDate(DateTime $expirationDate): Notification
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getIdPath(): ?int
    {
        return $this->idPath;
    }

    public function setIdPath(?int $idPath): self
    {
        $this->idPath = $idPath;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
