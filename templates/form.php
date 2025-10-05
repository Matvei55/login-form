<div class="container">
    <form method="post" action="../handlerLogin.php" novalidate>
        <div class="div-left">
            <h1>Login</h1>
            <label>Введите свое имя:</label>
            <input type="text" name="username" required>

            <label>Введите пароль:</label>
            <input type="password" name="password" required>

            <button type="submit" name="loginSubmit">войти</button>
        </div>
    </form>


    <form method="post" action="http://localhost:81/index.php/?page=registerSubmit" novalidate>
        <div class="div-right">
            <h1>Authorization</h1>
            <label>Придумайте свое имя:</label>
            <input type="text" name="username" required>

            <label>Придумайте пароль</label>
            <input type="password" name="password" required>

            <button type="submit" name="registerSubmit" >зарегестрироваться</button>
        </div>
    </form>
</div>
