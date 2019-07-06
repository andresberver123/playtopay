<style type="text/css">
    * {
        outline: none;
    }

    a {
        color: #00A5F0;
        text-decoration: none;
        -webkit-transition: color .2s linear;
        -moz-transition: color .2s linear;
    }

    a:hover {
        color: #24BBFF;
    }

    ::selection {
        background: #E92C6C;
        color: white;
        text-shadow: 0 1px rgba(0, 0, 0, .2);
    }

    ::-moz-selection {
        background: #E92C6C;
        color: white;
        text-shadow: 0 1px rgba(0, 0, 0, .2);
    }

    ::-o-selection {
        background: #E92C6C;
        color: white;
        text-shadow: 0 1px rgba(0, 0, 0, .2);
    }

    body#login-page {

    }

    body {
        margin: 0;
        background-color: #DDD;
        color: #222;
        font: 11px "Helvetica Neue",Helvetica,Arial,sans-serif;

    }

    .login-wrap .mensaje {
        text-shadow: 0 1px 0 #EEE;
        color: #222;
        text-align: center;

        padding: 15px 0 0;
        margin: 0 auto;

    }

    .login-wrap h2 {
        font-size: 28px;
        margin: 0;
    }

    form#form-logeo {
        width: 280px;
        margin: 15px auto;
        background: url("image/separador.png") no-repeat top center;
        padding-top: 30px;
    }

    form.generic .field {
        margin-bottom: 10px;
    }

    form.generic label {
        display: block;
        font-weight: bold;
        font-size: 12px;
        color: #212121;
        text-shadow: 0 1px 0 #EEE;
        margin-bottom: 8px;
    }

    form#form-logeo input.text {
        width: 258px;
    }

    form.generic input.text {
        font-size: 14px;
        border-color: #DDD;
        -webkit-transition: border-color .2s linear;
        -moz-transition: border-color .2s linear;
        border: 1px solid #BBB;
        border-radius: 5px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        box-shadow: 0 1px 0 #eee;
        -webkit-box-shadow: 0 1px 0 #eee;
        -moz-box-shadow: 0 1px 0 #eee;
        display: block;
        width: auto;
        border-image: initial;
    }

    form input.text, form input.search, .contact-wrapper textarea {
        padding: 10px;
        margin: 0;
        font: 14px "HelveticaNeue", Helvetica, Arial, sans-serif;
    }

    form.generic label span {
        color: #878787;
        font-weight: normal;
    }

    form.generic .btnlogeo {
        overflow: hidden;
    }

    form.generic #login-submit {
        height: 36px;
        width: 280px;
        padding: 0 0 1px;
    }

    form.generic a.button, form.generic #login-submit {
        cursor: pointer;
        display: block;
        line-height: 34px;
        padding: 0 20px;
        font-size: 12px;
        font-weight: bold;
        font-family: inherit;
        background-color: #00A5F0;
        color: white;
        border-radius: 5px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border: 1px solid rgba(0, 0, 0, .2);
        box-shadow: 0 0 0 1px rgba(255, 255, 255, .4) inset, 0 1px #fff;
        -moz-box-shadow: 0 0 0 1px rgba(255, 255, 255, .4) inset, 0 1px #fff;
        -webkit-box-shadow: 0 0 0 1px rgba(255, 255, 255, .4) inset, 0 1px #fff;
        text-shadow: 0 -1px rgba(0, 0, 0, .2);
        text-align: center;
        border-image: initial;
    }
</style>

<div id="contentwrapper">
    <div id="skip"></div>
    <section>
        <div class="login-wrap">
            <div class="mensaje">
                <h2>Datos de facturación</h2>
            </div> 
            
            <form id="form-logeo" class="generic" method="POST" action="<?= base_url() ?>checkout/paymentProcess">
                <div id="login-user" class="field">
                    <label>Email:</label>
                    <input id="login-username" type="text" class="text" name="email_user" placeholder="Email" tabindex="1" maxlength="200"></input>
                </div>
                <div id="login-pass" class="field">
                    <label>Password</label>
                    <input id="login-password" type="password" class="text" name="passwd_user" placeholder="Contraseña" tabindex="3" maxlength="32"></input>
                </div>
                <div class="btnlogeo">
                    <input id="login-submit" type="submit" value="Iniciar Sesión">
                </div>
            </form>

        </div>
        <aside class="right">

        </aside>

        <div class="clearfix"></div>
    </section>
</div>