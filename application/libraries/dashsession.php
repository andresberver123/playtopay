<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashsession {

    function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->library('session');
    }

    public function inicioSession() {
        $data['inicioSession'] = FALSE;
        if ($this->CI->session->userdata("username")) {
            $data['inicioSession'] = TRUE;
            $data['username'] = $this->CI->session->userdata("username");
            if($this->CI->session->userdata("orderId")){
                $data['orderId'] = $this->CI->session->userdata("orderId");
                $data['ordercurrent'] = TRUE;
            }
        }
        //$this->output->set_output("<pre>".$data."</pre>");
        return $data;
    }

}

?>