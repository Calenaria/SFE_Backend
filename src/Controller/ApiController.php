<?php

namespace App\Controller;

use App\Entity\Account;
use App\Repository\AccountRepository;
use App\Service\AccountService;
use App\Service\FilingService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/')]
class ApiController extends AbstractController
{
    private LoggerInterface $logger;
    private EntityManagerInterface $em;
    private AccountRepository $accountRepo;
    private FilingService $filing;
    private AccountService $accountService;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $em, FilingService $filing, AccountService $accountService) {
        $this->logger = $logger;
        $this->em = $em;
        $this->filing = $filing;
        $this->accountService = $accountService;
        $this->accountRepo = $this->em->getRepository(Account::class);
    }

    #[Route('/', name: 'app_api_index')]
    public function index(Request $request): Response {
        return new JsonResponse([
            'message' => 'Doki doki waku waku!'
        ]);
    }


    #[Route('account/register', name: 'app_api_register')]
    public function register(Request $request): Response {
        $body = json_decode($request->getContent(), true);
        

        if(empty($body['email'] || empty($body['password']))) {
            $this->logger->debug("Invalid email or password! (empty)");
            return $this->json([
                'error' => 'Invalid email or password!'
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
            $this->logger->debug("Invalid email format!");
            return $this->json([
                'error' => 'Invalid email format!'
            ], Response::HTTP_BAD_REQUEST);
        }

        if(!empty($this->accountRepo->exists($body['email']))) {
            $this->logger->debug("Email taken!");
            return $this->json([
                'error' => 'Invalid email or password!'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $token = $this->accountService->createAccount($body['email'], $body['password']);
            return $this->json([
                'message' => 'Account created!',
                'token' => $token
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            return $this->json([
                'error' => 'Something went wrong.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('account/login', name: 'app_api_login')]
    public function login(Request $request): Response {
        $body = json_decode($request->getContent(), true);

        if(empty($body['email'] || empty($body['password']))) {
            $this->logger->debug("Invalid email or password!");
            return $this->json([
                'error' => 'Invalid email or password!'
            ], Response::HTTP_BAD_REQUEST);
        }

        if(empty($account = $this->accountRepo->findOneBy(['email' => $body['email']]))) {
            $this->logger->debug("Account not existing!");
            return $this->json([
                'error' => 'Account not existing!'
            ], Response::HTTP_BAD_REQUEST);
        }

        if($account->getPassword() !== $body['password']) {
            $this->logger->debug("Wrong password!");
            return $this->json([
                'error' => 'Wrong password!'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'token' => $account->getAccountTokens()[0]->getValue()
        ], Response::HTTP_OK);
    }

    #[Route('file/all', 'api_retrieve_all')]
    public function retrieveAllFiles(Request $request) {
        $body = json_decode($request->getContent(), true);
    }

    #[Route('file/upload', 'api_upload_file')]
    public function uploadFile(Request $request) {
        $uploadedFile = $request->files->get('file');
        $accountToken = $request->headers->get('token');

        if ($uploadedFile) {
            try {
                $this->filing->add($uploadedFile, $accountToken);
            } catch (FileException $e) {
                return new JsonResponse([
                    'error' => 'Something went wrong uploading the file'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
    
            return new JsonResponse(['message' => 'File uploaded successfully'], Response::HTTP_OK);
        }
    
        return new Response('No file uploaded');
    }

    #[Route('file/download', 'api_download_file')]
    public function downloadFile(Request $request) {

    }

    #[Route('file/sync', 'api_sync_file')]
    public function syncFile(Request $request) {

    }

    #[Route('file/retrieve', 'api_retrieve_saved_files')]
    public function retrieveSavedFiles(Request $request) {
        $accountToken = $request->headers->get('token');
        return new JsonResponse($this->filing->getFileListByAccount($accountToken));
    }

    private static function generate_uuid() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
