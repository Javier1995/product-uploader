<?php
namespace App\Service\Importers;


Interface ProductImportInterface
{
    /*
        * Import products from a file
        *
        * @param string $filePath 
        * @param bool $isTestMode Acivate this mode to avoid saving data into database
        * @return array 
    */
   public function import(string $filePath, bool $isTestMode): array;
}