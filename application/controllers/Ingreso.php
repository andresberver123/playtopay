<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ingreso extends CI_Controller {

    function index() {
        $this->load->view('header');
        $this->load->view('templates/Ingreso');
        $this->load->view('footer');
    }

    function entrar() {
        
        $user = $_POST['email_user'];
        $pass = $_POST['passwd_user'];
        $passSha1 = sha1($pass);
        $sql = $this->db->query('select * from tbl_users where email = "' . $user . '" and password = "' . $passSha1 . '"');
        $arrays = $sql->row_array();
        $qryNumRows = $sql->num_rows();
        if ($qryNumRows == 1) {
            $usuario = $arrays['name'];
            $correo = $arrays['email'];
            $newdata = array(
                'username' => $usuario,
                'email' => $correo,
                'logged_in' => true
            );
            $this->session->set_userdata($newdata);
            redirect('productos', $newdata);
        } else {
            redirect('/');
        }
    }

    function logout() {
        $this->session->sess_destroy();
        redirect('/');
    }

}
