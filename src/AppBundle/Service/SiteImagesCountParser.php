<?php

declare(strict_types = 1);


namespace AppBundle\Service;


use AppBundle\DTO\LinkSummaryDTO;
use AppBundle\Types\UrlType;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Stopwatch\Stopwatch;

class SiteImagesCountParser
{
    private const MAX_NESTED_LEVEL = 10;

    /**
     * @var array | LinkSummaryDTO[]
     */
    private $linksForProcessing = [];

    /**
     * @var array | LinkSummaryDTO[]
     */
    private $linksProcessed = [];

    /**
     * @var int
     */
    private $maxProcessedLinks;

    /**
     * @var StatisticManager
     */
    private $statisticManager;

    /**
     * SiteImagesCountParser constructor.
     *
     * @param StatisticManager $statisticManager
     */
    public function __construct(StatisticManager $statisticManager)
    {
        $this->statisticManager = $statisticManager;
    }


    /**
     * @param string     $url
     * @param int | null $maxNestedLevel
     * @param int | null $maxProcessedLinks
     */
    public function handle(string $url, ?int $maxProcessedLinks = null, ?int $maxNestedLevel = null): void
    {
        $this->maxProcessedLinks = $maxProcessedLinks;

        if (!$maxNestedLevel) {
            $maxNestedLevel = self::MAX_NESTED_LEVEL;
        }

        $startUrl = new LinkSummaryDTO(new UrlType($url), 0);

        //TODO: parse images only from current domain

        $this->linksForProcessing[$startUrl->getUrlHash()] = $startUrl;

        do {
            $this->process($maxNestedLevel);
        } while (0 !== \count($this->linksForProcessing));

        $this->statisticManager->truncatePreviousData();
        $this->statisticManager->saveData($this->linksProcessed);
    }

    /**
     * @param int $maxNestedLevel
     */
    private function process(int $maxNestedLevel)
    {
        /** @var LinkSummaryDTO $link */
        foreach ($this->linksForProcessing as $processedLink) {
            $timer = new Stopwatch();
            $timer->start('link_handle');

            if ($maxNestedLevel >= $processedLink->getNestedLevel()) {

                $crawler = new Crawler(
                    file_get_contents(
                        $processedLink->getUrl(),
                        false,
                        stream_context_create(['http' => ['max_redirects' => 0]])
                    )
                );

                $processedLink->setImagesCount($crawler->filter('img')->count());
                $links = $crawler->filter('a');

                /** @var \DOMElement $link */
                foreach ($links as $link) {

                    $url = $link->getAttribute('href');

                    if (filter_var($url, FILTER_VALIDATE_URL) && !$this->isLinkExistInProcess($url)) {
                        try {
                            $this->linksForProcessing[md5($url)] = new LinkSummaryDTO(new UrlType($url), $processedLink->getNestedLevel() + 1);
                        } catch (\InvalidArgumentException $exception) {
                        }
                    }
                }
            }

            $duration = $timer->stop('link_handle')->getDuration();

            $processedLink->setParseTime($duration);
            $timer->reset();


            unset($this->linksForProcessing[$processedLink->getUrlHash()]);

            if ($maxNestedLevel >= $processedLink->getNestedLevel()) {
                $this->linksProcessed[$processedLink->getUrlHash()] = $processedLink;
            }

            if ($this->maxProcessedLinks && \count($this->linksProcessed) === $this->maxProcessedLinks) {
                $this->linksForProcessing = [];

                break;
            }
        }
    }

    /**
     * Check if link was processed or will be processed
     *
     * @param string $link
     *
     * @return bool
     */
    private function isLinkExistInProcess(string $link): bool
    {
        $linkHash = md5($link);

        if (array_key_exists($linkHash, $this->linksForProcessing) || array_key_exists($linkHash, $this->linksProcessed)) {
            return true;
        }

        return false;
    }
}
