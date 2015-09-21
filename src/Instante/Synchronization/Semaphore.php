<?php

namespace Instante\Synchronization;

/**
 * Instante semaphore service.
 *
 * Usage:
 * - install as a service to config.neon:
 *     - Instante\Synchronization\Semaphore(string tempDir)
 * - wrap critical sections to callback called through
 * $semaphore->synchronize(id, callback). This service will set a file semaphore
 * to ensure that at most one callback with the same semaphore id is ran
 * at once. A handle to a file that performs the synchronization is
 * passed as an argument to the callback which can be thread-safely read and
 * written.
 *
 */
class Semaphore
{
    private $dir;

    function __construct($dir)
    {
        $this->dir = $dir;
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, TRUE)) {

                throw new SyncIOException("Directory $dir needed for synchronization service does not exist and could not be created");
            }
        }
    }

    public function synchronizeShared($id, \Closure $closure)
    {
        return $this->synchronize($id, $closure, LOCK_SH);
    }

    public function synchronizeExclusive($id, \Closure $closure)
    {
        return $this->synchronize($id, $closure, LOCK_EX);
    }

    public function synchronize($id, \Closure $closure, $lockType)
    {
        $f = fopen($this->getStorageFilePath($id), 'c+');
        try {
            flock($f, $lockType);
            $return = $closure(new Storage($f));
        } finally {
            flock($f, LOCK_UN);
            fclose($f);
        }
        return $return;
    }
}
