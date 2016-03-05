<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    var $isLoggedIn = false;
    var $account = false;
    var $success = false;
    var $warning = false;
    var $error = false;
    var $editable = true;

    public function __construct()
    {
        parent::__construct();

        define('ACAD_YEAR', getAcadYear());

        $this->load->model('accounts_model');
        $this->load->model('settings_model');

        $this->settings = $this->getSettings();

        if ($this->session->accountId) {
            $this->isLoggedIn = true;
            $this->account = $this->accounts_model->getById($this->session->accountId);
        }

        // can be replaced by session info
        if ($this->session->acadYearView !== ACAD_YEAR) {
            $this->info = 'Viewing archive of AY' . $this->session->acadYearView . '! Editing is not allowed. Change year <a href="/settings">here</a>.';
            $this->editable = false;
        }

        $this->setSessionAlerts();
    }

    // will replace change password warning and archive info
    private function setSessionAlerts()
    {
        if ($this->session->success){
            $this->success = $this->session->success;
        }

        if ($this->session->error){
            $this->error = $this->session->error;
        }

        if ($this->session->warning){
            $this->warning = $this->session->warning;
        } 

        if ($this->session->info){
            $this->info = $this->session->info;
        } 
    }

    private function getSettings()
    {
        $settings = $this->settings_model->getByAcadYear();
        if ($settings) {
            return $settings;
        } else {
            return $this->settings_model->getById($this->createSettings());
        }
    }

    private function createSettings()
    {
        $defaultSettings = [
            'acad_year' => ACAD_YEAR,
            'allow_login' => 1,
            'allow_preference' => 0,
            'allow_points' => 0,
        ];
        return $this->settings_model->insert($defaultSettings);
    }
}
