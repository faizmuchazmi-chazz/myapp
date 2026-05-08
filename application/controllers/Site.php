<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Site extends Core_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->library('migration');
		$this->load->helper('app');
	}

	// function index()
	// {
	// 	redirect('ck/bht');
	// }
	function index()
	{
		$this->showBreadcrumb = false;

		$classes = [
			'Manu' => 'primary',
			'Web' => 'success',
			'Monitoring' => 'danger',
			'MA' => 'warning',
			'Badilag' => 'info',
			'PTA Surabaya' => 'light text-dark',
			'Lain-lain' => 'secondary',
		];

		$this->load->model('app/Web_Model', 'web');
		$this->vars['main_body'] = 'layout_content';
		$this->vars['view'] = 'site/one';
		$this->vars['classes'] = $classes;
		$this->vars['apps'] = $this->_transform_data($this->web->find());

		if (!$this->ion_auth->logged_in()) {
			$this->vars['lightAssets'] = true;
			// $this->vars['showParticles'] = true;
		}
		$this->load->vars($this->vars);

		if ($this->input->is_ajax_request()) {
			return $this->viewAjax('layout_content', [
				'title' => APP_NAME,
				'showPageHeader' => false,
			]);
		}

		return $this->load->view('layout');
	}

	function send_notif_kinerja()
	{
		$useQueue = $this->input->get('use_queue') !== 'false';
		$this->load->model('rekapitulasi/Ratio_Model', 'ratio');

		$ratio = $this->ratio->get_ratio();
		$kinerja_bas = $this->ratio->kinerja_bas();
		$kinerja_pp_setor = $this->ratio->kinerja_pp_setor();

		if (!$ratio) {
			$this->send_stream_data(['message' => 'Data ratio tidak tersedia', 'status' => false]);
			return;
		}

		$tunggakan_tahun_lalu = isset($ratio->tunggakan_tahun_lalu) ? $ratio->tunggakan_tahun_lalu : 0;
		$masuk_tahun_ini = isset($ratio->masuk_tahun_ini) ? $ratio->masuk_tahun_ini : 0;
		$total = $tunggakan_tahun_lalu + $masuk_tahun_ini;
		$minutasi_tahun_ini = isset($ratio->minutasi_tahun_ini) ? $ratio->minutasi_tahun_ini : 0;
		$tunggakan_total = isset($ratio->tunggakan_total) ? $ratio->tunggakan_total : 0;

		$persentase_perkara = isset($ratio->persentase_perkara) ? $ratio->persentase_perkara : 0;
		$minutasi_total_for_calc = isset($ratio->minutasi_total) ? $ratio->minutasi_total : $minutasi_tahun_ini;
		$perkara_putus_display = number_format_indo($minutasi_total_for_calc) . ' / ' . number_format_indo($tunggakan_tahun_lalu + $masuk_tahun_ini);

		$persentase_ecourt = isset($ratio->persentase_ecourt) ? $ratio->persentase_ecourt : 0;
		$ecourt = isset($ratio->ecourt) ? $ratio->ecourt : 0;
		$ecourt_display = number_format_indo($ecourt) . ' / ' . number_format_indo($masuk_tahun_ini);

		$percentage_bas = isset($kinerja_bas->percentage_bas) ? $kinerja_bas->percentage_bas : 0;
		$uploaded_bas = isset($kinerja_bas->uploaded_bas) ? $kinerja_bas->uploaded_bas : 0;
		$jumlah_sidang = isset($kinerja_bas->jumlah_sidang) ? $kinerja_bas->jumlah_sidang : 0;
		$not_uploaded_bas = $jumlah_sidang - $uploaded_bas;
		$bas_display = number_format_indo($uploaded_bas) . ' / ' . number_format_indo($jumlah_sidang);

		$percentage_minutasi = isset($kinerja_pp_setor->percentage_minutasi) ? $kinerja_pp_setor->percentage_minutasi : 0;
		$setor_putus_tahun_ini = isset($kinerja_pp_setor->setor_putus_tahun_ini) ? $kinerja_pp_setor->setor_putus_tahun_ini : 0;
		$jumlah_putus_tahun_ini = isset($kinerja_pp_setor->jumlah_putus_tahun_ini) ? $kinerja_pp_setor->jumlah_putus_tahun_ini : 0;
		$belum_setor_putus_tahun_ini = $jumlah_putus_tahun_ini - $setor_putus_tahun_ini;
		$minutasi_display = number_format_indo($setor_putus_tahun_ini) . ' / ' . number_format_indo($jumlah_putus_tahun_ini);

		$text = "📊 *Laporan Kinerja Penyelesaian Perkara " . date('Y') . "*\n\n";

		$text .= "👉🏼 *Penanganan Perkara: " . $persentase_perkara . "%*\n";
		$text .= "  • Sisa Tahun Lalu: " . number_format_indo($tunggakan_tahun_lalu) . " perkara\n";
		$text .= "  • Masuk Tahun Ini: " . number_format_indo($masuk_tahun_ini) . " perkara\n";
		$text .= "  • Total: " . number_format_indo($total) . " perkara\n";
		$text .= "  • Minutasi Tahun Ini: " . number_format_indo($minutasi_tahun_ini) . " perkara\n";
		$text .= "  • Tunggakan: " . number_format_indo($tunggakan_total) . " perkara\n\n";

		$text .= "👉🏼 *e-Court:* " . $persentase_ecourt . "% ({$ecourt_display})\n\n";

		$text .= "👉🏼 *Unggah BAS:* " . $percentage_bas . "%\n";
		$text .= "  • Jumlah Sidang: " . number_format_indo($jumlah_sidang) . "\n";
		$text .= "  • Belum Unggah: *" . number_format_indo($not_uploaded_bas) . "*\n\n";

		$text .= "👉🏼 *Putus Setor Panmud:* " . $percentage_minutasi . "%\n";
		$text .= "  • Jumlah Putus: " . number_format_indo($jumlah_putus_tahun_ini) . "\n";
		$text .= "  • Belum Setor: *" . number_format_indo($belum_setor_putus_tahun_ini) . "*\n\n";
		$text .= notif_footer();

		$targets = (is_development() ? cleanse_phone_number(WA_TEST_TARGET) : cleanse_phone_number(WA_KINERJA_TARGET));
		$x = 0;
		$total_targets = count($targets);

		if ($total_targets > 0) {
			foreach ($targets as $no) {
				$waData = [
					'type' => 'Laporan Kinerja Perkara',
					'target' => $no,
					'text' => $text,
				];
				send_wa($waData);
				sleep(2);

				$x++;
				$this->send_stream_data([
					'progress' => round($x * 100 / $total_targets),
					'no' => $x,
					'message' => 'Notifikasi sudah dalam proses pengiriman',
					'status' => true
				]);
			}

			$this->send_stream_data([
				'progress' => 100,
				'message' => "Selesai, {$x} notifikasi laporan kinerja terkirim",
				'status' => $x > 0
			]);
		} else {
			$this->send_stream_data([
				'progress' => 100,
				'message' => 'Target WhatsApp (WA_KINERJA_TARGET) belum diset',
				'status' => false
			]);
		}
	}

	function get_ratio()
	{
		$this->load->model('rekapitulasi/Ratio_Model', 'ratio');

		$this->vars['main_body'] = 'layout_content';
		$this->vars['view'] = 'site/_ratio';
		$this->vars['count_summary'] = $this->ratio->get_count_summary(date('Y'));
		$this->vars['count_dirput_antrian'] = $this->ratio->get_count_dirput_antrian();
		$this->vars['ratio'] = $this->ratio->get_ratio();
		$this->vars['count_redaksi'] = $this->ratio->get_count_redaksi();
		$this->vars['count_dirput_perkara'] = $this->ratio->get_count_dirput_perkara();
		$this->vars['kinerja_bas'] = $this->ratio->kinerja_bas();
		$this->vars['kinerja_minutasi'] = $this->ratio->kinerja_minutasi();

		$this->load->vars($this->vars);

		if ($this->input->is_ajax_request()) {
			return $this->viewAjax('site/_ratio');
		}

		$this->load->view('layout_no_sidebar');
	}

	private function _transform_data($data)
	{
		$result = [];
		foreach ($data as $item) {
			// Handle icon: if it's a FontAwesome class (contains 'fa-'), use as-is
			// Otherwise, treat as image file and prepend uploads/web/
			$icon = null;
			if ($item->icon) {
				if (strpos($item->icon, 'fa-') !== false) {
					// FontAwesome icon - use as-is
					$icon = $item->icon;
				} else {
					// Image icon - prepend path
					$icon = file_url2('uploads/web/' . $item->icon);
				}
			}

			$result[$item->category][] = [
				$item->name,
				filter_var($item->url, FILTER_VALIDATE_URL) ? $item->url : base_url($item->url),
				$icon,
				$item->category,
				$item->tag,
				$item->icon_width,
				$item->icon_height,
				isset($item->description) ? $item->description : '',
			];
		}
		return $result;
	}
}
