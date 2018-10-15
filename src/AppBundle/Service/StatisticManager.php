<?php

declare(strict_types = 1);


namespace AppBundle\Service;


use AppBundle\DTO\LinkSummaryDTO;
use AppBundle\Entity\Statistic;
use Doctrine\ORM\EntityManagerInterface;

class StatisticManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SiteImagesCountParser constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Remove old statistic data
     */
    public function truncatePreviousData(): void
    {
        $this->entityManager->createQueryBuilder()
            ->delete()
            ->from(Statistic::class, 's')
            ->getQuery()
            ->getResult();
    }

    /**
     * Save last parse statistic
     * @param LinkSummaryDTO[] | array
     */
    public function saveData(array $links): void
    {
        /** @var LinkSummaryDTO $link */
        foreach ($links as $link) {
            $data = new Statistic($link->getUrl(), $link->getImagesCount(), $link->getParseTime());
            $this->entityManager->persist($data);
        }
        $this->entityManager->flush();
    }
}
