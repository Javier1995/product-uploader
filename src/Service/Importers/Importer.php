<?php

namespace App\Service\Importers;

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;


class Importer {
    public const CSV_IMPORTER = 0;
    
    //Escalable for future importers
    public const XML_IMPORTER = 1;
        //Escalable for future importers
    public const JSON_IMPORTER = 2;
    
    private EntityManagerInterface $entityManager;

    private ContainerInterface $container;

    public const IMPORTERS_FILES = [
        self::CSV_IMPORTER => CSVImporter::class
       /*  self::XML_IMPORTER => 'XMLImporter:class' */
        /* self::JSON_IMPORTER => 'JSONImporter:class' */
    ];
    
    public function __construct(
        EntityManagerInterface $entityManager,
        ContainerInterface $container
    ) {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }


    public function file(int $type): ProductImportInterface
    {
        if (!isset(self::IMPORTERS_FILES[$type])) {
            throw new \InvalidArgumentException('Invalid importer type');
        }

        
        /**  
         *   @var ProductImportInterface $importer 
        */
        $importer = $this->container->get(self::IMPORTERS_FILES[$type]);

        if (!$importer instanceof ProductImportInterface) {
            throw new \LogicException("Importer must implement ProductImportInterface.");
        }

        return $importer;
    }


}