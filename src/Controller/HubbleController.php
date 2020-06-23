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
     * @var null
     */
    private $query = null;

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

        $hubble = $this->service->getCurl()->getApiData($this->query);

        return $this->render("hubble/hubble.twig", ["hubble" => $hubble]);
    }

    private function setItem()
    {
        if (empty($this->getPost()->getPostArray())) {
            $this->defaultMethod();
        }

        $this->item = "/" . (string) $this->getPost()->getPostVar("item");
    }

    private function setPage()
    {
        $this->page = "?page=1";

        if (!empty($this->getGet()->getGetVar("page"))) {
            $this->page = "?page=" . (string) $this->getGet()->getGetVar("page");
        }
    }

    private function setQuery()
    {
        $this->query = "https://hubblesite.org/api/v3/" . $this->type . $this->item . $this->page;
    }

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

        $news = $this->service->getCurl()->getApiData($this->query);

        return $this->render("hubble/news/hubbleNews.twig", ["news" => $news]);
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

        $release = $this->service->getCurl()->getApiData($this->query);

        return $this->render("hubble/news/hubbleRelease.twig", ["release" => $release]);
    }

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

        $images = $this->service->getCurl()->getApiData($this->query);

        return $this->render("hubble/images/hubbleImages.twig", ["images" => $images]);
    }

    /**
     * @param array $image
     * @param string $pattern
     * @return array
     */
    private function filterImage(array $image, string $pattern)
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
    private function filterImages(array $image)
    {
        $image =
            $this->filterImage(
                $this->filterImage(
                    $this->filterImage(
                        $this->filterImage(
                            $this->filterImage(
                                $this->filterImage(
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

        $image = $this->service->getCurl()->getApiData($this->query);
        $image = $this->filterImages($image);

        return $this->render("hubble/images/hubbleImage.twig", ["image" => $image]);
    }

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

        $videos = $this->service->getCurl()->getApiData($this->query);

        return $this->render("hubble/videos/hubbleVideos.twig", ["videos" => $videos]);
    }

    /**
     * @param array $video
     * @param string $pattern
     * @return array
     */
    private function filterVideo(array $video, string $pattern)
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
    private function filterVideos(array $video)
    {
        $video = $this->filterVideo($video,"/.mp4/");
        $video["video_files"] = array_column($video["video_files"], "file_url");

        return $video;
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

        $video = $this->service->getCurl()->getApiData($this->query);
        $video = $this->filterVideos($video);

        return $this->render("hubble/videos/hubbleVideo.twig", ["video" => $video]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function glossaryMethod()
    {
        $this->type = "glossary";
        $this->page = "?page=all";

        $this->setQuery();

        $glossary = $this->service->getCurl()->getApiData($this->query);

        return $this->render("hubble/hubbleGlossary.twig", ["glossary" => $glossary]);
    }
}
