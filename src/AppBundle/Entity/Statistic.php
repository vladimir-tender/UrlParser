<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statistic
 *
 * @ORM\Table(name="statistic")
 * @ORM\Entity()
 */
class Statistic
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     */
    private $url;

    /**
     * @var int
     *
     * @ORM\Column(name="imgCount", type="integer")
     */
    private $imgCount;

    /**
     * @var int
     *
     * @ORM\Column(name="parseTime", type="integer")
     */
    private $parseTime;

    /**
     * Statistic constructor.
     *
     * @param string $url
     * @param int    $imgCount
     * @param int    $parseTime
     */
    public function __construct(string $url, int $imgCount, int $parseTime)
    {
        $this->url = $url;
        $this->imgCount = $imgCount;
        $this->parseTime = $parseTime;
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Statistic
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set imgCount.
     *
     * @param int $imgCount
     *
     * @return Statistic
     */
    public function setImgCount($imgCount)
    {
        $this->imgCount = $imgCount;

        return $this;
    }

    /**
     * Get imgCount.
     *
     * @return int
     */
    public function getImgCount()
    {
        return $this->imgCount;
    }

    /**
     * Set parseTime.
     *
     * @param int $parseTime
     *
     * @return Statistic
     */
    public function setParseTime($parseTime)
    {
        $this->parseTime = $parseTime;

        return $this;
    }

    /**
     * Get parseTime.
     *
     * @return int
     */
    public function getParseTime()
    {
        return $this->parseTime;
    }
}
