<html class=" ">
    <head>
        <!-- 
         * @Package: Complete Admin - Responsive Theme
         * @Subpackage: Bootstrap
         * @Version: BS4-1.0
         * This file is part of Complete Admin Theme.
        -->
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta charset="utf-8" />
        <title>USUARIOS</title>
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
        <!-- <link href="<?php echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/> -->
        <link href="<?php echo base_url(); ?>assets/fonts/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/css/animate.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/plugins/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" type="text/css"/>
        <!-- CORE CSS FRAMEWORK - END -->

        <!-- HEADER SCRIPTS INCLUDED ON THIS PAGE - START --> 
        <link href="<?php echo base_url(); ?>assets/plugins/jquery-ui/smoothness/jquery-ui.min.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="<?php echo base_url(); ?>assets/plugins/select2/select2.css" rel="stylesheet" type="text/css" media="screen"/>

        <!-- HEADER SCRIPTS INCLUDED ON THIS PAGE - END --> 


        <!-- CORE CSS TEMPLATE - START -->
        <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/css/responsive.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/css/tecmmas.css" rel="stylesheet" type="text/css"/>
        <!-- CORE CSS TEMPLATE - END -->

    </head>
    <!-- END HEAD -->

    <!-- BEGIN BODY -->
    <body class=" ">
        <!-- START TOPBAR -->
        <div class='page-topbar '>
            <div class='logo-area'>

            </div>
            <div class='quick-area'>
                <div class='float-left'>
                    <ul class="info-menu left-links list-inline list-unstyled">
                        <li class="message-toggle-wrapper list-inline-item">
                            <ul class="dropdown-menu messages animated fadeIn">
                                <li class="list dropdown-item">
                                </li>
                            </ul>
                        </li>
                </div>		
            </div>

        </div>
        <!-- END TOPBAR -->

        <!-- START CONTENT -->
        <section class="wrapper main-wrapper row" style=''>
            <div class="clearfix"></div>
            <!-- MAIN CONTENT AREA STARTS -->
            <div class="col-xl-12">
                <section class="box ">
                    <div class="content-body"  style="background: whitesmoke">    
                        <div class="row" >
                            <div class="col-lg-12 col-md-12 col-12">
                                <section class="box ">
                                    <header class="panel_header">
                                        <h2 class="title float-left">CREAR USUARIOS</h2>
                                    </header>
                                    <div class="content-body" >    
                                        <form action="<?php echo base_url(); ?>index.php/oficina/usuarios/Cusuarios" style="width: 100px" method="post">
                                            <input name="button" class="btn btn-block bot_azul"  type="submit"  value="Atras" />   
                                        </form>     
                                        <br>
                                        <form action="<?php echo base_url(); ?>index.php/oficina/usuarios/Cusuarios/crearUsuario" id="form-reg-user" method="post">
                                            <input type="hidden" name="idcliente" value=""/>
                                            <table class="table dt-responsive display">
                                                <?php if (isset($usuario)) { ?>
                                                    <?php foreach ($usuario as $value): ?>
                                                        <tr>
                                                            <td style="text-align: right">
                                                                TIPO DOCUMENTO
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="tipo_identificacion" style="background: #FFE1E1">
                                                                    <?php if (isset($value->tipoidentificacion)): ?>
                                                                        <option value="<?= $value->tipoidentificacion ?>"><?= $value->nombreidentificacion ?></option>
                                                                    <?php endif; ?>
                                                                    <option value="1">Cédula de ciudadanía</option>
                                                                    <option value="2">Numero Identificación Tributaria (NIT)</option>
                                                                    <option value="3">Cédula de extrangería</option>
                                                                    <option value="4">Tarjeta de identidad</option>
                                                                    <option value="5">N. único de Id. Personal</option>
                                                                    <option value="6">Pasaporte</option>
                                                                </select>
                                                                <strong style="color: #E31F24;font-size: 12px "></strong>
                                                            </td>
                                                            <td style="text-align: right">
                                                                NUMERO DE DOCUMENTO
                                                            </td>
                                                            <td>
                                                                <input class="form-control" name="numero_identificacion" value="<?php
                                                                if (isset($value->identificacion)) {
                                                                    echo $value->identificacion;
                                                                }
                                                                ?>" style="background: #FFE1E1" type="number" >
                                                                <strong style="color: #E31F24;font-size: 12px "><?php echo form_error('numero_identificacion'); ?></strong>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align: right">
                                                                NOMBRES
                                                            </td>
                                                            <td>
                                                                <input class="form-control" name="nombres" value="<?php
                                                                if (isset($value->nombres)) {
                                                                    echo $value->nombres;
                                                                }
                                                                ?>" style="background: #FFE1E1; text-transform: uppercase" type="text" autocomplete="off" >
                                                                <strong style="color: #E31F24;font-size: 12px "></strong>
                                                            </td>
                                                            <td style="text-align: right">
                                                                APELLIDOS
                                                            </td>
                                                            <td>
                                                                <input class="form-control" name="apellidos" value="<?php
                                                                if (isset($value->apellidos)) {
                                                                    echo $value->apellidos;
                                                                }
                                                                ?>" style="background: #FFE1E1; text-transform: uppercase" type="text" autocomplete="off">
                                                                <strong style="color: #E31F24;font-size: 12px "></strong>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align: right">
                                                                PERFIL
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="tipo_perfil" style="background: #FFE1E1">
                                                                    <?php if (isset($value->idperfil)): ?>
                                                                        <option value="<?= $value->idperfil ?>"><?= $value->perfil ?></option>
                                                                    <?php endif; ?>
                                                                    <option value="1">Administrador</option>
                                                                    <option value="2">Operario</option>
                                                                    <option value="3">Supervisor</option>
                                                                    <option value="4">Administrativo</option>
                                                                    <option value="5">Sistemas</option>
                                                                    <option value="6">Auditor del sistema</option>
                                                                    <option value="7">Representante legal</option>
                                                                </select>
                                                                <strong style="color: #E31F24;font-size: 12px "></strong>
                                                            </td>
                                                            <td style="text-align: right">
                                                                USUARIO
                                                            </td>
                                                            <td>
                                                                <input class="form-control" name="usuario" value="<?php
                                                                if (isset($value->usuario)) {
                                                                    echo $value->usuario;
                                                                }
                                                                ?>" type="text" style="background: #FFE1E1">
                                                            </td>
                                                        </tr>
                                                        <tr>

                                                            <td style="text-align: right">
                                                                CONTRASEÑA
                                                            </td>
                                                            <td>
                                                                <input class="form-control contrasenaconeach" name="contrasena" id="contrasena" onkeyup="validarcontrasenauser()" value="<?php
                                                                if (isset($value->passwd)) {
                                                                    echo $value->passwd;
                                                                }
                                                                ?>" style="background: #FFE1E1" type="password">
                                                                <input class="form-control" name="contrasenaold"  value="<?php
                                                                if (isset($value->passwd)) {
                                                                    echo $value->passwd;
                                                                }
                                                                ?>" style="background: #FFE1E1" type="hidden">
                                                                <strong style="color: #E31F24;font-size: 12px "></strong>
                                                                <div id="divcontra"></div>
                                                            </td>
                                                            <td style="text-align: right">
                                                                ESTADO
                                                            </td>
                                                            <td>
                                                                <select class="form-control" name="estado" style="background: #FFE1E1">
                                                                    <?php if (isset($value->estado)): ?>
                                                                        <option value="<?= $value->idestado ?>"><?= $value->estado ?></option>
                                                                    <?php endif; ?>
                                                                    <option value="1">Activo</option>
                                                                    <option value="0">Inactivo</option>

                                                                </select>
                                                                <strong style="color: #E31F24;font-size: 12px "></strong>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align: right">
                                                                CONFIRMAR CONTRASEÑA
                                                            </td>
                                                            <td>
                                                                <input class="form-control confircontrasenaconeach" name="confirmcontrasena" value="<?php
                                                                if (isset($value->passwd)) {
                                                                    echo $value->passwd;
                                                                }
                                                                ?>" style="background: #FFE1E1" type="password">
                                                                <input name='idusuario'  id="idusuario" type='hidden' value='<?php
                                                                if (isset($value->IdUsuario)) {
                                                                    echo $value->IdUsuario;
                                                                }
                                                                ?>'>
                                                                <strong style="color: #E31F24;font-size: 12px "></strong>
                                                                <div id="divcontraconf"></div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td style="text-align: right">
                                                            TIPO DOCUMENTO
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="tipo_identificacion" style="background: #FFE1E1">
                                                                <?php
                                                                if (isset($tipoidentificacion)) {
                                                                    echo $tipoidentificacion;
                                                                } else {
                                                                    ?>
                                                                    <option value=""></option>
                                                                <?php }; ?>
                                                                <option value="1">Cédula de ciudadanía</option>
                                                                <option value="2">Numero Identificación Tributaria (NIT)</option>
                                                                <option value="3">Cédula de extrangería</option>
                                                                <option value="4">Tarjeta de identidad</option>
                                                                <option value="5">N. único de Id. Personal</option>
                                                                <option value="6">Pasaporte</option>
                                                            </select>
                                                            <strong style="color: #E31F24;font-size: 12px "><?php echo form_error('tipo_identificacion'); ?></strong>
                                                        </td>
                                                        <td style="text-align: right">
                                                            NUMERO DE DOCUMENTO
                                                        </td>
                                                        <td>
                                                            <input class="form-control" name="numero_identificacion" value="<?php
                                                            if (isset($identificacion)) {
                                                                echo $identificacion;
                                                            }
                                                            ?>" style="background: #FFE1E1" type="number" >
                                                            <strong style="color: #E31F24;font-size: 12px "><?php echo form_error('numero_identificacion'); ?></strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">
                                                            NOMBRES
                                                        </td>
                                                        <td>
                                                            <input class="form-control" name="nombres" value="<?php
                                                            if (isset($nombres)) {
                                                                echo $nombres;
                                                            }
                                                            ?>" style="background: #FFE1E1; text-transform: uppercase" type="text" autocomplete="off" >
                                                            <strong style="color: #E31F24;font-size: 12px "><?php echo form_error('nombres'); ?></strong>
                                                        </td>
                                                        <td style="text-align: right">
                                                            APELLIDOS
                                                        </td>
                                                        <td>
                                                            <input class="form-control" name="apellidos" value="<?php
                                                            if (isset($apellidos)) {
                                                                echo $apellidos;
                                                            }
                                                            ?>" style="background: #FFE1E1; text-transform: uppercase" type="text" autocomplete="off">
                                                            <strong style="color: #E31F24;font-size: 12px "><?php echo form_error('apellidos'); ?></strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">
                                                            PERFIL
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="tipo_perfil" style="background: #FFE1E1">
                                                                <?php
                                                                if (isset($perfil)) {
                                                                    echo $perfil;
                                                                } else {
                                                                    ?>
                                                                    <option value=""></option>
                                                                <?php }; ?>
                                                                <option value="1">Administrador</option>
                                                                <option value="2">Operario</option>
                                                                <option value="3">Supervisor</option>
                                                                <option value="4">Administrativo</option>
                                                                <option value="5">Sistemas</option>
                                                                <option value="6">Auditor del sistema</option>
                                                                <option value="7">Representante legal</option>
                                                            </select>
                                                            <strong style="color: #E31F24;font-size: 12px "><?php echo form_error('tipo_perfil'); ?></strong>
                                                        </td>
                                                        <td style="text-align: right">
                                                            USUARIO
                                                        </td>
                                                        <td>
                                                            <input class="form-control" name="usuario" value="<?php
                                                            if (isset($usuarios)) {
                                                                echo $usuarios;
                                                            }
                                                            ?>" type="text" style="background: #FFE1E1">
                                                            <strong style="color: #E31F24;font-size: 12px "><?php echo form_error('usuario'); ?></strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">
                                                            CONTRASEÑA
                                                        </td>
                                                        <td >
                                                            <input class="form-control" name="contrasena" id="contrasena" value="<?php
                                                            if (isset($contrasena)) {
                                                                echo $contrasena;
                                                            }
                                                            ?>" style="background: #FFE1E1" onkeyup="validarcontrasenauser()"  type="password">
                                                            
                                                            <strong style="color: #E31F24;font-size: 12px "><?php echo form_error('contrasena'); ?></strong>
                                                            <div id="divcontra"></div>
                                                        </td>

                                                        <td style="text-align: right">
                                                            ESTADO
                                                        </td>
                                                        <td>
                                                            <select class="form-control" name="estado" style="background: #FFE1E1">
                                                                <?php
                                                                if (isset($estado)) {
                                                                    echo $estado;
                                                                } else {
                                                                    ?>
                                                                    <option value=""></option>
                                                                <?php }; ?>
                                                                <option value="1">Activo</option>
                                                                <option value="0">Inactivo</option>
                                                            </select>
                                                            <strong style="color: #E31F24;font-size: 12px "><?php echo form_error('estado'); ?></strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right">
                                                            CONFIRMAR CONTRASEÑA
                                                        </td>
                                                        <td>
                                                            <input class="form-control" name="confirmcontrasena" id="confirmcontrasena" value="" style="background-color: #FFE1E1" onkeyup="validarconfcontra()" type="password">
                                                            <strong style="color: #E31F24;font-size: 12px "><?php echo form_error('confirmcontrasena'); ?></strong>
                                                            <div id="divcontraconf"></div>
                                                        </td>
                                                    </tr>
                                                <?php }; ?>
                                            </table>
                                            <table style="text-align: center;width: 100%" >
                                                <tr>
                                                    <td>
                                                        <input name="guardar" id="guardar" class="btn btn-block bot_gris" value="Guardar" />   
                                                        <input type="hidden" name="guardarref" id="guardarref" class="btn btn-block bot_gris" value="" />
                                                        <div style="color: green; font-size: 8 px"> <?php
                                                            echo $this->session->flashdata('error');
                                                            if (isset($mensaje)) {
                                                                echo $mensaje;
                                                            }
                                                            ?></div>
                                                    </td>
                                                    <td>
                                                        <input name="btnguardarnuevo" id="btnguardarnuevo" class="btn btn-block bot_gris" value="Guardar y nuevo"/> 
                                                        <input type="hidden" name="btnguardarnuevoref" id="btnguardarnuevoref" class="btn btn-block bot_gris" value="" />   
                                                    </td>
                                                    <td>
                                                        <input name="guardarfinalizar" id="guardarfinalizar" class="btn btn-block bot_gris" value="Guardar y finalizar"/>
                                                        <input type="hidden" name="guardarfinalizarref" id="guardarfinalizarref" class="btn btn-block bot_gris" value="" /> 
                                                    </td>
                                                    <td>
                                                        <input name="btnnuevo" class="btn btn-block bot_verde" type="submit" value="Nuevo" />   
                                                    </td>
                                                </tr>
                                            </table>
                                        </form>
                                        <br>
                                        <form action="<?php echo base_url(); ?>index.php/oficina/usuarios/Cusuarios" method="post" style="width: 200px">
                                            <input name="button" class="btn btn-block bot_rojo"  type="submit"  value="Cancelar" />   
                                        </form> 

                                        <br>
                                        <div style="text-align: center;color: gray">
                                            Copyright © 2021 TECMMAS SAS    
                                        </div>
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
    <!-- END CONTENT -->


    <!-- END CONTAINER -->
    <!-- LOAD FILES AT PAGE END FOR FASTER LOADING -->


    <!-- CORE JS FRAMEWORK - START --> 
    <script src="<?php echo base_url(); ?>assets/js/jquery-3.2.1.min.js" type="text/javascript"></script> 
    <script src="<?php echo base_url(); ?>assets/js/popper.min.js" type="text/javascript"></script> 
    <script src="<?php echo base_url(); ?>assets/js/jquery.easing.min.js" type="text/javascript"></script> 
    <script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script> 
    <script src="<?php echo base_url(); ?>assets/plugins/pace/pace.min.js" type="text/javascript"></script>  
    <script src="<?php echo base_url(); ?>assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js" type="text/javascript"></script> 
    <script src="<?php echo base_url(); ?>assets/plugins/viewport/viewportchecker.js" type="text/javascript"></script>  
    <script>window.jQuery || document.write('<script src="<?php echo base_url(); ?>assets/js/jquery-1.11.2.min.js"><\/script>');</script>
    <!-- CORE JS FRAMEWORK - END --> 


    <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - START --> 

    <script src="<?php echo base_url(); ?>assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/jquery-ui/smoothness/jquery-ui.min.js" type="text/javascript"></script> 
    <script src="<?php echo base_url(); ?>assets/plugins/select2/select2.min.js" type="text/javascript"></script> 
    <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 


    <!-- CORE TEMPLATE JS - START --> 
    <script src="<?php echo base_url(); ?>assets/js/scripts.js" type="text/javascript"></script> 
    <!-- END CORE TEMPLATE JS - END --> 

    <script type="text/javascript">


                                                            $(document).ready(function () {

                                                                var contra = $("#contrasena").val();
                                                                if (contra !== "" && contra !== null) {
                                                                    habilitarBotones();
                                                                } else {
                                                                    deshabilitarBotones();
                                                                }

                                                            })
                                                            $('#contrasena').click(function () {
                                                                var contrasenna = $('#contrasena').val();
                                                                if (contrasenna.length == 0) {
                                                                    $('#divcontra').html("<p style='font-weight: bold;color: black; text-align: left;; font-size: 9px'>" +
                                                                            "Bienvenido(a) tenga en cuenta lo siguiente para la asignacion de contraseña: <br>" +
                                                                            "1.Las contraseñas deben tener 6 o mas caracteres<br>" +
                                                                            "2.Debe combinar letras mayúsculas, minúsculas y números.<br>" +
                                                                            "4.Debe contener almenos un caracter especial. Ejemplo: @*,.<br>" +
                                                                            "5.No se pueden repetir caracteres en la contraseña.<br>" +
                                                                            "</p>");
                                                                }
                                                            });
                                                            function validarcontrasenauser() {
                                                                var contrasenna = $('#contrasena').val();
                                                                if (contrasenna.length >= 6) {
                                                                    var mayuscula = false;
                                                                    var minuscula = false;
                                                                    var numero = false;
                                                                    var caracter_raro = false;
                                                                    for (var i = 0; i < contrasenna.length; i++) {
                                                                        if (contrasenna.charCodeAt(i) >= 65 && contrasenna.charCodeAt(i) <= 90)
                                                                        {
                                                                            mayuscula = true;
                                                                        } else if (contrasenna.charCodeAt(i) >= 97 && contrasenna.charCodeAt(i) <= 122)
                                                                        {
                                                                            minuscula = true;
                                                                        } else if (contrasenna.charCodeAt(i) >= 48 && contrasenna.charCodeAt(i) <= 57)
                                                                        {
                                                                            numero = true;
                                                                        } else
                                                                        {
                                                                            caracter_raro = true;
                                                                        }
                                                                    }

                                                                    if (mayuscula == true && minuscula == true && caracter_raro == true && numero == true) {
                                                                        console.log($("#idusuario").val())
                                                                        $.ajax({
                                                                            url: '<?php echo base_url(); ?>index.php/oficina/contrasenas/Ccontrasenas/getpassword',
                                                                            type: 'post',
                                                                            mimeType: 'json',
                                                                            data: {iduser: $("#idusuario").val(),
                                                                                contrasenna: $('#contrasena').val()},
                                                                            success: function (data) {
                                                                                if (data == 1) {
                                                                                    $('#divcontra').html(' ');
                                                                                    $('#divcontra').html('<div style=" color: red; font-size: 12px">La contraseña fue asignada anteriormente.</div>');
                                                                                    deshabilitarBotones();
                                                                                }
                                                                            }
                                                                        });
                                                                        var rta = camposrepetidos(contrasenna);
                                                                        if (rta == true) {
                                                                            deshabilitarBotones()
                                                                            $('#divcontra').html(' ');
                                                                            $('#divcontra').html('<div style=" color: red; font-size: 12px">La contraseña no puede tener caracteres repetidos.</div>');
                                                                        } else {
                                                                            habilitarBotones();
                                                                            $('#divcontra').html('<div style=" color: green; font-size: 12px">La contraseña cumple con los parametros.</div>');
                                                                        }
                                                                    } else {
                                                                        deshabilitarBotones()
                                                                        $('#divcontra').html(' ');
                                                                        $('#divcontra').html('<div style=" color: red; font-size: 12px">La contraseña no cumple con los parametros.</div>');
                                                                    }
                                                                }
                                                            }
                                                            function camposrepetidos(contrasenna) {
                                                                var arraycontra = contrasenna.split("");
                                                                var campos = arraycontra.sort();
                                                                var repetido = false;
                                                                for (var i = 0; i < campos.length; i++) {
                                                                    if (campos[i] == campos[i + 1]) {
                                                                        return repetido = true;
                                                                    }
                                                                }
                                                                return repetido;
                                                            }
                                                            $('#guardar').click(function (ev) {
                                                                ev.preventDefault();
                                                                $('#guardarref').val('guardarref');
                                                                var contrasenaconeach = $('.contrasenaconeach').val();
                                                                var confircontrasenaconeach = $('.confircontrasenaconeach').val();
                                                                var contrasena = $('#contrasena').val();
                                                                var confirmcontrasena = $('#confirmcontrasena').val();
                                                                if (contrasena.length < 6) {
                                                                    deshabilitarBotones()
                                                                    $('#contrasena').val('');
                                                                    $('#confirmcontrasena').val('');
                                                                    $('#divcontra').html("<p style='font-weight: bold;color: red; text-align: left;; font-size: 9px'>La contraseña no cumple con la longitud</p>");
                                                                } else {
                                                                    if (contrasena == confirmcontrasena) {
                                                                        $('#form-reg-user').submit();
                                                                    } else if (contrasenaconeach == confircontrasenaconeach) {
                                                                        $('#form-reg-user').submit();
                                                                    } else {
                                                                        deshabilitarBotones();
                                                                        $('#contrasena').val('');
                                                                        $('#confirmcontrasena').val('');
                                                                        $('#divcontraconf').html('<div style=" color: red; font-size: 12px">Las contraseña no coinciden.</div>');
                                                                    }
                                                                }

//                                                                    $('#mesaje').html('Usuario Creado');

                                                            });
                                                            $('#btnguardarnuevo').click(function (ev) {
                                                                ev.preventDefault();
                                                                $('#btnguardarnuevoref').val('btnguardarnuevoref');
                                                                var contrasenaconeach = $('.contrasenaconeach').val();
                                                                var confircontrasenaconeach = $('.confircontrasenaconeach').val();
                                                                var contrasena = $('#contrasena').val();
                                                                var confirmcontrasena = $('#confirmcontrasena').val();
                                                                if (contrasena.length < 6) {
                                                                    deshabilitarBotones();
                                                                    $('#contrasena').val('');
                                                                    $('#confirmcontrasena').val('');
                                                                    $('#divcontra').html("<p style='font-weight: bold;color: red; text-align: left;; font-size: 9px'>La contraseña no cumple con la longitud</p>");
                                                                } else {
                                                                    if (contrasena == confirmcontrasena) {
                                                                        $('#form-reg-user').submit();
                                                                    } else if (contrasenaconeach == confircontrasenaconeach) {
                                                                        $('#form-reg-user').submit();
                                                                    } else {
                                                                        deshabilitarBotones();
                                                                        $('#contrasena').val('');
                                                                        $('#confirmcontrasena').val('');
                                                                        $('#divcontraconf').html('<div style=" color: red; font-size: 12px">Las contraseña no coinciden.</div>');
                                                                    }
                                                                }
                                                            });
                                                            $('#guardarfinalizar').click(function (ev) {
                                                                ev.preventDefault();
                                                                $('#guardarfinalizarref').val('guardarfinalizarref');
                                                                var contrasenaconeach = $('.contrasenaconeach').val();
                                                                var confircontrasenaconeach = $('.confircontrasenaconeach').val();
                                                                var contrasena = $('#contrasena').val();
                                                                var confirmcontrasena = $('#confirmcontrasena').val();
                                                                if (contrasena.length < 6) {
                                                                    deshabilitarBotones();
                                                                    $('#contrasena').val('');
                                                                    $('#confirmcontrasena').val('');
                                                                    $('#divcontra').html("<p style='font-weight: bold;color: red; text-align: left;; font-size: 9px'>La contraseña no cumple con la longitud</p>");
                                                                } else {
                                                                    if (contrasena == confirmcontrasena) {
                                                                        $('#form-reg-user').submit();
                                                                    } else if (contrasenaconeach == confircontrasenaconeach) {
                                                                        $('#form-reg-user').submit();
                                                                    } else {
                                                                        deshabilitarBotones();
                                                                        $('#contrasena').val('');
                                                                        $('#confirmcontrasena').val('');
                                                                        $('#divcontraconf').html('<div style=" color: red; font-size: 12px">Las contraseña no coinciden.</div>');
                                                                    }
                                                                }
                                                            });

                                                            function habilitarBotones() {
                                                                document.getElementById("guardar").disabled = false;
                                                                document.getElementById("btnguardarnuevo").disabled = false;
                                                                document.getElementById("guardarfinalizar").disabled = false;
                                                            }
                                                            function deshabilitarBotones() {
                                                                document.getElementById("guardar").disabled = true;
                                                                document.getElementById("btnguardarnuevo").disabled = true;
                                                                document.getElementById("guardarfinalizar").disabled = true;
                                                            }

    </script>

</body>
</html>