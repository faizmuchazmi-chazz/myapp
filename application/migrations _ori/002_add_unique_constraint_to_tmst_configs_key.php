<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_constraint_to_tmst_configs_key extends CI_Migration {

	public function up()
	{
		// Add UNIQUE constraint to the 'key' column
		$sql = "ALTER TABLE `tmst_configs` ADD UNIQUE KEY `uk_key` (`key`)";
		$this->db->query($sql);
	}

	public function down()
	{
		// Remove the UNIQUE constraint
		$sql = "ALTER TABLE `tmst_configs` DROP INDEX `uk_key`";
		$this->db->query($sql);
	}

}
