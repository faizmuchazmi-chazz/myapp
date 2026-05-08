<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ratio_Detail_Model extends SippBase_Model
{
    public function get_bas_belum_unggah_group_by_pp()
    {
        $tahun = date('Y');
        $hari_ini = date('Y-m-d');

        $subqueryPp = $this->database
            ->select('
                perkara_panitera_pn.perkara_id,
                panitera_pn.id AS panitera_id,
                panitera_pn.nama_gelar
            ')
            ->from('perkara_panitera_pn')
            ->join('panitera_pn', 'panitera_pn.id = perkara_panitera_pn.panitera_id', 'inner')
            ->where('perkara_panitera_pn.urutan', 1)
            ->where('perkara_panitera_pn.aktif', 'Y')
            ->group_by('perkara_panitera_pn.perkara_id')
            ->get_compiled_select();

        return $this->database
            ->select('
                pp.nama_gelar AS nama_pp,
                COUNT(jd.id) AS jumlah_belum_upload
            ')
            ->from('perkara_jadwal_sidang jd')
            ->join('perkara p', 'p.perkara_id = jd.perkara_id', 'inner')
            ->join('(' . $subqueryPp . ') pp', 'pp.perkara_id = jd.perkara_id', 'left')
            ->where('YEAR(jd.tanggal_sidang)', $tahun)
            ->where('jd.tanggal_sidang <=', $hari_ini)
            ->where('jd.edoc_bas IS NULL', null, false)
            ->group_by('pp.panitera_id, pp.nama_gelar')
            ->order_by('jumlah_belum_upload', 'DESC')
            ->get()
            ->result();
    }

    public function get_bas_belum_unggah_detail()
        {
            $tahun = date('Y');
            $hari_ini = date('Y-m-d');

            $subqueryPp = $this->database
                ->select('
                    perkara_panitera_pn.perkara_id,
                    panitera_pn.id AS panitera_id,
                    panitera_pn.nama_gelar
                ')
                ->from('perkara_panitera_pn')
                ->join('panitera_pn', 'panitera_pn.id = perkara_panitera_pn.panitera_id', 'inner')
                ->where('perkara_panitera_pn.urutan', 1)
                ->where('perkara_panitera_pn.aktif', 'Y')
                ->group_by('perkara_panitera_pn.perkara_id')
                ->get_compiled_select();

            return $this->database
                ->select('
                    pp.panitera_id,
                    pp.nama_gelar AS nama_pp,
                    jd.id AS jadwal_sidang_id,
                    jd.perkara_id,
                    p.nomor_perkara,
                    p.jenis_perkara_nama,
                    jd.tanggal_sidang,
                    jd.agenda
                ')
                ->from('perkara_jadwal_sidang jd')
                ->join('perkara p', 'p.perkara_id = jd.perkara_id', 'inner')
                ->join('(' . $subqueryPp . ') pp', 'pp.perkara_id = jd.perkara_id', 'left')
                ->where('YEAR(jd.tanggal_sidang)', $tahun)
                ->where('jd.tanggal_sidang <=', $hari_ini)
                ->where('jd.edoc_bas IS NULL', null, false)
                ->order_by('pp.nama_gelar', 'ASC')
                ->order_by('jd.tanggal_sidang', 'DESC')
                ->get()
                ->result();
        }
}