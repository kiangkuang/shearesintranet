<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_library
{
    public function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model('ccas_model');
        $this->CI->load->library('cca_library');
        $this->CI->load->library('membership_library');
    }

    public function appendTotalPoints($accounts)
    {
        foreach ($accounts as &$account) {
            $account->totalPoints = $this->CI->membership_library->getTotalPointsByAccountId($account->id);
        }

        return $accounts;
    }

    public function appendMembershipSummary($accounts)
    {
        foreach ($accounts as &$account) {
            // ccas list
            $memberships = $this->CI->memberships_model->getByAccountId($account->id);
            $memberships = $this->CI->cca_library->appendCca($memberships);

            $ccas = [];
            if ($memberships) {
                foreach ($memberships as $membership) {
                    $ccas[] = $membership->cca->name . ' <span class="pull-right">[' . $membership->points .']</span>';
                }
            }
            $account->ccas = implode('<br>', $ccas);
        }

        return $accounts;
    }
}
