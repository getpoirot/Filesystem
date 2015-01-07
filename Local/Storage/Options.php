<?php
namespace Poirot\Local\Storage;

use Poirot\Core\AbstractOptions;

class Options extends AbstractOptions
{
    protected $root_dir;

    /**
     * @return mixed
     */
    public function getRootDir()
    {
        return rtrim($this->root_dir, Storage::DS).Storage::DS;
    }

    /**
     * @param mixed $root_dir
     */
    public function setRootDir($root_dir)
    {
        $this->root_dir = $root_dir;
    }
}
