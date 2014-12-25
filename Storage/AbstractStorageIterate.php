<?php
namespace Poirot\Filesystem\Storage;

abstract class AbstractStorageIterate
    implements \IteratorAggregate
{
    /**
     * @var
     */
    protected $iterator;

    function getIterator()
    {
        return ($this->iterator)
            ?: $this->iterator = new StorageIterator($this);
    }
}
