<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iStream 
{
    /**
     * Set Resource Handler
     *
     * - Usually Handler injected from filesystem::stream()
     *
     * @param resource $resource Resource Handler
     *
     * @return $this
     */
    function setHandler($resource);

    /**
     * Read Data From Stream
     *
     * @param int  $byte       Read Data in byte
     * @param bool $binarySafe Binary Safe Data Read
     *
     * @return string
     */
    function read($byte = 0, $binarySafe = false);

    /**
     * Writes the contents of string to the file stream
     *
     * @param string $content The string that is to be written
     * @param int    $byte    writing will stop after length bytes
     *                        have been written or the end of string
     *                        is reached
     *
     * @return $this
     */
    function write($content, $byte = 0);

    /**
     * Is At The End Of Stream?
     *
     * !  If PHP is not properly recognizing the line endings
     *    when reading files either on or created by a Macintosh computer,
     *    enabling the auto_detect_line_endings run-time configuration
     *    option may help resolve the problem
     *
     * @return bool
     */
    function isStreamEnd();

    /**
     * Close Stream Resource
     *
     * @return null
     */
    function close();
}
