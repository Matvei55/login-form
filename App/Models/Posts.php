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

    public function setTags(array $tags): self
    {
        $postId = $this->getId();

        if($postId) {
            $this->deleteAllPostTags();

            foreach ($tags as $tag) {
                $this->attachTag($tag);
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
        if($postId && $tagId && !$this->tagExists($tag)) {
            $this->attachTag($tag);
            $this->clearTagsCache();
        }
        return $this;
    }

    public function getTags(): array
    {
        if (empty($this->tags)) {
            $this->tags = $this->tag->getPostTags($this->getId());
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
    public function getPostsByUserId(Users $user): array
    {
        $postsData = $this->builder
            ->table($this->table)
            ->where('user_id', $user->getId())
            ->fetchAll();

        $posts = [];
        foreach ($postsData as $data) {
            $post = new Posts();
            $post->setData($data);
            $post->setId($data['id']);
            $posts[] = $post;
        }
        return $posts;
    }

    public function attachTag(Tags $tag): bool
    {
        $postId = $this->getId();
        $tagId = $tag->getId();

        if (!$postId || !$tagId) {
            return false;
        }

        if($this->tagExists($tag)) {
            return true;
        }

        return $this->builder
            ->table('post_tag')
            ->insert([
                'post_id' => $postId,
                'tag_id' => $tagId
            ]) !== false;
    }

    public function detachTag (Tags $tag): bool
    {
        $postId = $this->getId();
        $tagId = $tag->getId();
        if(!$postId || !$tagId) {
            return false;
        }
        return $this->builder
            ->table('post_tag')
            ->where('post_id', $postId)
            ->where('tag_id', $tagId)
            ->delete();
    }

    public function deleteAllPostTags(): bool
    {
        $postId = $this->getId();
        if(!$postId) {
            return false;
        }
        return $this->builder
            ->table('post_tag')
            ->where('post_id', $postId)
            ->delete();
    }

    public function tagExists(Tags $tag): bool
    {
        $postId = $this->getId();
        $tagId = $tag->getId();

        if (!$postId || !$tagId) {
            return false;
        }
        $result = $this->builder
            ->clear()
            ->table('post_tag')
            ->select('1')
            ->where('post_id', $postId)
            ->where('tag_id', $tagId)
            ->fetchOne();

        return $result !== null;
    }

    public function getTitle(): string
    {
        return $this->data['title'] ?? '';
    }

    public function getContent(): string
    {
        return $this->data['content'] ?? '';
    }

}







