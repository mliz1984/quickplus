<?php
namespace Quickplus\Lib;
define('INCLUDE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
define('BASE_PATH',str_replace('\\','/',realpath(dirname(dirname(__FILE__)).'/'))."/");

class parameters
{
    const DB_HOST = 'test.quickplus.org';
    const DB_NAME = 'admin_test';
    const DB_USERNAME = 'admin_test';
    const DB_PASSWORD = 'YANx1984';
}

?>
