<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
// session_start();
Class Fungsi extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->CI = get_instance();
	}

	public function set_log($user_id, $username, $id, $class, $pk, $remark){

		date_default_timezone_set("Asia/Jakarta");

		$log = array(
			'user_id'		=>	$user_id,
			'username'		=>	$username,
			'id_previllage'	=> 	$id,
			'class_name'	=>	$class,
			'pk'			=>	$pk,
			'remark'		=> 	$remark,
			'log_stat'		=>	'1',
			'log_time'		=>	date("Y-m-d G:i:s")
		);
		$this->db->set('user_log_id', 'getNewUserLogId()', FALSE);
		return $this->db->insert('system_log_user',$log);
	}

	

	public function countUserLogin(){
		$hasil = $this->db->query("SELECT COUNT(username) as juser FROM system_user WHERE log_stat='on' AND data_state='0' AND user_group_id!='1' AND user_group_id!='2'");
		$hasil = $hasil->row_array();
		return $hasil['juser'];
	}

	

	public function set_change_log($old_data,$new_data,$user_id, $kode){
		$hasil = $this->db->query("SELECT user_log_id FROM system_log_user WHERE user_id = '$user_id' ORDER BY user_log_id DESC LIMIT 0,1");
		$hasil = $hasil->row_array();
		$log_id= $hasil['user_log_id'];

		$data = array(
			'user_log_id' 	=> $log_id,
			'kode'			=> $kode,
			'old_data'		=> str_replace(';','-',$this->_serialize($old_data)),
			'new_data'		=> str_replace(';','-',$this->_serialize($new_data))
		);
		$this->db->set('change_log_id', 'getNewChangeLogID()', FALSE);
		return $this->db->insert('system_change_log',$data);
	}
	
	public function getLastOpnameHistory(){
		$this->db->select('month,year')->from('system_opname_history');
		$this->db->order_by('last_update','desc');
		$this->db->limit(1,0);
		$result = $this->db->get()->row_array();
		return $result;
	}
	
	function _serialize($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				if (is_string($val))
				{
					$data[$key] = str_replace('\\', '{{slash}}', $val);
				}
			}
		}
		else
		{
			if (is_string($data))
			{
				$data = str_replace('\\', '{{slash}}', $data);
			}
		}

		return serialize($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Unserialize
	 *
	 * This function unserializes a data string, then converts any
	 * temporary slash markers back to actual slashes
	 *
	 * @access	private
	 * @param	array
	 * @return	string
	 */
	function _unserialize($data)
	{
		$data = @unserialize(strip_slashes($data));

		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				if (is_string($val))
				{
					$data[$key] = str_replace('{{slash}}', '\\', $val);
				}
			}

			return $data;
		}

		return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
	}
}