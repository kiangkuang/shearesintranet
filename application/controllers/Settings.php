<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('accounts_model');
        $this->load->model('ccas_model');
        $this->load->model('settings_model');
        $this->load->model('rankings_model');
    }

    public function index()
    {
        if (!$this->isLoggedIn) {
            redirect('/');
        }

        $data = [];

        $data['settings'] = $this->settings;
        $data['acadYears'] = $this->accounts_model->getAcadYears();
        $data['currentAcadYearView'] = $this->session->acadYearView;

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'settings';
        $data['this'] = $this;
        $this->twig->display('settings/index', $data);
    }

    public function general()
    {
        if (!$this->account->is_admin || !$this->input->post()) {
            redirect('/');
        }

        $input = $this->input->post();

        $input['allow_login'] = isset($input['allow_login']) ? 1 : 0;
        $input['allow_ranking'] = isset($input['allow_ranking']) ? 1 : 0;
        $input['allow_points'] = isset($input['allow_points']) ? 1 : 0;

        $result = $this->settings_model->update($input);
        if ($result) {
            $this->session->set_flashdata('success', 'Settings successfully updated!');
            redirect('/settings');
        } else {
            $this->session->set_flashdata('error', 'An error has occurred!');
            redirect('/settings');
        }
    }

    public function archive()
    {
        if (!$this->account->is_admin || !$this->input->post()) {
            redirect('/');
        }

        $input = $this->input->post();

        $this->session->set_userdata('acadYearView', $input['acad_year']);
        redirect('/account/view');
    }

    public function exportRanking()
    {
        $csvFile = new Keboola\Csv\CsvFile('downloads/test-output.csv');

        // committee type_id = 2
        $ccas = $this->ccas_model->getByTypeIdAcadYear(2, ACAD_YEAR);
        $rankings = $this->rankings_model->getByAcadYearJoinAccountName(ACAD_YEAR);

        $ccasArray[0] = '-';
        foreach ($ccas as $cca) {
            $ccasArray[$cca->id] = $cca->name;
        }

        $csvFile->writeRow(['Name', '1st Choice', '2nd Choice', '3rd Choice', '4th Choice', '5th Choice']);
        foreach ($rankings as $ranking) {
            $csvFile->writeRow([
                $ranking->account_name,
                $ccasArray[$ranking->rank_1],
                $ccasArray[$ranking->rank_2],
                $ccasArray[$ranking->rank_3],
                $ccasArray[$ranking->rank_4],
                $ccasArray[$ranking->rank_5]
            ]);
        }

        force_download('downloads/test-output.csv', NULL);
    }

}
