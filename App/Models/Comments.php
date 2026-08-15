<?php
namespace App\Models;

use App\Core\QueryBuilder;
use App\Core\EventDispatcherInterface;

class Comments extends AbstractModel implements Model
{
    protected $table = 'comments';

    public function __construct(
        protected QueryBuilder $builder,
        EventDispatcherInterface $dispatcher,
        private Users $userModel,
        private Posts $postModel,
    ){
        parent::__construct($builder, $dispatcher);
    }

    protected function getTable():string
    {
        return $this->table;
    }

    public function load(?int $id = null):self
    {
        if($id !== null){
            $result = $this->builder
                ->table($this->table)
                ->where('id', $id)
                ->fetchOne();
            $this->data = $result ?: [];

            if ($this->data){
                $this->id = $this->data['id'] ?? null;
            }
        }
        return $this;
    }

    public function delete():bool
    {
        $result = $this->builder
            ->table($this->table)
            ->where('id', $this->id)
            ->delete();

        if($result){
            $this->data = [];
            $this->id = null;
        }
        return $result;
    }
    public function getReplies():array
    {
        if(!$this->id){
            return [];
        }

        return $this->builder
            ->table($this->table)
            ->where('parent_id', $this->id)
            ->fetchAll();
    }

    public function getAuthor(): ?Users
    {
        if(!$this->id || empty($this->data['user_id'])){
            return null;
        }
        return $this->userModel->load($this->data['user_id']);
    }

    public function getPost(): ?Posts
    {
        if(!$this->id || empty($this->data['post_id'])){
            return null;
        }
        return $this->postModel->load($this->data['post_id']);
    }

    public function approve():self
    {
        $this->data['status'] = 'approved';
        return $this;
    }
    public function reject():self
    {
        $this->data['status'] = 'rejected';
        return $this;
    }
    public function markAsSpam():self
    {
        $this->data['status'] = 'spam';
        return $this;
    }
    public function getCommentsByPostId(int $postId):array
    {
        return $this->builder
            ->table($this->table)
            ->where('post_id', $postId)
            ->orderBy('created_at', 'ASC')
            ->fetchAll();
    }
    public function getPendingComments(): array
    {
        return $this->builder
            ->table($this->table)
            ->where('status', 'pending')
            ->orderBy('created_at', 'ASC')
            ->fetchAll();
    }
    public function getApprovedComments(): array
    {
        return $this->builder
            ->table($this->table)
            ->where('status', 'approved')
            ->orderBy('created_at', 'DESC')
            ->fetchAll();
    }
    public function getRejectedComments(): array
    {
        return $this->builder
            ->table($this->table)
            ->where('status', 'rejected')
            ->orderBy('created_at', 'DESC')
            ->fetchAll();
    }

    public function getCommentTree(int $postId):array
    {
        $comments = $this->builder
            ->table($this->table)
            ->where('post_id', $postId)
            ->where('status',  'approved')
            ->orderBy('created_at', 'ASC')
            ->fetchAll();
        $grouped = [];
        foreach ($comments as $comment){
            $parentId = $comment['parent_id'] ?? 0;
            $grouped[$parentId][] = $comment;
        }
        return $this->buildTree($grouped, 0);
    }
    public function buildTree(array $grouped, int $parentId):array
    {
        $tree = [];
        if(!isset($grouped[$parentId])){
            return $tree;
        }
        foreach ($grouped[$parentId] as $comment){
            $comment['children'] = $this->buildTree($grouped, $comment['id']);
            $tree[] = $comment;
        }
        return $tree;
    }
    public function getDeleteComments():array
    {
        return $this->builder
            ->table($this->table)
            ->where('status',  'deleted')
            ->orderBy('created_at', 'DESC')
            ->fetchAll();
    }
}