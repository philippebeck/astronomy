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
    private $rover = "perseverance";

    /**
     * @var null
     */
    private $date = null;

    private function setDate()
    {
        if ($this->date === null) {
            $this->date = date("Y-m-d", strtotime("-5 days"));
        }
    }

    private function getParams()
    {
        if ($this->checkArray($this->getPost())) {
            $this->rover    = (string) $this->getPost("rover");
            $this->date     = (string) $this->getPost("date");
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
            . "&api_key="
            . NASA_API;

        $mars = $this->getApiData($query);
        $mars = $mars["photos"];

        $params = [
            "rover" => $this->rover,
            "date"  => $this->date
        ];

        return $this->render("front/mars.twig", [
            "mars"      => $mars,
            "params"    => $params
        ]);
    }
}
