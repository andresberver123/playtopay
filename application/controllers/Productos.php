<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Productos extends CI_Controller {

    function index() {   
        $data = $this->dashsession->inicioSession();
        if ($data['inicioSession'] == TRUE) {
            $user = $data['username'];
            $data['user'] = $user;
            $usuarios = $this->db->query("select * from tbl_users where name = '" . $user . "' ");
            $usuario = $usuarios->row_array();
            
            $productos = $this->helperdb->getProductos();
            $data['productos'] = $productos['results'];
            
            $this->load->view('header');
            $this->load->view('templates/Productos',$data);
            $this->load->view('footer');
        } else {
            redirect("/");
        }        
    }

}
