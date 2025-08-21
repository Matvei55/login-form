<!DOCTYPE html>
<html>
<head>
  <title>Авторизация</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
    <form method="post" action="index.php">
        <div class="div-left">
        <h1>Login</h1>
        <label>Введите свое имя:</label>
        <input type="text" name="username" required>

        <label>Введите пароль:</label>
        <input type="password" name="password" required>

        <button type="submit">Войти в аккаунт</button>
        </div>

        <div class="div-right">
            <h1>Authorization</h1>
            <label>Придумайте свое имя:</label>
            <input type="text" name="username" required>

            <label>Придумайте пароль</label>
            <input type="password" name="password" required>

            <button type="submit">Завершить регистрацию</button>
        </div>
    </div>
    </form>
</body>
</html>



