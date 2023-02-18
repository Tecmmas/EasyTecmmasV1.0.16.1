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
                <div class="content-body"  style="background: green">    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12">
                            <section class="box ">
                                <header class="panel_header">
                                    <h2 class="title float-left">Vehiculo aprobado sin consecutivo</h2>
                                </header>
                                <div class="content-body" >
                                    <input id="idhojapruebas" value="<?php echo $dato; ?>" type="hidden" />
                                    <br>
                                    <table class="table table-bordered" style="text-align: center">
                                        <thead>
                                            <tr>
                                                <th >Id control</th>
                                                <th >Placa</th>
                                                <th>Ocasión</th>
                                                <th>FUR</th>
                                                <th>Tamaño hoja</th>
                                                <th>Consecutivo RUNT</th>
                                                <th>Enviar a SICOV</th>
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
                                        <td>
                                            <input id="consecutivoRunt"  type="number" class="form-control" onchange="guardarConsecutivo(this)">
                                            <label id="mensaje"
                                                   style="background: white;
                                                   width: 100%;
                                                   text-align: center;
                                                   font-weight: bold;
                                                   font-size: 15px;display: none;position: absolute;
                                                   padding: 5px;color: salmon">Este campo es obligatorio</label>
                                        </td>
                                        <td>
                                            <input type="button"  class="btn btn-success btn-block" id="btnenviarsicov"  style="border-radius: 40px 40px 40px 40px;font-size: 20px;"  value="✉️ Enviar" data-toggle='modal' data-target='#confirmacionEnvio'/>
                                        </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div style="text-align: center">
                                        <input class="btn btn-warning btn-block" id="btnenviarfirmado" style="border-radius: 40px 40px 40px 40px;font-size: 20px;width: 300px" value="🚫 Anular este envío" data-toggle='modal' data-target='#confirmacionAnulacion'/><br>
                                    </div>
                                    <form action="<?php echo base_url(); ?>index.php/oficina/CGestion" method="post">
                                        <input name="button" class="btn btn-accent btn-block" style="width: 100px;background: #393185" type="submit"  value="Atras" />   
                                    </form>
                                </div>
                                <img src="<?php echo base_url(); ?>assets/images/logo.png" />
                            </section>
                        </div>
                    </div>
                </div>
            </section>
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
                        <button id="btnAsignar"  class="btn btn-success" type="button" onclick="enviarASICOV()">SI</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="confirmacionAnulacion" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog animated bounceInDown">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" >Confirmación de anulación</h4>
                    </div>
                    <div class="modal-body" style="background: whitesmoke">
                        <label id="mensajeSicov"
                               style="background: white;
                               width: 100%;
                               text-align: center;
                               font-weight: bold;
                               font-size: 15px;
                               padding: 5px;border: solid gray 2px;
                               border-radius:  15px 15px 15px 15px;color: black"><strong style="color: salmon">ADVERTENCIA</strong> <br><br>Esta acción enviará la placa a la sección de APROBADOS SIN FIRMAR, se recomienda revisar si este FUR a sido firmado en el SICOV antes de confirmar esta acción. <br><br> ¿DESEA ENVIAR LA PLACA A "APROBADOS SIN FIRMAR"?</label>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">NO</button>
                        <button id="btnAsignar" class="btn btn-success" type="button" onclick="enviarAnulacion()">SI</button>
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
        <script src="<?php echo base_url(); ?>application/libraries/sesion.js"  type="text/javascript"></script>
        <!-- END CORE TEMPLATE JS - END --> 
        <script type="text/javascript">
                            var placa = "<?php
                                                    echo $placa;
                                                    ?>";
                            var ocasion = "<?php
                                                    echo $ocacion;
                                                    ?>";
                            var ipCAR = "<?php
                                                    echo $ipCAR;
                                                    ?>";

                            $(document).ready(function () {
                                var data = {
                                    desdeVisor: 'car',
                                    dato: $('#idhojapruebas').val(),
                                    IdUsuario: '1'
                                };
//                                                            console.log(data);
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/oficina/fur/CFUR',
                                    type: 'post',
                                    data: data,
                                    mimeType: 'json',
                                    success: function (rta) {
                                        console.log(rta);
                                        for (var c = 0; c < rta.length; c++) {
//                                            if (rta[c].basic) {
//                                                envioBasicCAr(rta[c].basic, rta[c].idprueba)
//                                            }
                                            if ((rta[c].cadena !== "" || rta[c].cadena !== null) && (rta[c].idprueba !== "" || rta[c].idprueba !== null)) {
                                                inforCAr(rta[c].cadena, rta[c].idprueba);
                                            }
                                        }
                                    },
                                    error(rta) {
                                        console.log(rta);
                                    }
                                });
                            });

//                            function envioBasicCAr(basic, idprueba) {
//                                $.ajax({
//                                    type: "POST",
//                                    url: "http://3.138.158.109:8480/cdapp/rest/basico/registro",
//                                    headers: {
//                                        "Authorization": "b56c19aa217e36a6c182be3ce6fab1851c32a6860f74a312f2cf6d230f6c1573",
//                                        "Content-Type": "application/json"
//                                    },
//
//                                    data: basic,
//                                    success: function (rta) {
//                                        console.log(rta)
//                                        if (rta.resp == "OK") {
//                                            var estado = 1;
//                                            var tipo = 'Envio basic exitoso.';
//                                            guardarTabla(estado, tipo, idprueba);
//                                        } else {
//                                            var estado = 0;
//                                            var tipo = 'Envio basic fallido.';
//                                            guardarTabla(estado, tipo, idprueba);
//                                        }
//                                    },
//                                    errors: function (rta) {
//                                        console.log(rta);
//                                    }
//                                });
//                            }

                            function inforCAr(rta, idprueba) {
                                $.ajax({
                                    type: "POST",
                                    url: "http://" + ipCAR + "/cdapp/rest/final/medicionfinal",
                                    headers: {
                                        "Authorization": "b56c19aa217e36a6c182be3ce6fab1851c32a6860f74a312f2cf6d230f6c1573",
                                        "Content-Type": "application/json"
                                    },

                                    data: rta,
                                    success: function (rta) {
                                        console.log(rta)
                                        if (rta.resp == "OK") {
                                            var estado = 1;
                                            var tipo = 'Envio car exitoso.';
                                            guardarTabla(estado, tipo, idprueba);
                                        } else {
                                            var estado = 0;
                                            var tipo = 'Envio car fallido.';
                                            guardarTabla(estado, tipo, idprueba);
                                        }
                                    },
                                    errors: function (rta) {
                                        console.log(rta);
                                    }
                                });
                            }

                            function guardarTabla(estado, tipo, idprueba) {
                                $.ajax({
                                    type: "POST",
                                    url: "<?php echo base_url(); ?>index.php/oficina/fur/CFUR/saveControl",
                                    data: {estado: estado,
                                        tipo: tipo,
                                        idprueba: idprueba},
                                    success: function (rta) {
                                        console.log(rta);
                                    },
                                    errors: function (rta) {
                                        console.log(rta);
                                    }
                                });
                            }
                            var guardarConsecutivo = function (e) {

                                var idht = $('#idhojapruebas').val().split('-');
                                var data = {
                                    idhojapruebas: idht[0],
                                    consecutivorunt: e.value,
                                    reinspeccion: idht[1]
                                };
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/guardarConsecutivoAprobado',
                                    type: 'post',
                                    data: data,
                                    async: false,
                                    success: function (rta) {
//                                                                    console.log(rta);
                                    }
                                });
                            };

                            var enviarAnulacion = function () {
                                var idht = $('#idhojapruebas').val().split('-');
                                var data = {
                                    idhojapruebas: $('#idhojapruebas').val(),
                                    placa: placa,
                                    ocasion: ocasion,
                                    reinspeccion: idht[1]
                                };
                                $.ajax({
                                    url: '<?php echo base_url(); ?>index.php/oficina/gestion/CGPrueba/CGVaproEnvioAnulacion',
                                    type: 'post',
                                    data: data,
                                    async: false,
                                    success: function () {
                                        window.location.replace("<?php echo base_url(); ?>index.php/oficina/CGestion");
                                    }
                                });
                            };

                            var enviarASICOV = function () {
                                if ($('#consecutivoRunt').val() === '') {
                                    var mensaje = document.getElementById('mensaje');
                                    mensaje.style.display = 'block';
                                    mensaje.style.position = 'relative';
                                    $("#confirmacionEnvio").modal('hide');
                                } else {
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
                                                envio: '2',
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
                                                        var segundos = 3;
                                                        $('#mensajeSicov').text("Mensaje de SICOV: " + dat[0] + ". Detalles en el visor.");
                                                        document.getElementById('mensajeSicov').style.color = 'salmon';
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
                                }
                            };
        </script>


    </body>
</html>



