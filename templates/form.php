<?php
use App\Models\Users;
$userModel = new Users();
$users = $userModel->load(null, true);
?>
<div class="flex-container">
    <form method="post" action="index.php?page=userSubmit" novalidate>
        <div class="div-left">
        <h1>Создать пользователя</h1>
        <label>Имя пользователя
            <input type="text" name="username" required placeholder="Введите имя">
        </label>

        <label>Пароль
            <input type="password" name="password" required placeholder="Введите пароль">
        </label>

        <button type="submit" name="userSubmit">Создать пользователя</button>

        <?php if(!empty($userErrors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach($userErrors as $value): ?>
                        <li><?= htmlspecialchars($value) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if(!empty($userSuccess)): ?>
            <div class="success">
                <?= htmlspecialchars($value) ?>
            </div>
        <?php endif; ?>
        </div>
    </form>



    <form method="post" action="/pub/index.php?page=postSubmit" novalidate>
        <div class="div-right">
            <h1>Создать пост</h1>
            <label>Заголовок поста
            <input type="text" name="title" required placeholder="Введите заголовок поста">
            </label>
            <label>Содержание
                <textarea name="content" rows="8" placeholder="Введите текст поста"></textarea>
                </label>
                    <label>
                    <input type="text" name="tags" placeholder="теги через пробел">
                </label>
                <label>Автор:
<!--                    <select name="user_id" required>-->
<!--                        <option value="">Выберите автора</option>-->
<!--                        --><?php //foreach($users as $user): ?>
<!--                            <option value="--><?php //= $user['id'] ?><!--">--><?php //= htmlspecialchars($user['name']) ?><!--</option>-->
<!--                        --><?php //endforeach; ?>
<!--                    </select>-->
                </label>
            <button type="submit" name="postSubmit">Создать пост</button>
        </div>
    </form>
</div>