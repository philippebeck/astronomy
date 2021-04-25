<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Pam\Model\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MapController
 * @package App\Controller
 */
class MapController extends MainController
{
    /**
     * @var array
     */
    private $data = [];

    public function defaultMethod()
    {
        $this->redirect("atlas");
    }

    // ******************** SETTERS ******************** \\

    private function setMapData()
    {
        $this->data["description"]  = (string) trim($this->getPost("description"));
        $this->data["atlas_id"]     = (int) $this->getPost("atlas_id");
    }

    private function setMapName()
    {
        $maps       = ModelFactory::getModel("Atlas")->listAtlasMaps();
        $mapCount   = 0;

        foreach ($maps as $map) {
            if ($map["atlas_id"] === $this->data["atlas_id"]) {

                preg_match_all('/[A-Z]/', $map["author_name"], $authorInitials);
                $authorInitials = strtolower(implode($authorInitials[0]));

                if (in_array($map["atlas_name"], $map)) {
                    $mapCount++;
                }

                $this->data["map_name"] =
                    $map["published_year"] .
                    $authorInitials .
                    ($mapCount + 1);
            }
        }
    }

    private function setMapImage()
    {
        $this->getUploadedFile("img/atlas/", $this->data["map_name"]);

        $img        = "img/atlas/" . $this->data["map_name"] . $this->getExtension();
        $thumbnail  = "img/thumbnails/tn_". $this->data["map_name"] . $this->getExtension();

        $this->getThumbnail($img, 300, $thumbnail);
    }

    // ******************** CRUD ******************** \\

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

            $this->setMapData();
            $this->setMapName();
            $this->setMapImage();

            ModelFactory::getModel("Map")->createData($this->data);

            $this->setSession([
                "message"   => "New map created successfully !", 
                "type"      => "green"
            ]);

            $this->redirect("admin");
        }

        $atlases = ModelFactory::getModel("Atlas")->listData();

        return $this->render("back/createMap.twig", ["atlases" => $atlases]);
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

        $map = ModelFactory::getModel("Map")->readData($this->getGet("id"));

        if ($this->checkArray($this->getPost())) {
            $this->setMapData();

            if (!empty($this->getFiles("name"))) {

                $this->data["map_name"] = $map["map_name"];
                $this->setMapImage();
            }

            ModelFactory::getModel("Map")->updateData(
                $this->getGet("id"), 
                $this->data
            );

            $this->setSession([
                "message"   => "Successful modification of the selected map !", 
                "type"      => "blue"
            ]);

            $this->redirect("admin");
        }

        $atlases = ModelFactory::getModel("Atlas")->listData();

        return $this->render("back/updateMap.twig", [
            "atlases"   => $atlases,
            "map"       => $map
        ]);
    }

    public function deleteMethod()
    {
        if ($this->checkAdmin() !== true) {
            $this->redirect("home");
        }

        ModelFactory::getModel("Map")->deleteData($this->getGet("id"));

        $this->setSession([
            "message"   => "Map actually deleted !", 
            "type"      => "red"
        ]);

        $this->redirect("admin");
    }
}
