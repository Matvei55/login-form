<!--<div class="posts-container">-->
<!--    <h1>посты</h1>-->
<!---->
<!--    --><?php //if (!empty($success)): ?>
<!--        <div class="success"> --><?php //= htmlspecialchars($success) ?><!--</div>-->
<!--    --><?php //endif; ?>
<!---->
<!--    <div class="create-post">-->
<!--        <h2>Создать новый пост</h2>-->
<!---->
<!--        --><?php //if (!empty($errors)): ?>
<!--            <div class="errors">-->
<!--                --><?php //foreach ($errors as $error): ?>
<!--                    <div>• --><?php //= htmlspecialchars($error) ?><!--</div>-->
<!--                --><?php //endforeach; ?>
<!--            </div>-->
<!--        --><?php //endif; ?>
<!---->
<!--        <form method="post" action="/index.php?action=createPost&page=posts">-->
<!--            <div class="form-group">-->
<!--                <label>Заголовок</label>-->
<!--                <input type="text" name="title" required>-->
<!--            </div>-->
<!---->
<!--            <div class="form-group">-->
<!--                <label>Содержание</label>-->
<!--                <textarea name="content" rows="5"></textarea>-->
<!--            </div>-->
<!---->
<!--            <div class="form-group">-->
<!--                <label>Теги (через пробел)</label>-->
<!--                <input type="text" name="tags">-->
<!--            </div>-->
<!---->
<!--            <button type="submit">Создать пост</button>-->
<!--        </form>-->
<!--    </div>-->
<!---->
<!--    <div class="user-posts">-->
<!--        <h2>Мои посты</h2>-->
<!---->
<!--        --><?php //if (empty($userPosts)): ?>
<!--            <p>У вас пока нет постов. Создайте первый!</p>-->
<!--        --><?php //else: ?>
<!--            --><?php //foreach ($userPosts as $post): ?>
<!--                <div class="post">-->
<!--                    <div class="post-header">-->
<!--                    <strong>Название:</strong>-->
<!--                    <h3>--><?php //= htmlspecialchars($post->getTitle()) ?><!--</h3>-->
<!--                    </div>-->
<!---->
<!--                    <div class="post-content">-->
<!--                    <strong>Контент:</strong>-->
<!--                    <p>--><?php //= nl2br(htmlspecialchars($post->getContent())) ?><!--</p>-->
<!--                    </div>-->
<!---->
<!--                    --><?php
//                    $tags = $post->getTags();
//                    if (!empty($tags)):?>
<!---->
<!--                    <div class = 'post-tags'>-->
<!--                        <strong>Теги:</strong>-->
<!--                        --><?php //foreach ($tags as $tag): ?>
<!--                            <span class="tag">--><?php //= htmlspecialchars($tag->getTitle())?><!--</span>-->
<!--                        --><?php //endforeach; ?>
<!--                    </div>-->
<!--                    --><?php //endif; ?>
<!--                </div>-->
<!--            --><?php //endforeach; ?>
<!--        --><?php //endif; ?>
<!--    </div>-->
<!--</div>-->
<div class="posts-container">
    <h1>посты</h1>

    <?php if (!empty($success)): ?>
        <div class="success"> <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="create-post">
        <h2>Создать новый пост</h2>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <div>• <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/index.php?action=createPost&page=posts">
            <div class="form-group">
                <label>Заголовок</label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Содержание</label>
                <textarea name="content" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label>Теги (через пробел)</label>
                <input type="text" name="tags">
            </div>

            <button type="submit">Создать пост</button>
        </form>
    </div>
    <div class="user-posts">
        <h2>Мои посты</h2>

        <?php if (empty($userPosts)): ?>
            <p>У вас пока нет постов. Создайте первый!</p>
        <?php else: ?>
            <?php foreach ($userPosts as $post): ?>
                <div class="post">
                    <div class="post-header">
                    <strong>Название:</strong>
                    <h3><?= htmlspecialchars($post->getTitle()) ?></h3>
                    </div>

                    <div class="post-content">
                    <strong>Контент:</strong>
                    <p><?= nl2br(htmlspecialchars($post->getContent())) ?></p>
                    </div>

                    <?php
                    $tags = $post->getTags();
                    if (!empty($tags)):
                        ?>
                    <div class = 'post-tags'>
                        <strong>Теги:</strong>
                        <?php foreach ($post->getTags() as $tag): ?>
                            <span class="tag"><?= htmlspecialchars($tag->getTitle())?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
