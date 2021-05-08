<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class NasaController
 * @package App\Controller
 */
class NasaController extends MainController
{
    /**
     * @var string
     */
    private $endPoint = "";

    /**
     * @var string
     */
    private $param = "";

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
        $this->endPoint = "search?q=";
        $this->param    = "nebula";

        $this->setSearch();
        $this->setQuery();

        $search     = $this->getApiData($this->query);
        $collection = $search["collection"];
        $items      = $collection["items"];

        return $this->render("front/nasa.twig", ["items" => $items]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function imageMethod()
    {
        $this->endPoint = "image/";

        $this->setMedia();
        $this->setQuery();

        $image = $this->getApiData($this->query);

        return $this->render("front/nasaImage.twig", ["image" => $image]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function videoMethod()
    {
        $this->endPoint = "video/";

        $this->setMedia();
        $this->setQuery();

        $video = $this->getApiData($this->query);

        return $this->render("front/nasaVideo.twig", ["video" => $video]);
    }

    // ******************** SETTERS ******************** \\

    private function setSearch()
    {
        if ($this->checkArray($this->getPost(), "search")) {
            $this->param = $this->getString($this->getPost("search"));
        }
    }

    private function setMedia()
    {
        if ($this->checkArray($this->getGet(), "id")) {
            $this->param = $this->getGet("id");
        }
    }

    private function setQuery()
    {
        if ($this->endPoint !== "search?q=") {
            $this->query = "https://images-assets.nasa.gov/" . $this->endPoint . $this->param . "/metadata.json";
        }

        if ($this->endPoint === "search?q=") {
            $this->query = "https://images-api.nasa.gov/" . $this->endPoint . $this->param;
        }
    }
}
