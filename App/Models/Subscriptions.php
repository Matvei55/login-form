<?php
namespace App\Models;

use App\Core\QueryBuilder;
use App\Core\EventDispatcherInterface;

class Subscriptions extends AbstractModel implements Model
{
    private string $table = 'subscriptions';
    public function __construct(
        EventDispatcherInterface $dispatcher,
        QueryBuilder $builder,
    ){
        parent::__construct($builder, $dispatcher);
    }
    protected function getTable(): string
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

    public function isSubscribed(int $followerId, int $authorId):bool
    {
        $result = $this->builder
            ->table($this->table)
            ->where('follower_id', $followerId)
            ->where('author_id', $authorId)
            ->fetchOne();

        return $result !== null;
    }

    public function getFollowers(int $authorId):array
    {
        return $this->builder
            ->table($this->table)
            ->where('author_id', $authorId)
            ->fetchAll();
    }
    public function getFollowing(int $followerId):array
    {
        return $this->builder
            ->table($this->table)
            ->where('follower_id', $followerId)
            ->fetchAll();
    }
    public function subscribe(int $followerId, int $authorId):bool
    {
        if ($this->isSubscribed($followerId, $authorId)){
            return true;
        }
        $this->setData([
            'follower_id' => $followerId,
            'author_id' => $authorId
        ]);
        return (bool) $this->save();
    }
    public function unsubscribe(int $followerId, int $authorId):bool
    {
        return (bool) $this->builder
            ->table($this->table)
            ->where('follower_id', $followerId)
            ->where('author_id', $authorId)
            ->delete();
    }
}