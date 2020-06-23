<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Pam\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AtlasController
 * @package App\Controller
 */
class AtlasController extends MainController
{
    /**
     * @var array
     */
    private $atlas = [];

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $maps       = ModelFactory::getModel("Atlas")->listAtlasMaps();
        $demoMaps   = [];

        foreach ($maps as $map) {
            if (strstr($map["map_name"], "04")) {
                $demoMaps[] = $map;
            }
        }

        return $this->render("atlas/atlas.twig", [
            "demoMaps" => $demoMaps
        ]);
    }

    private function setAtlasWiki()
    {
        $this->atlas["atlas_wiki"]  = str_replace("https://en.wikipedia.org/wiki/", "", $this->atlas["atlas_wiki"]);
        $this->atlas["author_wiki"] = str_replace("https://en.wikipedia.org/wiki/", "", $this->atlas["author_wiki"]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function createMethod()
    {
        if ($this->service->getSecurity()->checkIsAdmin() !== true) {
            $this->redirect("home");
        }

        if (!empty($this->getPost()->getPostArray())) {
            $this->atlas = $this->getPost()->getPostArray();

            $this->setAtlasWiki();

            ModelFactory::getModel("Atlas")->createData($this->atlas);
            $this->getSession()->createAlert("New atlas successfully created !", "green");

            $this->redirect("map!create");
        }

        return $this->render("atlas/createAtlas.twig");
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function readMethod()
    {
        $atlas      = ModelFactory::getModel("Atlas")->readData($this->getGet()->getGetVar("id"));
        $atlasMaps  = ModelFactory::getModel("Map")->listData($this->getGet()->getGetVar("id"), "atlas_id");

        return $this->render("atlas/atlasMaps.twig", ["atlas" => $atlas, "atlasMaps"  => $atlasMaps]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function updateMethod()
    {
        if ($this->service->getSecurity()->checkIsAdmin() !== true) {
            $this->redirect("home");
        }

        if (!empty($this->getPost()->getPostArray())) {
            $this->atlas  = $this->getPost()->getPostArray();

            $this->setAtlasWiki();

            ModelFactory::getModel("Atlas")->updateData($this->getGet()->getGetVar("id"), $this->atlas);
            $this->getSession()->createAlert("Successful modification of the selected atlas !", "blue");

            $this->redirect("admin");
        }

        $atlas = ModelFactory::getModel("Atlas")->readData($this->getGet()->getGetVar("id"));

        return $this->render("atlas/updateAtlas.twig", ["atlas" => $atlas]);
    }

    public function deleteMethod()
    {
        if ($this->service->getSecurity()->checkIsAdmin() !== true) {
            $this->redirect("home");
        }

        $maps = ModelFactory::getModel("Map")->listData($this->getGet()->getGetVar("id"), "atlas_id");

        foreach ($maps as $map) {
            ModelFactory::getModel("Map")->deleteData($map["id"]);
        }

        ModelFactory::getModel("Atlas")->deleteData($this->getGet()->getGetVar("id"));
        $this->getSession()->createAlert("Atlas permanently deleted !", "red");

        $this->redirect("admin");

    }
}
