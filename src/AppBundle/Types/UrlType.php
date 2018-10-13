<?php

declare(strict_types = 1);


namespace AppBundle\Types;

/**
 *  Class for work with valid url
 */
class UrlType
{
    /**
     * @var string
     */
    private $url;

    /**
     * UrlType constructor.
     *
     * @param string $url
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $this->url = $url;
        } else {
            throw new \InvalidArgumentException('Invalid url.');
        }
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->url;
    }
}
