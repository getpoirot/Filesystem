<?php
namespace Poirot\Filesystem;

use Poirot\Filesystem\Interfaces\Filesystem\iPermissions;

class Permissions implements iPermissions
{
    /**
     * @var int Octal Permissions Combination
     */
    protected $totalPerms;

    /**
     * Has Same Permissions as Given?
     *
     * @param iPermissions $permission
     *
     * @return bool
     */
    function hasPermissions(iPermissions $permission)
    {
        return $this->getTotalPerms() & $permission->getTotalPerms();
    }

    /**
     * Give An Access Perms.
     *
     * @param int $permission Octal (Combined) Permission(s)
     *
     * @return $this
     */
    function grantPermission($permission)
    {
        $this->totalPerms |= $permission;

        return $this;
    }

    /**
     * Take An Access Perms.
     *
     * @param int $permission Octal (Combined) Permission(s)
     *
     * @return $this
     */
    function revokePermission($permission)
    {
        $perm = new self();
        $perm->grantPermission($permission);

        if ($this->hasPermissions($perm))
            // with xor bitwise nature we have to check
            // presented permission to take it by xor
            // other wise it will be applied(granted)
            $this->totalPerms ^= $permission;

        return $this;
    }

    /**
     * Get Sum Of Permission Rights
     *
     * @throws \Exception If No Permission Added
     * @return int An Octal Combined Permission
     */
    function getTotalPerms()
    {
        if ($this->totalPerms === null)
            throw new \Exception('No Permission Granted Yet!');

        return $this->totalPerms;
    }

    /**
     * Get A Readable Permission String
     *
     * @return string
     */
    function toString()
    {
        $perms = $this->getTotalPerms();

        if (($perms & 0xC000) == 0xC000) {
            // Socket
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) {
            // Symbolic Link
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) {
            // Regular
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) {
            // Block special
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) {
            // Directory
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) {
            // Character special
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) {
            // FIFO pipe
            $info = 'p';
        } else {
            // Unknown
            $info = 'u';
        }

        // Owner
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x' ) :
            (($perms & 0x0800) ? 'S' : '-'));

        // Group
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x' ) :
            (($perms & 0x0400) ? 'S' : '-'));

        // World
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x' ) :
            (($perms & 0x0200) ? 'T' : '-'));

        return $info;
    }
}
 