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
}
