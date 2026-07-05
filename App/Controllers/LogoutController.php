<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class LogoutController extends Controller
{
    public function index(Request $request): void
    {
        // Очищаем сессию
        $this->session->logout();

        // Сохраняем сообщение
        $this->setSuccess('Вы вышли из системы');

        // Редирект на страницу входа
        $this->redirect('/login');
    }

    public function store(Request $request): void
    {
        // POST запрос — тоже выходим
        $this->session->logout();
        $this->setSuccess('Вы вышли из системы');
        $this->redirect('/login');
    }
}