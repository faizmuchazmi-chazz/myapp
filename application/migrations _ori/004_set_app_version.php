<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Set_app_version extends CI_Migration {

	public function up()
	{
		// Set APP_VERSION to 1.1
		$data = array(
			'key' => 'APP_VERSION',
			'value' => '1.1',
			'category' => 1, // System category
			'note' => 'Application version'
		);
		
		// Check if APP_VERSION already exists, if so update it, otherwise insert
		$this->db->where('key', 'APP_VERSION');
		$existing = $this->db->get('tmst_configs')->row();
		
		if ($existing) {
			$this->db->where('key', 'APP_VERSION');
			$this->db->update('tmst_configs', $data);
		} else {
			$this->db->insert('tmst_configs', $data);
		}
	}

	public function down()
	{
		// Set APP_VERSION back to 1.0 when rolling back
		$data = array(
			'key' => 'APP_VERSION',
			'value' => '1',
			'category' => 1, // System category
			'note' => 'Application version'
		);
		
		// Check if APP_VERSION already exists, if so update it, otherwise insert
		$this->db->where('key', 'APP_VERSION');
		$existing = $this->db->get('tmst_configs')->row();
		
		if ($existing) {
			$this->db->where('key', 'APP_VERSION');
			$this->db->update('tmst_configs', $data);
		} else {
			$this->db->insert('tmst_configs', $data);
		}
	}
}