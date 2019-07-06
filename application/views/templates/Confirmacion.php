<?php 
if($approved == 1){
?>
<div class="jumbotron text-xs-center">
    <h1 class="display-3">Pagina de confirmación!</h1>
    <p class="lead"><strong>Estado de la orden</strong> APROBADA</p>
    <hr>
    
    <p class="lead">
        <a class="btn btn-primary btn-sm" href="<?php= base_url().'productos/' ?>" role="button">
            Home
        </a>
    </p>
</div>
<?php } ?>


<?php 
if($approved == 0){
?>
<div class="jumbotron text-xs-center">
    <h1 class="display-3">Pagina de confirmación!</h1>
    <p class="lead"><strong>Estado de la orden</strong> RECHAZADA</p>
    <hr>
    
    <p class="lead">
        <a class="btn" href="javascript:;" onclick="generateOrder(<?= $producto ?>);">
            Reintentar
        </a>
        
        <a class="btn btn-primary btn-sm" href="<?= base_url().'productos/' ?>" role="button">
            Home
        </a>
        
    </p>
</div>
<?php } ?>