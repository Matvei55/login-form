<?php
//require "render.php";
//require "handlerRegister.php";
//require "auth.php";
//
//$render = new Render();
//$page= $_GET['page'] ?? 'home';
//
//switch ($page) {
//    case 'form';
//    if ($_POST) {
//        $render->render('form/form.php', $_POST);
//    }
//    $render->render('form');
//
//    case 'registerSubmit';
//    $r = new Registration();
//    $result= $r->saveUser($_POST);
//    $render->render("templateName", $result);
//    header('Location: http://localhost/page=form');
//    break;
//}

//require "render.php";
//require "handlerRegister.php";
//require "auth.php";
//
//$render = new Render();
//$page = $_GET['page'] ?? 'home';
//
//switch ($page) {
//    case 'form':
//        if (!empty($_POST)) {
//            $render->render('form/form.php', $_POST);
//        } else {
//            $render->render('form');
//        }
//        break;
//
//    case 'registerSubmit':
//        $r = new Registration();
//        // Выберите один из вариантов выше
//        $result = $r->saveUser($_POST['password'] ?? '',
//            $_POST['name'] ?? '');
//        $render->render("templateName", $result);
//        header('Location: http://localhost/?page=form');
//        exit;
//        break;
//
//    default:
//        echo "Страница не найдена";
//}

require "render.php";
require "handlerRegister.php";
require "auth.php";

$render = new Render();
$page = $_GET['page'] ?? 'form';

switch ($page) {
    case 'form':
        if (!empty($_POST)) {
            $render->render('form/form', $_POST);
        } else {

            echo $render->render('form');
        }
        break;

    case 'registerSubmit':
        $r = new Registration();
        $result = $r->saveUser($_POST['username'] ?? '', $_POST['password'] ?? '');
        var_dump($result);die;
        $render->render("templateName", $result);
        header('Location: http://localhost:81/?page=form');
        exit;

    default:
        var_dump($page);
        echo "Страница не найдена";
        break;
}