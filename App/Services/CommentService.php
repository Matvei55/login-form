<?php
namespace App\Services;

use App\DTO\CreatePostDTO;
use App\Models\Posts;
use App\Models\Comments;
use App\Models\Users;

class CommentService
{
    public function __construct(
        private Comments $commentModel,
        private Posts $postModel,
        private Users $userModel
    ){}

    public function addComment(int $postId, int $userId, string $content, ?int $parentId = null): ?int
    {
        $post = $this->postModel->load($postId);
        if(!$post->getData()){
            throw new \InvalidArgumentException('пост не найден');
        }
        $user = $this->userModel->load($userId);
        if(!$user->getData()){
            throw new \InvalidArgumentException('пользователь не найден');
        }
        return $this->commentModel->setData([
            'post_id' => $postId,
            'user_id' => $userId,
            'parent_id' => $parentId,
            'content' => $content,
            'status' => 'pending'
        ])->save();
    }

    public function getCommentTree(int $postId): array
    {
        $comments = $this->commentModel->builder
            ->table('comments')
            ->where('post_id', $postId)
            ->orderBy('created_at', 'ASC')
            ->fetchAll();

        $grouped = [];
        foreach($comments as $comment){
            $parentId = $comment['parent_id'] ?? 0;
            $grouped[$parentId][] = $comment;
        }
        return $this->buildTree($grouped, 0);
    }

    private function buildTree(array $grouped, int $parentId): array
    {
        $tree = [];

        if(!isset($grouped[$parentId])){
            return $tree;
        }
        foreach($grouped[$parentId] as $comment){
            $children = $this->buildTree($grouped, $comment['id']);
            $comment['children'] = $children;
            $tree[] = $comment;
        }
        return $tree;
    }
}