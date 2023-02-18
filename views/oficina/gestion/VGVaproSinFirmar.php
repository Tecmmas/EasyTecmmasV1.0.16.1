<!DOCTYPE html>
<html class=" ">
    <head>

        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta charset="utf-8" />
        <title>VEHICULO APROBADO</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/images/favicon.png" type="image/x-icon" />    <!-- Favicon -->
        <link rel="apple-touch-icon-precomposed" href="<?php echo base_url(); ?>assets/images/apple-touch-icon-57-precomposed.png">	<!-- For iPhone -->
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo base_url(); ?>assets/images/apple-touch-icon-114-precomposed.png">    <!-- For iPhone 4 Retina display -->
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo base_url(); ?>assets/images/apple-touch-icon-72-precomposed.png">    <!-- For iPad -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo base_url(); ?>assets/images/apple-touch-icon-144-precomposed.png">    <!-- For iPad Retina display -->

        <!-- CORE CSS FRAMEWORK - START -->
        <link href="<?php echo base_url(); ?>assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/fonts/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/plugins/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" type="text/css"/>
        <!-- CORE CSS FRAMEWORK - END -->

        <!-- HEADER SCRIPTS INCLUDED ON THIS PAGE - START --> 


        <!-- HEADER SCRIPTS INCLUDED ON THIS PAGE - END --> 


        <!-- CORE CSS TEMPLATE - START -->
        <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/css/responsive.css" rel="stylesheet" type="text/css"/>
        <!-- CORE CSS TEMPLATE - END -->
        <!-- CORE CSS TEMPLATE - END -->

    </head>
    <!-- END HEAD -->

    <!-- BEGIN BODY -->
    <body class="login_page" style="background: white">

        <div class="col-xl-12">
            <section class="box ">
                <div class="content-body"  style="background: lightgreen">    
                    <div class="row" >
                        <div class="col-lg-12 col-md-12 col-12">
                            <section class="box ">
                                <form action="<?php echo base_url(); ?>index.php/oficina/CGestion" method="post">
                                    <input name="button" class="btn btn-accent btn-block" style="width: 100px;background: #393185" type="submit"  value="Atras" />   
                                </form>
                                <header class="panel_header">
                                    <h2 class="title float-left">Vehiculo aprobado sin firmar</h2>
                                </header>
                                <div class="content-body" >
                                    <input id="idhojapruebas" value="<?php echo $dato; ?>" type="hidden" />
                                    <strong style="color: salmon">Seleccione al jefe de pista encargado de esta inspección.</strong><br><br>
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td style="text-align: right">
                                                    <strong>JEFE DE PISTA</strong>
                                                </td>
                                                <td colspan="3">
                                                    <select name="jefepista" onchange="cambiarJefe(this)" class="form-control input-lg m-bot15">
                                                        <option value="<?php
                                                        echo $jefePista->valor;
                                                        ?> "><?php
                                                                    echo $jefePista->valor;
                                                                    ?></option> 
                                                        <?php
                                                        foreach ($jefesPista->result() as $jp) {
                                                            echo "<option value='$jp->nombres $jp->apellidos'>$jp->nombres $jp->apellidos</option>";
                                                        }
                                                        ?>  
                                                    </select>  
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <?php
                                    if ($reinspeccion == "0" || $reinspeccion == "1") {
                                        ?>
                                        <br>
                                        <strong style="color: salmon">Es recomendable revisar el FUR antes de realizar el envío a SICOV para la firma.</strong>
                                        <?php
                                    }
                                    ?>       
                                    <br>
                                    <table class="table table-bordered" style="text-align: center">
                                        <thead>
                                            <tr>
                                                <th>Id control</th>
                                                <th>Placa</th>
                                                <th>Ocasión</th>
                                                <th>FUR</th>
                                                <th>Tamaño hoja</th>
                                                <?php
                                                if ($reinspeccion == "0" || $reinspeccion == "1") {
                                                    ?>                
                                                    <th>Enviar a SICOV para firmar</th>
                                                    <?php
                                                }
                                                ?>                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <?php
                                                    echo $idhojapruebas;
                                                    ?>                
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $vehiculo->placa;
                                                    ?>                
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $ocacion;
                                                    ?>                
                                                </td>
                                        <form action="<?php echo base_url(); ?>index.php/oficina/fur/CFUR" method="post" style="width: 100px;text-align: center">
                                            <td >
                                                <button  name="dato" class="btn btn-accent btn-block" value ="<?php echo $dato; ?>" type="submit" formtarget="_blank" style="border-radius: 40px 40px 40px 40px;font-size: 14px;background-color: #393185">📄 Ver</button>
                                            </td>
                                            <td >
                                                <select name="tamano" class="form-control input-lg m-bot15">
                                                    <option value="oficio" selected>Oficio</option>
                                                    <option value="carta">Carta</option>
                                                </select>
                                            </td>
                                        </form>
                                        <?php
                                        if ($reinspeccion == "0" || $reinspeccion == "1") {
                                            ?>                
                                            <td>
                                                <input class="btn btn-success btn-block" id="btnenviarsicov"  style="border-radius: 40px 40px 40px 40px;font-size: 20px;" value="✉️ Enviar" data-toggle='modal' data-target='#confirmacionEnvio'/>
                                            </td>
                                            <?php
                                        }
                                        ?>                
                                        </tr>
                                        </tbody>
                                    </table>
                                    <?php
                                    if ($reinspeccion == "0" || $reinspeccion == "1") {
                                        ?>                
                                        <div style="text-align: center">
                                            <input class="btn btn-warning btn-block" id="btnenviarfirmado" style="border-radius: 40px 40px 40px 40px;font-size: 20px;width: 300px" value="🖊️ Este FUR ya esta firmado" data-toggle='modal' data-target='#confirmacionFirma'/><br>
                                        </div>
                                        <?php
                                    }
                                    ?>   

                                    <section class="box ">
                                        <?php $this->load->view('oficina/gestion/Nav-conf-prueba'); ?>
                                        <div id="RasignacionI">
                                            <header class="panel_header">
                                                <h2 class="title float-left">Reasignacion individual</h2>
                                            </header>
                                            <div class="content-body">    
                                                <div class="row">
                                                    <div class="col-12">
                                                        <table class="table table-bordered" >
                                                            <thead>
                                                                <tr>
                                                                    <th>Id</th>
                                                                    <th>Estado</th>
                                                                    <th>Fecha inicial</th>
                                                                    <th>Fecha final</th>
                                                                    <th>Tipo ins</th>
                                                                    <th>Opciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <?php foreach ($placaR as $value): ?>
                                                                        <td><?= $value->idhojapruebas ?></td>
                                                                        <td><?= $value->estado ?></td>
                                                                        <td><?= $value->fechainicial ?></td>
                                                                        <td><?= $value->fechafinal ?></td>
                                                                        <td><?= $value->tipoins ?></td>
                                                                        <td>
                                                                            <input type="hidden" name="idhojapruebas" id="idhojapruebasR" value="<?= $value->idhojapruebas ?>">
                                                                            <input type="hidden" name="fechainicial" id="fechainicial" value="<?= $value->pfechainicial ?>">
                                                                            <input type="hidden" name="reinspeccion" id="reinspeccion" value="<?= $value->reinspeccion ?>">
                                                                            <input type="submit" name="consultar" id="btn-buscar-pruebas" class="btn btn-primary"  style="background-color: #393185;border-radius: 40px 40px 40px 40px" value="Buscar">
                                                                        </td>
                                                                    <?php endforeach; ?>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="content-body" id="div-pruebas" style="display: none">
                                                <header class="panel_header">
                                                    <div style="float: left; font-size: 1.57em">Pruebas</div>
                                                    <div id="val-razon" style="color: red; text-align: center;"></div>
                                                </header>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <!--                                                    <div class="form-group row" >
                                                                                                                <label  class="col-sm-4 col-form-label" style="font-weight: bold; color: black;text-align: center">Razón de la reasignación:</label>
                                                                                                                <input type="text" class="mx-sm-4" id="razon-reasignacion">
                                                                                                                
                                                                                                            </div>-->

                                                        <table class="table table-bordered" id="table-rea-prueba">
                                                            <thead>
                                                                <tr>
                                                                    <th>Id</th>
                                                                    <th>Fecha inicial</th>
                                                                    <th>Estado</th>
                                                                    <th>Fecha final</th>
                                                                    <th>Tipo prueba</th>
                                                                    <th>Opciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="pinEstado" style="display: none; position: absolute">
                                            <header class="panel_header">
                                                <h2 class="title float-left">Cambiar pin y estado</h2>
                                            </header>
                                            <div class="content-body">    
                                                <div class="row">
                                                    <div class="col-12">
                                                        <!--<form action="<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/updateEstadoPin" method="post">-->
                                                        <div id="div-rest-estado"></div>
                                                        <table class="table table-bordered" >
                                                            <thead>
                                                                <tr>
                                                                    <th>Placa</th>
                                                                    <th>Estado</th>
                                                                    <th>Fecha inicial</th>
                                                                    <th>Tipo ins</th>
                                                                    <th>Pin</th>
                                                                    <th>Opciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <?php foreach ($placaR as $value): ?>
                                                                        <td><?= $value->placa ?></td>
                                                                        <td><label style="padding-left: 0px" id="label-estado">
                                                                                <input type="checkbox" style="transform: scale(1.0)" class="skin-square-blue rta" id="estado"><label style="padding-left: 15px"><?= $value->estado ?></label></label>
                                                                            <div style="display: none" id="select-estado">
                                                                                <input type="checkbox" style="transform: scale(1.0)" class="skin-square-blue rta2" id="estado" checked="">
                                                                                <select name="estado" id="estadoP">
                                                                                    <option></option>
                                                                                    <option value="1">Asignado</option>
                                                                                    <option value="2">Aprobado</option>
                                                                                    <option value="3">Rechazado</option>
                                                                                    <option value="4">Certificado</option>
                                                                                    <option value="5">Abortado</option>
                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td><?= $value->fechainicial ?></td>
                                                                        <td><?= $value->tipoins ?></td>
                                                                        <td><input type="number" id="pin" name="pin" value="<?= $value->pin ?>"></td>
                                                                        <td>
                                                                            <input type="hidden" name="idhojapruebasR" id="idhojapruebasR" value="<?= $value->idhojapruebas ?>">
                                                                            <input type="submit" name="consultar" id="btn-pin-estado"  class="btn btn-primary"  style="background-color: #393185;border-radius: 40px 40px 40px 40px" value="Guardar">
                                                                        </td>
                                                                    <?php endforeach; ?>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <!--</form>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="reconfPrueba" style="display: none; position: absolute">
                                            <header class="panel_header">
                                                <h2 class="title float-left">Reconfigurar vehiculos y pruebas</h2>
                                            </header>
                                            <div class="content-body">    
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div id="div-reconf-prueba"></div>
                                                        <table class="table table-bordered" >
                                                            <thead>
                                                                <tr>
                                                                    <th>Placa</th>
                                                                    <th>Estado</th>
                                                                    <th>Tipo vehiculo</th>
                                                                    <th>Fecha inicial</th>
                                                                    <th>Tipo ins</th>
                                                                    <th>Reconfigurar</th>
                                                                    <th>Opciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <?php foreach ($placaR as $value): ?>
                                                                        <td><?= $value->placa ?></td>
                                                                        <td><?= $value->estado ?></td>
                                                                        <?php if ($value->tipovehiculo == 1): ?>
                                                                            <td><?php echo 'Liviano'; ?></td>
                                                                        <?php elseif ($value->tipovehiculo == 2): ?>
                                                                            <td><?php echo 'Pesado'; ?></td>
                                                                        <?php else : ?>
                                                                            <td><?php echo 'Moto'; ?></td>
                                                                        <?php endif; ?>
                                                                        <td><?= $value->fechainicial ?></td>
                                                                        <td><?= $value->tipoins ?></td>
                                                                        <td>
                                                                            <select name="selectrecofprueba" id="selectrecofprueba">
                                                                                <option value="1">Asignar taxímetro</option>
                                                                                <option value="2">Quitar taxímetro</option>
                                                                                <option value="3">Liviano a pesado</option>
                                                                                <option value="4">Pesado a liviano</option>
                                                                                <option value="5">Moto a liviano</option>
                                                                                <option value="6">Liviano a moto</option>
                                                                                <option value="7">Pesado a moto</option>
                                                                                <option value="8">Moto a pesado</option>
                                                                                <option value="9">Particular a público</option>
                                                                                <option value="10">Público a particular</option>
                                                                                <option value="11">Gasolina a diesel</option>
                                                                                <option value="12">Diesel a gasolina</option>
                                                                                <option value="13">Asignar sonometro</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="hidden" id="hojapruebasR" value="<?= $value->idhojapruebas ?>">
                                                                            <input type="hidden" name="pfechainicial" id="pfechainicialR" value="<?= $value->pfechainicial ?>">
                                                                            <input type="hidden" name="tipovehiculo" id="tipovehiculoR" value="<?= $value->tipovehiculo ?>">
                                                                            <input type="hidden" name="servicio" id="servicioR" value="<?= $value->servicio ?>">
                                                                            <input type="hidden" name="combustible" id="combustibleR" value="<?= $value->combustible ?>">
                                                                            <input type="hidden" name="placa" id="placaR" value="<?= $value->placa ?>">
                                                                            <input type="submit" name="consultar"  class="btn btn-primary" id="btn-reconf-prueba"  style="background-color: #393185;border-radius: 40px 40px 40px 40px" value="Guardar">
                                                                        </td>
                                                                    <?php endforeach; ?>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="cancelPrueba" style="display: none; position: absolute">
                                            <header class="panel_header">
                                                <h2 class="title float-left">Cancelación de pruebas</h2>
                                            </header>
                                            <div class="content-body">    
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div id="div-cancel-prueba"></div>
                                                        <table class="table table-bordered" id="table-cancel" >
                                                            <thead>
                                                                <tr>
                                                                    <th>Placa</th>
                                                                    <th>Estado</th>
                                                                    <th>Fecha inicial</th>
                                                                    <th>Tipo ins</th>
                                                                    <th>Opciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <?php foreach ($placaR as $value): ?>
                                                                        <td><?= $value->placa ?></td>
                                                                        <td><?= $value->estado ?></td>
                                                                        <td><?= $value->fechainicial ?></td>
                                                                        <td><?= $value->tipoins ?></td>
                                                                        <td>
                                                                            <input type="hidden"  value="<?= $value->idhojapruebas ?>">
                                                                            <input type="submit" name="consultar" id="btn-cancel-pruebas" class="btn btn-primary"  style="background-color: #393185;border-radius: 40px 40px 40px 40px" value="Cancelar">
                                                                        </td>
                                                                    <?php endforeach; ?>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </div>

                                <img src="<?php echo base_url(); ?>assets/images/logo.png" />
                            </section>

                        </div>
                    </div>
                </div>
            </section>
        </div>


        <div class="modal" id="xBModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog animated bounceInDown">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" >VEHICULO RECHAZADO</h4>
                    </div>
                    <div class="modal-body" style="background: whitesmoke">
                        <label id="mensaje"
                               style="background: white;
                               width: 100%;
                               text-align: center;
                               font-weight: bold;
                               font-size: 15px;
                               padding: 5px;border: solid gray 2px;
                               border-radius:  15px 15px 15px 15px;color: salmon">ESTE VEHÍCULO HA SIDO RECHAZADO POR CANTIDAD DE DEFECTOS DE TIPO B, POR FAVOR REVISE EL FUR PARA MAS INFORMACIÓN</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="confirmacionEnvio" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog animated bounceInDown">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" >Confirmación de envío</h4>
                    </div>
                    <div class="modal-body" style="background: whitesmoke">
                        <label id="mensajeSicov"
                               style="background: white;
                               width: 100%;
                               text-align: center;
                               font-weight: bold;
                               font-size: 15px;
                               padding: 5px;border: solid gray 2px;
                               border-radius:  15px 15px 15px 15px;color: black">¿ESTÁ SEGURO(A) DE REALIZAR EL ENVÍO A SICOV</label>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">NO</button>
                        <button id="btnAsignar" class="btn btn-success" type="button" onclick="enviarASICOV()">SI</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="confirmacionFirma" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog animated bounceInDown">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" >Confirmación de envío</h4>
                    </div>
                    <div class="modal-body" style="background: whitesmoke">
                        <label id="mensajeSicov"
                               style="background: white;
                               width: 100%;
                               text-align: center;
                               font-weight: bold;
                               font-size: 15px;
                               padding: 5px;border: solid gray 2px;
                               border-radius:  15px 15px 15px 15px;color: black"><strong style="color: salmon">ADVERTENCIA</strong> <br><br>Esta acción enviará la placa a la sección de APROBADOS SIN CONSECUTIVO, se recomienda revisar si este FUR a sido firmado en el SICOV antes de confirmar esta acción. <br><br> ¿DESEA ENVIAR LA PLACA A "APROBADOS SIN CONSECUTIVO"?</label>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">NO</button>
                        <button id="btnAsignar" class="btn btn-success" type="button" onclick="enviarFirmar()">SI</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="modal-visual" role="dialog" aria-hidden="true" style="display: none" data-backdrop="false" >
            <div class="modal-dialog animated bounceInDown">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="titulo_">Pruebas visual</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table" id="table-modal-visual">
                            <tbody>
                            <div id="cap-new">
                                <label>En captador no esta asignado, si lo desea lo puede crear:</label>
                                <label style="padding-left: 40px"><input type="checkbox" style="transform: scale(2.0)" class="skin-square-blue" id="captador"><labe style="padding-left: 15px">Captador</labe></label><br>
                                <div style="color: #1D8348" id="msj-cap"></div>
                                <hr>
                            </div>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" id="btn-close" type="button">Cerrar</button>
                        <button class="btn btn-success" type="button" id="btn-reasig-visual">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT AREA ENDS -->
        <!-- LOAD FILES AT PAGE END FOR FASTER LOADING -->


        <!-- CORE JS FRAMEWORK - START --> 
        <script src="<?php echo base_url(); ?>assets/js/jquery-1.11.2.min.js" type="text/javascript"></script> 
        <script src="<?php echo base_url(); ?>assets/js/jquery.easing.min.js" type="text/javascript"></script> 
        <script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script> 
        <script src="<?php echo base_url(); ?>assets/plugins/pace/pace.min.js" type="text/javascript"></script>  
        <script src="<?php echo base_url(); ?>assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js" type="text/javascript"></script> 
        <script src="<?php echo base_url(); ?>assets/plugins/viewport/viewportchecker.js" type="text/javascript"></script>  
        <script>window.jQuery || document.write('<script src="<?php echo base_url(); ?>assets/js/jquery-1.11.2.min.js"><\/script>');</script>
        <!-- CORE JS FRAMEWORK - END --> 


        <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - START --> 

        <script src="<?php echo base_url(); ?>assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/autonumeric/autoNumeric-min.js" type="text/javascript"></script>
        <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 


        <!-- CORE TEMPLATE JS - START --> 
        <script src="<?php echo base_url(); ?>assets/js/scripts.js" type="text/javascript"></script> 
        <script src="<?php echo base_url(); ?>/application/libraries/package/dist/sweetalert2.all.min.js"></script>
        <script src="<?php echo base_url(); ?>application/libraries/sesion.js"  type="text/javascript"></script>
        <!-- END CORE TEMPLATE JS - END --> 
        <script type="text/javascript">
                            var placa = "<?php
                                                                    echo $placa;
                                                                    ?>";
                            var ocasion = "<?php
                                                                    echo $ocacion;
                                                                    ?>";
                            $(document).ready(function () {
                                var data = {
                                    desdeVisor: 'true',
                                    dato: $('#idhojapruebas').val(),
                                    IdUsuario: '1'
                                };
//                                                            console.log(data);
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/oficina/fur/CFUR',
                                    type: 'post',
                                    data: data,
                                    success: function (rta) {
                                        var apro = rta.split('|');
                                        if (apro[0] === 'APROBADO: SI_____ NO__X__' ||
                                                apro[1] === 'APROBADO: SI_____ NO__X__') {
                                            $("#xBModal").modal('show');
                                            var data2 = {
                                                idprueba: apro[2]
                                            };
                                            $.ajax({
                                                url: '<?php echo base_url(); ?>index.php/oficina/pruebas/Cpruebas/actualizarPruebaXB',
                                                type: 'post',
                                                data: data2,
                                                async: false,
                                                success: function () {
                                                    var segundos = 4;
                                                    var proceso = setInterval(function () {
                                                        if (segundos === 0) {
                                                            clearInterval(proceso);
                                                            window.location.replace("<?php echo base_url(); ?>index.php/oficina/CGestion");
                                                        }
                                                        segundos--;
                                                    }, 1000);
                                                }
                                            });
                                        }
                                    }
                                });
                            });
                            var cambiarJefe = function (e) {
                                var idht = $('#idhojapruebas').val().split('-');
                                //                                                            console.log(idht[0]);
                                var data = {
                                    jefepista: e.value,
                                    idhojapruebas: idht[0]
                                };
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/setJefePista',
                                    type: 'post',
                                    data: data,
                                    success: function (rta) {
                                        console.log(rta);
                                    }
                                });
                            };

                            var enviarFirmar = function () {
                                var reinspeccion = $('#reinspeccion').val();
                                var data = {
                                    idhojapruebas: $('#idhojapruebas').val(),
                                    placa: placa,
                                    ocasion: ocasion,
                                    reinspeccion: reinspeccion
                                };
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/CGVaproEnvioFirmar',
                                    type: 'post',
                                    data: data,
                                    async: false,
                                    success: function () {
                                        window.location.replace("<?php echo base_url(); ?>index.php/oficina/CGestion");
                                    }
                                });
                            };

                            var enviarASICOV = function () {
                                var segundos = 2;
                                var proceso = setInterval(function () {
                                    document.getElementById('btnAsignar').disabled = 'true';
                                    document.getElementById('mensajeSicov').style.color = 'black';
                                    $('#mensajeSicov').text('Por favor espere. Este proceso puede tomar hasta un minuto');
                                    if (segundos === 0) {
                                        clearInterval(proceso);
                                        var data = {
                                            envioSicov: 'true',
                                            dato: $('#idhojapruebas').val(),
                                            envio: '1',
                                            IdUsuario: '1',
                                            sicovModoAlternativo: localStorage.getItem("sicovModoAlternativo")
                                        };
                                        $.ajax({
                                            url: '<?php echo base_url(); ?>index.php/oficina/fur/CFUR',
                                            type: 'post',
                                            data: data,
                                            async: false,
                                            success: function (rta) {
                                                var dat = rta.split('|');
//                                                console.log(dat[1]);
                                                if (dat[1] === '0000' || dat[1] === '1') {
                                                    var segundos = 3;
                                                    var proceso = setInterval(function () {
                                                        $('#mensajeSicov').text("Mensaje de SICOV: " + dat[0] + ". Detalles en el visor.");
                                                        document.getElementById('mensajeSicov').style.color = 'green';
                                                        if (segundos === 0) {
                                                            clearInterval(proceso);
                                                            window.location.replace("<?php echo base_url(); ?>index.php/oficina/CGestion");
                                                        }
                                                        segundos--;
                                                    }, 1000);
                                                } else {
                                                    $('#mensajeSicov').text("Mensaje de SICOV: " + dat[0] + ". Detalles en el visor.");
                                                    document.getElementById('mensajeSicov').style.color = 'salmon';
                                                    var segundos = 3;
                                                    var proceso = setInterval(function () {
                                                        if (segundos === 0) {
                                                            clearInterval(proceso);
                                                            window.location.replace("<?php echo base_url(); ?>index.php/oficina/CGestion");
                                                        }
                                                        segundos--;
                                                    }, 1000);
                                                }
                                            }
                                        });
                                    }
                                    segundos--;
                                }, 500);
                            };
                            //---------------------------------------INTEGRACION 20210320 BRAYAN LEON

                            var cargar = function () {
                                $('#razon-reasignacion').val('');
                                document.getElementById("div-pruebas").style.display = '';
                                var idhojapruebas = $('#idhojapruebasR').val();
                                var reinspeccion = $('#reinspeccion').val();
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/getPruebas',
                                    type: 'post',
                                    mimeType: 'json',
                                    data: {idhojapruebas: idhojapruebas,
                                        reinspeccion: reinspeccion, },
                                    success: function (data, textStatus, jqXHR) {
                                        $('#table-rea-prueba tbody').html('');

                                        $.each(data, function (i, data) {
                                            if (data.estado == 'Asignado') {
                                                var asignado = "";
                                                var asignado = "<td style= 'color: gray '><strong>" + data.estado + "</strong></td>";
                                            } else if (data.estado == 'Aprobado') {
                                                var asignado = "";
                                                var asignado = "<td style= 'color: green'><strong>" + data.estado + "</strong></td>";
                                            } else {
                                                var asignado = "";
                                                var asignado = "<td style= 'color: red'><strong>" + data.estado + "</strong></td>";
                                            }
                                            var body = "<tr>";
                                            body += "<td>" + data.idprueba + "</td>";
                                            body += "<td>" + data.fechainicial + "</td>";
                                            body += asignado;
                                            body += "<td>" + data.fechafinal + "</td>";
                                            body += "<td>" + data.pruebas + "</td>";
                                            body += '<td style="text-aling: center;"><type="submit" class="btn btn-primary"  style="background-color: #393185;border-radius: 40px 40px 40px 40px"  onClick="reasignarpru(\'' + data.idtipo_prueba + '\',\'' + data.idprueba + '\',\'' + idhojapruebas + '\',\'' + data.pruebas + '\',\'' + data.prueba + '\')">Reasignar</td>';
                                            body += "</tr>";
                                            $("#table-rea-prueba tbody").append(body);
                                        });
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {

                                    }
                                });
                            };
                            $('#btn-buscar-pruebas').click(function (ev) {
                                $('#val-razon').html('');
                                cargar();
                            });


                            function reasignarpru(idtipo_prueba, idprueba, idhojapruebas, pruebas, prueba) {
//                                                if ($('#razon-reasignacion').val().length == 0) {
//                                                    $('#val-razon').html('Escriba por favor la razÃ³n de la reasignaciÃ³n.');
//                                                    $("html, body").animate({scrollTop: "0px"});
//                                                } else {
                                $('#val-razon').html('');
                                var razon = $('#razon-reasignacion').val();
                                if (idtipo_prueba == 8) {
                                    pruebavisual(idprueba, idhojapruebas);
                                } else {
                                    pruebanormal(idprueba, idhojapruebas, pruebas, idtipo_prueba, prueba);
                                }
//                                                }
                            }
                            function pruebavisual(idprueba, idhojapruebas) {
                                localStorage.setItem("idvisual", idprueba);
                                $('#modal-visual tbody').html('');
                                $('#modal-visual').modal('show');
                                var reinspeccion = $('#reinspeccion').val();
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/getPruebasvisual',
                                    type: 'post',
                                    mimeType: 'json',
                                    data: {idhojapruebas: idhojapruebas,
                                        reinspeccion: reinspeccion, },
                                    success: function (data, textStatus, jqXHR) {
                                        document.getElementById("cap-new").style.display = '';
                                        document.getElementById("captador").disabled = false;
                                        $('input[type=checkbox]').prop('checked', false);
                                        $('#msj-cap').html('');
                                        console.log(data);
                                        $.each(data, function (i, data) {
                                            if (data.idtipo_prueba == 14) {
                                                document.getElementById("cap-new").style.display = 'none';
                                            }
                                            if (data.idtipo_prueba == 21 || data.idtipo_prueba == 22) {
                                                var input = '<td><input checked type="checkbox" style="transform: scale(2.0)" data2="' + data.idtipo_prueba + '" data="' + data.idprueba + '"class="skin-square-blue idpruebas"  >'
                                            } else {
                                                var input = '<td><input type="checkbox" style="transform: scale(2.0)" data2="' + data.idtipo_prueba + '" data="' + data.idprueba + '"class="skin-square-blue idpruebas"  >'
                                            }
                                            var body = '<tr>';
                                            body += input;
                                            body += '<td style="padding-left: 3px">' + data.pruebas + '</td>';
                                            body += '</tr>';
                                            $("#table-modal-visual tbody").append(body);
                                        });
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {

                                    }
                                });
                            }

                            $(document).on('click', '#captador', function (event) {
                                if ($(this).is(':checked')) {
                                    var idhojapruebas = $('#idhojapruebasR').val();
                                    var fechainicial = $('#fechainicial').val();
                                    $.ajax({
                                        url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/getCreateCaptador',
                                        type: 'post',
                                        mimeType: 'json',
                                        data: {idhojapruebas: idhojapruebas,
                                            fechainicial: fechainicial},
                                        success: function (data, textStatus, jqXHR) {
                                            if (data == 1) {
                                                document.getElementById("captador").disabled = true;
                                                $('#msj-cap').html('Se creo la prueba captador');
                                            }
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {

                                        }
                                    });
                                }
                            });

                            $('#btn-reasig-visual').click(function () {
                                var idhojapruebas = $('#idhojapruebasR').val();
                                var idvisual = localStorage.getItem('idvisual');
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/updateVisual',
                                    type: 'post',
                                    mimeType: 'json',
                                    data: {idhojapruebas: idhojapruebas,
                                        idvisual: idvisual, },
                                    success: function (data, textStatus, jqXHR) {
                                        if (data == 1) {
                                            var i = 0;
                                            $('#table-modal-visual .idpruebas').each(function () {
                                                if ($(this).is(':checked')) {
                                                    var idtipoprueba = $(this).attr('data');
                                                    var idtipo_prueba = $(this).attr('data2');
                                                    $.ajax({
                                                        url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/updatePruebasVisual',
                                                        type: 'post',
                                                        mimeType: 'json',
                                                        data: {idhojapruebas: idhojapruebas,
                                                            idtipoprueba: idtipoprueba,
                                                            idtipo_prueba: idtipo_prueba},
                                                        success: function (data, textStatus, jqXHR) {
                                                            $('#btn-close').click();
                                                            $('html, body').animate({
                                                                scrollTop: $("#val-razon").offset().top
                                                            }, 900);
                                                            $('#val-razon').html('<div style="color: #1D8348; font-size: 17px; text-align: center;">La prueba visual fue asignada.</div>');
                                                            $('#razon-reasignacion').val('');
                                                            reload();
                                                        },
                                                        error: function (jqXHR, textStatus, errorThrown) {

                                                        }
                                                    });
                                                    i++;
                                                } else {
                                                    $('#btn-close').click();
                                                    $('html, body').animate({
                                                        scrollTop: $("#val-razon").offset().top
                                                    }, 900);
                                                    $('#val-razon').html('<div style="color: #1D8348; font-size: 17px; text-align: center;">La prueba visual fue asignada.</div>');
                                                    $('#razon-reasignacion').val('');
                                                    reload();
                                                }
                                            });
                                        }
                                        ;
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {

                                    }
                                });

                            });
                            function pruebanormal(idprueba, idhojapruebas, pruebas, idtipo_prueba, prueba) {
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/updatePruebas',
                                    type: 'post',
                                    mimeType: 'json',
                                    data: {idhojapruebas: idhojapruebas,
                                        idprueba: idprueba,
                                        idtipo_prueba: idtipo_prueba,
                                        prueba: prueba},
                                    success: function (data, textStatus, jqXHR) {
                                        $('html, body').animate({
                                            scrollTop: $("#val-razon").offset().top
                                        }, 900);
                                        $('#val-razon').html('<div style="color: #1D8348; font-size: 17px; text-align: center">La prueba ' + pruebas + ' fue asignada.</div>');
                                        $('#razon-reasignacion').val('');
                                        reload();
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {

                                    }
                                });
                            }
                            $('#btn-close').click(function () {
                                document.getElementById("modal-visual").style.display = 'none';
                            });

                            var reload = function () {
                                var count = 0;
                                var process = setInterval(function () {
                                    if (count === 0) {
                                        cargar();
                                    }
                                    if (count === 1)
                                        clearInterval(process);
                                    count++;
                                }, 1000);
                            };
                            $(document).on('click', '#estado', function (event) {
                                if ($(this).is(':checked')) {
                                    $('.rta').prop('checked', false);
                                    document.getElementById("select-estado").style.display = '';
                                    document.getElementById("label-estado").style.display = 'none';
                                } else {
                                    $('.rta2').prop('checked', true);
                                    document.getElementById("select-estado").style.display = 'none';
                                    document.getElementById("label-estado").style.display = '';
                                }
                            });
                            $('#btn-pin-estado').click(function () {
                                var pin = $('#pin').val();
                                var idhojapruebasR = $('#idhojapruebasR').val();
                                var estadop = $('#estadoP option:selected').attr('value');
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/updateEstadoPin',
                                    type: 'post',
                                    mimeType: 'json',
                                    data: {idhojapruebas: idhojapruebasR,
                                        pin: pin,
                                        estado: estadop},
                                    success: function (data, textStatus, jqXHR) {
                                        $('#div-rest-estado').html('<div style="color: #1D8348;font-size: 17px; text-align: center">' + data + '</div>');
                                        $('.rta').prop('checked', false);
                                        document.getElementById("select-estado").style.display = 'none';
                                        document.getElementById("label-estado").style.display = '';
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {

                                    }
                                });
                            });
                            $('#btn-reconf-prueba').click(function () {
                                var selectrecofprueba = $('#selectrecofprueba option:selected').attr('value');
                                var hojapruebas = $('#hojapruebasR').val();
                                var pfechainicial = $('#pfechainicialR').val();
                                var tipovehiculo = $('#tipovehiculoR').val();
                                var servicio = $('#servicioR').val();
                                var combustible = $('#combustibleR').val();
                                var placa = $('#placaR').val();
//                                                console.log(selectrecofprueba, hojapruebas, pfechainicial, tipovehiculo, 'servicio:', servicio, combustible, placa);
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/reConfvehiculosPruebas',
                                    type: 'post',
                                    mimeType: 'json',
                                    data: {idhojapruebas: hojapruebas,
                                        selectrecofprueba: selectrecofprueba,
                                        servicio: servicio,
                                        pfechainicial: pfechainicial,
                                        tipovehiculo: tipovehiculo,
                                        combustible: combustible,
                                        placa: placa
                                    },
                                    success: function (data, textStatus, jqXHR) {
                                        $('#div-reconf-prueba').html('<div style="color: #1D8348;font-size: 17px; text-align: center">' + data + '</div>');
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {

                                    }
                                });
                            });
                            $('#btn-cancel-pruebas').click(function () {
                                var hojapruebas = $('#hojapruebasR').val();
                                var reinspeccion = $('#reinspeccion').val();
                                if (reinspeccion !== '1') {
                                    $.ajax({
                                        url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/cancelarPruebas',
                                        type: 'post',
                                        mimeType: 'json',
                                        data: {idhojapruebas: hojapruebas,
                                            reinspeccion: reinspeccion},
                                        success: function (data, textStatus, jqXHR) {
                                            $('#div-cancel-prueba').html('<div style="color: #1D8348; font-size: 17px; text-align: center">' + data + '</div>');
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {

                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        html: '<div style="color: red; font-size: 22px">Esta cancelando una prueba de reinspección</div>',
                                        showCancelButton: true,
                                        confirmButtonText: `Aceptar`,
                                        cancelButtonColor: '#d33',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $.ajax({
                                                url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/cancelarPruebas',
                                                type: 'post',
                                                mimeType: 'json',
                                                data: {idhojapruebas: hojapruebas,
                                                    reinspeccion: reinspeccion},
                                                success: function (data, textStatus, jqXHR) {
                                                    $('#div-cancel-prueba').html('<div style="color: #1D8348; font-size: 17px; text-align: center">' + data + '</div>');
                                                },
                                                error: function (jqXHR, textStatus, errorThrown) {

                                                }
                                            });
                                        }
                                    })
                                }
                            });
        </script>
    </body>
</html>



