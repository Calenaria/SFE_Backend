<?php

namespace App\Entity;

use App\Repository\AccountTokenRepository;
use App\Entity\Base;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountTokenRepository::class)]
class AccountToken extends Base
{

    #[ORM\Column(length: 511)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'accountTokens')]
    private ?Account $account = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): static
    {
        $this->account = $account;

        return $this;
    }
}
