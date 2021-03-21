<?php


use Lib\database\Model;

class File extends Model
{
    // * File:    id, user_id, parent_folder_id, name, size, path = user1/folder1/abc.pdf
    public int $user_id;
    public int $parent_folder_id;
    public string $name;
    public float $size;
    public string $path;

}