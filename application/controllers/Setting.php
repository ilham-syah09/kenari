<?php
class Setting extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (empty($this->session->userdata('log_admin'))) {
            $this->session->set_flashdata('toastr-eror', 'Anda Belum Login');
            redirect('auth', 'refresh');
        }

        $this->db->where('id', $this->session->userdata('id'));
        $this->dt_admin = $this->db->get('tb_admin')->row();

        $this->load->model('M_admin', 'admin');
    }

    public function index()
    {
        $query = $this->db->get('tb_setting', 1)->row();

        $data = [
            'title'     => 'Setting',
            'page'      => 'admin/setting',
            'data'      => $query,
        ];

        $this->load->view('admin/index', $data);
    }

    public function change_setting()
    {
        $this->form_validation->set_rules('kondisi_suhu', 'Kondisi Suhu', 'trim|required|numeric');


        if ($this->form_validation->run() == FALSE) {
            $this->index();
            $this->session->set_flashdata('toastr-eror', 'isi semua kolom!');
            redirect('setting');
        } else {
            $data = [
                'status'          => $this->input->post('status'),
                'kondisi_suhu'    => $this->input->post('kondisi_suhu'),
            ];

            $this->db->where('id', $this->input->post('id'));
            $update = $this->db->update('tb_setting', $data);

            if ($update) {
                $this->session->set_flashdata('toastr-sukses', 'Sukses');
                redirect('setting');
            } else {
                $this->session->set_flashdata('toastr-eror', 'gagal');
                redirect('setting');
            }
        }
    }
}
