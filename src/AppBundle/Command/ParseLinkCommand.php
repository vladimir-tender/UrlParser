<?php

declare(strict_types = 1);


namespace AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseLinkCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this->setName("parse:link")
            ->addArgument("url", InputArgument::REQUIRED, "The url for parse")
            ->addArgument("max_links",  InputArgument::OPTIONAL, "The count of max links for parse")
            ->addArgument("max_level", InputArgument::OPTIONAL, "Max nested level for parse");
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $url = $input->getArgument('url');
        $max_links = $input->getArgument('max_links') ? (int) $input->getArgument('max_links') : null;
        $max_level = $input->getArgument('max_level') ? (int) $input->getArgument('max_level') : null;

        $this->getContainer()->get('url_parser')->handle($url, $max_links, $max_level);

        $output->writeln('Parse finished');
    }
}
