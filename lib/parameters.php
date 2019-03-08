<?php
namespace Quickplus\Lib;
define('INCLUDE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
define('BASE_PATH',str_replace('\\','/',realpath(dirname(dirname(__FILE__)).'/'))."/");

class parameters
{
    const DB_HOST = '';
    const DB_NAME = '';
    const DB_USERNAME = '';
    const DB_PASSWORD = '';
}

?>
