<?php
namespace Poirot\Filesystem\Interfaces;

interface iCommon extends iCommonInfo, iWritable
{
    /**
     * Lock File
     *
     * @return $this
     */
    function lock();

    /**
     * Unlock file
     *
     * @return $this
     */
    function unlock();

    /**
     * Set Basename of file or folder
     *
     * ! throw exception if file is lock
     *
     * - /path/to/filename.ext
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
     * ! throw exception if file is lock
     *
     * - if null storage use default/current path
     *
     * @param string|null $path Path To File/Folder
     *
     * @return $this
     */
    function setPath($path);

    /**
     * Rename File And Write To Storage
     *
     * @param string $newname New name
     *
     * @return $this
     */
    function rename($newname);

    /**
     * Set Owner
     *
     * @param int $owner
     *
     * @return $this
     */
    function setOwner($owner);

    /**
     * Set Permissions
     *
     * @param $perms
     *
     * @return $this
     */
    function setPerms($perms);

    /**
     * Set Group
     *
     * @param $group
     *
     * @return $this
     */
    function setGroup($group);

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
