<?php
namespace App\Models;
use App\Models\Users;
use App\QueryBuilder;
use App\Models\Model;
use App\Models\Tags;
use PDO;
use App\Models\AbstractModel;

class Posts extends AbstractModel implements Model
{
    private string $table = 'posts';
    private ?Users $user = null;
    private array $tags = [];


    public function save()
    {
        if ($this->id !== null) {
            $result = $this->builder
                ->table($this->table)
                ->where('id', $this->id)
                ->update($this->data);
            return $result;
        }
        $newId = $this->builder
            ->table($this->table)
            ->insert($this->data);

        if ($newId) {
            $this->id = $newId;
            $this->data['id'] = $newId;
            return $newId;
        }
        return false;
    }

    public function load(?int $id = null): self
    {
        if ($id !== null) {
            $result = $this->builder
                ->table($this->table)
                ->where('id', $id)
                ->fetchOne();

            $this->data = $result ?: [];

            if ($this->data) {
                $this->id = $this->data['id'] ?? null;
            }
        }
        return $this;
    }

    public function delete(): bool
    {
        $result = $this->builder
            ->table($this->table)
            ->where('id', $this->id)
            ->delete();

        if ($result) {
            $this->data = [];
            $this->id = null;
        }
        return $result;
    }


    public function setUser(Users $user): self
    {
        if ($user->getId() === null) {
            throw new \RuntimeException("пользователь не создан");
        }
        $this->user = $user;
        $this->data['user_id'] = $user->getId();
        return $this;
    }

    public function getUser(): Users
    {
        if ($this->user === null) {
            $user = new Users();
            $user->load($this->data['user_id']);
            $this->user = $user;
        }
        return $this->user;
    }

    public function setTag(array $tags): self
    {
        $postId = $this->getId();

        if($postId) {
            $this->builder->deleteAllPostTags($postId);

            foreach ($tags as $tag) {
                $this->builder->attachTag($postId, $tag->getId());
            }
            $this->clearTagsCache();
        }
        return $this;
    }

    private function clearTagsCache(): self
    {
        $this->tags = [];
        return $this;
    }

    public function addTag(Tags $tag): self
    {
       $postId = $this->getId();
       $tagId = $tag->getId();
       if($postId && $tagId && !$this->builder->tagExists($postId, $tagId)) {
           $this->builder->attachTag($postId, $tagId);
           $this->clearTagsCache();
       }
       return $this;
    }

    public function getTags(): array
    {
        if($this->tags == null) {
            $tagsData = $this->builder->getPostTags($this->getId());
            $tagsList = [];

            foreach ($tagsData as $tagData) {
                $tag = new Tags();
                $tag->setData($tagData);
                $tagsList[] = $tag;
            }
            $this->tags = $tagsList;
        }
        return $this->tags;
    }

    public function getTagTitles(): array
    {
        $tags = $this->getTags();
        $titles = [];

        foreach ($tags as $tag) {
        $data = $tag->getData();
        $titles[] = $data['title'];
        }
        return $titles;
    }
}







