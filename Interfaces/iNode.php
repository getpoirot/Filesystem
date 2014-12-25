<?php
namespace Poirot\Filesystem\Interfaces;

interface iNode extends iNodeInfo
{
    /**
     * Set Basename of file or folder
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
     * - if null storage use default/current path
     *
     * @param string|null $path Path To File/Folder
     *
     * @return $this
     */
    function setPath($path);

    /**
     * Make File/Folder if not exists
     *
     * @return bool
     */
    function mkIfNotExists();

    /**
     * Is File/Folder Exists?
     *
     * @return bool
     */
    function isExists();

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
}
