<?php
namespace Poirot\Filesystem\Interfaces;

use Poirot\Filesystem\Adapter\Directory;
use Poirot\Filesystem\Interfaces\Filesystem\iCommon;
use Poirot\Filesystem\Interfaces\Filesystem\iCommonInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectory;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectoryInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFile;
use Poirot\Filesystem\Interfaces\Filesystem\iFileInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iFilePermissions;
use Poirot\Filesystem\Interfaces\Filesystem\iLinkInfo;
use Poirot\PathUri\Interfaces\iPathFileUri;

/**
 * @method string getFilename(iCommonInfo $file)
 * @method string getFileExtension(iFileInfo $file)
 * @method string getBasename(iCommonInfo $file)
 *
 * @method $this chgrp(iCommonInfo $file, $group)
 * @method $this chmod(iCommonInfo $file, iFilePermissions $mode)
 * @method $this chown(iCommonInfo $file, $user)
 * @method $this copy(iCommonInfo $source, iCommon $dest)
 * @method mixed getFreeSpace()
 * @method mixed getTotalSpace()
 * @method $this putFileContents(iFile $file, $contents)
 * @method $this flock(iFileInfo $file, $lock = LOCK_EX)
 * @method bool isWritable(iCommonInfo $file)
 * @method $this mkLink(iLinkInfo $link)
 * @method $this mkDir(iDirectoryInfo $dir, iFilePermissions $mode = null)
 * @method $this rename(iCommonInfo $file, $newName)
 * @method $this rmDir(iDirectoryInfo $dir)
 * @method $this chFileATime(iFile $file, $time = null)
 * @method $this chFileMTime(iFile $file, $time = null)
 * @method $this unlink($file)
 *
 * @method iDirectory getCwd()
 * @method $this chDir(iDirectoryInfo $dir)
 * @method iCommonInfo mkFromPath($path)
 * @method array scanDir(iDirectoryInfo $dir = null, $sortingOrder = iFsBase::SCANDIR_SORT_NONE)
 * @method mixed getFileGroup(iCommonInfo $node)
 * @method iFilePermissions getFilePerms(iCommonInfo $file)
 * @method mixed getFileOwner(iCommonInfo $file)
 * @method bool isFile($source)
 * @method bool isDir($source)
 * @method bool isLink($source)
 * @method bool isExists(iCommonInfo $file)
 * @method string getFileContents(iFile $file, $maxlen = 0)
 * @method int getATime(iCommonInfo $file)
 * @method int getCTime(iCommonInfo $file)
 * @method int getMTime(iCommonInfo $file)
 * @method int getFileSize(iFileInfo $file)
 * @method bool isReadable(iCommonInfo $file)
 * @method Directory dirUp(iCommonInfo $file)
 * @method iCommonInfo linkRead(iLinkInfo $link)
 *
 * @method iPathFileUri pathUri()
 *
 */
interface iFilesystem 
{
    // we have some wrapper that used as a call proxy to Filesystem classes
    // so i can't define a master interface for that wrapper classes ...
    // but for more readability of codes i mention to iFilesystem that is alias
    // for iFsBase
}
