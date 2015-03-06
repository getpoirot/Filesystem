# Poirot\Filesystem

Filesystem abstraction.

## General Usage

```php
// Create isolated filesystem:
// on /var/www/html/upload directory
$fs = new LocalFS;

// check folder is exists:
if ($fs->isExists(new Directory('user')))
    // change cwd to user directory
    $fs->chDir(new Directory('user'));

// get current working directory path:
echo sprintf(
    'Current Directory: "%s"'
    , $fs->getCwd()->pathUri()->toString()
);

// Scan current working directory for files:
foreach ($fs->getCwd()->scanDir() as $path) {
    // make an object from path
    $node = $fs->mkFromPath($path);

    echo '<br/>';
    if (!$fs->isFile($node))
        // check that is node file?
        echo '<b>';

    // get filename
    echo '[] '.($node->pathUri()->getFilename());

    if ($fs->isDir($node))
        // check that is node dir?
        echo '</b>'
            .'<div style="padding-left: 20px;">'
            // if node is dir, get list of files from
            // Directory Object
            .var_export($node->scanDir(), true)
            .'</div>'
        ;
}

k($fs->getFileOwner($fs->mkFromPath('images/file.jpg')));

$fs->copy(
    $fs->mkFromPath('images/file.jpg')
    , new File('backup/file.copy.jpg')
);

k($fs->getFreeSpace());
```

## Filesystem Wrappers

__Isolated Filesystem Root Directory__

All path actions are isolated on root directory as home

```php
$fs = new IsolatedWrapper(new LocalFS, '/var/www/html/data/user');
$fs->chDir('media/audio');

// You are always in cwd scope
// if scope has changed from outside
chdir('/var');
$fs->getCwd(); // you are still in "/media/audio"

$fs->chDir('/'); // you are now on "/var/www/html/data/user" of real filesystem
```

## Filesystem Interfaces

All The Filesystem Adapters using same api, all other functionality added by Plugin Or Wrapper Around
Base Filesystem Adapter.

## Virtual Filesystem

Virtual InMemory filesystem can used for unit testing, or cache on slow filesystems with combination of syncTool.

```php
$fs = new InMemoryFS;

if (!$fs->isExists(new Directory('/var/www/html/upload')))
    $fs->mkDir(new Directory('/var/www/html/upload'), new FilePermissions(0755));

$fs->chDir(new Directory('/var/www/html/upload'));

$fs->isExists(new Directory('user')) ?:
    $fs->mkDir(new Directory('user'), new FilePermissions(0755));

echo '<br/> Current Directory: '. $fs->getCwd()->pathUri()->toString();
echo '<br/>';
echo '<br/>';
print_r($fs->scanDir());

echo (!$fs->isDir('not-exists')) ?: 'You did`t see this';
echo '<br/>';

$fs->putFileContents(new File('user/index.html'), 'content of file');

echo (!$fs->isDir('user')) ?: 'You have "html" directory, list:';
echo '<br/>';
/** @var Directory $htmlDirectory */
$htmlDirectory = $fs->mkFromPath('user');
print_r($htmlDirectory->scanDir());

/** @var iFile $Indexhtml */
$Indexhtml = $fs->mkFromPath('user/index.html');
echo $fs->getFileContents($Indexhtml);

$home = new Directory('/var/www/html/upload');
$fs->copy($Indexhtml, $home);

echo '<br/>';
echo '<br/>';
echo '<br/> Home DIR: ';
echo '<br/>';
print_r($htmlDirectory->scanDir($home));
echo $Indexhtml->getSize().' bytes';

echo '<br/>';

$fs->rename($Indexhtml, 'index.php');
print_r($htmlDirectory->scanDir(new Directory('user')));

die('>_');
```

