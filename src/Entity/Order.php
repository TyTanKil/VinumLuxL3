<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $OrderNumber = null;

    #[ORM\Column]
    private ?bool $Valid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateTime = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'Command', targetEntity: CommandLine::class)]
    private Collection $Line;

    public function __construct()
    {
        $this->Line = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?string
    {
        return $this->OrderNumber;
    }

    public function setOrderNumber(string $OrderNumber): self
    {
        $this->OrderNumber = $OrderNumber;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->Valid;
    }

    public function setValid(bool $Valid): self
    {
        $this->Valid = $Valid;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->DateTime;
    }

    public function setDateTime(\DateTimeInterface $DateTime): self
    {
        $this->DateTime = $DateTime;

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

    /**
     * @return Collection<int, CommandLine>
     */
    public function getLine(): Collection
    {
        return $this->Line;
    }

    public function addLine(CommandLine $line): self
    {
        if (!$this->Line->contains($line)) {
            $this->Line->add($line);
            $line->setCommand($this);
        }

        return $this;
    }

    public function removeLine(CommandLine $line): self
    {
        if ($this->Line->removeElement($line)) {
            // set the owning side to null (unless already changed)
            if ($line->getCommand() === $this) {
                $line->setCommand(null);
            }
        }

        return $this;
    }
}
