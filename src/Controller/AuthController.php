<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager,
        private UserRepository $userRepository,
        private ClientRegistry $clientRegistry,
    ) {}

    #[Route('/auth/register', name: 'auth_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json([
                'message' => 'Missing required fields'
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                'message' => 'Invalid email format'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        if ($this->userRepository->findOneBy(['email' => $data['email']])) {
            return $this->json([
                'message' => 'Email already exists'
            ], Response::HTTP_CONFLICT);
        }
        
        if (strlen($data['password']) < 8) {
            return $this->json([
                'message' => 'Password must be at least 8 characters long'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $user = new User();
            $user->setUuid(Uuid::v4()->toRfc4122());
            $user->setEmail($data['email']);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $data['password'])
            );
            
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            
            return $this->json([
                'token' => $this->jwtManager->create($user)
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'An error occurred while creating the user'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/auth/login', name: 'auth_login', methods: ['POST'])]
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json([
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        return $this->json([
            'token' => $this->jwtManager->create($user)
        ]);
    }

    #[Route('/connect/github', name: 'connect_github_start')]
    public function connectGithub(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('github')
            ->redirect([
                'read:user', 'user:email'
            ], []);
    }

    #[Route('/connect/github/check', name: 'connect_github_check')]
    public function githubCallback(): void
    {

    }

    #[Route('/connect/discord', name: 'connect_discord_start')]
    public function connectDiscord(): Response
    {
        return $this->clientRegistry
            ->getClient('discord')
            ->redirect(['identify', 'email'], []);
    }

    #[Route('/connect/discord/check', name: 'oauth_discord_callback')]
    public function discordCallback(): void
    {
    }

    #[Route('/logout', name: 'auth_logout')]
    public function logout(): void
    {
    }
}