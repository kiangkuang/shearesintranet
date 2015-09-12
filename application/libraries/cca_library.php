<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cca_library
{
    public function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model('ccas_model');
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

    public function getUnjoinedCcas($memberships)
    {
        $joinedCcaIds = pluck($memberships, 'cca_id');
        $ccas = $this->CI->ccas_model->getAllOrderedByName();

        // remove joined CCAs from array
        if ($ccas) {
            foreach ($ccas as $key => $cca) {
                if (in_array($cca->id, $joinedCcaIds)) {
                    unset($ccas[$key]);
                }
            }
        }

        return $ccas;
    }
}
