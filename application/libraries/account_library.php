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

    public function appendMembershipSummary($accounts)
    {
        foreach ($accounts as &$account) {
            // ccas list
            $memberships = $this->CI->memberships_model->getByAccountId($account->id);
            $memberships = $this->CI->cca_library->appendCca($memberships);

            $membershipSummary = [];
            if ($memberships) {
                foreach ($memberships as $membership) {
                    $membershipSummary[] = (object) [
                        'cca_id' => $membership->cca->id,
                        'cca_name' => $membership->cca->name,
                        'points' => $membership->points,
                    ];
                }
            }
            $account->membershipSummary = $membershipSummary;
        }

        return $accounts;
    }

    public function getUnjoinedCcas($memberships)
    {
        $joinedCcaIds = pluck($memberships, 'cca_id');
        $ccas = $this->CI->ccas_model->getAllOrderedByName();

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
