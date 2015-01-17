<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iCommon extends iCommonInfo
{
    /**
     * Set Filename of file or folder
     *
     * ! without extension
     *
     * - /path/to/filename[.ext]
     * - /path/to/folderName/
     *
     * @param string $name Basename
     *
     * @return $this
     */
    function setBasename($name);

    /**
     * Set Path
     *
     * @param string|null $path Path To File/Folder
     *
     * @return $this
     */
    function setPath($path);

    /**
     * Set Owner
     *
     * @param int $owner
     *
     * @return $this
     */
    function chown($owner);

    /**
     * Changes file mode
     *
     * @param iPermissions $mode
     *
     * @return $this
     */
    function chmod(iPermissions $mode);

    /**
     * Set Group
     *
     * @param $group
     *
     * @return $this
     */
    function chgrp($group);

    /**
     * Tells if the entry is writable
     *
     * - The writable beside of filesystem must
     *   implement iWritable
     *
     * @return bool
     */
    function isWritable();

    /**
     * Is File/Folder Exists?
     *
     * @return bool
     */
    function isExists();
}
