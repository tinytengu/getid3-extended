<?php

/**
 * GetID3 libary extension class
 * 
 * @author tinytengu <tinytengu@protonmail.com>
 */

if (!defined("GETID3_INCLUDEPATH")) {
    die("Set GETID3_INCLUDEPATH constant before including this script");
}

require_once GETID3_INCLUDEPATH . "getid3.php";
require_once GETID3_INCLUDEPATH . "write.php";

class GetId3Extended
{
    /**
     * Current opened file absolute path
     *
     * @var string
     */
    private string $filename;

    /**
     * Loaded id3 tags
     *
     * @var array
     */
    private array $savedTags;

    /**
     * Class constructor
     *
     * @param string $encoding default reader/writer encoding
     * @param string $version default reader tag version
     */
    public function __construct(public string $encoding = "UTF-8", public string $version = "id3v2")
    {
    }

    /**
     * Get tags values from the array
     *
     * @param array $info info array
     * @return array
     */
    private function getTags(array $info): array
    {
        $keys = ["title", "artist", "album", "year", "genre", "composer"];
        $out = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $info["tags"][$this->version])) {
                $out[$key] = $info["tags"][$this->version][$key];
            } else {
                $out[$key] = [];
            }
        }

        return $out;
    }

    /**
     * Open file for read/write
     * 
     * 
     * @param string $filename file path
     * @return void
     */
    public function openFile(string $filename)
    {
        $reader = new getID3();
        $reader->encoding = $this->encoding;
        $reader->analyze($filename);

        $this->filename = $filename;
        $this->savedTags = $this->getTags($reader->info);
    }

    /**
     * Set tag value for current opened file
     *
     * @param string $tag tag name
     * @param mixed $value tag value
     * @return void
     */
    public function setTag(string $tag, mixed $value)
    {
        $this->savedTags[$tag][0] = $value;
    }

    /**
     * Merge tags data array to the current loaded one
     *
     * @param array $tags tags info array to merge
     * @return void
     */
    public function setTags(array $tags)
    {
        foreach ($tags as $k => $v) {
            $this->savedTags[$k] = $v;
        }
    }

    /**
     * Set cover tag image from file path
     *
     * @param string $filepath image file path
     * @return void
     */
    public function setCover(string $filepath)
    {
        $file = fopen($filepath, "rb");
        $data = fread($file, filesize($filepath));
        fclose($file);

        $this->setTags([
            "attached_picture" => [[
                "data" => $data,
                "picturetypeid" => 3,
                "description" => "cover",
                "mime" => "image/jpeg"
            ]]
        ]);
    }

    /**
     * Save current tags changes
     *
     * @param string $filename output filename (current opened file by default)
     * @return void
     */
    public function saveFile(string $filename = null)
    {
        if ($filename !== null) {
            copy($this->filename, $filename);
        } else {
            $filename = $this->filename;
        }

        $writer = new getid3_writetags;
        $writer->tagformats = array("id3v2.3");
        $writer->tag_encoding = $this->encoding;
        $writer->filename = $filename;
        $writer->tag_data = $this->savedTags;
        $writer->WriteTags();
    }
}