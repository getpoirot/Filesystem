<?php
namespace Poirot\Filesystem;

class Util 
{
    /**
     * Generate Safe Web Name From Filename
     *
     * @param string $fileName
     *
     * @return string
     */
    public static function makeSafe($fileName)
    {
        $regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\-]#', '#^\.#');

        return preg_replace($regex, '', $fileName);
    }
}
