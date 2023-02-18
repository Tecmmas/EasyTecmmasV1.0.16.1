</div><div class="page-chatapi hideit">

    <div class="search-bar">
        <input type="text" placeholder="Search" class="form-control">
    </div>

    <div class="chat-wrapper">
        <h4 class="group-head">Groups</h4>
        <ul class="group-list list-unstyled">
            <li class="group-row">
                <div class="group-status available">
                    <i class="fa fa-circle"></i>
                </div>
                <div class="group-info">
                    <h4><a href="#">Work</a></h4>
                </div>
            </li>
            <li class="group-row">
                <div class="group-status away">
                    <i class="fa fa-circle"></i>
                </div>
                <div class="group-info">
                    <h4><a href="#">Friends</a></h4>
                </div>
            </li>

        </ul>


        <h4 class="group-head">Favourites</h4>
        <ul class="contact-list">




        </ul>


        <h4 class="group-head">More Contacts</h4>
        <ul class="contact-list">



        </ul>
    </div>

</div>


<div class="chatapi-windows ">




</div>    
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
<script src="<?php echo base_url(); ?>assets/plugins/datatables/js/dataTables.min.js" type="text/javascript"></script> 



<!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - START --> 

<script src="<?php echo base_url(); ?>assets/plugins/datatables/js/jquery.dataTables.min.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/extensions/Responsive/bootstrap/3/dataTables.bootstrap.js" type="text/javascript"></script>

<!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 


<!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - START --> 



<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui/smoothness/jquery-ui.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/plugins/echarts/echarts-custom-for-dashboard.js" type="text/javascript"></script>
<!-- OTHER SCRIPTS INCLUDED ON THIS PAGE - END --> 


<!-- CORE TEMPLATE JS - START --> 
<script src="<?php echo base_url(); ?>assets/js/scripts.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/plugins/datepicker/js/datepicker.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/plugins/multi-select/js/jquery.multi-select.js" type="text/javascript"></script> 
<!-- END CORE TEMPLATE JS - END --> 

<script src="<?php echo base_url(); ?>assets/plugins/daterangepicker/js/moment.min.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>assets/plugins/daterangepicker/js/daterangepicker.js" type="text/javascript"></script> 
<script src="<?php echo base_url(); ?>/application/libraries/package/dist/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url(); ?>application/libraries/table2csv/table2csv.js" type="text/javascript"></script> 
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



<script src="<?php echo base_url(); ?>assets/plugins/messenger/js/messenger.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/plugins/messenger/js/messenger-theme-future.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/plugins/messenger/js/messenger-theme-flat.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/js/messenger.js" type="text/javascript"></script>
<!--<script src="<?php echo base_url(); ?>assets/sesion.js"  type="text/javascript"></script>-->
 <script src="<?php echo base_url(); ?>application/libraries/sesion.js"  type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        setInterval(function () {
            getCronAudit();
        }, 15000);
    });

    var getCronAudit = function () {
        $.ajax({
            url: '<?php echo base_url(); ?>index.php/oficina/reportes/pruebas/Cpruebas/getCronAudit',
            type: 'post',
            mimeType: 'json',
            data: {},
            success: function (data, textStatus, jqXHR) {
                if (data !== null || data !== "") {
                    if (localStorage.getItem("alert") == null) {
                        var datos = {
                            placa: "",
                            id: "",
                            notificado: 0,
                        };
                        localStorage.setItem("alert", JSON.stringify(datos));
                    } else {
                        var dat = JSON.parse(localStorage.getItem("alert"));
                        if (data[0].id == 1 && dat.notificado == 0) {
                            var dat = JSON.parse(localStorage.getItem("alert"));
                            var datos = {
                                placa: data[0].placa,
                                id: data[0].id,
                                notificado: 1,
                            };
                            localStorage.setItem("alert", JSON.stringify(datos));
                            var tex = "Se detecto una alteración del sistema." + "<br>" + "Fecha: " + data[0].fecha + "<br>" + "Placa: " + data[0].placa
                            Swal.fire({
                                title: '¡Atención!',
                                html: tex,
                                imageUrl: '<?php echo base_url(); ?>application/libraries/advertencia.png',
                                imageWidth: 150,
                                imageHeight: 150,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Aceptar',
                            });
                        } else {
                            if (data[0].id !== dat.id) {
                                var datos = {
                                    placa: data[0].placa,
                                    id: data[0].id,
                                    notificado: 1,
                                };
                                localStorage.setItem("alert", JSON.stringify(datos));
                                var tex2 = "Se detecto una alteración del sistema." + "<br>" + "Fecha: " + data[0].fecha + "<br>" + "Placa: " + data[0].placa
                                Swal.fire({
                                    title: '¡Atención!',
                                    html: tex2,
                                    imageUrl: '<?php echo base_url(); ?>application/libraries/advertencia.png',
                                    imageWidth: 150,
                                    imageHeight: 150,
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'Aceptar',
                                });
                            }

                        }
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    }
</script>
<!-- General section box modal start -->
<div class="modal" id="section-settings" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog animated bounceInDown">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Section Settings</h4>
            </div>
            <div class="modal-body">

                Body goes here...

            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                <button class="btn btn-success" type="button">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!-- modal end -->
</body>
</html>


