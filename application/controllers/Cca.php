<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cca extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->isLoggedIn) {
            redirect('/login');
        }
        $this->load->model('ccas_model');
        $this->load->model('ccatypes_model');
        $this->load->model('ccaclassifications_model');
        $this->load->model('memberships_model');
        $this->load->library('cca_library');
    }

    public function userCca()
    {
        $data = [];

        $data['memberships'] = $this->memberships_model->getByAccountIdJoinCcaName($this->account->id);
        $data['totalPoints'] = $this->memberships_model->getTotalPointsByAccountId($this->account->id);

        //$data['mainMenu'] = 'myCca';
        $data['this'] = $this;
        $this->twig->display('cca/userCca',$data);
    }

    public function view($search = null)
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];

        $data['ccas'] = $this->ccas_model->getAllJoinTypeNameJoinClassificationName();

        if ($this->input->get('search')) {
            $search = $this->input->get('search');
        }
        $data['search'] = urldecode($search);

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['subSubMenu'] = 'viewCca';
        $data['this'] = $this;
        $this->twig->display('cca/view',$data);
    }

    public function edit($id = null)
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];
        if ($id) {
            $data['cca'] = $this->ccas_model->getByIdAcadYear($id);

            if ($data['cca'] === false) {
                $this->session->set_flashdata('error', 'CCA not found!');
                redirect('/cca/view');
            }

            $data['memberships'] = $this->memberships_model->getByCcaIdJoinAccountName($id);
            $data['accounts'] = $this->cca_library->getUnjoinedAccounts($data['memberships']);
        }

        $data['types'] = $this->ccatypes_model->getAll();
        $data['classifications'] = $this->ccaclassifications_model->getAll();

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['this'] = $this;
        $this->twig->display('cca/edit',$data);
    }

    public function update()
    {
        if (!$this->account->is_admin || !$this->input->post() || !$this->editable) {
            redirect('/');
        }

        $input = $this->input->post();
        $input['shortname'] = strtolower(str_replace(' ', '-', $input['name']));

        if (isset($input['id'])) {
            // update
            $result = $this->ccas_model->update($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA successfully updated!');
                redirect('/cca/edit/'.$input['id']);
            } else {
                $this->session->set_flashdata('error', 'An error has occured!');
                redirect('/cca/edit/'.$input['id']);
            }
        } else {
            // add
            $result = $this->ccas_model->insert($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA successfully created!');
                redirect('/cca/edit/'.$result);
            } else {
                $this->session->set_flashdata('error', 'An error has occured!');
                redirect('/cca/edit');
            }
        }
    }

    public function delete($id = null)
    {
        if (!$this->account->is_admin || !$this->editable || !$id) {
            redirect('/');
        }

        $result = $this->ccas_model->deleteById($id);
        $result2 = $this->memberships_model->deleteByCcaId($id);
        if ($result && $result2) {
            $this->session->set_flashdata('success', 'CCA successfully deleted!');
        } else {
            $this->session->set_flashdata('error', 'An error has occured!');
        }
        redirect('/cca/view');
    }

    public function import()
    {
        if (!$this->account->is_admin || !$this->editable) {
            redirect('/');
        }

        $data = [];

        $input = $this->input->post();
        if ($input) {
            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'csv';
            $config['max_size']             = 2048;

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload('file'))
            {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect('cca/import');
            }
            else
            {
                $upload = $this->upload->data();


                $csvFile = new Keboola\Csv\CsvFile($upload['full_path']);
                foreach($csvFile as $row) {
                    $file[] = $row;
                }
                var_dump($file);
            }
        }

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['this'] = $this;
        $this->twig->display('cca/import',$data);
    }

}
