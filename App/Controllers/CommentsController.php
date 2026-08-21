<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Core\EventDispatcherInterface;
use App\Services\CommentService;
class CommentsController extends Controller
{
    public function __construct(
        Request $request,
        View $view,
        Session $session,
        EventDispatcherInterface $dispatcher,
        private CommentService $commentService,
    )
    {
        parent::__construct($request, $view, $session, $dispatcher);
    }
    public function store(Request $request): void
    {
        $this->requireAuth();

        $postId = (int) $request->postParam('post_id', 0);
        $userId = $this->session->getUserId();
        $content = trim($request->postParam('content', ''));
        $parentId = $request->postParam('parent_id') ? (int) $request->postParam('parent_id') : null;
        if($postId <= 0 || empty($content)){
            $this->setError('заполните все поля');
            $this->redirect('/posts');
            return;
        }
        try{
            $this->commentService->addComment($postId, $userId, $content, $parentId);
            $this->setSuccess('комментарий добавлен и отправлен на модерацию');
        }catch (\Exception $e){
            $this->setError($e->getMessage());
        }
        $this->redirect('/posts');
    }
}