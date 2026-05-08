<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Wa_kinerja_target extends CI_Migration {

	public function up()
	{
		$data = array(
			'key' => 'WA_KINERJA_TARGET',
			'value' => '',
			'category' => 5,
			'note' => 'string. no whatsapp untuk penerima notifikasi kinerja perkara'
		);
		$this->db->insert('tmst_configs', $data);
	}

	public function down()
	{
		$this->db->delete('tmst_configs', array('key' => 'WA_KINERJA_TARGET'));
	}

}
