<?php
/*
 * PHP Simple File Manager
 * Copyright Daniel Wojtak (etherneco)
 * config file
 * 
 * 
 */


//setting debug 
error_reporting( error_reporting() & ~E_NOTICE );


// UTF-8 or `basename` doesn't work valid work
setlocale(LC_ALL,'en_US.UTF-8');



//Security options
define('set_delete', true); // disable/enable delete button and delete POST request.
define('set_upload', true); // allow/disallow upload files
define('set_create_folder', true); // enable/disable folder creation
define('set_direct_link', true); // allow downloads and not direct link
define('set_show_folders', true); //set to hide all subdirectories


define('ACCESS_PASSWORD', ''); //set to hide all subdirectories
define('APP_ROOT', '../'); //set to hide all subdirectories




class Config{ 
    public static $disallowed_extensions = ['php'];  // must be an array. Extensions disallowed to be uploaded
    public static $hidden_extensions = ['php']; // must be an array of lowercase file extensions. Extensions hidden in directory index

    
}