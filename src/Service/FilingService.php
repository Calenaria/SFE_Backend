<?php

namespace App\Service;

use App\Entity\File;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Service\AccountService;
use Doctrine\ORM\EntityManagerInterface;
use SensitiveParameter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class FilingService {

    private const FILE_DIRECTORY = "/resources/files";

    private string $rootDirectory;

    public function __construct(KernelInterface $kernel, private AccountService $accountService, private EntityManagerInterface $em) {
        $this->rootDirectory = $kernel->getProjectDir();
        $this->accountService = $accountService;
        $this->em = $em;
    }

    public function add(UploadedFile $file, #[SensitiveParameter] string $ownerAccountToken): bool {
        if(empty($account = $this->accountService->getAccountByToken($ownerAccountToken))) {
            return false;
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = uniqid().'.'. $file->guessExtension();
        
        $filesystem = new Filesystem();
        $targetDirectory = $this->rootDirectory . self::FILE_DIRECTORY . "/" . $account->getUuid();
        
        if (!$filesystem->exists($targetDirectory)) {
            $filesystem->mkdir($targetDirectory);
        }
        
        $file->move($targetDirectory, $newFilename);

        // entry in db

        $fileEntry = new File();
        $fileEntry->setName($newFilename);
        $fileEntry->addOwner($account);
        $this->em->persist($fileEntry);
        $this->em->flush();

        return true;
    }

    public function remove(string $fileName, #[SensitiveParameter] string $ownerAccountToken) {

    }

    public function getFileListByAccount(#[SensitiveParameter] string $ownerAccountToken): array {
        if(empty($account = $this->accountService->getAccountByToken($ownerAccountToken))) {
            return [];
        }

        return $account->getFiles();
    }
}