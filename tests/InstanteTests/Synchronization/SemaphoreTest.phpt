<?php

namespace InstanteTests\Synchronization;

use Instante\Synchronization\Semaphore;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';


class SemaphoreTest extends TestCase
{
    public function testExecuteCallbackMethodExclusive()
    {
        $semaphore = new Semaphore(TEMP_DIR);
        $executedCallback = FALSE;
        $foo = $semaphore->synchronizeExclusive('x', function($storage) use (&$executedCallback){
            Assert::type('Instante\Synchronization\Storage', $storage);
            $executedCallback = TRUE;
            return 'foo';
        });
        Assert::true($executedCallback);
        Assert::same('foo', $foo, 'returning value from exclusive lock callback');
    }

    public function testExecuteCallbackMethodShared()
    {
        $semaphore = new Semaphore(TEMP_DIR);
        $executedCallback = FALSE;
        $foo = $semaphore->synchronizeShared('x', function($storage) use (&$executedCallback){
            Assert::type('Instante\Synchronization\Storage', $storage);
            $executedCallback = TRUE;
            return 'foo';
        });
        Assert::true($executedCallback);
        Assert::same('foo', $foo, 'returning value from shared lock callback');
    }

}

run(new SemaphoreTest());
