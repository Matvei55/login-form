<?php
namespace App\Models;
use App\Models\Tags;
class Posts extends AbstractModel implements Model
{
    private string $table = 'posts';
    private ?Users $user = null;
    private array $tags = [];

    private Tags $tag;

    public function __construct()
    {
        parent::__construct();
        $this->tag = new Tags();
    }
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
        $userId = $user->getId();
        error_log("setUser: user_id = " . $userId);
        $this->data['user_id'] = $userId;
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
            $this->tag->deleteAllPostTags($postId);

            foreach ($tags as $tag) {
                $this->tag->attachTag($postId, $tag->getId());
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
        if($postId && $tagId && !$this->tag->tagExists($postId, $tagId)) {
            $this->tag->attachTag($postId, $tagId);
            $this->clearTagsCache();
        }
        return $this;
    }

    public function getTags(): array
    {
        if($this->tags == null) {
            $tagsData = $this->tag->getPostTags($this->getId());
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

    //функция должна возращать массив с моделями экзеипляра класса пост
    public function getPostsByUserId(int $userId): array
    {
        return $this->builder
            ->table($this->table)
            ->where('user_id', $userId)
            ->fetchAll();
    }

    public function attachTag (int $postId, int $tagId): bool
    {
        if ($this->tagExists($postId, $tagId)) {
            return true;
        }
        return $this->builder
                ->table('post_tag')
                ->insert([
                    'post_id' => $postId,
                    'tag_id' => $tagId
                ]) !== false;
    }

    public function detachTag (int $postId, int $tagId): bool
    {
        return $this->builder
            ->table('post_tag')
            ->where('post_id', $postId)
            ->where('tag_id', $tagId)
            ->delete();
    }

    public function deleteAllPostTags(int $postId): bool
    {
        return $this->builder
            ->table('post_tag')
            ->where('post_id', $postId)
            ->delete();
    }

    public function tagExists(int $postId, int $tagId): bool
    {
        $result = $this->builder
            ->clear()
            ->table('post_tag')
            ->select('1')
            ->where('post_id', $postId)
            ->where('tag_id', $tagId)
            ->fetchOne();

        return $result !== null;
    }

}







