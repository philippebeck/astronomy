<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class NeoController
 * @package App\Controller
 */
class NeoController extends MainController
{
    /**
     * @var null
     */
    private $startDate = null;

    /**
     * @var null
     */
    private $endDate = null;

    private function setDates()
    {
        if ($this->startDate === null || $this->endDate === null) {
            $this->startDate    = date("Y-m-d");
            $this->endDate      = date("Y-m-d", strtotime("+1 day"));
        }

        if ($this->checkArray($this->getPost())) {
            $this->startDate    = $this->getPost("start-date");
            $this->endDate      = $this->getPost("end-date");
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
        $this->setDates();

        $query = "https://api.nasa.gov/neo/rest/v1/feed?start_date="
            . $this->startDate
            . "&end_date="
            . $this->endDate
            . "&api_key="
            . NASA_API;

        $neo = $this->getApiData($query);
        $neo = $neo["near_earth_objects"];

        ksort($neo);

        return $this->render("front/neo.twig", ["neo" => $neo]);
    }
}
