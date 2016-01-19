<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('accounts_model');
        $this->load->model('ccas_model');
        $this->load->model('settings_model');
        $this->load->model('preferences_model');
        $this->load->library('account_library');
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
        $input['allow_preference'] = isset($input['allow_preference']) ? 1 : 0;
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

    public function export($type = null)
    {
        if ($type === 'preference') {
            $this->exportPreference();
        } elseif ($type === 'points') {
            $this->exportPoints();
        } else {
            redirect('/');
        }
    }

    public function exportPreference()
    {
        $csvFile = new Keboola\Csv\CsvFile('downloads/committee_preference.csv');

        // committee type_id = 2
        $ccas = $this->ccas_model->getByTypeIdAcadYear(2, ACAD_YEAR);
        $preferences = $this->preferences_model->getByAcadYearJoinAccountName(ACAD_YEAR);

        $ccasArray[0] = '-';
        foreach ($ccas as $cca) {
            $ccasArray[$cca->id] = $cca->name;
        }

        $csvFile->writeRow(['Name', '1st Choice', '2nd Choice', '3rd Choice', '4th Choice', '5th Choice']);
        foreach ($preferences as $preference) {
            $csvFile->writeRow([
                $preference->account_name,
                $ccasArray[$preference->rank_1],
                $ccasArray[$preference->rank_2],
                $ccasArray[$preference->rank_3],
                $ccasArray[$preference->rank_4],
                $ccasArray[$preference->rank_5]
            ]);
        }

        force_download('downloads/committee_preference.csv', NULL);
    }

    public function exportPoints()
    {
        $csvFile = new Keboola\Csv\CsvFile('downloads/points.csv');

        $accounts = $this->accounts_model->getByAcadYear(ACAD_YEAR);
        if ($accounts) {
            $accounts = $this->account_library->appendTotalPoints($accounts);
            $accounts = $this->account_library->appendMemberships($accounts);
        }

        $csvFile->writeRow(['Name', 'CCA', 'Points', 'Total Points']);
        foreach ($accounts as $account) {
            foreach ($account->memberships as $membership) {
                $csvFile->writeRow([
                    $account->name,
                    $membership->cca_name,
                    $membership->points,
                    $account->totalPoints->points
                ]);
            }
        }

        force_download('downloads/points.csv', NULL);
    }

}
