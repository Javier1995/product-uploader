<?php

namespace App\Command;

use App\Service\Importers\Importer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:upload-product',
    description: 'Tool to upload product data in csv format',
    hidden: false,
    aliases: ['app:product-importer']
)]
class UploadProductCommand extends Command
{
    private $entityManager;
    private $importer;

    public function __construct(Importer $importer)
    {
        $this->importer = $importer;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filePath', InputArgument::REQUIRED, 'Path to the CSV file')
            ->addOption('test', null, InputOption::VALUE_NONE, 'Run in test mode (does not insert data into the database)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $filePath = $input->getArgument('filePath');
        $isTestMode = $input->getOption('test');
        $io = new SymfonyStyle($input, $output);

        $result = $this->importer->file(Importer::CSV_IMPORTER)->import($filePath, $isTestMode);

        if ($isTestMode) {
            $io->warning('Test mode enabled. No data was inserted into the database.');
        }   

        $output->writeln([
            'Importing CSV file...',
            '==================',
            '',
        ]);

        sleep(1);

        $output->writeln([
            "<info>Total Processed: {$result['processed']}</info>",
            "<info>Successfully Imported: {$result['successful']}</info>"
        ]);

        // Display rows that were skipped
        if($result['errors']['rows'] > 0 ){
            "<info></info>";
            $io->error("Skipped: {$result['skipped']}. Please check the following:");
            $io->table($result['errors']['header'], $result['errors']['rows']);
        }
        return Command::SUCCESS;
    }
}
