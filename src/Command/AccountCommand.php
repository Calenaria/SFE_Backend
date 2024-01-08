<?php

namespace App\Command;

use App\Entity\Account;
use App\Entity\AccountToken;
use App\Entity\File;
use App\Repository\AccountRepository;
use App\Repository\AccountTokenRepository;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:account',
    description: 'Account control',
)]
class AccountCommand extends Command
{
    private EntityManagerInterface $em;
    private AccountRepository $accountRepo;
    private AccountTokenRepository $accountTokenRepo;
    private FileRepository $fileRepo;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->accountRepo = $this->em->getRepository(Account::class);
        $this->accountTokenRepo = $this->em->getRepository(AccountToken::class);
        $this->fileRepo = $this->em->getRepository(File::class);
        
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        dd($this->fileRepo->findAll());

        return Command::SUCCESS;
    }
}
