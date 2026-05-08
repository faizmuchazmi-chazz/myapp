<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Start_tanggal_putus_setor extends CI_Migration {

	public function up()
	{
		$data = array(
			'key' => 'START_TANGGAL_PUTUS_SETOR',
			'value' => '',
			'category' => 8,
			'note' => 'date. tanggal awal hitung setor panmud'
		);
		$this->db->insert('tmst_configs', $data);
	}

	public function down()
	{
		$this->db->delete('tmst_configs', array('key' => 'START_TANGGAL_PUTUS_SETOR'));
	}

}
