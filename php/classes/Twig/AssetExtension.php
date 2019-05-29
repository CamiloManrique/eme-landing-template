<?php


namespace Classes\Twig;


use Slim\Views\TwigExtension;
use Twig\TwigFunction;

class AssetExtension extends TwigExtension
{
    public function __construct()
    {

    }

    public function getName()
    {
        return 'asset';
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('asset', array($this, 'asset'))
        ];
    }

    public function asset ($path)
    {
        $app_url = isset($_ENV['APP_URL']) ? $_ENV['APP_URL'] : "http://localhost";
        return $app_url."/public/".ltrim($path, "/");
    }

}