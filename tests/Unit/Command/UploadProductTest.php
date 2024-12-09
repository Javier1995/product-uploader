<?php

namespace App\Tests\Unit\Command;

use App\Command\UploadProductCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class UploadProductTest extends KernelTestCase
{
    private $testFilePath;

    protected function setUp(): void
    {
        if (!class_exists(UploadProductCommand::class)) {
            self::markTestSkipped('UploadProductCommand class does not exist');
        }
        parent::setUp();
        self::bootKernel();
        $this->testFilePath = self::$kernel->getContainer()->getParameter('kernel.project_dir') . '/public/uploads/stock.csv';
    }

    public function testCsvFileExistsInLocalEnvironment(): void
    {
        self::assertFileExists($this->testFilePath);
    }

    public function testCsvFileIsInLocalEnvironment(): void
    {
        $isCsv = pathinfo($this->testFilePath, PATHINFO_EXTENSION) === 'csv';
        self::assertTrue($isCsv);
    }

    public function testCommandIsInTestMode(): void
    {
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'app:upload-product',
            'filePath' => $this->testFilePath,
            '--test' => true,
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();
        self::assertStringContainsString('Test mode enabled. No data was inserted into the database.', $content);
    }

    public function testCommandIsNotInTestMode(): void
    {
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'app:upload-product',
            'filePath' => $this->testFilePath,
            '--test' => false,
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();
        self::assertStringNotContainsString('Test mode enabled. No data was inserted into the database.', $content);
    }
}
