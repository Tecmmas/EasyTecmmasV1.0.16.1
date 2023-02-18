<?php $this->load->view('././header'); ?>
<script type="text/javascript">

</script>
<!-- START CONTENT -->
<section id="main-content" class=" ">
    <section class="wrapper main-wrapper row" style=''>
        <div class='col-12'>
            <div class="page-title">
                <div class="float-left">
                    <!-- PAGE HEADING TAG - START --><h4 class="title">FORMATO UNIFORME RTMEC, PREVENTIVAS Y PRUEBAS LIBRES</h4><!-- PAGE HEADING TAG - END -->  
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <!-- MAIN CONTENT AREA STARTS -->
        <div class="col-xl-12">
            <section class="box ">
                <header class="panel_header">
                    <h4 class="title float-left">Generación de formatos</h4>
                </header>
                <div class="content-body">    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12">
                            <section class="box ">
                                <header class="panel_header">
                                    <h2 class="title float-left">buscador</h2>
                                </header>

                                <div class="content-body">    
                                    <form action="<?php echo base_url(); ?>index.php/oficina/informes/CFur/consultar" method="post">
                                        <div class="row">
                                            <div class="col-2">
                                                <label style="font-weight: bold;color: black" for="placa">PLACA<br/>
                                                    <input type="text" name="placa" id="placa" class="input" style="font-size: 15px;height: 37px"  size="15" 
                                                           value="<?php
                                                           if (isset($placa)) {
                                                               echo $placa;
                                                           }
                                                           ?>" />
                                                </label>
                                            </div>
                                            <div class="col-3">
                                                <p class="submit">
                                                    <input type="submit" name="consultar" id="wp-submit" class="btn btn-accent btn-block" onclick="showSuccess('Generando el informe, por favor espere.')" style="background-color: #393185;border-radius: 40px 40px 40px 40px" value="Consultar" />
                                                </p>
                                            </div>
                                        </div>
                                    </form>
                                    <form action="<?php echo base_url(); ?>index.php/oficina/fur/CFUR" method="post">
                                        <input type="hidden" name="desdeConsulta" value="true" />
                                        <div class="col-12">
                                            <table>
                                                <tr>
                                                    <td>
                                                        Tamaño hoja
                                                    </td>
                                                    <td>
                                                        <select name="tamano" class="form-control input-lg m-bot15">
                                                            <option value="oficio" selected>Oficio</option>
                                                            <option value="carta">Carta</option>
                                                        </select>            
                                                    </td>
                                                </tr>
                                            </table>
                                            <table  class="table table-bordered" >
                                                <thead>
                                                    <tr>
                                                        <th>Id Control</th>
                                                        <th>Placa</th>
                                                        <th>Tipo</th>
                                                        <th>Fecha inicial</th>
                                                        <th>Fecha final</th>
                                                        <th>Generar</th>
                                                        <th>Email</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (isset($formatos)) {
                                                        foreach ($formatos->result() as $p) {
                                                            ?>
                                                            <tr>
                                                                <td ><?php echo $p->idcontrol; ?></td>
                                                                <th ><?php echo $p->placa; ?></th>
                                                                <th ><?php echo $p->tipo; ?></th>
                                                                <th><?php echo $p->fechainicial; ?></th>
                                                                <th><?php echo $p->fechafinal; ?></th>
                                                                <th><?php echo $p->btnFur; ?></th>
                                                                <th><?php echo $p->btnEmail; ?></th>
                                                            </tr>
                                                            <?php
                                                        }
                                                    }
                                                    ?>

                                                </tbody>
                                            </table>
                                        </div>

                                    </form>
                                </div>

                            </section>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- MAIN CONTENT AREA ENDS -->
    </section>
</section>
<div class="modal" id="envioEmail" s tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog animated bounceInDown">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titulo_">ENVIO DE FORMATO</h4>
            </div>
            <div class="modal-body" style="background: whitesmoke">
                <label id="mensaje"
                       style="background: white;
                       width: 100%;
                       text-align: center;
                       font-weight: bold;
                       font-size: 15px;
                       padding: 5px;border: solid gray 2px;
                       border-radius:  15px 15px 15px 15px;color: gray">Bienvenido</label>
                <br>
                <table class="table">
                    <tr>
                        <td style="text-align: right">Email</td>
                        <td colspan="3" style="text-align: left;padding-left: 10px">
                            <input id="datEmail" type="email" class="form-control"/>
                        </td>
                    </tr>

                </table>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" id="cancelar" class="btn btn-default" type="button">Cancelar</button>
                <button class="btn btn-success" id="btnEnviar" type="submit" onclick="enviarEmailData()">Enviar</button>
            </div>
        </div>
    </div>
</div>
<!-- END CONTENT -->
<script type="text/javascript">

    var envioCorreo = '<?php
                                                    if (isset($envioCorreo)) {
                                                        echo $envioCorreo;
                                                    } else {
                                                        echo'0';
                                                    }
                                                    ?>';
    var idhojaprueba = 0;
    var reins = 0;
    var datos = "";
    function emailFur(event, value, title) {
        event.preventDefault();
        $('#datEmail').val(title);
        var dato = value.split('-');
        idhojaprueba = dato[0];
        reins = dato[1];
        datos = idhojaprueba + "-" + reins + "-0-1";
    }

    function enviarEmailData() {
        var emaild = $('#datEmail').val();
        if (envioCorreo === "1") {
            document.getElementById('btnEnviar').disabled = true;
            $('#mensaje').html('Enviado Información, por favor espere.');
//        console.log(emaild, idpred);
            if ((idhojaprueba === null || idhojaprueba === "") || (emaild === null || emaild === "")) {
                $('#cancelar').click();
                Swal.fire({
                    icon: 'error',
                    text: 'Campo email vacio.'
                });
            } else {
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/oficina/fur/CFUR',
                    type: 'post',
                    data: {dato: datos,
                        email: emaild},
                    success: function (data, textStatus, jqXHR) {
                        var v = 0;
//                        if (document.getElementById('email_prerevision').checked) {
//                            v = enviarPrerevision(emaild);
//                        }
                        $('#cancelar').click();
                        if (v == 1 || data == 1) {
                            Swal.fire({
                                icon: 'success',
                                text: 'Email enviado con exito.'
                            });
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('#cancelar').click();
                        Swal.fire({
                            icon: 'error',
                            html: 'No se pudo enviar el email.<br>'.jqXHR,
                        })
                    }
                })
            }
        } else {
            $('#cancelar').click();
            Swal.fire({
                icon: 'error',
                html: 'Apreciado usuario, usted no tiene habilitado este módulo de envío. por favor comuníquese con TECMMAS SAS<br>',
            });
        }

    }
</script>
<?php
$this->load->view('././footer');
