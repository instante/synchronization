<?php

namespace InstanteTests\Synchronization;

use Instante\Synchronization\Semaphore;
use Instante\Synchronization\Storage;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;
use Thread;
use Threaded;

require_once __DIR__ . '/../bootstrap.php';


class SemaphoreTest extends TestCase
{
    const EXT_PTHREADS = 'pthreads';

    /**
     * This method is called before a test is executed.
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        if (!extension_loaded(self::EXT_PTHREADS)) {
            Environment::skip('Test requires PHP pthreads extension.');
        }
    }

    public function testSharedLock()
    {
        $semaphore = new Semaphore(TEMP_DIR);
        $t1 = new LockThread($semaphore, LOCK_SH, 0.7);
        $t2 = new LockThread($semaphore, LOCK_SH, 1.0);
        $t3 = new LockThread($semaphore, LOCK_EX, 1.2);
        //has to be there to force load Storage class
        $dummy = new Storage(fopen(__FILE__, 'r'));
        $t1->start();
        $t3->start();
        $t2->start();

        Assert::true(abs($t1->getExecutionTime() - 0.7) < 0.1, 'first two threads ran concurrently');
        Assert::true(abs($t2->getExecutionTime() - 1.0) < 0.1, 'first two threads ran concurrently');
        Assert::true(abs($t3->getExecutionTime() - (1.0 + 1.2)) < 0.1, 'exclusive lock thread had to wait');
    }
}

class LockThread extends Thread
{
    /** @var Semaphore */
    private $s;
    /** @var int */
    private $lockType;
    /** @var int */
    private $sleepMS;
    /** @var bool */
    private $joined = FALSE;
    /** @var float */
    private $executionTime;

    public function __construct(Semaphore $s, $lockType, $sleepMS = 0)
    {
        $this->s = $s;
        $this->sleepMS = $sleepMS;
        $this->lockType = $lockType;
    }

    public function run()
    {
        $start = microtime(TRUE);
        $this->s->synchronize('a', function () {
            usleep(1000000 * $this->sleepMS);
        }, $this->lockType);
        $this->executionTime = microtime(TRUE) - $start;
    }

    public function getExecutionTime()
    {
        if (!$this->joined) {
            $this->joined = TRUE;
            $this->join();
        }
        return $this->executionTime;
    }
}

run(new SemaphoreTest());
