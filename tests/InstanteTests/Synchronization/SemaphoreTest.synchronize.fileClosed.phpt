<?php

namespace InstanteTests\Synchronization;

use Instante\Synchronization\Semaphore;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';


class SemaphoreTestSynchronize extends TestCase
{
    public function testStorage()
    {
        $semaphore = new Semaphore(TEMP_DIR);
        $f = TRUE;
        try {
            $semaphore->synchronizeShared('x', function ($storage) use (&$f) {
                $rc = new \ReflectionClass($storage);
                $pr = $rc->getProperty('f');
                $pr->setAccessible(TRUE);
                $f = $pr->getValue($storage);
                Assert::true(is_resource($f));
                throw new \Exception;
            });
        } catch (\Exception $ex) {

        }
        Assert::false($f === TRUE);
        Assert::false(is_resource($f));
    }

}

run(new SemaphoreTestSynchronize);
