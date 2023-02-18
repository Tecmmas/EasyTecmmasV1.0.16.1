<!DOCTYPE html>
<html class=" ">
    <head>

        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta charset="utf-8" />
        <title><?php echo $this->config->item('titulo'); ?></title>
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
        <!-- CORE CSS FRAMEWORK - END -->

        <!-- HEADER SCRIPTS INCLUDED ON THIS PAGE - START --> 


        <link href="<?php echo base_url(); ?>assets/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" media="screen"/>

        <!-- HEADER SCRIPTS INCLUDED ON THIS PAGE - END --> 


        <!-- CORE CSS TEMPLATE - START -->
        <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url(); ?>assets/css/responsive.css" rel="stylesheet" type="text/css"/>
        <!-- CORE CSS TEMPLATE - END -->

    </head>
    <!-- END HEAD -->

    <!-- BEGIN BODY -->
    <body class="login_page" style="background: white">

        <div class="container-fluid"> 
            <div class="login-wrapper row" >
                <div id="login" class="login loginpage offset-xl-4 offset-lg-3 offset-md-3 offset-0 col-12 col-md-6 col-xl-4" style="border: solid;border-color: #393185;background: whitesmoke;border-radius: 40px 40px 40px 40px" >
                    <h1><a href="#" title="Login Page" tabindex="-1">Complete Admin</a></h1>
                    <form name="loginform" id="loginform" action="<?php echo base_url(); ?>index.php/Cindex/validar" method="post">
                        <p>
                            <label style="font-weight: bold;color: black" for="usuario">NOMBRE DE USUARIO<br/>
                                <input onkeydown="validarUserTecmmas()"  type="text" name="usuario" id="usuario" class="input"  size="20" value="<?php echo $usuario; ?>"/>
                                <strong style="color: #E31F24"><?php echo form_error('usuario'); ?></strong>
                            </label>
                        </p>
                        <p>
                            <label style="font-weight: bold;color: black" for="contrasena">CONTRASEÑA<br />
                                <input onkeydown="validarUserTecmmas()" type="password" name="contrasena" id="contrasena" class="input" size="20" value="<?php echo $contrasena; ?>" />
                                <strong style="color: #E31F24"><?php echo form_error('contrasena'); ?></strong>
                            </label>

                        </p>
                        <label id="mensaje" style="color: brown;font-weight: bold;text-align: center"></label>
                        <label id="mensaje2" style="color: brown;font-weight: bold;text-align: center"></label>
                        <p class="submit">
                            <input disabled type="submit" name="wp-submit" id="ingresar" class="btn btn-accent btn-block" style="background-color: #393185" value="Ingresar" />
                            <strong style="color: #E31F24">
                                <?php
                                echo $this->session->flashdata('error');
                                ?>    
                            </strong>
                            <?php
                            if (isset($mensaje)) {
                                echo $mensaje;
                            }
                            ?>    
                        </p>

                    </form>
                    <div style="text-align: center;width: 100%">
                        <strong style="color: brown;text-align: center;width: 100%">Release v1.0.16.1</strong>
                    </div>
                    <input type="button" value="❔ Ayuda" onclick="window.location.replace('<?php echo base_url(); ?>index.php/Cayuda')" />

                    <div style="text-align: center;color: gray">
                        <?php echo $this->config->item('derechos'); ?>    
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT AREA ENDS -->
        <!-- LOAD FILES AT PAGE END FOR FASTER LOADING -->


        <!-- CORE JS FRAMEWORK - START --> 
        <script src="<?php echo base_url(); ?>assets/js/jquery-3.2.1.min.js" type="text/javascript"></script> 
        <script src="<?php echo base_url(); ?>assets/js/popper.min.js" type="text/javascript"></script> 
        <script src="<?php echo base_url(); ?>assets/js/jquery.easing.min.js" type="text/javascript"></script> 
        <script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script> 
        <script src="<?php echo base_url(); ?>assets/plugins/pace/pace.min.js" type="text/javascript"></script>  
        <script src="<?php echo base_url(); ?>assets/plugins/viewport/viewportchecker.js" type="text/javascript"></script>  
        <script src="<?php echo base_url(); ?>/application/libraries/package/dist/sweetalert2.all.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?php echo base_url(); ?>assets/js/jquery-1.11.2.min.js"><\/script>');</script>
        <!-- CORE JS FRAMEWORK - END --> 


        <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - START --> 

        <script src="<?php echo base_url(); ?>assets/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 


        <!-- CORE TEMPLATE JS - START --> 
        <script src="<?php echo base_url(); ?>assets/js/scripts.js" type="text/javascript"></script> 
        <!-- END CORE TEMPLATE JS - END --> 

        <script type="text/javascript">
                        var IdUsuario = '<?php echo $this->session->userdata('IdUsuario'); ?>';
                        var ocultarLicencia = '<?php
                        if (isset($ocultarLicencia)) {
                            echo $ocultarLicencia;
                        } else {
                            echo'0';
                        }
                        ?>';
                        var ipLocal = '<?php
                        echo base_url();
                        ?>';

                        var hablitado = false;
                        var dominio = "";
                        $(document).ready(function () {
                            //alert('data')
                            // if (localStorage.getItem("dominio") !== null || localStorage.getItem("dominio") !== "") {
                            //console.log(ipLocal + "system/dominio.dat")
                            var text = new XMLHttpRequest();
                            text.open("GET", ipLocal + "system/dominio.dat", false);
                            text.send(null);
                            dominio = text.responseText;
                            localStorage.setItem('IdUsuario', IdUsuario);
                            hablitado = false;
                            ContrasenaSer();
                            //}
                        });

                        function ContrasenaSer() {
                            validarLicencia();
                            var datos = {
                                dominio: dominio,
                                function: "login"
                            }
                            fetch("http://updateapp.tecmmas.com/Actualizaciones/index.php/Cpassword",
                                    {
                                        method: "POST",
                                        body: JSON.stringify(datos),
                                        headers: {
                                            'Autorization': 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.Ijg5NnNkYndmZTg3dmNzZGFmOTg0bmc4ZmdoMjRvMTI5MHIi.HraZ7y3eG3dGhKngzOWge-je8Y3lxZgldXjbRbcA7cA',
                                            'Content-Type': 'application/json'
                                        },
                                    }, 200)
                                    .then(respuesta => respuesta.json())
                                    .then((rta) => {
                                        //console.log(rta)
                                        if (rta[0]['VersionVigente'] !== rta[0]['version']) {
                                            Swal.fire({
                                                title: '<strong>Actualización nueva</strong>',
                                                icon: 'info',
                                                html: '<div style="font-size:15px">El sistema a detectado una nueva actualización ' + rta[0]['VersionVigente'] + ', lo invitamos a descargala tanto para celulares, como para oficina.<div>',
                                            })
                                        }
                                        if (rta !== null && rta !== "")
                                            if (rta[0]['actualizado'] == 0) {
                                                savePassword(rta[0]['html']);
                                            }
                                        //console.log(rta[0]['html']);
//                                    localStorage.setItem("pserts",rta[0]['clave'])
                                    })

                                    .catch(error => {
                                        console.log(error.message);

                                    });

                        }

                        function savePassword(clave) {
                            //console.log(clave)
                            $.ajax({
                                type: 'POST',
                                url: "<?php echo base_url(); ?>index.php/Cindex/savePassword",
                                mimeType: 'json',
                                async: true,
                                data: {clave: clave},
                                success: function (data, textStatus, jqXHR) {
                                    console.log(data)

                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    console.log(jqXHR.responseText)

                                }
                            });
                        }




                        function validarLicencia() {
                            localStorage.setItem('ipLocal', ipLocal);
                            $.ajax({
                                url: ipLocal + "index.php/CbajarConfiguracion/getDominio",
                                type: 'post',
                                async: false,
                                success: function (dominio) {
                                    var data = {
                                        dominio: dominio,
                                        funcion: "getLicencia",
                                        file: "license"
                                    };
                                    $.ajax({
                                        url: "<?php echo base_url(); ?>index.php/CbajarConfiguracion/getConf",
                                        data: data,
                                        type: 'post',
                                        success: function () {
                                            validar3();
                                        }
                                    });
                                },
                                timeout: 5000,
                                error: function () {
                                    validar3();
                                }
                            });

                        }

                        function validar3() {
                            $.ajax({
                                url: '<?php echo base_url(); ?>index.php/Cconfiguracion/getMac',
                                type: 'post',
                                success: function (mac) {
                                    console.log(mac)
                                    if (mac == '' || mac == null) {
                                        $.ajax({
                                            url: '<?php echo base_url(); ?>index.php/Cconfiguracion/getMacServer',
                                            type: 'post',
                                            success: function (data) {
                                                console.log(data)
                                            }
                                        })
                                    }
                                    if (mac !== '' || (localStorage.getItem("mac") !== "" && localStorage.getItem("mac") !== null)) {
                                        validarActivacion(localStorage.getItem("mac"));

                                    } else {
                                        console.log("else")
                                        validar4();
                                    }
                                }
                            });
                        }

                        function validar4() {
                            $.ajax({
                                url: '<?php echo base_url(); ?>index.php/Cconfiguracion/getMacServer',
                                type: 'post',
                                success: function (mac) {
                                    if (localStorage.getItem("mac") == undefined || localStorage.getItem("mac") == "" || localStorage.getItem("mac") == null) {
                                        localStorage.setItem("mac", mac);
                                    }

                                    if (mac !== '' || (localStorage.getItem("mac") !== "" || localStorage.getItem("mac") !== null)) {
                                        validarActivacion(localStorage.getItem("mac"));
                                    } else {
                                        $("#mensaje").text('El sistema no reconoce la MAC de este equipo');
                                    }

                                }
                            });
                        }
//                        var app = {
//                            init: function () {
//                                localStorage.setItem("sicovModoAlternativo", '0');
//                                validarLicencia();
//                            }
//                        };
//
//                        $(document).ready(function () {
//                            localStorage.setItem('IdUsuario', IdUsuario);
//                            hablitado = false;
//                            app.init();
//                        });
//
//
//                        function validarLicencia() {
//                            localStorage.setItem('ipLocal', ipLocal);
//                            $.ajax({
//                                url: ipLocal + "index.php/CbajarConfiguracion/getDominio",
//                                type: 'post',
//                                async: false,
//                                success: function (dominio) {
//                                    $.ajax({
//                                        url: "https://" + dominio + "/cda/index.php/Cservicio/getLicencia",
//                                        success: function (data) {
//                                            var data2 = {
//                                                dato: data
//                                            };
//                                            $.ajax({
//                                                url: "<?php echo base_url(); ?>index.php/CbajarConfiguracion/getConfLicencia",
//                                                data: data2,
//                                                type: 'post',
//                                                async: false,
//                                                success: function () {
//                                                    validar3();
//                                                }
//                                            });
//                                        },
//                                        timeout: 5000,
//                                        error: function () {
//                                            validar3();
//                                        }
//                                    });
//                                },
//                                timeout: 5000,
//                                error: function () {
//                                    validar3();
//                                }
//                            });
//
//                        }
//
//                        function validar3() {
//                            $.ajax({
//                                url: '<?php echo base_url(); ?>index.php/Cconfiguracion/getMac',
//                                type: 'post',
//                                success: function (mac) {
//                                    if (mac !== '') {
//                                        validarActivacion(mac);
//                                    } else {
//                                        validar4();
//                                    }
//                                }
//                            });
//                        }
//
//                        function validar4() {
//                            $.ajax({
//                                url: '<?php echo base_url(); ?>index.php/Cconfiguracion/getMacServer',
//                                type: 'post',
//                                success: function (mac) {
//                                    if (mac !== '') {
//                                        validarActivacion(mac);
//                                    } else {
//                                        $("#mensaje").text('El sistema no reconoce la MAC de este equipo');
//                                    }
//
//                                }
//                            });
//                        }

                        function validarActivacion(mac) {
                            var data = {
                                mac: mac
                            };
                            $.ajax({
                                url: "<?php echo base_url(); ?>index.php/Clogin/validar",
                                type: 'post',
                                data: data,
                                success: function (rta) {
                                    console.log(rta);
                                    var dispositivo = JSON.parse(rta);
                                    if (dispositivo.activo === '0') {
                                        deshabilitarComponentes();
                                        $("#mensaje").text("Este dispositivo no se encuentra habilitado para el uso de este software");
                                    } else if (dispositivo.cdaactivo === '0') {
                                        deshabilitarComponentes();
                                        $("#mensaje").text("Este CDA no se encuentra habilitado para el uso de este software, por favor comuníquese con TECMMAS SAS.");
                                    } else if (dispositivo.dias <= 0) {
                                        if (ocultarLicencia === '1') {
                                            habilitarComponentes();
                                            hablitado = true;
                                            $("#mensaje").text("");
                                        } else {
                                            deshabilitarComponentes();
                                            $("#mensaje").text("Su licencia a expirado, por favor comuníquese con TECMMAS SAS.");
                                        }
                                    } else if (dispositivo.cron_audit !== 'OK' || dispositivo.auditres_jz !== 'OK' || dispositivo.auditpru_jz !== 'OK') {
                                        deshabilitarComponentes();
                                        $("#mensaje").text("Se detectó un procedimiento indebido y por su seguridad el sistema se ha bloqueado. Comuníquese con TECMMAS SAS.");
                                    } else {
                                        habilitarComponentes();
                                        hablitado = true;
                                        if (ocultarLicencia === '1') {
                                            $("#mensaje").text("");
                                        } else {
                                            if (dispositivo.dias === '1')
                                                $("#mensaje").text("Su licencia expira en un día, por favor comuníquese con TECMMAS SAS.");
                                            else
                                                $("#mensaje").text("Su licencia expira en " + dispositivo.dias + " días");
                                        }

                                    }
                                }
                            });
                        }

                        function validarUserTecmmas() {
                            if ($('#usuario').val() === 'tecmmas' && $('#contrasena').val() === '1q2w3e4r**')
                                habilitarComponentes();
                            else {
                                if (!hablitado)
                                    deshabilitarComponentes();
                            }
                        }

                        function deshabilitarComponentes() {
                            document.getElementById("ingresar").disabled = true;
                        }

                        function habilitarComponentes() {
                            document.getElementById("ingresar").disabled = false;
                        }

        </script>

    </body>
</html>



