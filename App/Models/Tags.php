<?php

namespace App\Models;
use App\QueryBuilder;
use PDO;
use App\Models\AbstractModel;
class Tags extends AbstractModel implements Model
{
    private string $table = 'tags';

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

    public function findByName (string $name): ?array
    {
        return $this->builder
            ->table($this->table)
            ->where('title', $name)
            ->fetchOne();
    }

    public function getPostTags(int $postId): array
    {
        return $this->builder
            ->table($this->table)
            ->select('tags.*')
            ->join('post_tag', 'tags.id','=', 'post_tag.tag_id')
            ->where('post_tag.post_id', $postId)
            ->fetchAll();
    }

    public function attachTag (int $postId, int $tagId): bool
    {
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
}