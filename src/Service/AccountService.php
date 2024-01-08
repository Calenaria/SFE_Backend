<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\AccountToken;
use App\Repository\AccountRepository;
use App\Repository\AccountTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use SensitiveParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountService {

    private EntityManagerInterface $em;
    private AccountRepository $accountRepo;
    private AccountTokenRepository $accountTokenRepo;
    private UserPasswordHasherInterface  $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher) {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->accountRepo = $this->em->getRepository(Account::class);
        $this->accountTokenRepo = $this->em->getRepository(AccountToken::class);
    }

    public function createAccount(string $email, #[SensitiveParameter] string $password): string {
        $newAccount = new Account();
        $newAccount->setEmail($email);
        $newAccount->setPassword($this->hashPassword($password, $newAccount));
        $this->em->persist($newAccount);

        $newAccountToken = new AccountToken();
        $newAccountToken->setValue(Uuid::uuid4()->toString());
        $newAccountToken->setAccount($newAccount);
        $this->em->persist($newAccountToken);

        $this->em->flush();

        return $newAccountToken->getValue();
    }

    public function deleteAccount(string $email): bool {
        if(empty($account = $this->accountRepo->findOneBy(['email' => $email]))) {
            return false;
        }
        
        $this->em->remove($account);
        return true;
    }

    public function getAccountByToken(#[SensitiveParameter] string $token): ?Account {
        $tokenObject = $this->accountTokenRepo->findOneBy(['value' => $token]);
        return $this->accountRepo->findOneBy(['id' => $tokenObject->getAccount()->getId()]);
    }

    private function hashPassword(#[SensitiveParameter] string $plainTextPassword, Account $account): string {
        return $this->passwordHasher->hashPassword($account, $plainTextPassword);
    }

    public function accountExists($email): bool {
        if(!empty($this->accountRepo->exists($email))) {
            return true;
        }
        return false;
    }
}