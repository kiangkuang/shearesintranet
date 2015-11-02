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

        if ($this->session->accountId) {
            $this->isLoggedIn = true;
            $this->account = $this->accounts_model->getById($this->session->accountId);
        }

        if ($this->session->success){
            $this->success = $this->session->success;
        }

        if ($this->session->error){
            $this->error = $this->session->error;
        }

        if ($this->session->warning){
            $this->warning = $this->session->warning;
        } else if ($this->account && $this->account->is_first_login) {
            $this->warning = 'Change your password now to proceed!';
        }

        if ($this->session->info){
            $this->info = $this->session->info;
        } else if ($this->session->acadYearView !== ACAD_YEAR) {
            $this->info = 'Viewing past AY' . $this->session->acadYearView . ' archive! Editing is not allowed. Change <a href="/archive">here</a>.';
            $this->editable = false;
        }

    }
}
