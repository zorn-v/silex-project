<?php

namespace App;

class Application extends \Silex\Application
{
    use \Silex\Application\TwigTrait;
    use \Silex\Application\FormTrait;
    use \Silex\Application\SecurityTrait;
    use \Silex\Application\UrlGeneratorTrait;

    public function redirectToRoute($route, $parameters = [])
    {
        return $this->redirect($this->path($route, $parameters));
    }
}
