<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rekap extends CI_Controller
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
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('tb_data')->result();

        $data = [
            'title' => 'Rekap Data',
            'page'  => 'admin/rekap',
            'nilai' => $query,
        ];

        $this->load->view('admin/index', $data);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        $this->admin->delete($id);
        redirect('rekap');
    }
}

/* End of file Rekap.php */
