<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:upload-product',
    description: 'Tool to upload product data in csv format'
)]
class UploadProductCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ->setDescription('Imports products from a CSV file into the database.')
        ->addArgument('file', InputArgument::REQUIRED, 'Path to the CSV file to import');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        $io = new SymfonyStyle($input, $output);
        if (!file_exists($filePath)) {
            $io->error("File not found: $filePath");
            return Command::FAILURE;
        }

        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        if (strtolower($fileExtension) !== 'csv') {
            $io->error('<error>Invalid file type. Only CSV files are allowed.</error>');
            return Command::FAILURE;
        }    
        
        $file = fopen($filePath, 'r');
    
        fgetcsv($file);

        //counters
        $processed = 0;
        $successful = 0;
        $skipped = 0;
        $notImported = [];

        while (($data = fgetcsv($file)) !== false) {
            $processed++;
    
            $stock = $data[3]??0;

            $price = $data[4]??0;

            if(is_numeric($price)){
                $price = (float)$price;
            }else {
                $notImported[] = $data;
                $skipped++;
                continue;
            }

            if(is_numeric($stock)){
                $stock = (int)$stock;
            }else{
                $notImported[] = $data;
                $skipped++;
                continue;
            }
            $isDiscontinued = strtolower( $data[5]?? '' ) === 'yes';
    
            if (($stock < 10 && $price < 5) || $price > 1000) {
                $notImported[] = $data;
                $skipped++;
                continue;
            }
            try {
            $product = new Product();
            $product->setstrtProductName($data[1])
                    ->setStrProductDesc($data[2])
                    ->setStrProductCode($data[0])
                    ->setStockLevel($stock)
                    ->setPrice($price)
                    ->setDtmDiscontinued($isDiscontinued ? new \DateTime() : null);
                $this->entityManager->persist($product);
                $this->entityManager->flush();
                $successful++;
            } catch (\Exception $e) {
                $notImported[] = $data;
                $skipped++;
            }
        }
    
        fclose($file);
        
        $io->info("Processed: $processed");
        $io->text("Successful: $successful");
        $io->text("Skipped: $skipped. See them down below in the following table:");
        if($skipped > 0){
            $io->table(
                ["Product Code","Product Name",	"Product Description","Stock","Cost in GBP","Discontinued"
            ],
                $notImported
            );

        }
        return Command::SUCCESS;
    }
}
