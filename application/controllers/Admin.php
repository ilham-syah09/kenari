<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
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
        $data = $this->db->get('tb_data', 1)->row();

        $data = [
            'title'     => 'Dashboard',
            'page'      => 'admin/dashboard',
            'nilai'     => $data
        ];

        $this->load->view('admin/index', $data);
    }

    public function get_realtime()
    {
        $count = $this->db->get('tb_data')->num_rows();

        $this->db->order_by('id', 'desc');
        $data = $this->db->get('tb_data', 1)->row();

        echo json_encode([
            'data'      => $data,
            'count'     => $count
        ]);
    }

    public function list_admin()
    {
        $data = [
            'title' => 'List Admin',
            'page'  => 'admin/list_admin',
            'admin' => $this->admin->getadmin()
        ];

        $this->load->view('admin/index', $data);
    }

    public function add_admin()
    {
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            # code...
            $this->list_admin();
            redirect('list-admin');
        } else {

            $img = $_FILES['image']['name'];

            if ($img) {

                $config['upload_path']      = 'uploads/profile';
                $config['allowed_types']    = 'jpg|jpeg|png';
                $config['max_size']         = 2000;
                $config['remove_spaces']    = TRUE;
                $config['encrypt_name']     = TRUE;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('image')) {
                    $this->session->set_flashdata('toastr-eror', $this->upload->display_errors());
                    redirect('list-admin');
                } else {
                    $upload_data = $this->upload->data();
                    $data = [
                        'username'      => htmlspecialchars($this->input->post('username')),
                        'password'      => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
                        'image'     => $upload_data['file_name']
                    ];

                    $insert = $this->db->insert('tb_admin', $data);

                    if ($insert) {
                        $this->session->set_flashdata('toastr-sukses', 'success !');
                        redirect('list-admin', 'refresh');
                    } else {
                        $this->session->set_flashdata('toastr-eror', 'failed!');
                        redirect('list-admin', 'refresh');
                    }
                }
            } else {
                $data = [
                    'username'      => htmlspecialchars($this->input->post('username')),
                    'password'      => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
                ];

                $insert = $this->db->insert('tb_admin', $data);
                if ($insert) {
                    $this->session->set_flashdata('toastr-sukses', 'success !');
                    redirect('list-admin', 'refresh');
                } else {
                    $this->session->set_flashdata('toastr-eror', 'failed!');
                    redirect('list-admin', 'refresh');
                }
            }
        }
    }

    public function delete_admin($id)
    {
        if ($id) {
            $this->db->where('id', $id);
            $data = $this->db->get('tb_admin')->row();

            $this->db->where('id', $id);
            $delete = $this->db->delete('tb_admin');

            if ($delete) {
                if ($data->image != 'default.png') {
                    unlink(FCPATH . 'uploads/profile/' . $data->image);
                }

                $this->session->set_flashdata('toastr-sukses', 'Data berhasil di hapus');
            } else {
                $this->session->set_flashdata('toastr-eror', 'Data gagal di hapus!!');
            }
        }

        redirect('list-admin', 'refresh');
    }

    public function update_admin()
    {
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            # code...
            $this->list_admin();
            redirect('list-admin');
        } else {

            $img = $_FILES['image']['name'];

            if ($img) {

                $config['upload_path']      = 'uploads/profile';
                $config['allowed_types']    = 'jpg|jpeg|png';
                $config['max_size']         = 2000;
                $config['remove_spaces']    = TRUE;
                $config['encrypt_name']     = TRUE;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('image')) {
                    $this->session->set_flashdata('toastr-eror', $this->upload->display_errors());
                    redirect('list-admin');
                } else {
                    $upload_data = $this->upload->data();
                    $previmage = $this->input->post('previmage');

                    $data = [
                        'username'      => htmlspecialchars($this->input->post('username')),
                        'password'      => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
                        'image'     => $upload_data['file_name']
                    ];

                    $this->db->where('id', $this->input->post('id'));
                    $insert = $this->db->update('tb_admin', $data);

                    if ($insert) {
                        if ($previmage != 'default.png') {
                            unlink(FCPATH . 'uploads/profile/' . $previmage);
                        }
                        $this->session->set_flashdata('toastr-sukses', 'success !');
                        redirect('list-admin', 'refresh');
                    } else {
                        $this->session->set_flashdata('toastr-eror', 'failed!');
                        redirect('list-admin', 'refresh');
                    }
                }
            } else {
                $data = [
                    'username'      => htmlspecialchars($this->input->post('username')),
                    'password'      => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
                ];

                $this->db->where('id', $this->input->post('id'));
                $insert = $this->db->update('tb_admin', $data);

                if ($insert) {
                    $this->session->set_flashdata('toastr-sukses', 'success !');
                    redirect('list-admin', 'refresh');
                } else {
                    $this->session->set_flashdata('toastr-eror', 'failed!');
                    redirect('list-admin', 'refresh');
                }
            }
        }
    }
}

/* End of file Admin.php */
