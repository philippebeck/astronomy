<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MarsController
 * @package App\Controller
 */
class MarsController extends MainController
{
    /**
     * @var null
     */
    private $rover = "curiosity";

    /**
     * @var null
     */
    private $camera = "navcam";

    /**
     * @var null
     */
    private $date = null;

    /**
     * @var null
     */
    private $page = 1;

    private function setDate()
    {
        if ($this->date === null) {
            $this->date = date("Y-m-d", strtotime("-1 week"));
        }
    }

    private function getParams()
    {
        if (!empty($this->getPost()->getPostArray())) {
            $this->rover    = (string) $this->getPost()->getPostVar("rover");
            $this->camera   = (string) $this->getPost()->getPostVar("camera");
            $this->date     = (string) $this->getPost()->getPostVar("date");
            $this->page     = (string) $this->getPost()->getPostVar("page");
        }
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $this->setDate();
        $this->getParams();

        $query = "https://api.nasa.gov/mars-photos/api/v1/rovers/"
            . $this->rover
            . "/photos?earth_date="
            . $this->date
            . "&camera="
            . $this->camera
            . "&page="
            . $this->page
            . "&api_key="
            . NASA_API;

        $mars = $this->service->getCurl()->getApiData($query);
        $mars = $mars["photos"];

        $params = [
            "rover"     => $this->rover,
            "date"      => $this->date,
            "camera"    => $this->camera,
            "page"      => $this->page,

        ];

        return $this->render("mars/mars.twig", [
            "mars"      => $mars,
            "params"    => $params
        ]);
    }
}
