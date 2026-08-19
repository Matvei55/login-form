<div class="posts-container">
    <div class="post-header">
         <h1>Me Posts:</h1>
        <a href="/logout" class="exit-btn">Выйти</a>
    </div>

    <?php if(!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success)?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <div> <?= htmlspecialchars($error)?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>


    <div class="create-post">
        <h2>Создать новый пост</h2>

        <form method="post" action="/posts/store">
            <div class="form-group">
                <label for="title">Заголовок</label>
                <input type="text" id="title" name="title" required placeholder="Введите заголовок">
            </div>
            <div class="form-group">
                <label for="content">Содержание</label>
                <textarea id="content" name="content" rows="5" placeholder="Введите текст поста"></textarea>
            </div>
            <div class="form-group">
                <label for="tags">Теги (через запятую)</label>
                <input type="text" id="tags" name="tags" placeholder="1,2,3...">
            </div>
            <button type="submit" class="btn btn-primary">СОЗДАТЬ ПОСТ</button>
        </form>
    </div>

    <div class="user-posts">
        <h2>Посты:</h2>
        <?php if (empty($userPosts)): ?>
            <p class="empty">У вас пока нет постов</p>
        <?php else: ?>
            <?php foreach ($userPosts as $post): ?>
                <div class="post-item">
                    <div class="post-header">
                        <h3><?= htmlspecialchars($post->getTitle())?></h3>
                        <span class="post-status <?=$post->getStatus()?>">
                            <?php if ($post->getStatus() === 'pending'): ?>
                                на модерации
                            <?php elseif ($post->getStatus() === 'approved'): ?>
                                одобрено
                            <?php elseif ($post->getStatus() === 'rejected'): ?>
                                отклонено
                            <?php endif;?>
                        </span>
                    </div>

                    <div class="post-content">
                        <p><?= nl2br(htmlspecialchars($post->getContent())) ?></p>
                    </div>
                    <?php $tags = $post->getTags(); ?>
                    <?php if (!empty($tags)): ?>
                        <div class="post-tags">
                            <strong>Теги</strong>
                            <?php foreach ($tags as $tag): ?>
                                <span class="tag"><?= htmlspecialchars($tag->getName()) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="comments-section">
                        <h4>Комментарии</h4>
                        <?php
                        $container = App\Core\Application::getInstance()->getContainer();
                        $commentService = $container->get(App\Services\CommentService::class);
                        $comments = $commentService->getCommentTree($post->getId());
                        ?>

                        <?php if (!empty($comments)): ?>
                            <div class="comments-tree">
                                <?php foreach ($comments as $comment): ?>
                                    <?php include __DIR__.'/comments/_comment_item.php'; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="empty-comments">Комментариев пока нет</p>
                        <?php endif; ?>
                        <form method="post" action="/comments/store" class="comment-form">
                            <input type="hidden" name="post_id" value="<?= $post->getId()?>">
                            <div class="form-group">
                                <label for="comment_<?= $post->getId() ?>">Добавить комментарий</label>
                                <textarea id="comment_<?= $post->getId() ?>" name="content" rows="3" placeholder="напишите комментарий" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-comment">ОТПРАВИТЬ</button>
                        </form>
                    </div>
                </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>