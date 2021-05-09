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
     * @var string
     */
    private $rover = "perseverance";

    /**
     * @var null
     */
    private $date = null;

    /**
     * @var string
     */
    private $query = "";

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $this->setDate();
        $this->setParams();
        $this->setQuery();

        $photos = $this->getPhotos();

        if (!$this->checkArray($photos)) {
            $this->date = date("Y-m-d", strtotime("-2 days"));

            $this->setQuery();
            $photos = $this->getPhotos();
        }

        $params = [
            "rover" => $this->rover,
            "date"  => $this->date
        ];

        return $this->render("front/mars.twig", [
            "photos"    => $photos,
            "params"    => $params
        ]);
    }

    /**
     * @return array
     */
    private function getPhotos()
    {
        $mars = $this->getApiData($this->query);

        return $mars["photos"];
    }

     // *********************************************** \\
    // ******************** SETTERS ******************** \\

    private function setDate()
    {
        if ($this->date === null) {
            $this->date = date("Y-m-d", strtotime("-1 day"));
        }
    }

    private function setParams()
    {
        if ($this->checkArray($this->getPost())) {
            $this->rover    = (string) $this->getPost("rover");
            $this->date     = (string) $this->getPost("date");
        }
    }

    private function setQuery()
    {
        $this->query = "https://api.nasa.gov/mars-photos/api/v1/rovers/"
            . $this->rover
            . "/photos?earth_date=" 
            . $this->date
            . "&api_key="
            . NASA_API;
    }
}
