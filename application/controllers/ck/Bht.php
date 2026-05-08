<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bht extends Core_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('auth');
		$this->load->helper('app');

		$this->load->model('ck/Bht_Model', 'disposisi');
		$this->model = $this->disposisi;

		$this->indexTitle = 'Kontrol Tanggal BHT';
		$this->indexSubtitle = 'Modul ini menampilkan progress rencana BHT.';
		$this->indexIcon = 'fa-solid fa-clipboard-check';
		$this->indexView = 'ck/bht/index';
		$this->module_id = 'monitoring_rencana_bht';
	}

	function send_notif_rencana_bht()
	{
		$useQueue = false;
		$today = date('Y-m-d');
		$tomorrow = date('Y-m-d', strtotime('+1 day'));
		$dayAfterTomorrow = date('Y-m-d', strtotime('+2 day'));

		$result = $this->disposisi->getRencanaBhtByDate($today, $tomorrow, $dayAfterTomorrow);

		if (!empty($result)) {
			$text = "📅 *Kontrol Tanggal BHT*\n";
			foreach ($result as $row) {
				$formattedDate = format_date($row->tanggal_rencana_bht, "EEEE, dd MMMM yyyy");
				$text .= "\n*{$formattedDate} ({$row->jumlah} perkara)*\n";
				foreach ($row->perkaras as $perkara) {
					$text .= "• {$perkara->nomor} ({$perkara->pp})\n";
				}
			}
			$text .= "\n" . notif_footer();

			$targets = (is_development() ? cleanse_phone_number(WA_TEST_TARGET) : cleanse_phone_number(WA_BHT_TARGET));
			$x = 0;
			foreach ($targets as $no) {
				$waData = [
					'type' => 'Rencana BHT',
					'target' => $no,
					'text' => $text,
				];
				if ($useQueue) {
					queue_wa_message($waData);
				} else {
					send_wa($waData);
					sleep(3);
				}
				$x++;
				$this->send_stream_data(['progress' => round($x * 100 / count($targets)), 'no' => $x, 'message' => 'Notifikasi sudah dalam proses pengiriman', 'status' => true]);
			}

			$this->send_stream_data(['progress' => 100, 'message' => $targets > 0 ? "Selesai, {$x} notifikasi sudah dalam proses pengiriman" : "WA_TEST_TARGET dan/atau WA_BHT_TARGET belum diset", 'status' => $x >= 0]);
		} else {
			$this->send_stream_data([
				'progress' => 100,
				'status' => true,
				'message' => 'Tidak ada rencana BHT untuk dikirim',
			]);
		}
	}
}
