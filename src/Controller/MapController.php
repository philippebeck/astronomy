<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Pam\Model\Factory\ModelFactory;
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

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $maps = ModelFactory::getModel("Map")->listData();

        return $this->render("map/maps.twig", ["maps" => $maps]);
    }

    private function setMapData()
    {
        $this->data["description"]  = $this->getPost()->getPostVar("description");
        $this->data["atlas_id"]     = $this->getPost()->getPostVar("atlas_id");
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
        $this->getFiles()->uploadFile("img/atlas/", $this->data["map_name"]);

        $img        = "img/atlas/" . $this->data["map_name"] . $this->getFiles()->setFileExtension();
        $thumbnail  = "img/thumbnails/tn_". $this->data["map_name"] . $this->getFiles()->setFileExtension();

        $this->service->getImage()->makeThumbnail($img, 300, $thumbnail);
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
            $this->setMapData();
            $this->setMapName();
            $this->setMapImage();

            ModelFactory::getModel("Map")->createData($this->data);
            $this->getSession()->createAlert("New map created successfully !", "green");

            $this->redirect("admin");
        }

        $atlases = ModelFactory::getModel("Atlas")->listData();

        return $this->render("map/createMap.twig", ["atlases" => $atlases]);
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

        $map = ModelFactory::getModel("Map")->readData($this->getGet()->getGetVar("id"));

        if (!empty($this->getPost()->getPostArray())) {
            $this->setMapData();

            if (!empty($this->getFiles()->getFileVar("name"))) {
                $this->data["map_name"] = $map["map_name"];
                $this->setMapImage();
            }

            ModelFactory::getModel("Map")->updateData($this->getGet()->getGetVar("id"), $this->data);
            $this->getSession()->createAlert("Successful modification of the selected map !", "blue");

            $this->redirect("admin");
        }

        $atlases = ModelFactory::getModel("Atlas")->listData();

        return $this->render("map/updateMap.twig", [
            "atlases"   => $atlases,
            "map"       => $map
            ]);
    }

    public function deleteMethod()
    {
        if ($this->service->getSecurity()->checkIsAdmin() !== true) {
            $this->redirect("home");
        }

        ModelFactory::getModel("Map")->deleteData($this->getGet()->getGetVar("id"));
        $this->getSession()->createAlert("Map actually deleted !", "red");

        $this->redirect("admin");
    }
}
