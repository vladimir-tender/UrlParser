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
     * @var string
     */
    private $imageSearchPattern;

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

        $this->resolveImageSearchPattern($url);

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

                $processedLink->setImagesCount($crawler->filter($this->imageSearchPattern)->count());
                $links = $crawler->filter('a[href^="http"]')->extract(['href']);

                /** @var string $link */
                foreach ($links as $link) {

                    if (filter_var($link, FILTER_VALIDATE_URL) && !$this->isLinkExistInProcess($link)) {
                        try {
                            $this->linksForProcessing[md5($link)] = new LinkSummaryDTO(new UrlType($link), $processedLink->getNestedLevel() + 1);
                        } catch (\InvalidArgumentException $exception) {
                        }
                    }
                }
            }

            $processedLink->setParseTime($timer->stop('link_handle')->getDuration());
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

    /**
     * Ignore images from subdomains, other web-sites
     *
     * @param string $url
     */
    private function resolveImageSearchPattern(string $url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        $scheme = parse_url($url, PHP_URL_SCHEME);

        $pattern = 'img[src^='.$scheme.'\:\/\/'.$host.'], img[src^='.$scheme.'\:\/\/www.'.$host.'], img[src^=\/]';
        $pattern = str_replace('.', '\.', $pattern);

        $this->imageSearchPattern = $pattern;
    }
}
