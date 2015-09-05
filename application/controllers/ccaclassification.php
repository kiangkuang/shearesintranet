<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ccaclassification extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->isLoggedIn) {
            redirect('/');
        }
        $this->load->model('ccaclassifications_model');
    }

    public function view($search = false)
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];
        $data['ccaclassifications'] = $this->ccaclassifications_model->getAll();

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['subSubMenu'] = 'viewCcaclassification';
        $this->load->view('ccaclassification/view',$data);
    }

    public function edit($id = false)
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];
        if ($id) {
            // editing existing cca
            $data['ccaclassification'] = $this->ccaclassifications_model->getById($id);
        }

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        if ($id === false) {
            $data['subSubMenu'] = 'addCcaclassification';
        }
        $this->load->view('ccaclassification/edit',$data);
    }

    public function update()
    {
        if (!$this->input->post()) {
            redirect('/');
        }

        $input = $this->input->post();
        $input['shortname'] = strtolower(str_replace(' ', '-', $input['name']));

        if (isset($input['id'])) {
            // update
            $result = $this->ccaclassifications_model->update($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA Classification successfully updated!');
                redirect('/ccaclassification/view/'.$input['name']);
            } else {
                $this->session->set_flashdata('error', 'An error has occured!');
                redirect('/ccaclassification/edit/'.$input['id']);
            }
        } else {
            // add
            $result = $this->ccaclassifications_model->insert($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA Classification successfully created!');
                redirect('/ccaclassification/view/'.$input['name']);
            } else {
                $this->session->set_flashdata('error', 'An error has occured!');
                redirect('/ccaclassification/edit');
            }
        }
    }

    public function delete($id = false)
    {
        if (!$id) {
            redirect('/ccaclassification/view');
        }

        $result = $this->ccaclassifications_model->deleteById($id);
        if ($result) {
            $this->session->set_flashdata('success', 'CCA Classification successfully deleted!');
        } else {
            $this->session->set_flashdata('error', 'An error has occured!');
        }
        redirect('/ccaclassification/view');
    }
}
