<div class='container'>
    <div class='row' style='padding-top:25px; padding-bottom:25px;'>
        <div class='col-md-12'>
            <div id='mainContentWrapper'>
                <div class="col-md-8 col-md-offset-2">
                    <h2 style="text-align: center;">
                        Mi orden
                    </h2>
                    <hr/>
                    
                    <div class="shopping_cart">
                        <div class="panel-body">
                            <div class="items">
                                <div class="col-md-9">
                                    <table class="table table-striped">
                                        <tr>
                                            <td colspan="2">                                                
                                                <?= $resultado['nombre'] ?>
                                            </td>
                                        </tr>
                                        <tr>                                            
                                            <td>
                                                <b>$<?= $resultado['price'] ?></b>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-3">
                                    <div style="text-align: center;">
                                        <h3>Order Total</h3>
                                        <h3><span style="color:green;">$<?= $resultado['Total'] ?></span></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="<?=base_url()?>checkout/paymentProcess/" class="btn btn-primary btn-lg">
                        Comprar
                   </a>
                    
                </div>
            </div>
        </div>
    </div>
</div>