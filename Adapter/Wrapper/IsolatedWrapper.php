<?php
namespace Poirot\Filesystem\Adapter\Wrapper;

use Poirot\Filesystem\Adapter\BaseWrapper;
use Poirot\Filesystem\Adapter\Directory;
use Poirot\Filesystem\Interfaces\Filesystem\iCommonInfo;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectory;
use Poirot\Filesystem\Interfaces\Filesystem\iDirectoryInfo;
use Poirot\Filesystem\Interfaces\iFilesystem;
use Poirot\PathUri\Interfaces\iPathFileUri;
use Poirot\PathUri\Interfaces\iPathJoinedUri;
use Poirot\PathUri\PathJoinUri;

class IsolatedWrapper extends BaseWrapper
{
    /**
     * @var iPathJoinedUri
     */
    protected $rootDir;

    /**
     * cached real dir path on latest chDir
     * @var string
     */
    protected $__lastCDir;

    /**
     * Construct
     *
     * @param iFilesystem $filesystem
     * @param null|iPathJoinedUri|string $rootDir
     *
     * @throws \Exception
     */
    function __construct(iFilesystem $filesystem, $rootDir = null)
    {
        parent::__construct($filesystem);

        if ($rootDir !== null)
            $this->chRootPath($rootDir);
    }

    /**
     * Changes Root Directory Path
     *
     * - root directory must be absolute
     *
     * @param string|iPathJoinedUri $dir
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chRootPath($dir)
    {
        if (!is_string($dir) && !$dir instanceof iPathJoinedUri)
            throw new \Exception(sprintf(
                'Dir Path must be string or instanceof iPathJoinedUri but "%s" given.'
                , is_object($dir) ? get_class($dir) : gettype($dir)
            ));

        if (is_string($dir))
            $dir = new PathJoinUri([
                'path'      => $dir,
                'separator' => $this->pathUri()->getSeparator()
            ]);

        $dir->setSeparator(
            $this->pathUri()->getSeparator()
        );

        $dir->normalize();

        if (!$dir->isAbsolute()
            || !is_dir($dir->toString())
        )
            throw new \Exception(sprintf(
                'Dir path must be an absolute address, to an existence directory.'
            ));

        // Set Root Dir:
        $this->rootDir = $dir;

        // Finalize:

        // Check that current working directory is within root path >>>>> {
        $rdPath = new PathJoinUri($dir->toString());
        $cdPath  = new PathJoinUri([
            'path'      => $this->gear()->getCwd()->pathUri()->toString(),
            #'separator' => $this->pathUri()->getSeparator()
        ]);

        // (root = /var/www/) mask (cwd = /var/www/html) === root
        $trdPath = clone $rdPath;
        if ($trdPath->joint($cdPath, false)->getPath() !== $rdPath->getPath()) {
            // current directory is not within root directory
            // change current directory to root
            $this->chDir(new Directory('/'));
        }
        // <<<<<< }

        return $this;
    }

    /**
     * Get Root Directory Path
     *
     * @return iPathJoinedUri
     */
    function getRootPath()
    {
        if (!$this->rootDir)
            // root "/"
            $this->chRootPath(new PathJoinUri(
                [ 'path' => [''] ]
            ));

        return $this->rootDir;
    }

    /**
     * Gets the current working directory
     *
     * - current working directory must exist
     *   from within root directory
     *
     * - if not chDir to root dir
     *
     * @throws \Exception On iFailure
     * @return iDirectory
     */
    function getCwd()
    {
        $dirObj = $this->gear()->getCwd();
        $cwd    = $dirObj->pathUri()->toString();

        $rdPath = new PathJoinUri($this->getRootPath()->toString());

        // check cwd scope:
        if ($this->__lastCDir !== null
            && $cwd !== $this->__lastCDir
        ) {
            // Current Directory Changed Outside of class scope
            $ldPath  = new PathJoinUri([
                'path'      => $this->__lastCDir,
                'separator' => $this->pathUri()->getSeparator()
            ]);
            $path = $ldPath->mask($rdPath)
                ->prepend(new PathJoinUri($this->pathUri()->getSeparator()))
                ->toString()
            ;

            // restore cwd:
            $this->chDir(new Directory($path));

            return $this->getCwd();
        }

        $cdPath  = new PathJoinUri([
            'path'      => $cwd,
            'separator' => $this->pathUri()->getSeparator()
        ]);

        // Make Paths Absolute From Root
        // if root is      [/var/www/data]
        // and real cwd is [/var/www/data/]images
        // we turn it into /images
        $path = $cdPath->mask($rdPath)
            ->prepend(new PathJoinUri($this->pathUri()->getSeparator()))
            ->toString()
        ;

        $return = new Directory($path);
        $return->setFilesystem($this);

        return $return;
    }

    /**
     * Changes Filesystem current directory
     *
     * @param iDirectoryInfo $dir
     *
     * @throws \Exception On Failure
     * @return $this
     */
    function chDir(iDirectoryInfo $dir)
    {
        $dirRealpath = $this->__getRealIsoPath($dir);

        $this->__lastCDir = $dirRealpath;

        return $this->gear()->chDir(new Directory($dirRealpath));
    }



    /**
     * Get Isolated Filesystem Path Of given Nodes
     *
     * @param iCommonInfo|iPathFileUri|iPathJoinedUri|string $node
     *
     * @return string
     */
    protected function __getRealIsoPath($node)
    {
        // Achieve Path Object:
        if ($node instanceof iCommonInfo)
            $path = new PathJoinUri([
                'path'      => $node->pathUri()->toString(),
                'separator' => $node->pathUri()->getSeparator()
            ]);
        elseif (is_string($node))
            $path = new PathJoinUri([
                'path'      => $node,
                'separator' => $this->pathUri()->getSeparator()
            ]);

        // Get Isolated Real Filesystem Path To File:

        if (!$path->isAbsolute()) {
            $cwdPath = new PathJoinUri([
                'path'      => $this->getCwd()->pathUri()->toString(),
                'separator' => $this->pathUri()->getSeparator()
            ]);

            $path = $cwdPath->append($path)
                ->normalize();
        }

        $path = $this->pathUri()
            ->setBasepath($this->getRootPath())
            ->setPath($path)
            ->allowOverrideBasepath(false)
            ->normalize();

        return $path->toString();
    }
}
