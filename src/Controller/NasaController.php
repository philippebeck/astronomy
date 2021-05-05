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
    private $search = "";

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
        $this->search = "orion";

        $this->setSearch();
        $this->setQuery();

        $nasa = $this->getApiData($this->query);
        $nasa = $nasa["collection"];
        $nasa = $nasa["items"];

        return $this->render("front/nasa.twig", ["nasa" => $nasa]);
    }

    // ******************** SETTERS ******************** \\

    private function setSearch()
    {
        if ($this->checkArray($this->getPost(), "search")) {
            $this->search = $this->getString($this->getPost("search"));
        }
    }

    private function setQuery()
    {
        $this->query = "https://images-api.nasa.gov/search?q=" . $this->search;
    }
}
