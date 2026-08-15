<?php
namespace App\Services;

use App\Core\Session;
use App\Models\Users;

class RoleService
{
    private function __construct(
        public Session $session,
    ){}
    public function isAuthenticated(): bool
    {
        return $this->session->getUser() !== null;
    }
    public function requireModerator(): void
    {
        $user = $this->session->getUser();
        if (!$user) {
            header("Location: /login");
            exit;
        }
        if(!$user->isModerator()) {
            $this->session->setFlash('error' , 'доступ к странице запрещен');
            header("Location: /posts");
            exit;
        }
    }
    public function requireAdmin(): void
    {
        $user = $this->session->getUser();
        if (!$user) {
            header("Location: /login");
            exit;
        }
        if(!$user->isAdmin()) {
            $this->session->setFlash('error' , 'доступ к странице запрещен');
            header("Location: /posts");
            exit;
        }
    }
    public function getCurrentUser(): ?Users
    {
        return $this->session->getUser();
    }
}