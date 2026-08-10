<?php
namespace App\Services;

use App\Models\Posts;
use App\Models\Tags;
use App\Models\Users;
use App\Models\Categories;
use App\DTO\CreatePostDTO;
use App\Events\PostPendingModerationEvent;
use App\Core\EventDispatcherInterface;

class PostService
{
    public function __construct
    (
    private Posts $postModel,
    private Tags $tagModel,
    private Users $userModel,
    private Categories $categoryModel,
    private EventDispatcherInterface $dispatcher
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
        if($postId){
            $savedPost = $this->postModel->load($postId);
            if($savedPost->getData()){
                $this->dispatcher->dispatch(new PostPendingModerationEvent($savedPost, $user));
            }
        }
        return $postId;
    }

    public function getUserPosts(int $userId, ?string $status = null): array
    {
        $user = $this->userModel->load($userId);
        return $this->postModel->getPostsByUserId($user, $status);
    }
    public function getPost(int $postId): ?Posts
    {
        $post = $this->postModel->load($postId);
        return $post->getData() ? $post : null;
    }
    public function approvedPost(int $postId): bool
    {
        error_log("[PostService] Ищем пост #$postId");
        $post = $this->postModel->load($postId);
        error_log("[PostService] Данные поста: " . json_encode($post->getData()));
        if(!$post->getData()){
            error_log("[PostService] Пост #$postId не найден в БД");
            throw new \InvalidArgumentException('пост не найден');
        }
        $post->approve();
        $result= $post->save();
        error_log("[PostService] Пост #$postId одобрен, результат сохранения: " . ($result ? 'true' : 'false'));
        return (bool) $result;
    }
    public function rejectPost(int $postId): bool
    {
        error_log("[PostService] Ищем пост #$postId для отклонения");
        $post = $this->postModel->load($postId);
        error_log("[PostService] Данные поста: " . json_encode($post->getData()));

        if(!$post->getData()){
            error_log("[PostService] Пост #$postId не найден в БД");
            throw new \InvalidArgumentException("пост #$postId не найден");
        }

        $post->reject();
        $result = $post->save();
        error_log("[PostService] Пост #$postId отклонен, результат сохранения: " . ($result ? 'true' : 'false'));
        return (bool) $result;
    }
}