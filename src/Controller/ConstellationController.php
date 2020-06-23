<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Pam\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ConstellationController
 * @package App\Controller
 */
class ConstellationController extends MainController
{
    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $constellations = ModelFactory::getModel("Constellation")->listData();

        return $this->render("constellation/listConstellations.twig", ["constellations" => $constellations]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function readMethod()
    {
        $constellation = ModelFactory::getModel("Constellation")->readData($this->getGet()->getGetVar("id"));

        return $this->render("constellation/constellation.twig", ["constellation" => $constellation]);
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

        $constellation = ModelFactory::getModel("Constellation")->readData($this->getGet()->getGetVar("id"));

        if (!empty($this->getPost()->getPostArray())) {
            $data["description"] = $this->getPost()->getPostVar("description");

            if (!empty($this->getFiles()->getFileVar("name"))) {
                $this->getFiles()->uploadFile("img/constellation/", $constellation["name"]);

                $img        = "img/constellation/" . $constellation["name"] . $this->getFiles()->setFileExtension();
                $thumbnail  = "img/thumbnails/tn_" . $constellation["name"] . $this->getFiles()->setFileExtension();

                $this->service->getImage()->makeThumbnail($img, 300, $thumbnail);
            }

            ModelFactory::getModel("Constellation")->updateData($this->getGet()->getGetVar("id"), $data);
            $this->getSession()->createAlert("Successful modification of the selected constellation !", "blue");

            $this->redirect("admin");
        }

        return $this->render("constellation/updateConstellation.twig", ["constellation" => $constellation]);
    }
}
