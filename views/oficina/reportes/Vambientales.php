<?php $this->load->view('./header'); ?>
<?php $informeNewAnt; ?>
<!-- START CONTENT -->
<script type="text/javascript">

    window.onload = function () {

<?php if ($this->session->userdata('mesajeError')) { ?>;
            var mensaje = "<?php echo $this->session->userdata('mesajeError'); ?>";
            Swal.fire({
                icon: "error",
                title: 'Error',
                text: mensaje,
                showConfirmButton: true,
            });
    <?php
    $this->session->unset_userdata('mesajeError');
}
?>;
    };

</script>
<section id="main-content" class=" ">
    <section class="wrapper main-wrapper row">
        <!--        <div class='col-12'>
                    <div class="page-title">
                    </div>
                </div>
                <div class="clearfix">
        
                </div>-->
        <!-- MAIN CONTENT AREA STARTS -->
        <div class="col-xl-12">
            <section class="box ">
                <?php $this->load->view('./nav'); ?>
                <div class="content-body">    
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12">
                            <section class="box ">

                                <header class="panel_header">
                                    <h2 class="title float-left">Informe ambiental <?= $tipoinforme ?></h2>
                                    <div  class="title float-right" style="margin-right: 30px">
                                        <?php if ($FugasCal == "NA") { ?>
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        Tipo informe
                                                    </tr>
                                                    <tr>
                                                <select id="sel-tipo-informe-fugas-cal">
                                                    <option value="0">Gases Anterior</option>
                                                    <option value="1">Gases Nuevo</option>
                                                </select>
                                                </tr>
                                                </tbody>
                                            </table>
                                        <?php } ?>
                                    </div>
                                </header>

                                <div class="content-body">    
                                    <div class="row">
                                        <div class="col-12">
                                            <?php if ($tipoinforme == 'Dagma') { ?>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Pista</th>
                                                            <th>Equipo</th>
                                                            <th>Generar informe</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $c = 0;
                                                        foreach ($maquina as $item):
                                                            ?>

                                                            <?php if (($item->prueba == 'analizador' || $item->prueba == 'opacidad') && $item->activo == 1) { ?>
                                                                <tr>
                                                                    <td><?= $item->idconf_maquina ?></td>
                                                                    <td><?php
                                                                        if ($item->idconf_linea_inspeccion == 1 || $item->idconf_linea_inspeccion == 7 || $item->idconf_linea_inspeccion == 8) {
                                                                            echo 'Liviano';
                                                                        } elseif ($item->idconf_linea_inspeccion == 4 || $item->idconf_linea_inspeccion == 11 || $item->idconf_linea_inspeccion == 12) {
                                                                            echo 'Mixta';
                                                                        } else {
                                                                            echo 'Moto';
                                                                        }
                                                                        ?></td>
                                                                    <td><?= $item->nombre . '-' . $item->marca . '<br>' . $item->serie_maquina . '-' . $item->serie_banco ?></td>

                                                                    <td>
                                                                        <form action="<?php echo base_url(); ?>index.php/oficina/reportes/Cambientales/informe_dagma" method="post">
                                                                            <div class="row">
                                                                                <div class="col-md-3 col-lg-3 col-sm-3">
                                                                                    <div class="form-group">
                                                                                        <label style="font-weight: bold; color: grey" for="nombres">Fecha inicial</label>
                                                                                        <input type="text" class="form-control datepicker" id="fechainicial" name="fechainicial" data-format="yyyy-mm-dd " autocomplete="off" >
                                                                                    </div>	
                                                                                </div>
                                                                                <div class="col-md-3 col-lg-3 col-sm-3">
                                                                                    <div class="form-group">
                                                                                        <label style="font-weight: bold; color: grey" for="nombres">Fecha final</label>
                                                                                        <input type="text" class="form-control datepicker" id="fechainicial" name="fechafinal" data-format="yyyy-mm-dd " autocomplete="off" >
                                                                                    </div>	
                                                                                </div>
                                                                                <div class="col-md-3 col-lg-3 col-sm-3" style="align-content:  center">
                                                                                    <div class="form-group">
                                                                                        <input name="check-cvc" type="checkbox" class="form-check-input" id="exampleCheck1" value="1" style="margin-top: 40px">
                                                                                        <label class="form-check-label" for="exampleCheck1" style="margin-top: 35px">Informe para la CVC</label>
                                                                                    </div>	
                                                                                </div>
                                                                                <div class="col-md-3 col-lg-3 col-sm-3" style="align-content:  center">
                                                                                    <div class="form-group">
                                                                                        <label></label>
                                                                                        <input type="hidden" id="idconf_maquina" name="idconf_maquina" value="<?= $item->idconf_maquina ?>">
                                                                                        <input type="submit" id="btn-informe-dagma" name="consultar" id="btn-generar-carder" class="btn btn-accent btn-block" onclick="showSuccess('Generando el informe, por favor espere.')" style="background-color: #393185;border-radius: 40px 40px 40px 40px" value="Generar">
                                                                                    </div>	
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </td>
                                                                    <?php $c++; ?>
                                                                <?php }; ?>
                                                            <?php endforeach; ?>   
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            <?php } elseif ($tipoinforme == 'Epa' || $tipoinforme == 'Superintendencia' || $tipoinforme == 'Corpoboyaca') { ?>
                                                <form action="<?php echo base_url(); ?>index.php/oficina/reportes/Cambientales/informes" method="post">
                                                    <table class="table" >
                                                        <thead>
                                                            <tr>
                                                                <th >Generar</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <div class="row" >
                                                                        <div class="form-group mx-sm-5" style="margin-top: 10px">
                                                                            <label style="font-weight: bold; color: grey" for="nombres">Fecha inicial<br/>
                                                                                <input type="text" class="form-control datepicker" id="fechainicial" name="fechainicial" data-format="yyyy-mm-dd " autocomplete="off" >
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-group mx-sm-5" style="margin-top: 10px">
                                                                            <label style="font-weight: bold; color: grey" for="nombres">Fecha final<br/>
                                                                                <input type="text" class="form-control datepicker" id="fechafinal" name="fechafinal" data-format="yyyy-mm-dd " autocomplete="off" >
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-group mx-sm-4" >
                                                                            <label style="font-weight: bold; color: black"></label>
                                                                            <input type="hidden" id="tipoinforme" name="tipoinforme" value="<?= $tipoinforme ?>">
                                                                            <div type="hidden" id="div-informeNewAnt2" name="div-informeNewAnt"></div>
                                                                            <input type="submit" name="consultar" id="btn-generar-ambiental" class="btn btn-accent btn-block" onclick="showSuccess('Generando el informe, por favor espere.')" style="background-color: #393185;border-radius: 40px 40px 40px 40px; width: 180px" value="Generar">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </form>
                                            <?php } else { ?>
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Pista</th>
                                                            <th>Equipo</th>
                                                            <th>Generar informe</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $c = 0;
                                                        foreach ($maquina as $item):
                                                            ?>

                                                            <?php if (($item->prueba == 'analizador' || $item->prueba == 'opacidad') && $item->activo == 1) { ?>
                                                                <tr>
                                                                    <td><?= $item->idconf_maquina ?></td>
                                                                    <td><?php
                                                                        if ($item->idconf_linea_inspeccion == 1 || $item->idconf_linea_inspeccion == 7 || $item->idconf_linea_inspeccion == 8) {
                                                                            echo 'Liviano';
                                                                        } elseif ($item->idconf_linea_inspeccion == 4 || $item->idconf_linea_inspeccion == 11 || $item->idconf_linea_inspeccion == 12) {
                                                                            echo 'Mixta';
                                                                        } else {
                                                                            echo 'Moto';
                                                                        }
                                                                        ?></td>
                                                                    <td><?= $item->nombre . '-' . $item->marca . '<br>' . $item->serie_maquina . '-' . $item->serie_banco ?></td>

                                                                    <td>
                                                                        <form action="<?php echo base_url(); ?>index.php/oficina/reportes/Cambientales/generar" method="post">
                                                                            <div class="row">
                                                                                <div class="form-group mx-sm-1">
                                                                                    <label style="font-weight: bold; color: grey" for="nombres">Fecha inicial<br/>
                                                                                        <input type="text" class="form-control datepicker" id="fechainicial" name="fechainicial" data-format="yyyy-mm-dd " autocomplete="off" >
                                                                                        <!--<strong style="color: #E31F24"><?php echo form_error('fechainicial'); ?></strong>-->
                                                                                    </label>
                                                                                </div>
                                                                                <div class="form-group mx-sm-1">
                                                                                    <label style="font-weight: bold; color: grey" for="nombres">Fecha final<br/>
                                                                                        <input type="text" class="form-control datepicker" id="fechafinal" name="fechafinal" data-format="yyyy-mm-dd " autocomplete="off" >
                                                                                        <!--<strong style="color: #E31F24"><?php echo form_error('fechafinal'); ?></strong>-->
                                                                                    </label>
                                                                                </div>
                                                                                <?php if ($tipoinforme == 'Corantioquia'): ?>
                                                                                    <div class="form-group mx-sm-1" style="margin-top: 22px;">
                                                                                        <select id="tipo_inspeccion" name="tipo_inspeccion" style="height: 35px">
                                                                                            <option value="1">Certificadas</option>
                                                                                            <option value="8888">Pruebas libres</option>
                                                                                            <option value="4444">Preventivas</option>
                                                                                        </select>
                                                                                    </div>
                                                                                <?php endif; ?>
                                                                                <div class="form-group mx-sm-1" >
                                                                                    <label style="font-weight: bold; color: black"></label>
                                                                                    <input type="hidden" id="idconf_maquina" name="idconf_maquina" value="<?= $item->idconf_maquina ?>">
                                                                                    <input type="hidden" id="prueba" name="prueba" value="<?= $item->prueba ?>">
                                                                                    <input type="hidden" id="idconf_linea_inspeccion" name="idconf_linea_inspeccion" value="<?= $item->idconf_linea_inspeccion ?>">
                                                                                    <input type="hidden" id="tipoinforme" name="tipoinforme" value="<?= $tipoinforme ?>">
                                                                                    <input type="hidden" id="serieanalizador" name="serieanalizador" value="<?= $item->serie_maquina ?>">
                                                                                    <div type="hidden" id="div-informeNewAnt<?php echo $c ?>" name="div-informeNewAnt"></div>
                                                                                    <input type="submit" id="consultar" name="consultar" id="btn-generar-carder" class="btn btn-accent btn-block" onclick="showSuccess('Generando el informe, por favor espere.')" style="background-color: #393185;border-radius: 40px 40px 40px 40px;" value="Generar">
                                                                                    <?php if (isset($message)) echo $message; ?>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                        <?php $c++; ?>
                                                                    <?php }; ?>
                                                                <?php endforeach; ?>   
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            <?php } ?>
                                        </div>
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



<?php $this->load->view('./footer'); ?>
<script type="text/javascript">
    var FugasCal = "<?php
echo $FugasCal;
?>";
    var c = "<?php
echo $c;
?>";
    $(document).ready(function () {
        if (FugasCal == "NA") {
            var tipoInforme = $('#sel-tipo-informe-fugas-cal option:selected').attr('value');
            console.log(tipoInforme);
            for (var i = 0; i < c; i++) {
                console.log(i);
                $("#div-informeNewAnt" + i + "").append('<input type="hidden" id="informeNewAnt" name="informeNewAnt" value="' + tipoInforme + '">');
            }
        }
    })
    if (FugasCal == "NA") {
        $("#sel-tipo-informe-fugas-cal").change(function () {
            var tipoInforme = $('#sel-tipo-informe-fugas-cal option:selected').attr('value');
            for (var i = 0; i < c; i++) {
                $("#div-informeNewAnt" + i + "").html("");
                $("#div-informeNewAnt" + i + "").append('<input type="hidden" id="informeNewAnt" name="informeNewAnt" value="' + tipoInforme + '">');
            }
        });
    }



</script>

