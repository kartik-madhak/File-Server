<?php


use Lib\database\Model;

class Folder extends Model
{
// Folder:  id, user_id, parent_folder_id, name, size, no_of_items
    public int $user_id;
    public int $parent_folder_id;
    public string $name;
    public float $size;
    public int $no_of_items;
}