<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cca_library
{
    public function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model('accounts_model');
        $this->CI->load->model('ccas_model');
        $this->CI->load->model('ccatypes_model');
        $this->CI->load->model('ccaclassifications_model');
    }

    public function appendCca($array)
    {
        if ($array === false) {
            return false;
        }

        foreach ($array as &$object) {
            $object->cca = $this->CI->ccas_model->getById($object->cca_id);
        }
        return $array;
    }

    public function appendTypeObject($array)
    {
        if ($array === false) {
            return false;
        }

        foreach ($array as &$object) {
            $object->typeObject = $this->CI->ccatypes_model->getById($object->type);
        }
        return $array;
    }

    public function appendClassificationObject($array)
    {
        if ($array === false) {
            return false;
        }

        foreach ($array as &$object) {
            $object->classificationObject = $this->CI->ccaclassifications_model->getById($object->classification);
        }
        return $array;
    }

    public function getUnjoinedAccounts($memberships)
    {
        $joinedAccountIds = pluck($memberships, 'account_id');
        $accounts = $this->CI->accounts_model->getAllOrderedByName();

        // remove joined accounts from array
        if ($accounts) {
            foreach ($accounts as $i => $account) {
                if (in_array($account->id, $joinedAccountIds) || $account->is_admin === '1') {
                    unset($accounts[$i]);
                }
            }
        }

        return $accounts;
    }
}
