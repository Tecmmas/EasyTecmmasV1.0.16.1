<?php $this->load->view('./header'); ?>
<script type="text/javascript">

</script>
<!-- START CONTENT -->
<section id="main-content" class=" ">
    <section class="wrapper main-wrapper row" style=''>

        <div class='col-12'>
            <div class="page-title">

                <div class="float-left">
                    <!-- PAGE HEADING TAG - START --><h4 class="title">INICIO</h4><!-- PAGE HEADING TAG - END -->  
                </div>

            </div>
        </div>
        <div class="clearfix"></div>
        <!-- MAIN CONTENT AREA STARTS -->

        <div class="col-xl-12" >
            <section class="box " >
                <header class="panel_header">
                    <h2 class="title float-left">INFORMACIÓN IMPORTANTE</h2>
                </header>
                <div class="content-body" style="background: whitesmoke">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12">
                            <section class="box ">
                                <!--                                <header class="panel_header">
                                                                    <h2 class="title float-left">INFORMACIÓN IMPORTANTE</h2>
                                                                </header>-->
                                <div class="content-body">    <div class="row">
                                        <div class="col-12">
                                            <p style="text-align: justify">
                                                <strong>Apreciado usuario:</strong><br>
                                                Reciba un cordial saludo de parte de TECMMAS SAS, queremos recordarle que este sistema de información tiene como finalidad dar cumplimiento a los procesos que se vinculan a la resolución 20203040003625 del 21 de mayo del 2020 por parte del Ministerio de Puertos y Transporte. Por lo anterior, es preciso aclarar que el alcance de este sistema se limita a los siguientes módulos:<br><br>
                                                <strong>Módulos administrativos</strong><br>
                                                -	Gestión de usuarios.<br>
                                                -	Gestión de vehículos.<br>
                                                -	Gestión de asignación de pruebas.<br>
                                                -	Monitorización de placas en tiempo real (Visor).<br>
                                                -	Generación de formato uniforme 20203040003625.<br>
                                                -	Generación digital de formato de prerevisión (Requiere activación)<br>
                                                -	Asignación de número de consecutivo RUNT.<br>
                                                -	Envíos de primera y segunda a vez a SICOV (CI2 e INDRA).<br><br>
                                                <strong>Módulos de pruebas – aplicación móvil</strong><br>
                                                -	Módulo de cámara, cambio por ubicación de datos y marco de agua.<br>
                                                -	Módulo de inspección sensorial, por nueva codificación de resolución 20203040003625.<br>
                                                -	Módulo de prerevisión y posrevisión (Requiere activación).<br>
                                                -	Módulo de luces para marca combi (Requiere activación).<br><br><br>
                                                De acuerdo a lo anterior, los demás procesos como:<br><br>
                                                -	Reasignación individual.<br>
                                                -	Cancelación de pruebas.<br>
                                                -	Reconfiguración de pruebas.<br>
                                                -	Generación de informes para entes ambientales.<br>
                                                -	Generación de informes de procesos de las maquinas.<br>
                                                -	Gestión de usuarios (actores del CDA).<br>
                                                -	Entre otros.<br><br>
                                                Seguirán funcionando en la versión convencional del software, sin embargo, cabe resaltar que todos estos procesos serán migrados al nuevo sistema de información EasyTecmmas v1.0 en las próximas actualizaciones, no sin antes poner en conocimiento los cambios presentados.<br><br>
                                                <strong>Es importante que los procesos que esta disponibles en este sistema (EasyTecmmas v1.0), no sean ejecutados en las versiones convencionales del software para evitar inconsistencia de datos y procedimientos erróneos.</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </section>
<!--                            <section class="box ">
                                <header class="panel_header">
                                    <h2 class="title float-left">GESTIONES Y PROCESOS</h2>
                                </header>
                                <div class="content-body">    <div class="row">
                                        <div class="col-12">
                                            <form action="<?php echo base_url(); ?>index.php/oficina/CGestion" method="post">
                                                <p class="submit">
                                                    <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-accent btn-block" style="background-color: #393185;width: 300px;height: 70px;border-radius: 40px 40px 40px 40px" value="Visor y gestion" />
                                                    <strong style="color: #E31F24">
                                                        <?php
                                                        echo $this->session->flashdata('error');
                                                        ?>    
                                                    </strong>
                                                </p>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <section class="box ">
                                <header class="panel_header">
                                    <h2 class="title float-left">GENERACION DE INFORMES</h2>
                                </header>
                                <div class="content-body">    <div class="row">
                                        <div class="col-12">
                                            <form action="<?php echo base_url(); ?>index.php/oficina/informes/CPrerevision" method="post">
                                                <p class="submit">
                                                    <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-accent btn-block" style="background-color: #393185;width: 200px;height: 70px;border-radius: 40px 40px 40px 40px" value="Prerevisión" />
                                                    <strong style="color: #E31F24">
                                                        <?php
                                                        echo $this->session->flashdata('error');
                                                        ?>    
                                                    </strong>
                                                </p>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </section>-->

                        </div>
                    </div>
                </div>
            </section>

        </div>



        <!-- MAIN CONTENT AREA ENDS -->
    </section>
</section>
<!-- END CONTENT -->



<?php
$this->load->view('./footer');
