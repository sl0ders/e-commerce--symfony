<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportRepository::class)
 */
class Report
{
    const SUBJECT_ORDER= "report.subject.order";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string")
     */
    private $reportNumber;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=ReportMessage::class, mappedBy="report")
     */
    private $reportMessages;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    public function __construct()
    {
        $this->reportMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrders(): ?Orders
    {
        return $this->orders;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getReportNumber(): ?string
    {
        return $this->reportNumber;
    }

    public function setReportNumber(string $reportNumber): self
    {
        $this->reportNumber = $reportNumber;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|ReportMessage[]
     */
    public function getReportMessages(): Collection
    {
        return $this->reportMessages;
    }

    public function addReportMessage(ReportMessage $reportMessage): self
    {
        if (!$this->reportMessages->contains($reportMessage)) {
            $this->reportMessages[] = $reportMessage;
            $reportMessage->setReport($this);
        }

        return $this;
    }

    public function removeReportMessage(ReportMessage $reportMessage): self
    {
        if ($this->reportMessages->removeElement($reportMessage)) {
            // set the owning side to null (unless already changed)
            if ($reportMessage->getReport() === $this) {
                $reportMessage->setReport(null);
            }
        }

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }
}
