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
        $this->load->model('preferences_model');
        $this->load->library('cca_library');
    }

    public function points()
    {
        if ($this->account->is_admin) {
            redirect('/');
        } else if (!$this->settings->allow_points) {
            $data['mainMenu'] = 'myCca';
            $data['subMenu'] = 'points';
            $data['this'] = $this;
            return $this->twig->display('settings/disabled', $data);
        }

        $data = [];

        $data['memberships'] = $this->memberships_model->getByAccountIdJoinCcaName($this->account->id);
        $data['totalPoints'] = $this->memberships_model->getTotalPointsByAccountId($this->account->id);

        $data['mainMenu'] = 'myCca';
        $data['subMenu'] = 'points';
        $data['this'] = $this;
        $this->twig->display('cca/points', $data);
    }

    public function preference()
    {
        if ($this->account->is_admin) {
            redirect('/');
        } else if (!$this->settings->allow_preference) {
            $data['mainMenu'] = 'myCca';
            $data['subMenu'] = 'preference';
            $data['this'] = $this;
            return $this->twig->display('settings/disabled', $data);
        }

        $input = $this->input->post();

        if ($input && isset($input['id'])) {
            // update
            $result = $this->preferences_model->update($input);
            if ($result) {
                $this->session->set_flashdata('success', 'Preference saved!');
                redirect('/cca/preference');
            } else {
                $this->session->set_flashdata('error', 'An error has occurred!');
                redirect('/cca/preference');
            }
        } elseif ($input) {
            // add
            $input['account_id'] = $this->account->id;
            $input['acad_year'] = ACAD_YEAR;

            $result = $this->preferences_model->insert($input);
            if ($result) {
                $this->session->set_flashdata('success', 'Preference saved!');
                redirect('/cca/preference');
            } else {
                $this->session->set_flashdata('error', 'An error has occurred!');
                redirect('/cca/preference');
            }
        }

        $data = [];

        // committee type_id = 2
        $data['ccas'] = $this->ccas_model->getByTypeIdAcadYear(2, ACAD_YEAR);
        $data['preference'] = $this->preferences_model->getByAccountId($this->account->id)[0];

        $data['mainMenu'] = 'myCca';
        $data['subMenu'] = 'preference';
        $data['this'] = $this;
        $this->twig->display('cca/preference', $data);
    }

    public function view($search = null)
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];

        $data['ccas'] = $this->ccas_model->getByAcadYearJoinTypeNameJoinClassificationName($this->session->acadYearView);

        if ($this->input->get('search')) {
            $search = $this->input->get('search');
        }
        $data['search'] = urldecode($search);

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['subSubMenu'] = 'viewCca';
        $data['this'] = $this;
        $this->twig->display('cca/view', $data);
    }

    public function edit($id = null)
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];
        if ($id) {
            $data['cca'] = $this->ccas_model->getByIdAcadYear($id, $this->session->acadYearView);

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
        $this->twig->display('cca/edit', $data);
    }

    public function update()
    {
        if (!$this->account->is_admin || !$this->input->post() || !$this->editable) {
            redirect('/');
        }

        $input = $this->input->post();
        foreach ($input as &$row) {
            $row = trim($row);
        }

        if (isset($input['id'])) {
            // update
            $exist = $this->ccas_model->getByNameAcadYear($input['name'], ACAD_YEAR);
            if ($exist && $exist->id !== $input['id']){
                $this->session->set_flashdata('error', 'Name already exists!');
                redirect('/cca/edit/'.$input['id']);
            }

            $result = $this->ccas_model->update($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA successfully updated!');
                redirect('/cca/edit/'.$input['id']);
            } else {
                $this->session->set_flashdata('error', 'An error has occurred!');
                redirect('/cca/edit/'.$input['id']);
            }
        } else {
            // add
            $exist = $this->ccas_model->getByNameAcadYear($input['name'], ACAD_YEAR);
            if ($exist){
                $this->session->set_flashdata('error', 'Name already exists!');
                redirect('/cca/edit');
            }

            $input['acad_year'] = ACAD_YEAR;

            $result = $this->ccas_model->insert($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA successfully created!');
                redirect('/cca/edit/'.$result);
            } else {
                $this->session->set_flashdata('error', 'An error has occurred!');
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
            $this->session->set_flashdata('error', 'An error has occurred!');
        }
        redirect('/cca/view');
    }

    public function import()
    {
        if (!$this->account->is_admin || !$this->editable) {
            redirect('/');
        }

        $input = $this->input->post();
        if ($input) {
            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'csv';
            $config['max_size']             = 2048;

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload('file')) {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect('cca/import');
            } else {
                $ccaTypes = $this->ccatypes_model->getAll();
                foreach ($ccaTypes as $ccaType) {
                    $ccaTypeArray[$ccaType->id] = $ccaType->name;
                }

                $ccaClassifications = $this->ccaclassifications_model->getAll();
                foreach ($ccaClassifications as $ccaClassification) {
                    $ccaClassificationArray[$ccaClassification->id] = $ccaClassification->name;
                }

                $upload = $this->upload->data();
                $csvFile = new Keboola\Csv\CsvFile($upload['full_path']);
                $import = [];
                $update = [];
                foreach ($csvFile as $row) {
                    // ignore header row and empty names
                    if (trim($row[0]) !== 'Name' && trim($row[1]) !== 'Type' && trim($row[2]) !== 'Classification' && trim($row[0]) !== '') {
                        $importRow['name'] = trim($row[0]);
                        $importRow['type_id'] = array_search(trim($row[1]), $ccaTypeArray) ? : 1; // defaults to None type
                        $importRow['classification_id'] = array_search(trim($row[2]), $ccaClassificationArray) ? : 1; // defaults to None classification
                        $importRow['acad_year'] = ACAD_YEAR;

                        $existingRow = $this->ccas_model->getByNameAcadYear($importRow['name'], ACAD_YEAR);
                        if ($existingRow) {
                            $importRow['id'] = $existingRow->id;
                            $update[] = $importRow;
                        } else {
                            unset($importRow['id']);
                            $import[] = $importRow;
                        }
                    }
                }

                if ($import) {
                    $importResult = $this->ccas_model->insertBatch($import);
                }
                if ($update) {
                    $updateResult = $this->ccas_model->updateBatch($update);
                }

                if (isset($importResult) && $importResult !== false || isset($updateResult) && $updateResult !== false) {
                    $successMsg = count($import) ? count($import) . ' new CCAs added!<br>' : '';
                    $successMsg .= count($update) ? count($update) . ' existing CCAs updated!' : '';

                    $this->session->set_flashdata('success', $successMsg);
                    foreach ($import as &$row) {
                        $row['type_name'] = $ccaTypeArray[$row['type_id']];
                        $row['classification_name'] = $ccaClassificationArray[$row['classification_id']];
                    }
                    foreach ($update as &$row) {
                        $row['type_name'] = $ccaTypeArray[$row['type_id']];
                        $row['classification_name'] = $ccaClassificationArray[$row['classification_id']];
                    }
                    $this->session->set_flashdata('imported', $import);
                    $this->session->set_flashdata('updated', $update);
                    redirect('cca/import');
                } else {
                    $this->session->set_flashdata('error', 'Nothing imported!');
                    redirect('cca/import');
                }
            }
        }

        $data = [];

        if ($this->session->imported) {
            $data['imported'] = $this->session->imported;
        }
        if ($this->session->updated) {
            $data['updated'] = $this->session->updated;
        }

        $data['lastAcadYear'] = substr(ACAD_YEAR, 0, 2)-1 . '/' . substr(ACAD_YEAR, 0, 2);
        $data['lastAcadYearCcas'] = $this->ccas_model->getByAcadYear($data['lastAcadYear']);

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['this'] = $this;
        $this->twig->display('cca/import', $data);
    }

    public function importLastYear()
    {
        if (!$this->account->is_admin || !$this->editable) {
            redirect('/');
        }

        $lastAcadYear = substr(ACAD_YEAR, 0, 2)-1 . '/' . substr(ACAD_YEAR, 0, 2);

        $ccas = $this->ccas_model->getByAcadYear($lastAcadYear);

        foreach ($ccas as $cca) {
            unset($cca->id, $cca->updated_at, $cca->created_at);
            $cca->acad_year = ACAD_YEAR;
        }

        $result = $this->ccas_model->insertBatch($ccas);

        if ($result !== false) {
            $this->session->set_flashdata('success', $result . ' CCAs imported from previous year!');
            redirect('cca/import');
        } else {
            $this->session->set_flashdata('error', 'Error updating database!');
            redirect('cca/import');
        }
    }

}
