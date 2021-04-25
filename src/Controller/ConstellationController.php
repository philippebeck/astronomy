<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Pam\Model\ModelFactory;
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

        return $this->render("front/constellations.twig", ["constellations" => $constellations]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function readMethod()
    {
        $constellation = ModelFactory::getModel("Constellation")->readData($this->getGet("id"));

        return $this->render("front/constellation.twig", ["constellation" => $constellation]);
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

        $constellation = ModelFactory::getModel("Constellation")->readData($this->getGet("id"));

        if ($this->checkArray($this->getPost())) {
            $data["description"] = (string) trim($this->getPost("description"));

            if (!empty($this->getFiles("name"))) {
                $this->getUploadedFile("img/constellation/", $constellation["name"]);

                $img        = "img/constellation/" . $constellation["name"] . $this->getExtension();
                $thumbnail  = "img/thumbnails/tn_" . $constellation["name"] . $this->getExtension();

                $this->getThumbnail($img, 300, $thumbnail);
            }

            ModelFactory::getModel("Constellation")->updateData(
                $this->getGet("id"), 
                $data
            );
            
            $this->setSession([
                "message"   => "Successful modification of the selected constellation !", 
                "type"      => "blue"
            ]);

            $this->redirect("admin");
        }

        return $this->render("back/updateConstellation.twig", ["constellation" => $constellation]);
    }
}
