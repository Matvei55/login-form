<?php

namespace App\Models;
use App\Core\Application;

class Tags extends AbstractModel implements Model
{
    private string $table = 'tags';

//    public function save()
//    {
//        if ($this->id !== null) {
//            $result = $this->builder
//                ->table($this->table)
//                ->where('id', $this->id)
//                ->update($this->data);
//            return $result;
//        }
//        $newId = $this->builder
//            ->table($this->table)
//            ->insert($this->data);
//
//        if ($newId) {
//            $this->id = $newId;
//            $this->data['id'] = $newId;
//            return $newId;
//        }
//        return false;
//    }

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
            ->where('name', $name)
            ->fetchOne();
    }

    public function getPostTags(int $postId): array
    {
        $tagsData=$this->builder
            ->table($this->table)
            ->select('tags.*')
            ->join('post_tag', 'tags.id','=', 'post_tag.tag_id')
            ->where('post_tag.post_id', $postId)
            ->fetchAll();
        $tags = [];
        foreach ($tagsData as $data) {
            $tag = new Tags();
            $tag->setData($data);
            $tag->setId($data['id']);
            $tags[] = $tag;
        }
        return $tags;
    }
    public function findOrCreate(string $name): Tags
    {
           $existing = $this->findByName($name);
    if ($existing) {
        $container = Application::getInstance()->getContainer();
        $tag = $container->get(Tags::class);
        $tag->setData($existing);
        $tag->setId($existing['id']);
        return $tag;
    }

    $container = Application::getInstance()->getContainer();
    $tag = $container->get(Tags::class);
    $tag->setData(['name' => $name]);
    $tag->save();
    return $tag;
    }
    public function getName(): string
    {
        return $this->data['name'] ?? '';
    }

    protected function getTable(): string
    {
        return $this->table;
    }
}