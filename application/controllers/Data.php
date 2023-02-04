<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data extends CI_Controller
{
    public function save()
    {
        $jml_pakan  = $this->input->get('jml_pakan');
        $jml_air    = $this->input->get('jml_air');
        $suhu       = $this->input->get('suhu');
        $kelembapan = $this->input->get('kelembapan');

        if ($jml_pakan != null && $jml_air != null && $suhu != null && $kelembapan != null) {

            $data = [
                'jml_pakan'     => $jml_pakan,
                'jml_air'       => $jml_air,
                'suhu'          => $suhu,
                'kelembapan'    => $kelembapan,
            ];

            $this->db->order_by('id', 'desc');

            $data_sebelumnya = $this->db->get('tb_data', 1)->row();

            $jmlpakan_sebelumnya    = $data_sebelumnya->jml_pakan;
            $jmlair_sebelumnya      = $data_sebelumnya->jml_air;
            $suhu_sebelumnya        = $data_sebelumnya->suhu;
            $kelembapan_sebelumnya  = $data_sebelumnya->kelembapan;

            if ($data_sebelumnya) {
                if ($jmlpakan_sebelumnya != $jml_pakan || $jmlair_sebelumnya != $jml_air || $suhu_sebelumnya != $suhu || $kelembapan_sebelumnya != $kelembapan) {

                    $this->db->insert('tb_data', $data);

                    echo 'Data berhasil masuk';
                } else {
                    echo 'Nilai data masih sama';
                }
            } else {
                $this->db->insert('tb_data', $data);

                echo 'Data berhasil masuk';
            }
        } else {
            echo 'Data kosong';
        }
    }

    public function setting()
    {
        $data = $this->db->get('tb_setting')->row();

        $jam_sekarang = date('H:i');

        if ($jam_sekarang == $data->jadwal) {
            echo "ON#" . $data->kondisi_suhu . "#OK";
        } else {
            echo "OFF#" . $data->kondisi_suhu . "#OK";
        }
    }
}

/* End of file Data.php */
