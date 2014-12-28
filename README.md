# Poirot\Filesystem

Filesystem abstraction.

## General Usage

__Storage__

Reading Files Only From Storage Home

```php
$storageName = $storage->getBasename();

echo sprintf('Reading Entire Files Lists On Home From %s Storage ..', $storageName);
foreach($storage->lsContents() as $fsc) {
    if ($storage->typeOf($fsc) === $storage::FS_TYPE_FILE)
        echo $fsc->getBasename().'<br/>';
}
```
