<?php

namespace App\Service\Importers;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;


class CSVImporter implements ProductImportInterface {
    private array $result = ['processed' => 0, 'successful' => 0, 'skipped' => 0, 'errors' => []];
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

   
    public function import(string $filePath, bool $isTestMode = false): array {

        if(!$this->checkIfTableExists('product') && !$isTestMode) {
            throw new \InvalidArgumentException('Table product does not exist. Please make sure to run migrations first.');
        }
        
        if (!file_exists($filePath) || pathinfo($filePath, PATHINFO_EXTENSION) !== 'csv') {
            throw new \InvalidArgumentException('Invalid file format. Only CSV files are allowed.');
        }

        $handle = fopen($filePath, 'r');

        // Skip the header row by reading it and not doing anything with it
        fgetcsv($handle); // Skip the header;

        if ($handle !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $this->result['processed']++;
                

                // check if the row has all the required columns otherwise skip the row
                if (count($data) < 6) {
                    $this->result['skipped']++;
                    $this->result['errors']['header'] = $this->headers();
                    $this->result['errors']['rows'][] = $data;
                    continue;
                }

                // Map data by column
                $productCode = $data[0]?? '';
                $productName = $data[1]?? '';
                $productDesc = $data[2]?? '';
                $stockLevel  = $data[3]?? 0;
                $price       = $data[4]?? 0;

                //validate the data type
                if(is_numeric($price)){
                    $price = (float)$price;
                }else {
                    $this->result['errors']['rows'][] = $data;
                    $this->result['skipped']++;
                    continue;
                }
                if(is_numeric($stockLevel)){
                    $stockLevel = (int)$stockLevel;
                }else{
                    $this->result['errors']['rows'][] = $data;
                    $this->result['skipped']++;
                    continue;
                }
                $isDiscontinued = strtolower($data[5] ?? '') === 'yes';

                // Follow buiness rules based on PDF file
                if (($price < 5 && $stockLevel < 10) || $price > 1000) {
                    $this->result['skipped']++;
                    $this->result['errors']['rows'][] = $data;
                    continue;
                }

                try {
                    $product = new Product();
                    $product->setStrProductCode($productCode)
                        ->setstrtProductName($productName)
                        ->setStrProductDesc($productDesc)
                        ->setStockLevel($stockLevel)
                        ->setPrice($price);

                    if ($isDiscontinued) {
                        $product->setDtmDiscontinued(new \DateTime());
                    }
                    // test mode does not insert data into the database
                    if (!$isTestMode) {
                        $this->entityManager->persist($product);
                        $this->entityManager->flush();
                    }
                    
                    $this->result['successful']++;
                } catch (\Exception $e) {
                    $this->result['skipped']++;
                    $this->result['errors']['header'] = $this->headers();
                    $this->result['errors']['rows'][] = $data;
                    
                }
            }
            fclose($handle);
        }
        return $this->result;
    }

    public function checkIfTableExists(string $tableName): bool
    {
        $schemaManager = $this->entityManager->getConnection()->createSchemaManager();
        return $schemaManager->tablesExist([$tableName]);
    }

    public function headers(): array {
        return [
           'Product Code', 
           'Product Name', 
           'Product Description', 
           'Stock Level', 
           'Price', 
           'Discontinued'
       ];
   }

}