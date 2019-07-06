<?php

require_once APPPATH . '../vendor/autoload.php';

use Dnetix\Redirection\PlacetoPay;

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Checkout extends CI_Controller {

    function index() {
        $data = $this->dashsession->inicioSession();
        if ($data['ordercurrent'] == TRUE) {
            $orderId = $data['orderId'];
            $orders = $this->db->query("select * from tbl_orders, tbl_orderproducts where pk_order = " . $orderId . " and fk_order = " . $orderId);
            $order = $orders->row_array();

            $products = $this->db->query("select * from tbl_productos where pk_producto = " . $order['fk_product']);
            $product = $products->row_array();
            $resultado = array_merge($order, $product);
            $data['resultado'] = $resultado;

            $this->load->view('header');
            $this->load->view('templates/Checkout', $data);
            $this->load->view('footer');
        } else {
            redirect("/");
        }
    }

    function payment() {

        $this->load->view('header');
        $this->load->view('templates/Payment');
        $this->load->view('footer');
    }

    function paymentProcess() {
        $data = $this->dashsession->inicioSession();
        $user = $data['username'];
        $data['user'] = $user;
        $usuarios = $this->db->query("select * from tbl_users where name = '" . $user . "' ");
        $usuario = $usuarios->row_array();
        $orderId = $data['orderId'];
        $orders = $this->db->query("select * from tbl_orders, tbl_orderproducts where pk_order = " . $orderId . " and fk_order = " . $orderId);
        $order = $orders->row_array();

        $products = $this->db->query("select * from tbl_productos where pk_producto = " . $order['fk_product']);
        $product = $products->row_array();
        $resultado = array_merge($order, $product);
        $ip = $_SERVER['REMOTE_ADDR'];
        $agent = $this->getAgent();

        if (function_exists('random_bytes')) {
            $nonce = bin2hex(random_bytes(16));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $nonce = bin2hex(openssl_random_pseudo_bytes(16));
        } else {
            $nonce = mt_rand();
        }
        $nonceBase64 = base64_encode($nonce);
        $seed = date('c');
        $secretKey = "024h1IlD";
        $tranKey = base64_encode(sha1($nonce . $seed . $secretKey, true));

        $placetopay = new Dnetix\Redirection\PlacetoPay([
            'login' => '6dd490faf9cb87a9862245da41170ff2',
            'tranKey' => $tranKey,
            'url' => 'https://test.placetopay.com/redirection/api/session/',
        ]);


        $request = [
            'payment' => [
                'reference' => $resultado['reference'],
                'description' => "prueba andres bernal - " . $resultado['reference'],
                'amount' => [
                    'currency' => 'USD',
                    'total' => $resultado['Total'],
                ],
            ],
            'expiration' => date('c', strtotime('+2 days')),
            'returnUrl' => base_url() . "/checkout/confirmacion/?orderId=" . $orderId,
            'ipAddress' => $ip,
            'userAgent' => $agent,
        ];

        $response = $placetopay->request($request);
        if ($response->isSuccessful()) {
            header('Location: ' . $response->processUrl());
        } else {
            $response->status()->message();
        }
        redirect(base_url() . "checkout/confirmacion/?orderId=" . $orderId);
    }

    function confirmacion() {

        $data = $this->dashsession->inicioSession();
        $user = $data['username'];
        $data['user'] = $user;
        
        $orderId = $data['orderId'];
        $orders = $this->db->query("select * from tbl_orders, tbl_orderproducts where pk_order = " . $orderId . " and fk_order = " . $orderId);
        $order = $orders->row_array();
        
        
        if (function_exists('random_bytes')) {
            $nonce = bin2hex(random_bytes(16));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $nonce = bin2hex(openssl_random_pseudo_bytes(16));
        } else {
            $nonce = mt_rand();
        }
        $seed = date('c');
        $secretKey = "024h1IlD";
        $tranKey = base64_encode(sha1($nonce . $seed . $secretKey, true));

        $placetopay = new Dnetix\Redirection\PlacetoPay([
            'login' => '6dd490faf9cb87a9862245da41170ff2',
            'tranKey' => $tranKey,
            'url' => 'https://test.placetopay.com/redirection/api/session/'
        ]);

        $response = $placetopay->query($order['reference']);
        if ($response->isSuccessful()) {
            if ($response->status()->isApproved()) {
                $this->db->query("UPDATE tbl_orders SET status = 'PAYED' where pk_order = " . $orderId);
                $data["approved"] = 1;
            }
        } else {
            $this->db->query("UPDATE tbl_orders SET status = 'REJECTED' where pk_order = " . $orderId);
            $data["approved"] = 0;
        }
        $data['producto'] = $order['fk_product'];
        $keys = array('orderId', 'ordercurrent');
        $this->session->unset_userdata($keys);

        $this->load->view('header');
        $this->load->view('templates/Confirmacion', $data);
        $this->load->view('footer');
    }

    function getAgent() {
        if ($this->agent->is_browser()) {
            $agent = $this->agent->browser() . ' ' . $this->agent->version();
        } elseif ($this->agent->is_robot()) {
            $agent = $this->agent->robot();
        } elseif ($this->agent->is_mobile()) {
            $agent = $this->agent->mobile();
        } else {
            $agent = 'Unidentified User Agent';
        }
        return $this->agent->platform();
    }

}
