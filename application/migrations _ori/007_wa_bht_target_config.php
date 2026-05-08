<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Wa_bht_target_config extends CI_Migration {

	public function up()
	{
		$data = array(
			'key' => 'WA_BHT_TARGET',
			'value' => '',
			'category' => 5,
			'note' => 'string. no whatsapp target untuk notifikasi BHT'
		);
		$this->db->insert('tmst_configs', $data);
	}

	public function down()
	{
		$this->db->delete('tmst_configs', array('key' => 'WA_BHT_TARGET'));
	}

}
