
<!DOCTYPE html>
<html>
<head>
  <title>Авторизация</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
    <form method="post" action="index.php" novalidate>
        <div class="div-left">
        <h1>Login</h1>
        <label>Введите свое имя:</label>
        <input type="text" name="username" required>

        <label>Введите пароль:</label>
        <input type="password" name="password" required>

        <input type="submit" name="loginSubmit" value="войти">
        </div>
    </form>
    <form method="post" action="handlerRegister.php" novalidate>
        <div class="div-right">
            <h1>Authorization</h1>
            <label>Придумайте свое имя:</label>
            <input type="text" name="username" required>

            <label>Придумайте пароль</label>
            <input type="password" name="password" required>

            <input type="submit" name="registerSubmit" value="зарегестрироваться">
        </div>
    </div>
    </form>
</body>
</html>



