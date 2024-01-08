<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account extends Base implements PasswordAuthenticatedUserInterface
{
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 511)]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: File::class, mappedBy: 'owner')]
    private Collection $files;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: AccountToken::class)]
    private Collection $accountTokens;

    public function __construct()
    {
        parent::__construct();
        $this->files = new ArrayCollection();
        $this->accountTokens = new ArrayCollection();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->addOwner($this);
        }

        return $this;
    }

    public function removeFile(File $file): static
    {
        if ($this->files->removeElement($file)) {
            $file->removeOwner($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, AccountToken>
     */
    public function getAccountTokens(): Collection
    {
        return $this->accountTokens;
    }

    public function addAccountToken(AccountToken $accountToken): static
    {
        if (!$this->accountTokens->contains($accountToken)) {
            $this->accountTokens->add($accountToken);
            $accountToken->setAccount($this);
        }

        return $this;
    }

    public function removeAccountToken(AccountToken $accountToken): static
    {
        if ($this->accountTokens->removeElement($accountToken)) {
            // set the owning side to null (unless already changed)
            if ($accountToken->getAccount() === $this) {
                $accountToken->setAccount(null);
            }
        }

        return $this;
    }
}
