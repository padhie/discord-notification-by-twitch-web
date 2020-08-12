<?php

namespace App\Service;

final class ViewService
{
    private string $viewDirectory;

    public function __construct() {
        $this->viewDirectory = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'view/';
    }

    public function display(string $template, array $variables = []): void
    {
        $this->checkTemplate($template);

        // use in templates
        $var = new TemplateVariables($variables);

        echo include $this->getFullTemplatePath($template);
    }

    private function checkTemplate(string $template): void
    {
        $fullTemplate = $this->getFullTemplatePath($template);
        if (!file_exists($fullTemplate)) {
            throw new \Exception(
                sprintf(
                    'template \'%s\' not exist' . PHP_EOL,
                    $template
                )
            );
        }
    }

    private function getFullTemplatePath(string $template): string
    {
        return sprintf(
            '%s%s.phtml',
            $this->viewDirectory,
            $template
        );
    }
}