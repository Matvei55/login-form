<?php
//require_once 'render.php';
class Render{
    private $templatePath;

    public function __construct($templatePath = '/var/www/html/templates'){
        $this->templatePath = $templatePath;
    }
    public function render($templateName, $data = [], $layout='layout'){
        $content= $this->renderTemplate($templateName,$data);
        return $this->renderTemplate("layouts/{$layout}", array_merge($data, ['content'=>$content]));
    }

    public function renderTemplate($template, $data = []){
        $templatePath = $this->templatePath . '/' . $template . '.php';
//        var_dump($templatePath); die;
        if(file_exists($templatePath)){
//            throw new Exception("шаблон не нанйден: {$templatePath}");
        }
        extract($data);
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}


//$errors = [];
//
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
//    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
//
//    $validator = new RegistrationValidator($username, $password);
//    $result = $validator->validateRegistration();
//
//    if ($result === true) {
//        $userManager = new Registration();
//        $saveResult = $userManager->saveUser($username, $password);
//
//        if ($saveResult) {
//            echo "Регистрация успешна! Имя пользователя: " . htmlspecialchars($username) . "<br>";
//        } else {
//            // ✅ Используем метод для получения сообщения об ошибке
//            $errorMessage = $userManager->getErrorMessage($username, $password);
//            echo "Ошибка при сохранении: " . htmlspecialchars($errorMessage) . "<br>";
//        }
//    } else {
//        echo "Ошибки валидации:<br>";
//        foreach ($result as $error) {
//            echo "- " . htmlspecialchars($error) . "<br>";
//        }
//    }
//}
//$templateName = 'index.php';
//
//$data = [
//    "errors" => $errors,
//];
