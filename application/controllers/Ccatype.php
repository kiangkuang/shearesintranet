<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ccatype extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->isLoggedIn || !$this->account->is_admin) {
            redirect('/');
        }
        $this->load->model('ccatypes_model');
    }

    public function view($search = null)
    {
        $data = [];
        $data['ccatypes'] = $this->ccatypes_model->getAll();

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['subSubMenu'] = 'viewCcatype';
        $data['this'] = $this;
        $this->twig->display('ccatype/view', $data);
    }

    public function edit($id = null)
    {
        $data = [];
        if ($id) {
            // editing existing cca
            $data['ccatype'] = $this->ccatypes_model->getById($id);
            if ($data['ccatype'] === false) {
                $this->session->set_flashdata('error', 'CCA Type not found!');
                redirect('/ccatype/view');
            }
        }

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['this'] = $this;
        $this->twig->display('ccatype/edit', $data);
    }

    public function update()
    {
        if (!$this->input->post() || !$this->editable) {
            redirect('/');
        }

        $input = $this->input->post();

        if (isset($input['id'])) {
            // update
            $exist = $this->ccatypes_model->getByName($input['name']);
            if ($exist && $exist->id !== $input['id']){
                $this->session->set_flashdata('error', 'Name already exists!');
                redirect('/ccatype/edit/'.$input['id']);
            }

            $result = $this->ccatypes_model->update($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA Type successfully updated!');
                redirect('/ccatype/edit/'.$input['id']);
            } else {
                $this->session->set_flashdata('error', 'An error has occurred!');
                redirect('/ccatype/edit/'.$input['id']);
            }
        } else {
            // add
            $exist = $this->ccatypes_model->getByName($input['name']);
            if ($exist){
                $this->session->set_flashdata('error', 'Name already exists!');
                redirect('/ccatype/edit');
            }

            $result = $this->ccatypes_model->insert($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA Type successfully created!');
                redirect('/ccatype/edit/'.$result);
            } else {
                $this->session->set_flashdata('error', 'An error has occurred!');
                redirect('/ccatype/edit');
            }
        }
    }

    public function delete($id = null)
    {
        if (!$this->editable || !$id) {
            redirect('/');
        }

        $result = $this->ccatypes_model->deleteById($id);
        if ($result) {
            $this->session->set_flashdata('success', 'CCA Type successfully deleted!');
        } else {
            $this->session->set_flashdata('error', 'An error has occurred!');
        }
        redirect('/ccatype/view');
    }

}
