<?php
namespace App\Services;

use App\Models\Posts;
use App\Models\Tags;
use App\Models\Users;
use App\Models\Categories;
use App\DTO\CreatePostDTO;

class PostService
{
    public function __construct
    (
    private Posts $postModel,
    private Tags $tagModel,
    private Users $userModel,
    private Categories $categoryModel,
    ){}

    public function create(CreatePostDTO $dto, int $userId): ?int
    {
        $user = $this->userModel->load($userId);
        if (!$user->getData()) {
            throw new \Exception('пользователь не найден');
        }
        $post = $this->postModel->setUser($user)
            ->setData([
                'title' => $dto->title,
                'content' => $dto->content,
            ]);
        if($dto->categoryId){
            $category = $this->categoryModel->load($dto->categoryId);
            if($category->getData()){
                $post->setCategory($category);
            }
        }
        $postId = $post->save();
        if($postId && !empty($dto->tags)){
            foreach ($dto->tags as $tagName){
                $tag = $this->tagModel->findOrCreate($tagName);
                $this->postModel->attachTag($tag);
            }
        }
        return $postId;
    }

    public function getUserPosts(int $userId): array
    {
        $user = $this->userModel->load($userId);
        return $this->postModel->getPostsByUserId($user);
    }
}