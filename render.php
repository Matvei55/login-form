<?php
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
        extract($data);
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}
