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
}