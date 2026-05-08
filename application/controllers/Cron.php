<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('cronjob');
	}

	public function index()
	{
		$result = $this->cronjob->sync();

		foreach ($result['ok'] as $label) {
			echo "[OK]     {$label}" . PHP_EOL;
		}

		foreach ($result['failed'] as $label) {
			echo "[FAILED] {$label}" . PHP_EOL;
		}

		$total   = count($result['ok']) + count($result['failed']);
		$success = count($result['ok']);
		echo PHP_EOL . "{$success}/{$total} jobs synced successfully." . PHP_EOL;
	}
}
