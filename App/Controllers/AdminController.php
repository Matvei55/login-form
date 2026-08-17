<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Core\EventDispatcherInterface;
use App\Services\CommentService;
use App\Services\RoleService;
class AdminController extends Controller
{
    public function __construct(
        Request $request,
        View $view,
        Session $session,
        EventDispatcherInterface $dispatcher,
        public CommentService $commentService,
        public RoleService $roleService,
    )
    {
        parent::__construct($request,$view,$session,$dispatcher);
    }
    public function comments(Request $request): void
    {
        $this->roleService->requireModerator();
        $pendingComments = $this->commentService->getPendingComments();
        $approvedComments = $this->commentService->getApprovedComments();
        $rejectedComments = $this->commentService->getRejectedComments();

        $data = [
            'user' => $this->roleService->getCurrentUser(),
            'pendingComments' => $pendingComments,
            'approvedComments' => $approvedComments,
            'rejectedComments' => $rejectedComments,
            'errors' => $this->getErrors(),
            'success' => $this->getSuccess(),
        ];
        echo $this->render('admin/comments', $data);
        $this->clearSession();
    }
    public function approveComment(Request $request): void
    {
        $this->roleService->requireModerator();
        $commentId = (int) $request->postParam('comment_id', 0);
        if($commentId > 0){
            try{
                $this->commentService->approveComment($commentId);
                $this->setSuccess('комментарий одобрен');
            }catch (\Exception $e){
                $this->setError($e->getMessage());
            }
        }else{
            $this->setError('неверный ID комментария');
        }
        $this->redirect('/admin/comments');
    }
    public function rejectComment(Request $request): void
    {
        $this->roleService->requireModerator();
        $commentId = (int) $request->postParam('comment_id', 0);
        if($commentId > 0){
            try{
                $this->commentService->rejectComment($commentId);
                $this->setSuccess('комментарий отклонён');
            }catch (\Exception $e){
                $this->setError($e->getMessage());
            }
        }else{
            $this->setError('неверный ID комментария');
        }
        $this->redirect('/admin/comments');
    }
}