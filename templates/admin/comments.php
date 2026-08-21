<div class="admin-container">
    <h1>Модерация комментариев</h1>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errors) ?></div>
    <?php endif; ?>
    <div class="comments-section pending">
        <h2>Ожидают модерации (<?= count($pendingComments) ?>)</h2>

        <?php if (empty($pendingComments)): ?>
            <p class="empty">Нет комментариев на модерации</p>
        <?php else: ?>
            <?php foreach ($pendingComments as $comment): ?>
                <div class="comment-item">
                    <div class="comment-meta">
                        <strong><?= htmlspecialchars($comment['author_name'] ?? 'Аноним') ?></strong>
                        <span class="date"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                    </div>
                    <div class="comment-content"><?= nl2br(htmlspecialchars($comment['content'])) ?></div>
                    <div class="comment-actions">
                        <form method="POST" action="/admin/approveComment" style="display:inline;">
                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                            <button type="submit" class="btn btn-approve">Одобрить</button>
                        </form>
                        <form method="POST" action="/admin/rejectComment" style="display:inline;">
                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                            <button type="submit" class="btn btn-reject">Отклонить</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="comments-section approved">
        <h2>Одобренные (<?= count($approvedComments) ?>)</h2>

        <?php if (empty($approvedComments)): ?>
            <p class="empty">Нет одобренных комментариев</p>
        <?php else: ?>
            <?php foreach ($approvedComments as $comment): ?>
                <div class="comment-item">
                    <div class="comment-meta">
                        <strong><?= htmlspecialchars($comment['author_name'] ?? 'Аноним') ?></strong>
                        <span class="date"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                    </div>
                    <div class="comment-content"><?= nl2br(htmlspecialchars($comment['content'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="comments-section rejected">
        <h2>Отклонённые (<?= count($rejectedComments) ?>)</h2>

        <?php if (empty($rejectedComments)): ?>
            <p class="empty">Нет отклонённых комментариев</p>
        <?php else: ?>
            <?php foreach ($rejectedComments as $comment): ?>
                <div class="comment-item">
                    <div class="comment-meta">
                        <strong><?= htmlspecialchars($comment['author_name'] ?? 'Аноним') ?></strong>
                        <span class="date"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                    </div>
                    <div class="comment-content"><?= nl2br(htmlspecialchars($comment['content'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>