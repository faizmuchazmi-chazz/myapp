<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['cronjobs'] = [
	[
		'expression' => '0 9 * * 1-5', //senin-jumat jam 09.00
		'command'    => 'php ' . FCPATH . 'index.php ck/bht send_notif_rencana_bht',
		'label'      => 'send_notif_rencana_bht',
	],
	[
		'expression' => '0 16 * * 1-5', //senin-jumat jam 16.00
		'command'    => 'php ' . FCPATH . 'index.php site send_notif_kinerja',
		'label'      => 'send_notif_kinerja',
	],
];
