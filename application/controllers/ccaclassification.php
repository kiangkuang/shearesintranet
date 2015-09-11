<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ccaclassification extends MY_Controller {

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
            if ($data['ccaclassification'] === false) {
                $this->session->set_flashdata('error', 'CCA Classification not found!');
                redirect('/ccaclassification/view');
            }
        }

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
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
                redirect('/ccaclassification/edit/'.$input['id']);
            } else {
                $this->session->set_flashdata('error', 'An error has occured!');
                redirect('/ccaclassification/edit/'.$input['id']);
            }
        } else {
            // add
            $result = $this->ccaclassifications_model->insert($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA Classification successfully created!');
                redirect('/ccaclassification/edit/'.$result);
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
