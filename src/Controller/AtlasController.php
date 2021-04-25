<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Pam\Model\ModelFactory;
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

        return $this->render("front/atlas/atlases.twig", ["demoMaps" => $demoMaps]);
    }

    private function setAtlasData()
    {
        $this->atlas["atlas_wiki"]  = (string) trim($this->getPost("atlas_wiki"));
        $this->atlas["author_wiki"] = (string) trim($this->getPost("author_wiki"));

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
        if ($this->checkAdmin() !== true) {
            $this->redirect("home");
        }

        if ($this->checkArray($this->getPost())) {
            $this->setAtlasData();

            ModelFactory::getModel("Atlas")->createData($this->atlas);

            $this->setSession([
                "New atlas successfully created !", 
                "green"
            ]);

            $this->redirect("map!create");
        }

        return $this->render("back/atlas/createAtlas.twig");
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function readMethod()
    {
        $atlas      = ModelFactory::getModel("Atlas")->readData($this->getGet("id"));
        $atlasMaps  = ModelFactory::getModel("Map")->listData($this->getGet("id"), "atlas_id");

        return $this->render("front/atlas/atlasMaps.twig", [
            "atlas"         => $atlas,
            "atlasMaps"     => $atlasMaps
        ]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function updateMethod()
    {
        if ($this->checkAdmin() !== true) {
            $this->redirect("home");
        }

        if ($this->checkArray($this->getPost())) {
            $this->atlas = $this->getPost();

            $this->setAtlasData();

            ModelFactory::getModel("Atlas")->updateData(
                $this->getGet("id"), 
                $this->atlas
            );

            $this->setSession([
                "Successful modification of the selected atlas !", 
                "blue"
            ]);

            $this->redirect("admin");
        }

        $atlas = ModelFactory::getModel("Atlas")->readData($this->getGet("id"));

        return $this->render("back/atlas/updateAtlas.twig", ["atlas" => $atlas]);
    }

    public function deleteMethod()
    {
        if ($this->checkAdmin() !== true) {
            $this->redirect("home");
        }

        $maps = ModelFactory::getModel("Map")->listData(
            $this->getGet("id"), 
            "atlas_id"
        );

        foreach ($maps as $map) {
            ModelFactory::getModel("Map")->deleteData($map["id"]);
        }

        ModelFactory::getModel("Atlas")->deleteData($this->getGet("id"));

        $this->setSession([
            "Atlas permanently deleted !", 
            "red"
        ]);

        $this->redirect("admin");

    }
}
