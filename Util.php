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
    public static function makeSafeFilename($fileName)
    {
        $regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\-]#', '#^\.#');

        return preg_replace($regex, '', $fileName);
    }

    /**
     * TODO Fix ME!!!
     *
     * @param $url
     * @param null $flags
     * @return string|array
     */
    public static function urlParse($url, $flags = null)
    {
        $sflfdfldf=$url;
        if(strpos($url,"?")>-1){
            $a=explode("?",$url,2);
            $url=$a[0];
            $query=$a[1];
        }
        if(strpos($url,"://")>-1){
            $scheme=substr($url,0,strpos($url,"//")-1);
            $url=substr($url,strpos($url,"//")+2,strlen($url));
        }
        if(strpos($url,"/")>-1){
            $a=explode("/",$url,2);
            $url=$a[0];
            $path="/".$a[1];
        }
        if(strpos($url,":")>-1){
            $a=explode(":",$url,2);
            $url=$a[0];
            $port=$a[1];
        }

        $host=$url;
        $url=null;
        $return = [];
        /**
         * TODO FIX
        define ('PHP_URL_SCHEME', 0);
        define ('PHP_URL_HOST', 1);
        define ('PHP_URL_PORT', 2);
        define ('PHP_URL_USER', 3);
        define ('PHP_URL_PASS', 4);
        define ('PHP_URL_PATH', 5);
        define ('PHP_URL_QUERY', 6);
        define ('PHP_URL_FRAGMENT', 7);
         */
        $flagArrays = array("scheme", "host", "port", 'user', 'pass', "path", "query", "url",);
        foreach($flagArrays as $var){
            if(!empty($$var)){
                $return[$var]=$$var;
            }
        }

        if ($flags !== null) {
            $return = $return[$flagArrays[$flags]];
        }

        return $return;
    }
}
