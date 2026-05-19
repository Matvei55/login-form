<div class="form-card">
    <h1>Вход в аккаунт</h1>
    <?php if(!empty($errors)): ?>
    <div class="errors">
        <?php foreach($errors as $error): ?>
        <div><?= htmlspecialchars($error)?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <form method="post" action="/index.php?action=login&page=login">
        <div class="form-group">
            <label>
                Имя пользователя
            </label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>
                Пароль
            </label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Войти</button>
    </form>
    <p>Нет аккаунта?<a href="/index.php?page=register">Зарегестрироваться</a></p>
</div>
