<?php

namespace Instante\Synchronization;

class Storage
{
    const READ_CLUSTER_SIZE = 0x4000; //16KiB blocks
    /** @var resource file handle */
    private $f;

    /**
     * @internal
     */
    public function __construct($f)
    {
        $this->f = $f;
    }

    public function getSize()
    {
        return fstat($this->f)['size'];
    }

    public function read()
    {
        fseek($this->f, 0);
        $content = '';
        while (!feof($this->f)) {
            $content .= fread($this->f, self::READ_CLUSTER_SIZE);
        }
        return $content;
    }

    public function write($content)
    {
        fseek($this->f, 0);
        ftruncate($this->f, 0);
        fwrite($this->f, $content);
    }
}
