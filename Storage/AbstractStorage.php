<?php
namespace Poirot\Filesystem\Storage;

use Poirot\Core\Interfaces\OptionsProviderInterface;
use Poirot\Filesystem\Interfaces\iStorage;

abstract class AbstractStorage extends AbstractStorageIterate
    implements iStorage
{
    function __construct(array $options = array())
    {
        if ($this instanceof OptionsProviderInterface)
            foreach ($options as $key => $value)
                $this->options()->{$key} = $value;

        if (method_exists($this, 'consIt'))
            $this->consIt();
    }
}
