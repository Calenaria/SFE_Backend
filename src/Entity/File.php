<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File extends Base
{
    #[ORM\Column(length: 511)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Account::class, inversedBy: 'files')]
    private Collection $owner;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastSynced = null;

    public function __construct()
    {
        parent::__construct();
        $this->owner = new ArrayCollection();
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
     * @return Collection<int, Account>
     */
    public function getOwner(): Collection
    {
        return $this->owner;
    }

    public function addOwner(Account $owner): static
    {
        if (!$this->owner->contains($owner)) {
            $this->owner->add($owner);
        }

        return $this;
    }

    public function removeOwner(Account $owner): static
    {
        $this->owner->removeElement($owner);

        return $this;
    }

    public function getLastSynced(): ?\DateTimeInterface
    {
        return $this->lastSynced;
    }

    public function setLastSynced(?\DateTimeInterface $lastSynced): static
    {
        $this->lastSynced = $lastSynced;

        return $this;
    }
}
