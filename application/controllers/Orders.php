<?php



if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Orders extends CI_Controller {    

    function index() {
        
    }

    function Generate() {
        
        $data = $this->dashsession->inicioSession();
        if ($data['inicioSession'] == TRUE) {
            $user = $data['username'];
            $data['user'] = $user;
            $usuarios = $this->db->query("select * from tbl_users where name = '" . $user . "' ");
            $usuario = $usuarios->row_array();
            
            $productId = $_POST['productId'];
            $qty = $_POST['qty'];
            $productos = $this->db->query("select * from tbl_productos where pk_producto =" .$productId);
            $producto = $productos->row_array();

            $total = $producto['price'] * $qty;
            $reference = time();
            $dataOrder = array(
                "customer_name" => $usuario['name'],
                "customer_email" => $usuario['email'],
                "status" => "CREATED",
                "fk_estatus" => 1,
                "reference" => $reference,
                "SubTotal" => $producto['price'],
                "Total" => $total
            );

            $orderId = $this->helperdb->addOrders($dataOrder);
            if($orderId){
                $dataOrderProduct = array(
                    "fk_order" => $orderId,
                    "fk_product" => $productId,
                    "fk_estatus" => 1
                );        
                $this->helperdb->addOrderproducts($dataOrderProduct);
                $newdata = array(
                    'orderId' => $orderId,
                    'ordercurrent' => true
                );
                $this->session->set_userdata($newdata);
                return 1;
            }else{
                return 0;
            }             
            
        }else{
            redirect('/');
        }
    }
}

//Login: 6dd490faf9cb87a9862245da41170ff2
//TranKey: 024h1IlD