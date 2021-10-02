# getid3-extended

> getID3 PHP libary extension class

## Usage
Before including `getid3ex.php` to your script make sure to define `GETID3_INCLUDEPATH` constant so it points at getid3 library folder (see example below);


### Example
```php
<?php

define("GETID3_INCLUDEPATH", __DIR__ . "/getid3/");
require __DIR__ . "/getid3ex.php";

$filename = __DIR__ . "/song.mp3";
$out = __DIR__ . "/song_new.mp3";
$picture = __DIR__ . "/cover.jpg";

$getid3 = new GetId3Extended();
$getid3->openFile($filename);

// Single tag value
$getid3->setTag("title", "Some new title");

// Multiple tags values
$getid3->setTags([
    "title" => "Some new title",
    "artist" => "tinytengu",
    "genre" => "Some non-id3v1 genre name"
]);

$getid3->setCover($picture);

$getid3->saveFile($out);
```


Original repository: [getID3](https://github.com/JamesHeinrich/getID3)

License: [GNU GPL v3.0](https://github.com/tinytengu/getid3-extended/blob/main/LICENSE)
