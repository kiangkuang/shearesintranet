<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ccatype extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->isLoggedIn) {
            redirect('/');
        }
        $this->load->model('ccatypes_model');
    }

    public function view($search = false)
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];
        $data['ccatypes'] = $this->ccatypes_model->getAll();

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['subSubMenu'] = 'viewCcatype';
        $this->load->view('ccatype/view',$data);
    }

    public function edit($id = false)
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];
        if ($id) {
            // editing existing cca
            $data['ccatype'] = $this->ccatypes_model->getById($id);
        }

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        if ($id === false) {
            $data['subSubMenu'] = 'addCcatype';
        }
        $this->load->view('ccatype/edit',$data);
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
            $result = $this->ccatypes_model->update($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA Type successfully updated!');
                redirect('/ccatype/view/'.$input['name']);
            } else {
                $this->session->set_flashdata('error', 'An error has occured!');
                redirect('/ccatype/edit/'.$input['id']);
            }
        } else {
            // add
            $result = $this->ccatypes_model->insert($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA Type successfully created!');
                redirect('/ccatype/view/'.$input['name']);
            } else {
                $this->session->set_flashdata('error', 'An error has occured!');
                redirect('/ccatype/edit');
            }
        }
    }

    public function delete($id = false)
    {
        if (!$id) {
            redirect('/ccatype/view');
        }

        $result = $this->ccatypes_model->deleteById($id);
        if ($result) {
            $this->session->set_flashdata('success', 'CCA Type successfully deleted!');
        } else {
            $this->session->set_flashdata('error', 'An error has occured!');
        }
        redirect('/ccatype/view');
    }
}
