<?php $this->load->view('./header'); ?>
<script type="text/javascript">

</script>
<!-- START CONTENT -->
<section id="main-content" class=" ">
    <section class="wrapper main-wrapper row" style=''>
        <!-- MAIN CONTENT AREA STARTS -->
        <div id="infoPrincipal"></div>
        <input type="hidden" id="fecha" value="<?= $fecha ?>">
        <section class="box " id="sectionMetrologia" style="display: none">
            <div class="content-body">   

                <div class="col-12">
                    <div class="col-md-12"><h4>METROLOGIA MAQUINAS</h4><br></div>
                    <div id="getMetrologia"></div>
                </div>
            </div>
        </section>
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
    var ipLocal = '<?php
echo base_url();
?>';
    $(document).ready(function () {
        getdatos()

    });

    var getdatos = function () {
        $.ajax({
            url: "https://updateapp.tecmmas.com/Actualizaciones/index.php/Cdescargas/infoPrincipal",
            type: 'get',
            async: false,
            mimeType: 'json',
            success: function (data) {
                $("#infoPrincipal").html(data[0].html);
                getMetrologia()
            },
            timeout: 5000,
            error: function (res) {
                console.log(res.responseText)
            }
        });
    }
    var getMetrologia = function () {
        var text = new XMLHttpRequest();
        text.open("GET", ipLocal + "system/dominio.dat", false);
        text.send(null);
        var dominio = text.responseText
        localStorage.setItem("dominio", dominio);
        $.ajax({
            url: "http://" + dominio + "/cda/index.php/Cadicionales/selMetrologia",
            type: 'get',
            async: false,
            mimeType: 'json',
            success: function (data) {
                var nB = "Fecha vencimiento certificado:";
                var tipoContainer = "bg-info";
                console.log($("#fecha").val())
                $.each(data, function (i, data) {
                    if ($("#fecha").val() > data.fechacertificado) {
                        document.getElementById("sectionMetrologia").style.display = "";
                        nB = "Certificado vencido:";
                        tipoContainer = "bg-danger"
                    }
                    var body = "<div class='row'>";
                    body += "<div class='col-xs-12 col-sm-8 col-lg-8'>";
                    body += "<div class='tile-counter inverted "+tipoContainer+"'>";
                    body += "<div class='content'>";
                    body += "<i class='fa fa-thumbs-down icon-lg'></i>";
                    body += "<span style='font-size: 13px'>" + data.maquina.toString().replace("-", " ").toUpperCase() + "</span>";
                    body += "<div class='clearfix'>" + nB + "</div>";
                    body += "<label style='color: whitesmoke; font-size: 18px'>" + data.fechacertificado + "</label>";
                    body += "</div>";
                    body += "</div>";
                    body += "</div>";
                    body += "</div>";
                    $("#getMetrologia").append(body);
                });
            },
            timeout: 5000,
            error: function (res) {
                console.log(res.responseText)
            }
        });
    }



</script>
