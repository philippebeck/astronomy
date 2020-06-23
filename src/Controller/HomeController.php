<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends MainController
{
    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $query  = "https://api.nasa.gov/planetary/apod?concept_tags=True&api_key=" . NASA_API;
        $apod   = $this->service->getCurl()->getApiData($query);

        return $this->render("home.twig", ["apod" => $apod]);
    }
}
