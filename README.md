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

## Using File Content Delivery

It uses to implement content delivery fo reading large files content, 
generating fake content or storing content of files on memory in lazy mode 
to reduce memory size on contents.

Mostly Can Used In Combination With InMemory File System:

```php
// map InMemory File Content To Existence Local File
// always fetch latest file content on reading
$sc = new WrapperClient('file:///var/www/data/user/about.txt', 'r');

// or reading google index
// the file content when reading is always google updated content
$sc = new WrapperClient('http://google.com', 'r');

$fs = new InMemoryFS;
$fs->mkDir(new Directory('Backup'));
$fs->putFileContents(
    new File('Backup\\myFile.tst')
    , new StreamContentDelivery($sc)
);

$fs->chDir(new Directory('Backup'));
if ($fs->isFile('myFile.tst')) {
    $content = $fs->getFileContents($fs->mkFromPath('myFile.tst'));

    echo $content;
}
```

## Using Filesystem As PHP Stream Wrapper

```php
// register sama:// as php stream wrapper
$samaFs = new InMemoryFS;
$fsaw   = new FilesystemAsStreamWrapper($samaFs, 'sama');
SWrapperManager::register($fsaw);

// register another wrapper for local filesystem that isolated on specific path
$isoLocal = new IsolatedWrapper(new LocalFS, '/var/www/html/upload');
SWrapperManager::register(new FilesystemAsStreamWrapper($isoLocal, 'local'));

// making new directory on sama:// filesystem ...............\
mkdir('sama:///Backup');

if (!$samaFs->isExists(new Directory('/Backup')))
    die('Directory Not Found.');

// list directory ............................................\
if ($dh = opendir('sama:///')) {
    rewinddir($dh);
    while (($file = readdir($dh)) !== false)
        echo "filename: $file " . "\n";

    closedir($dh);
}

// check given path is directory
if (!is_dir('sama:///Backup'))
    die('Script Not Dead!');

// or
$dirList = scandir('sama:///');
var_dump($dirList);

// Rename Directory ...........................................\
rename('sama:///Backup', 'sama:///backup');

// Remove Directory ...........................................\
rmdir('sama:///backup');

// Write new file content .....................................\
$fh = fopen('sama:///test-file.txt', iSRAccessMode::MODE_RWB);
fwrite($fh, 'This is Test Content Of File', 100);

// or

file_put_contents('sama:///new-file.txt', "The second content provided.\r\n");

if (!is_file('sama:///new-file.txt'))
    die('Script Not Dead!');

// Read contents of file ......................................\
# echo fread($fh, 1000); // output: This is Test Content Of File

// Output all remaining data on a file pointer
# fpassthru($fh);

# rewind($fh);
while (($buffer = fgets($fh, 4096)) !== false) {
    echo $buffer;
}
echo '<br/>';

var_dump(feof($fh));

if (is_readable('sama:///new-file.txt'))
{
    // This section will run
    $content = file_get_contents('sama:///new-file.txt');
    echo $content;
}

$lines = file('sama:///test-file.txt');
k($lines);

rewind($fh);
while (false !== $char = fgetc($fh))
    echo $char;


// Interaction Between Virtual Filesystem Wrapper And Local .........\
copy('sama:///test-file.txt', 'local:///backup/test-file.txt');

// copy from isolated local filesystem
copy('local:///backup/test-file.txt', 'file:///var/www/html/upload/test-file.txt');

// delete file from virtual filesystem
unlink('sama:///test-file.txt');

k($samaFs->scanDir());

die('>_');
```