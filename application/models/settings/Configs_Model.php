<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Configs_Model extends Crud_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->tableName = $this->config->item('tbl_configs');
	}

	protected function list_query($where, $do_filter = true)
	{
		$this->colSearch = ['key', 'value'];
		$this->colOrder = ['key' => 'asc'];

		$this->db->from($this->tableName);

		parent::list_query($where, $do_filter);
	}
}
