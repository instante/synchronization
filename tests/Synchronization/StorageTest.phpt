<?php

namespace Instante\Tests\Synchronization;

use Instante\Synchronization\Storage;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';


class StorageTest extends TestCase
{
    public function testStorage()
    {
        $f = fopen($fName = TEMP_DIR . '/testStorage.tmp', 'w+b');
        $storage = new Storage($f);
        $testData = 'testData';
        $storage->write($testData);
        Assert::same($testData, $storage->read());
        fclose($f);
        Assert::same($testData, file_get_contents($fName));
    }

}

run(new StorageTest());
