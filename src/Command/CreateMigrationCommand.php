<?php

namespace DennisPansegrau\PimcoreContentMigrationBundle\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'content:migration:create',
    description: 'Creates a migration file of a specific content object.'
)]
class CreateMigrationCommand extends AbstractCommand
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output->writeln('<info>Hallo aus deinem Bundle-Command!</info>');

        return self::SUCCESS;
    }
}
