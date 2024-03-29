<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_library
{
    public function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model('accounts_model');
        $this->CI->load->model('ccas_model');
        $this->CI->load->model('memberships_model');
        $this->CI->load->library('cca_library');
    }

    public function appendAccount($array)
    {
        if ($array === false) {
            return false;
        }

        foreach ($array as &$object) {
            $object->account = $this->CI->accounts_model->getById($object->account_id);
        }
        return $array;
    }

    public function appendTotalPoints($accounts)
    {
        foreach ($accounts as &$account) {
            $account->totalPoints = $this->CI->memberships_model->getTotalPointsByAccountId($account->id);
        }

        return $accounts;
    }

    public function appendMemberships($accounts)
    {
        foreach ($accounts as &$account) {
            $account->memberships = $this->CI->memberships_model->getByAccountIdJoinCcaName($account->id);
        }

        return $accounts;
    }

    public function getUnjoinedCcas($memberships)
    {
        $joinedCcaIds = $memberships? pluck($memberships, 'cca_id') : [];
        $ccas = $this->CI->ccas_model->getByAcadYearOrderedByName(ACAD_YEAR);

        // remove joined CCAs from array
        if ($ccas) {
            foreach ($ccas as $i => $cca) {
                if (in_array($cca->id, $joinedCcaIds)) {
                    unset($ccas[$i]);
                }
            }
        }

        return $ccas;
    }
}
