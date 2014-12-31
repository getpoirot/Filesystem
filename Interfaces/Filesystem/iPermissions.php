<?php
namespace Poirot\Filesystem\Interfaces\Filesystem;

interface iPermissions 
{
    /*
     * Permissions
     *
     * to apply 644 perm.: or
     *     PERMS_OWNER_READ | PERMS_OWNER_WRITE
     *   | PERMS_GROUP_READ
     *   | PERMS_ALL_READ
     *
     */
    const PERMS_OWNER_READ  = 0400;
    const PERMS_OWNER_WRITE = 0200;
    const PERMS_OWNER_EXEC  = 0100;

    const PERMS_GROUP_READ  = 0040;
    const PERMS_GROUP_WRITE = 0020;
    const PERMS_GROUP_EXEC  = 0010;

    const PERMS_ALL_READ  = 0004;
    const PERMS_ALL_WRITE = 0002;
    const PERMS_ALL_EXEC  = 0001;

    /**
     * Has Same Permissions as Given?
     *
     * @param iPermissions $permission
     *
     * @return bool
     */
    function hasPermissions(iPermissions $permission);

    /**
     * Give An Access Perms.
     *
     * @param int $permission Octal (Combined) Permission(s)
     *
     * @return $this
     */
    function grantPermission($permission);

    /**
     * Take An Access Perms.
     *
     * @param int $permission Octal (Combined) Permission(s)
     *
     * @return $this
     */
    function revokePermission($permission);

    /**
     * Get Sum Of Permission Rights
     *
     * @return int An Octal Combined Permission
     */
    function getTotalPerms();

    /**
     * Get A Readable Permission String
     *
     * @link http://php.net/manual/en/function.fileperms.php
     *
     * @return string
     */
    function toString();
}
