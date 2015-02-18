<?php
namespace Poirot\Filesystem\Adapter\Local\Storage;

use Poirot\Core\AbstractOptions;
use Poirot\Filesystem\Adapter\Local\Filesystem;

class LocalStorageOptions extends AbstractOptions
{
    protected $root_dir;

    /**
     * @return mixed
     */
    public function getRootDir()
    {
        return rtrim($this->root_dir, Filesystem::DS).Filesystem::DS;
    }

    /**
     * @param mixed $root_dir
     */
    public function setRootDir($root_dir)
    {
        $this->root_dir = $root_dir;
    }
}
