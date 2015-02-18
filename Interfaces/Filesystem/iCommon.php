<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iCommon extends iCommonInfo
{
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
     * @param iFilePermissions $mode
     *
     * @return $this
     */
    function chmod(iFilePermissions $mode);

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
