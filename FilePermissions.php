<?php
namespace Poirot\Filesystem;

use Poirot\Filesystem\Interfaces\Filesystem\iFilePermissions;

class FileFilePermissions implements iFilePermissions
{
    /**
     * @var int Octal Permissions Combination
     */
    protected $totalPerms = 0;

    /**
     * Construct
     *
     * @param $perms
     */
    function __construct($perms = null)
    {
        if ($perms !== null)
            $this->grantPermission($perms);
    }

    /**
     * Has Same Permissions as Given?
     *
     * @param iFilePermissions $permission
     *
     * @return bool
     */
    function hasPermissions(iFilePermissions $permission)
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
     * Import Permissions From symbolic notation
     *
     * @param string $permissions drwxr-xr-x
     *
     * @return $this
     */
    function fromString($permissions)
    {
        $mode = $this->getTotalPerms();

        if ($permissions[1] == 'r') $mode += 0400;
        if ($permissions[2] == 'w') $mode += 0200;
        if ($permissions[3] == 'x') $mode += 0100;
        else if ($permissions[3] == 's') $mode += 04100;
        else if ($permissions[3] == 'S') $mode += 04000;

        if ($permissions[4] == 'r') $mode += 040;
        if ($permissions[5] == 'w') $mode += 020;
        if ($permissions[6] == 'x') $mode += 010;
        else if ($permissions[6] == 's') $mode += 02010;
        else if ($permissions[6] == 'S') $mode += 02000;

        if ($permissions[7] == 'r') $mode += 04;
        if ($permissions[8] == 'w') $mode += 02;
        if ($permissions[9] == 'x') $mode += 01;
        else if ($permissions[9] == 't') $mode += 01001;
        else if ($permissions[9] == 'T') $mode += 01000;

        $this->grantPermission($mode);

        return $this;
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
 