<?php

namespace App\Command;

use App\Repository\SortieRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:archive-sortie',
    description: 'Archive les sorties de plus d un mois dans la table',
)]
class ArchivedSortieCommand extends Command
{
    public function __construct(private SortieRepository $repository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')) {
            $io->note("Dry mode enabled");
        } else {
           $this->repository->ArchivedEtat();
        }

        $io->success('Etat des sorties passées en Archivées avec succès');

        return Command::SUCCESS;
    }
}
