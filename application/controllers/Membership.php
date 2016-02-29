<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Membership extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->isLoggedIn) {
            redirect('/');
        }
        $this->load->model('accounts_model');
        $this->load->model('ccas_model');
        $this->load->model('memberships_model');
    }

    public function addMembership()
    {
        if (!$this->account->is_admin || !$this->input->post() || !$this->editable) {
            redirect('/');
        }
        if ($this->input->post('cca_id')) {
            $type = 'cca';
        } elseif ($this->input->post('account_id')) {
            $type = 'account';
        }

        if ($type === 'cca') {
            // cca adding members
            foreach ($this->input->post('account_ids') as $account_id) {
                $input[] = [
                    'cca_id' => $this->input->post('cca_id'),
                    'account_id' => $account_id,
                    'acad_year' => ACAD_YEAR, 
                ];
            }
        }

        if ($type === 'account') {
            // accounts joining ccas
            foreach ($this->input->post('cca_ids') as $cca_id) {
                $input[] = [
                    'account_id' => $this->input->post('account_id'),
                    'cca_id' => $cca_id,
                    'acad_year' => ACAD_YEAR, 
                ];
            }
        }

        $result = $this->memberships_model->insertBatch($input);
        if ($result) {
            $this->session->set_flashdata('success', count($input).' membership successfully added!');
        } else {
            $this->session->set_flashdata('error', 'An error has occurred!');
        }
        if ($type === 'cca') {
            redirect('/cca/edit/'.$this->input->post('cca_id'));
        } elseif ($type === 'account') {
            redirect('/account/edit/'.$this->input->post('account_id'));
        }
    }

    public function updateMemberships()
    {
        if (!$this->account->is_admin || !$this->input->post() || !$this->editable) {
            redirect('/');
        }
        if ($this->input->post('cca_id')) {
            $type = 'cca';
        } elseif ($this->input->post('account_id')) {
            $type = 'account';
        }

        $input = $this->input->post('memberships');
        foreach ($input as &$row) {
            foreach ($row as &$col) {
                $col = trim($col);
            }
        }

        $result = $this->memberships_model->updateBatch($input);

        if ($result !== false) {
            $this->session->set_flashdata('success', 'Memberships successfully updated!');
        } else {
            $this->session->set_flashdata('error', 'An error has occurred!');
        }
        
        if ($type === 'cca') {
            redirect('/cca/edit/'.$this->input->post('cca_id'));
        } elseif ($type === 'account') {
            redirect('/account/edit/'.$this->input->post('account_id'));
        }
    }

    public function delete()
    {
        if (!$this->account->is_admin || !$this->input->post() || !$this->editable) {
            redirect('/');
        }

        $id = $this->input->post('id');
        $redirect = $this->input->post('redirect');
        $membership = $this->memberships_model->getById($id);
        $result = $this->memberships_model->deleteById($id);
        if ($result) {
            $this->session->set_flashdata('success', 'Membership successfully deleted!');
        } else {
            $this->session->set_flashdata('error', 'An error has occurred!');
        }

        redirect($redirect);
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
                redirect('membership/import');
            } else {
                $accounts = $this->accounts_model->getByAcadYear(ACAD_YEAR);
                foreach ($accounts as $account) {
                    $accountArray[$account->id] = $account->user;
                }

                $ccas = $this->ccas_model->getByAcadYear(ACAD_YEAR);
                foreach ($ccas as $cca) {
                    $ccaArray[$cca->id] = $cca->name;
                }

                $upload = $this->upload->data();
                $csvFile = new Keboola\Csv\CsvFile($upload['full_path']);
                $import = [];
                $update = [];
                $processedMemberships = [];
                foreach ($csvFile as $row) {
                    // ignore header row and empty names
                    if (trim($row[0]) !== 'CCA' && trim($row[1]) !== 'NUSNET ID' && trim($row[2]) !== 'Role' && trim($row[3]) !== 'Points' && trim($row[0]) !== '') {
                        $importRow = [];
                        $importRow['cca_id'] = array_search(trim($row[0]), $ccaArray) ? : null; // defaults to None type
                        $importRow['account_id'] = array_search(trim($row[1]), $accountArray) ? : null;
                        $importRow['role'] = trim($row[2]);
                        $importRow['points'] = (int)$row[3];
                        $importRow['acad_year'] = ACAD_YEAR;

                        if ($importRow['account_id'] === null || $importRow['cca_id'] === null) {
                            break;
                        }

                        if (array_search($importRow['cca_id'] + ',' + $importRow['account_id'], $processedMemberships) !== false) {
                            break;
                        }

                        $existingRow = $this->memberships_model->getByAccountIdCcaIdAcadYear($importRow['account_id'], $importRow['cca_id'], ACAD_YEAR);

                        if ($existingRow) {
                            $importRow['id'] = $existingRow->id;
                            $update[] = $importRow;
                        } else {
                            $import[] = $importRow;
                        }

                        $processedMemberships[] = $importRow['cca_id'] + ',' + $importRow['account_id'];
                    }
                }

                if ($import) {
                    $importResult = $this->memberships_model->insertBatch($import);
                }
                if ($update) {
                    $updateResult = $this->memberships_model->updateBatch($update);
                }

                if (isset($importResult) && $importResult !== false || isset($updateResult) && $updateResult !== false) {
                    $successMsg = count($import) ? count($import) . ' new Memberships added!<br>' : '';
                    $successMsg .= count($update) ? count($update) . ' existing Memberships updated!' : '';

                    $this->session->set_flashdata('success', $successMsg);
                    foreach ($import as &$row) {
                        $row['account_user'] = $accountArray[$row['account_id']];
                        $row['cca_name'] = $ccaArray[$row['cca_id']];
                    }
                    foreach ($update as &$row) {
                        $row['account_user'] = $accountArray[$row['account_id']];
                        $row['cca_name'] = $ccaArray[$row['cca_id']];
                    }
                    $this->session->set_flashdata('imported', $import);
                    $this->session->set_flashdata('updated', $update);
                    redirect('membership/import');
                } else {
                    $this->session->set_flashdata('error', 'Nothing imported!');
                    redirect('membership/import');
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

        $data['csrf'] = [
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        ];

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['this'] = $this;
        $this->twig->display('membership/import', $data);
    }

}
