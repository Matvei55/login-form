<?php

namespace App\Core;
use App\Models\Users;

abstract class Controller
{
    protected View  $view;
    protected Request $request;
    protected Session $session;

    public function __construct()
    {
        $this->request = new Request();
         $this->view = new View();
        $this->session = new Session();
    }

    abstract public function index(Request $request):void;
    abstract public function store(Request $request):void;

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
            $userModel = new Users();
            return $userModel->load('userId')->getData();
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
        $_SESSION['success'][] = $success;
    }

    protected function getSuccess(): string
    {
        return $_SESSION['success'] ?? '';
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
}