<?php

class Lib {

    public static function is_entry_ignored($entry, $allow_show_folders, $hidden_extensions) {
        if ($entry === basename(__FILE__)) {
            return true;
        }

        if (is_dir($entry) && !$allow_show_folders) {
            return true;
        }

        $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
        if (in_array($ext, $hidden_extensions)) {
            return true;
        }

        return false;
    }

    public static function rmrf($dir) {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file)
                rmrf("$dir/$file");
            rmdir($dir);
        } else {
            unlink($dir);
        }
    }

    public static function is_recursion_delete($d) {
        $stack = [$d];
        while ($dir = array_pop($stack)) {
            if (!is_readable($dir) || !is_writable($dir))
                return false;
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file)
                if (is_dir($file)) {
                    $stack[] = "$dir/$file";
                }
        }
        return true;
    }

    public static function get_absolute_path($path) {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' == $part)
                continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }


    public static function countBytes($ini_v) {
        $ini_v = trim($ini_v);
        $s = ['g' => 1 << 30, 'm' => 1 << 20, 'k' => 1 << 10];
        return intval($ini_v) * ($s[strtolower(substr($ini_v, -1))] ?: 1);
    }

}
