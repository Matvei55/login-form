<?php
$level = $level ?? 0;
?>

<div class="comment-item" style="margin-left: <?= $level * 30 ?>px;">
    <div class="comment-meta">
        <strong><?= htmlspecialchars($comment['author_name'] ?? 'Аноним') ?></strong>
        <span class="comment-date"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
    </div>

    <div class="comment-content">
        <?= nl2br(htmlspecialchars($comment['content'] ?? '')) ?>
    </div>

    <button class="btn-reply" onclick="document.getElementById('reply_form_<?= $comment['id'] ?>').style.display='block'">
        ↩️ Ответить
    </button>

    <form method="post" action="/comments/store" id="reply_form_<?= $comment['id'] ?>" style="display: none; margin-top: 10px;">
        <input type="hidden" name="post_id" value="<?= $post->getId() ?>">
        <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
        <div class="form-group">
            <textarea name="content" rows="2" placeholder="Напишите ответ..." required></textarea>
        </div>
        <button type="submit" class="btn btn-comment">📩 Ответить</button>
        <button type="button" class="btn btn-cancel" onclick="document.getElementById('reply_form_<?= $comment['id'] ?>').style.display='none'">Отмена</button>
    </form>

    <?php if (!empty($comment['children'])): ?>
        <div class="comment-replies">
            <?php foreach ($comment['children'] as $child): ?>
                <?php
                $comment = $child;
                $level = $level + 1;
                include __DIR__ . '/_comment_item.php';
                ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
