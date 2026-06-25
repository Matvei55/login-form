<?php
namespace Core;

class View{

    private string $templatePath;

    public function __construct($templatePath = '/var/www/html/templates')
    {
        $this->templatePath = $templatePath;
    }
    public function render(string $templateName, array $data = [], string $layout='layouts/layout') : string
    {
        $content= $this->renderTemplate($templateName,$data);
        return $this->renderTemplate($layout, array_merge($data, ['content'=>$content]));
    }

    public function renderTemplate(string $template, array $data = []) :string
    {
        $templatePath = $this->templatePath . '/' . $template . '.php';

        if (!file_exists($templatePath)) {
            throw new \Exception('шаблон не найден');
        }
        extract($data);
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }
}
