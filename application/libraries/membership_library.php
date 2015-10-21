<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Membership_library
{
    public function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model('memberships_model');
    }

    public function getTotalPointsByAccountId($account_id)
    {
        $memberships = $this->CI->memberships_model->getByAccountId($account_id);

        if ($memberships) {
            $sum = 0;
            foreach ($memberships as $membership) {
                $sum += $membership->points;
            }
            return $sum;
        }

        return false;
    }
}
