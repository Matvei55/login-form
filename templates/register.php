<div class="form-card">
    <h1>Регистрация</h1>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <div>• <?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" action="/register/store">
        <div class="form-group">
            <label>Имя пользователя</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Зарегистрироваться</button>
    </form>

    <p>Уже есть аккаунт? <a href="/login">Войти</a></p>
</div>