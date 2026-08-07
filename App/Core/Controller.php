<?php

namespace App\Core;

use App\Container\ContainerInterface;
use App\Models\Users;

abstract class Controller
{
    public function __construct(
        protected Request $request,
        protected View $view,
        protected Session $session)
    {}
    protected function getMiddlewareConfig(): array
    {
        return [];
    }

    public function getMiddlewareForAction(string $action): array
    {
        $config = $this->getMiddlewareConfig();
        return $config[$action] ?? [];
    }

    public function getAllMiddleware(): array
    {
        return $this->getMiddlewareConfig();
    }
    
    protected function render(string $view, array $data = []): string
    {
        return $this->view->render($view, $data);
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function requireAuth(): void
    {
        if(!$this->session->has('user_id')){
            $this->redirect('/login');
        }
    }

    protected function getUser(): ?array
    {
        if($this->session->has('user_id')){
            $userId = $this->session->getUserId();
            $container = Application::getInstance()->getContainer();
            $userModel = $container->get(Users::Class);
            return $userModel->load($userId)->getData();
        }
        return null;
    }

    protected function setError(string $error): void
    {
        $_SESSION['errors'][] = $error;
    }

    protected function getErrors(): array
    {
        return $_SESSION['errors'] ?? [];
    }

    protected function setSuccess(string $success): void
    {
        $_SESSION['success'] = $success;
    }

    protected function getSuccess(): ?string
    {
        $value = $_SESSION['success'] ?? null;
        if(empty($value)){
            return null;
        }
        if(is_array($value)){
            return implode(',', $value);
        }
        return (string)$value;
    }

    protected function clearSession(): void
    {
        unset($_SESSION['errors'], $_SESSION['success']);
    }

    protected function getParam(string $key, $default = null)
    {
        return $this->request->getParam($key, $default);
    }

    protected function getPost(string $key, $default = null)
    {
        return $this->request->postParam($key, $default);
    }

    protected function isPost(): bool
    {
        return $this->request->isPost();
    }

    protected function isGet(): bool
    {
        return $this->request->isGet();
    }

    protected function hasErrors(): bool
    {
        return !empty($this->getErrors());
    }
}