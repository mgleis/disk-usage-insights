<?php

namespace Mgleis\DiskUsageInsights\Domain;

class FileEntry {

    const TYPE_DIR = 'dir';
    const TYPE_FILE = 'file';

    public int $id = 0;
    public int $parent_id = 0;              // 0=no parent
    public string $name = '';               // dir=full path, file = relative name
    public string $type = 'file';           // dir|file
    public int $size = 0;                   // dir=0, file=file size
    public int $dir_size = 0;               // only for dirs: size of all files in this directory
    public int $dir_recursive_size = 0;     // only for dirs: size of all files in this and all sub directories
    public int $dir_count = 0;              // only for dirs: number of files in this director
    public int $dir_recursive_count = 0;    // only for dirs: number of files in this and all sub directories
    public int $last_modified_date = 0;
    public int $is_wp_core_file = 0;

}
