<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class HubbleController
 * @package App\Controller
 */
class HubbleController extends MainController
{
    /**
     * @var string
     */
    private $type = "";

    /**
     * @var string
     */
    private $item = "";

    /**
     * @var string
     */
    private $page = "";

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
        $this->type = "news_release";
        $this->item = "/last";

        $this->setQuery();

        $hubble = $this->getApiData($this->query);

        return $this->render("front/hubble/hubble.twig", ["hubble" => $hubble]);
    }

    // ******************** SETTERS ******************** \\

    private function setItem()
    {
        if ($this->checkArray($this->getPost())) {
            $this->item = "/" . (string) $this->getPost("item");

        } elseif ($this->checkArray($this->getGet())) {
            $this->item = "/" . (string) $this->getGet("id");

        } else {
            $this->defaultMethod();
        }
    }

    private function setPage()
    {
        $this->page = "?page=1";

        if (!empty($this->getGet("page"))) {
            $this->page = "?page=" . (string) $this->getGet("page");
        }
    }

    private function setQuery()
    {
        $this->query = "https://hubblesite.org/api/v3/" . $this->type . $this->item . $this->page;
    }

    // ******************** GETTERS ******************** \\

    /**
     * @param array $image
     * @param string $pattern
     * @return array
     */
    private function getFilteredImage(array $image, string $pattern)
    {
        $file = implode(preg_grep($pattern, array_column($image["image_files"], "file_url")));

        if ($file !== "") {
            $fileId = array_search($file, array_column($image["image_files"], "file_url"));
            array_splice($image["image_files"], $fileId, 1);
        }

        return $image;
    }

    /**
     * @param array $image
     * @return array
     */
    private function getFilteredImages(array $image)
    {
        $image =
            $this->getFilteredImage(
                $this->getFilteredImage(
                    $this->getFilteredImage(
                        $this->getFilteredImage(
                            $this->getFilteredImage(
                                $this->getFilteredImage(
                                    $image,
                                    "/mini_thumb/"),
                                "/thumb/"),
                            "/small_web/"),
                        "/.pdf/"),
                    "/.tif/"),
                "/bw_/")
        ;

        $image["image_files"] = array_column($image["image_files"], "file_url");

        return $image;
    }

    /**
     * @param array $video
     * @param string $pattern
     * @return array
     */
    private function getFilteredVideo(array $video, string $pattern)
    {
        $file = implode(preg_grep($pattern, array_column($video["video_files"], "file_url")));

        if ($file !== "") {
            $fileId = array_search($file, array_column($video["video_files"], "file_url"));
            $video["video_files"] = array_slice($video["video_files"], $fileId, 1);
        }

        return $video;
    }

    /**
     * @param array $video
     * @return mixed
     */
    private function getFilteredVideos(array $video)
    {
        $video = $this->getFilteredVideo($video,"/.mp4/");
        $video["video_files"] = array_column($video["video_files"], "file_url");

        return $video;
    }

    // ******************** NEWS ******************** \\

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function newsMethod()
    {
        $this->type = "news";

        $this->setPage();
        $this->setQuery();

        $news = $this->getApiData($this->query);

        return $this->render("front/hubble/news/hubbleNews.twig", ["news" => $news]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function releaseMethod()
    {
        $this->type = "news_release";

        $this->setItem();
        $this->setQuery();

        $release = $this->getApiData($this->query);

        return $this->render("front/hubble/news/hubbleRelease.twig", ["release" => $release]);
    }

    // ******************** IMAGES ******************** \\

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function imagesMethod()
    {
        $this->type = "images";

        $this->setPage();
        $this->setQuery();

        $images = $this->getApiData($this->query);

        return $this->render("front/hubble/images/hubbleImages.twig", ["images" => $images]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function imageMethod()
    {
        $this->type = "image";

        $this->setItem();
        $this->setQuery();

        $image = $this->getApiData($this->query);
        $image = $this->getFilteredImages($image);

        return $this->render("front/hubble/images/hubbleImage.twig", ["image" => $image]);
    }

    // ******************** VIDEOS ******************** \\

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function videosMethod()
    {
        $this->type = "videos";

        $this->setPage();
        $this->setQuery();

        $videos = $this->getApiData($this->query);

        return $this->render("front/hubble/videos/hubbleVideos.twig", ["videos" => $videos]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function videoMethod()
    {
        $this->type = "video";

        $this->setItem();
        $this->setQuery();

        $video = $this->getApiData($this->query);
        $video = $this->getFilteredVideos($video);

        return $this->render("front/hubble/videos/hubbleVideo.twig", ["video" => $video]);
    }
}
