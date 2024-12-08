<?php

namespace App\Tests\Unit\Service;

use App\Service\Importers\CSVImporter;
use App\Service\Importers\Importer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class CsvImporterTest extends KernelTestCase
{
    private $testFilePath;
    private $entityManager;

    protected function setUp(): void
    {
        if (!class_exists(CSVImporter::class)) {
            self::markTestSkipped('CSVImporter class does not exist');
        }

        if (!class_exists(Importer::class)) {
            self::markTestSkipped('Importer class does not exist');
        }
        parent::setUp();
        self::bootKernel();
        $this->testFilePath = self::$kernel->getContainer()->getParameter('kernel.project_dir') . '/public/uploads/stock.csv';

        $this->entityManager =self::$kernel->getContainer()->get('doctrine')->getManager();

       
    }

    public function testCsvFileExists(): void
    {
        self::assertFileExists($this->testFilePath);
    }

    public function testCsvFileIsCsv(): void
    {
        $isCsv = pathinfo($this->testFilePath, PATHINFO_EXTENSION) === 'csv';
        self::assertTrue($isCsv);
    }

    public function testImporterIsValid(): void
    {
        $importer = new Importer(self::$kernel->getContainer()->get('doctrine.orm.entity_manager'), self::$kernel->getContainer());
        $csvImporter = $importer->file(Importer::CSV_IMPORTER);
        self::assertInstanceOf(CSVImporter::class, $csvImporter);
    }

    public function testCsvImporterImports(): void
    {
        $importer = new Importer(self::$kernel->getContainer()->get('doctrine.orm.entity_manager'), self::$kernel->getContainer());
        $csvImporter = $importer->file(Importer::CSV_IMPORTER);
        $result = $csvImporter->import($this->testFilePath, true);
        self::assertIsArray($result);
        self::assertArrayHasKey('processed', $result);
        self::assertArrayHasKey('successful', $result);
        self::assertArrayHasKey('skipped', $result);
        self::assertArrayHasKey('errors', $result);
    }


    public function testCsvImporterImportsData(): void
    {
        $importer = new Importer(self::$kernel->getContainer()->get('doctrine.orm.entity_manager'), self::$kernel->getContainer());
        $csvImporter = $importer->file(Importer::CSV_IMPORTER);
        $result = $csvImporter->import($this->testFilePath, true);
        self::assertGreaterThan(0, $result['processed']);
        self::assertGreaterThan(0, $result['successful']);
        self::assertGreaterThan(0, $result['skipped']);
        self::assertIsArray($result['errors']);
    }

    public function testCsvImporterImportsDataInTestMode(): void
    {
        $importer = new Importer(self::$kernel->getContainer()->get('doctrine.orm.entity_manager'), self::$kernel->getContainer());
        $csvImporter = $importer->file(Importer::CSV_IMPORTER);
        $result = $csvImporter->import($this->testFilePath, true);
        self::assertIsArray($result);
        self::assertEquals(29, $result['processed']);
        self::assertEquals(23, $result['successful']);
        self::assertEquals(6, $result['skipped']);
       
    }

    public function testCsvImporterImportsDataInProductionModeForTheFirstTime(): void
    {
         
         $connection = $this->entityManager->getConnection();
         $platform = $connection->getDatabasePlatform();
         $connection->executeStatement(
             $platform->getTruncateTableSQL('product', true)
         );

        $importer = new Importer(self::$kernel->getContainer()->get('doctrine.orm.entity_manager'), self::$kernel->getContainer());
        $csvImporter = $importer->file(Importer::CSV_IMPORTER);
        $result = $csvImporter->import($this->testFilePath, false);
        self::assertIsArray($result);
        self::assertEquals(29, $result['processed']);
        self::assertEquals(23, $result['successful']);
        self::assertEquals(6, $result['skipped']);
       
    }

    public function testCsvImporterImportsDataInProductionModeWhenDataAlreadyExists(): void
    {
        $importer = new Importer(self::$kernel->getContainer()->get('doctrine.orm.entity_manager'), self::$kernel->getContainer());
        $csvImporter = $importer->file(Importer::CSV_IMPORTER);
        $result = $csvImporter->import($this->testFilePath, false);
        self::assertEquals(29, $result['processed']);
        self::assertEquals(0, $result['successful']);
        self::assertEquals(29, $result['skipped']);
        self::assertIsArray($result['errors']);
    }
}
