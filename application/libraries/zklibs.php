<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'zklib/zklib.php';
class zklibs extends ZKLib{
    public function __construct() { 
        parent::__construct($ip, $port); 
    }
}