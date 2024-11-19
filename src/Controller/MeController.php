<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class MeController extends AbstractController
{
    public function __invoke(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        return $this->json($user);
    }
}
