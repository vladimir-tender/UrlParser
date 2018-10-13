<?php

declare(strict_types = 1);

namespace AppBundle\DTO;

use AppBundle\Types\UrlType;

class LinkSummaryDTO
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $urlHash;

    /**
     * @var int
     */
    private $parseTime;

    /**
     * @var int
     */
    private $imagesCount;

    /**
     * @var int
     */
    private $nestedLevel;

    /**
     * LinkSummaryDTO constructor.
     *
     * @param UrlType $url
     * @param int     $nestedLevel
     */
    public function __construct(UrlType $url, int $nestedLevel = 0)
    {
        $this->url = $url->getValue();
        $this->urlHash = md5($url->getValue());
        $this->imagesCount = 0;
        $this->parseTime = 0;
        $this->nestedLevel = $nestedLevel;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getUrlHash(): string
    {
        return $this->urlHash;
    }

    /**
     * @return int
     */
    public function getNestedLevel(): int
    {
        return $this->nestedLevel;
    }

    /**
     * @param int $parseTime
     */
    public function setParseTime(int $parseTime): void
    {
        $this->parseTime = $parseTime;
    }

    /**
     * @param int $imagesCount
     */
    public function setImagesCount(int $imagesCount): void
    {
        $this->imagesCount = $imagesCount;
    }

    /**
     * @return int
     */
    public function getParseTime(): int
    {
        return $this->parseTime;
    }

    /**
     * @return int
     */
    public function getImagesCount(): int
    {
        return $this->imagesCount;
    }
}
