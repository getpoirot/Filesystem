# Poirot\Filesystem

Filesystem abstraction.

## General Usage

```php
// Create isolated filesystem:
// on /var/www/html/upload directory
$fs = new FSLocal('/var/www/html/upload');
$fs->chRootPath('/var/www/html/upload');

// check folder is exists:
if ($fs->isExists(new Directory('user')))
    // change cwd to user directory
    $fs->chDir(new Directory('user'));

$fs->chDir($fs->getCwd()->dirUp());

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