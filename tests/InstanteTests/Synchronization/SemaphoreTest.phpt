<?php

namespace InstanteTests\Synchronization;

use Instante\Synchronization\Semaphore;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';


class SemaphoreTest extends TestCase
{
    public function testExecuteCallbackMethod()
    {
        $semaphore = new Semaphore(TEMP_DIR);
        $executedCallback = FALSE;
        $semaphore->synchronizeExclusive('x', function($storage) use (&$executedCallback){
            Assert::type('Instante\Synchronization\Storage', $storage);
            $executedCallback = TRUE;
        });
        Assert::true($executedCallback);
    }

}

run(new SemaphoreTest());
