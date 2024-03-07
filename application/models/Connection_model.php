<?php
	defined('BASEPATH') or exit('No direct script access allowed');
class Connection_model extends CI_Model {
    public function define_database($member, $database) {
        $db[$member] = array(
                'dsn'   => '',
                'hostname' => '192.168.1.250',
                'username' => 'root',
                'password' => '123456',
                'database' => $database,
                'dbdriver' => 'mysqli',
                'dbprefix' => '',
                'pconnect' => FALSE,
                'db_debug' => (ENVIRONMENT !== 'production'),
                'cache_on' => FALSE,
                'cachedir' => '',
                'char_set' => 'utf8',
                'dbcollat' => 'utf8_general_ci',
                'swap_pre' => '',
                'encrypt' => FALSE,
                'compress' => FALSE,
                'stricton' => FALSE,
                'failover' => array(),
                'save_queries' => TRUE
        );

        return $db[$member];
    }
}
?>