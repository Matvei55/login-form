<?php
namespace App\Models;

use App\Core\Application;
use App\Core\QueryBuilder;
use App\Core\EventDispatcherInterface;
use Cake\Core\App;

class Categories extends AbstractModel implements Model
{
    protected $table = 'categories';

    public function __construct(
        QueryBuilder $builder,
        EventDispatcherInterface $dispatcher,
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

    public function findBySlug(string $slug): ?array
    {
        return  $this->builder
            ->table($this->table)
            ->where('slug', $slug)
            ->fetchOne();
    }

    public function getPosts():array
    {
        if(!$this->id){
            return [];
        }
        $container = Application::getInstance()->getContainer();
        $postModel = $container->get(Posts::class);
        $postData=  $this->builder
            ->table('posts')
            ->where('category_id', $this->id)
            ->fetchAll();

        $posts =  [];
        foreach ($postData as $data){
            $post= $postModel->load($data['id']);
            if($post->getData()){
                $posts[] = $post;
            }
        }
        return $posts;
    }

    public function getPostsCount():int
    {
        if(!$this->id){
            return 0;
        }
        return $this->builder
            ->table('posts')
            ->where('category_id', $this->id)
            ->count();
    }

    public function getPostsWithAuthor():array
    {
        if(!$this->id){
            return [];
        }

        return $this->builder
            ->table('posts')
            ->select('posts.*', 'users.name as author_name')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->where('posts.category_id', $this->id)
            ->fetchAll();
    }
}