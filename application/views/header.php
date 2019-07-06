<?php
$data = $this->dashsession->inicioSession();

if ($data['inicioSession'] == TRUE) {
    $user = $data['username'];
    $data['user'] = $user;

    $usuarios = $this->db->query("select * from tbl_users where name = '" . $user . "' ");
    $usuario = $usuarios->row_array();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <head>
            <title>.:: Andres Bernal ::.</title>
            
            <link href="<?= base_url() ?>static/css/main_styles.css" rel="stylesheet" media="screen">
            <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>         
            <link href="<?= base_url() ?>static/css/bootstrap.css" rel="stylesheet" />
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"></link>
            <link href="<?= base_url() ?>static/css/bootstrap-responsive.css" rel="stylesheet" />            
            <script src="<?= base_url() ?>static/js/bootstrap.min.js" type="text/javascript"></script>
            <script src="<?= base_url() ?>static/js/Utils.js" type="text/javascript"></script>
        </head>
        <body id="homepage"> 
            <nav id="mainnav"> 
                <ul>                                       
                    <li>
                        <?php if ($data['inicioSession'] == true) { ?>
                            <a href="javascript:;"  accesskey="u">Bienvenvenid@: <?= $usuario['name'] ?></a>
                            <a href="<?= base_url() ?>ingreso/logout" >Salir</a>
                        <?php } ?>                            
                    </li>
                </ul>
            </nav>