<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 */
class Ticket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\Column(type="boolean")
     */
    private $escalated;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    /**
     * @ORM\Column(type="string", length=700)
     */
    private $ticketMessage;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateClosed;

    /**
     * @ORM\ManyToOne(targetEntity=Status::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="ticketID", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="createdByTickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="assignedToTickets")
     */
    private $assignedTo;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="solvedByTickets")
     */
    private $solvedBy;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEscalated(): ?bool
    {
        return $this->escalated;
    }

    public function setEscalated(bool $escalated): self
    {
        $this->escalated = $escalated;

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

    public function getTicketMessage(): ?string
    {
        return $this->ticketMessage;
    }

    public function setTicketMessage(string $ticketMessage): self
    {
        $this->ticketMessage = $ticketMessage;

        return $this;
    }

    public function getDateClosed(): ?\DateTimeInterface
    {
        return $this->dateClosed;
    }

    public function setDateClosed(?\DateTimeInterface $dateClosed): self
    {
        $this->dateClosed = $dateClosed;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTicketID($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTicketID() === $this) {
                $comment->setTicketID(null);
            }
        }

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): self
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    public function getSolvedBy(): ?User
    {
        return $this->solvedBy;
    }

    public function setSolvedBy(?User $solvedBy): self
    {
        $this->solvedBy = $solvedBy;

        return $this;
    }
}
