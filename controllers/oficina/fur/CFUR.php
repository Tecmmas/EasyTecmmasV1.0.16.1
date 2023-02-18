<?php

defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
ini_set('memory_limit', '-1');
set_time_limit(0);

class CFUR extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('security');
//        $this->load->model("oficina/fur/MFUR");
        $this->load->model("dominio/Mhojatrabajo");
        $this->load->model("dominio/Mvehiculo");
        $this->load->model("dominio/Mpropietario");
        $this->load->model("dominio/Mcda");
        $this->load->model("dominio/Msede");
        $this->load->model("dominio/Mciudad");
        $this->load->model("dominio/Mdepartamento");
        $this->load->model("dominio/Mpais");
        $this->load->model("dominio/Mservicio");
        $this->load->model("dominio/Mclase");
        $this->load->model("dominio/Mmarca");
        $this->load->model("dominio/Mlinea");
        $this->load->model("dominio/Mcolor");
        $this->load->model("dominio/Mcombustible");
        $this->load->model("dominio/Mcarroceria");
        $this->load->model("dominio/Mprueba");
        $this->load->model("dominio/Mresultado");
        $this->load->model("dominio/Mimagenes");
        $this->load->model("dominio/MconsecutivoTC");
        $this->load->model("dominio/Mconfig_prueba");
        $this->load->model("dominio/Mmaquina");
        $this->load->model("dominio/Musuarios");
        $this->load->model("dominio/Mcertificados");
        $this->load->model("dominio/Mpre_prerevision");
        $this->load->model("dominio/Mpre_atributo");
        $this->load->model("dominio/Mpre_dato");
        $this->load->model("dominio/MEventosindra");
        $this->load->model("dominio/Mcontrol_salae");
        $this->load->model('GeneralModel');
        $this->load->model("oficina/reportes/Mambientales");
        $this->load->library('Opensslencryptdecrypt');
    }

    var $order;
    var $defectosMA;
    var $defectosMB;
    var $defectosSA;
    var $defectosSB;
    var $defectosEA;
    var $defectosEB;
    var $observaciones;
    var $aprobado;
    var $aprobadoE;
    var $nombreClase;
    var $defectos;
    var $arrayCi2;
    var $taxonomiaIndra;
    var $virtualRunt;
    var $idCdaRUNT;
    var $idSoftwareRunt;
    var $idConsecutivoRunt;
    var $software;
    var $nombreSicov;
    var $enpista;
    var $codigoOnac;
    var $ipSicov;
    var $sicovModoAlternativo;
    var $ipSicovAlternativo;
    var $usuarioSicov;
    var $claveSicov;
    var $desquilibrioBmulti = '0';
    var $noMostrarDepFur = '0';
    var $mostrarFecha = '0';
    var $espejoImagenes = '0';
    var $desdeConsulta = "false";
    var $habilitarPerifericos = "0";
    var $horarioAtencion = "";
    var $modoDobleExt = "1";
    var $logoColorOnac = "0";
    var $logoColorSuper = "0";
    var $obseConfLuces = '0';
    var $obseCorrOxigeno = '0';
    var $evalLucFullMotos = '0';
    var $habilitarLogoOnac = '1';
    var $mostrarO2motos = '0';
    var $dirCARinforme = 'C:/Informes_Ambientales/';
    var $CARinformeActivo = '0';
    var $fechaLogoOnac = '0';
    var $posicionLogoOnac = '1';
    var $idprueba_gases = 0;
    var $tipo_informe_fugas_cal_lin;
    var $salaEspera2 = '0';
    var $ajustarGrupos = '0';
    var $envioCorreo = "0";
    var $emailCDA = "";
    var $pwEmailCDA = "";
    var $asuntoEmailCDA = "";
    var $vistaEmailCDA = "";
    var $nombreCDA = "";
    var $observacionesExtra = "";
    var $generarLogGases = "0";
    var $fechares762 = "";
    var $fechares762_K1 = "";
    var $fechares762_Chispa = "2023-02-08";
    var $kCruda = "";
    var $fechaGlobal = "";

    public function index() {

        if (($this->session->userdata('IdUsuario') == '' ||
                $this->session->userdata('IdUsuario') == '1024' ||
                $this->input->post('dato') == '') && $this->input->post('desdeVisor') == 'false') {
            redirect('Cindex');
        }
        $this->enpista = false;
        $this->mostrarFecha = '0';
        $this->setConf();
//        $this->arrayCi2 = array();
        $this->taxonomiaIndra = "";
        $dat = explode("-", $this->input->post('dato'));
        $ocasion = $dat[1];
        if ($ocasion == "0" || $ocasion == "4444" || $ocasion == "8888") {
            $this->order = 'ASC';
        } else {
            $this->order = 'DESC';
        }
//        echo
        $idhojaprueba = $dat[0];
        $tercer = "";
        if (isset($dat[2])) {
            $tercer = $dat[2];
        }
        if ($tercer == "1") {
            $this->enpista = true;
        }
        $envioEmailFur = "0";
        if (isset($dat[3])) {
            $envioEmailFur = $dat[3];
        }
//        $idhojaprueba = 273956;
        $this->aprobado = true;
        $this->aprobadoE = true;
        $this->defectosMA = array();
        $this->defectosMB = array();
        $this->defectosSA = array();
        $this->defectosSB = array();
        $this->defectosEA = array();
        $this->defectosEB = array();
        $this->observaciones = array();

        $cons = "";
        $consecutivo = $this->getConsecutivo($idhojaprueba);
        if ($this->virtualRunt === "0") {
            $cons = $consecutivo . "-" . $ocasion;
        } else {
            $cons = $this->idCdaRUNT . $this->idSoftwareRunt . $this->idConsecutivoRunt;
        }
        $data['reins'] = $ocasion;
        $data['cda'] = $this->getCda();
        $data['cda']->logo = "@" . base64_encode($data['cda']->logo);
        $data['sede'] = $this->getSede();
        $data['ciudadCDA'] = $this->getCiudad($data['sede']->cod_ciudad);
        if ($this->noMostrarDepFur == '1') {
            $data['departamentoCDA'] = (object) array('nombre' => '');
        } else {
            $data['departamentoCDA'] = $this->getDepartamento($data['ciudadCDA']->cod_depto);
            $data['departamentoCDA']->nombre = " - " . $data['departamentoCDA']->nombre;
        }
        $data['hojatrabajo'] = $this->getHojatrabajo($idhojaprueba);
        $fechaMaquinas = '';
        $data["logoCda"] = '<img style="width: 115px;height: 56.66px" src="' . $data['cda']->logo . '">';

        if ($ocasion == "0" || $ocasion == '8888' || $ocasion == '4444') {
            $data['fechafur'] = $data['hojatrabajo']->fechainicial;
            $this->fechaGlobal = $data['fechafur'];
            $fechaMaquinas = $data['fechafur'];
            $pr2 = $this->getFechaLastReins($idhojaprueba, 'ASC');
            $data['ocasion'] = "false";
            $data['fechainicioprueba'] = "Fecha y hora inicial de la prueba: " . $data['fechafur'] . "<br>";
            if (isset($pr2->fechafinal)) {
                $data['fechafinalprueba'] = "Fecha y hora final de la prueba: " . $pr2->fechafinal;
            } else {
                $data['fechafinalprueba'] = "";
            }
        } else {
            $pr = $this->getFechaReins($idhojaprueba);
            $pr2 = $this->getFechaLastReins($idhojaprueba, 'DESC');
            $data['fechafur_ant'] = $data['hojatrabajo']->fechainicial;
            $data['fechafur'] = $pr->fechainicial;
            $this->fechaGlobal = $data['fechafur'];
            $data['ocasion'] = "true";
            $data['fechainicioprueba'] = "Fecha inicial de la prueba: " . $pr->fechainicial . "<br>";
            $fechaMaquinas = $pr->fechainicial;
            if (isset($pr2->fechafinal)) {
                $data['fechafinalprueba'] = "Fecha final de la prueba: " . $pr2->fechafinal;
            } else {
                $data['fechafinalprueba'] = "";
            }
        }
//                 $this->fechaGlobal = "2023-02-08";

        if ($ocasion == '4444' || $ocasion == '44441') {
            $data['titulo'] = 'FORMATO DE INSPECCIÓN PREVENTIVA';
            $data['consecutivo'] = "<strong>PR N°: </strong>PR$idhojaprueba";
            $data['numeroOnac'] = '';
            $data['fur_aso'] = '';
            $data['infoOnac'] = '';
            $data['logoSuper'] = '';
            $data['escudoColombia'] = '';
            $data['tituloMinisterio'] = '';
            $data['tituloB'] = '<strong>B. RESULTADOS DE LA INSPECCIÓN MECANIZADA.</strong>';
            $data['tituloC'] = '<strong>C. DEFECTOS ENCONTRADOS EN LA INSPECCIÓN MECANIZADA.</strong>';
            $data['tituloD'] = '<strong>D. DEFECTOS ENCONTRADOS EN LA INSPECCIÓN SENSORIAL</strong>';
            $data['tituloE'] = '<strong>E. CONFORMIDAD DE LA INSPECCIÓN';
            $data['tituloG'] = '<strong>G. REGISTRO FOTOGRÁFICO DE LA REVISIÓN PREVENTIVA</strong>';
            $data['tituloJ'] = '<strong>J. NOMBRE DE LOS INSPECTORES QUE REALIZARON LA REVISIÓN PREVENTIVA</strong>';
            $data["colOnac"] = "97px";
            $data["colCda"] = "114px";
            $data["colMid"] = "3px";
            $data["colDatCda"] = "124px";
        } elseif ($ocasion == '8888') {
            $data['titulo'] = 'FORMATO DE INSPECCIÓN PRUEBA LIBRE';
            $data['consecutivo'] = "<strong>PL N°: </strong>PL$idhojaprueba";
            $data['numeroOnac'] = '';
            $data['fur_aso'] = '';
            $data['infoOnac'] = '';
            $data['logoSuper'] = '';
//            if ($this->logoColorSuper == "0") {
////Monocromatica
//                $data['logoSuper'] = '<img style="width: 99.16px;height: 42.5px" src="@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"  />';
//            } else {
////Color
//                $data['logoSuper'] = '<img style="width: 99.16px;height: 42.5px" src="@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"  />';
//            }
//            $data['escudoColombia'] = '<img src="escudocolombia.jpg" style="width:34px;height:42.5px;">';
            $data['escudoColombia'] = '';
//            $data['tituloMinisterio'] = '<br>REPÚBLICA DE COLOMBIA<BR>MINISTERIO DE TRANSPORTE<br><br>';
            $data['tituloMinisterio'] = '';
            $data['tituloB'] = '<strong>B. RESULTADOS DE LA INSPECCIÓN MECANIZADA.</strong>';
            $data['tituloC'] = '<strong>C. DEFECTOS ENCONTRADOS EN LA INSPECCIÓN MECANIZADA.</strong>';
            $data['tituloD'] = '<strong>D. DEFECTOS ENCONTRADOS EN LA INSPECCIÓN SENSORIAL</strong>';
            $data['tituloE'] = '<strong>E. CONFORMIDAD DE LA INSPECCIÓN';
            $data['tituloG'] = '<strong>G. REGISTRO FOTOGRÁFICO DE LA REVISIÓN DE PRUEBA LIBRE</strong>';
            $data['tituloJ'] = '<strong>J. NOMBRE DE LOS INSPECTORES QUE REALIZARON LA REVISIÓN DE PRUEBA LIBRE</strong>';
            $data["colOnac"] = "97px";
            $data["colCda"] = "114px";
            $data["colMid"] = "3px";
            $data["colDatCda"] = "124px";
        } else {
            $fechaAhora = $data['fechafur'];

            $data["colOnac"] = "97px";
            $data["colCda"] = "114px";
            $data["colMid"] = "3px";
            $data["colDatCda"] = "124px";
            if ($this->fechaLogoOnac == "0" || ($this->fechaLogoOnac > $fechaAhora)) {
                if ($this->habilitarLogoOnac == "1") {
                    if ($this->logoColorOnac == "0") {
//IMAGEN MONOCROMATICA
                        $logoOnac = "@iVBORw0KGgoAAAANSUhEUgAAAbUAAAETCAYAAACx75guAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAAZdEVYdFNvZnR3YXJlAEFkb2JlIEltYWdlUmVhZHlxyWU8AAADImlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4zLWMwMTEgNjYuMTQ1NjYxLCAyMDEyLzAyLzA2LTE0OjU2OjI3ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGMUNFNTlDMDE4QjQxMUU3ODEyNEIxMDgwNjJCRTY4NSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGMUNFNTlDMTE4QjQxMUU3ODEyNEIxMDgwNjJCRTY4NSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkYxQ0U1OUJFMThCNDExRTc4MTI0QjEwODA2MkJFNjg1IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkYxQ0U1OUJGMThCNDExRTc4MTI0QjEwODA2MkJFNjg1Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwv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HO2Jv52EUCKSiCOUREvib/73SkxWzpUzpywyygrVLCqaQae0+m/GuBBaJh6iwRA7jFAz3NAgmNDWiMBB+CgnEcPxjz/+lNBXEnXDCYHlCD2dEG6nTp+WyP5A8OTkWighxCIFl04yMTlFCvk/RQoWCE0hC4LifEFKoRNCiuQsEmowGMKDEWoGg8FguGHwFGpmNNlgMBgMNwxGqBkMBoPhhsEINYPBYDDcMBihZjAYDIYbBiPUDAaDwXDDYISawWAwGG4YjFAzGAwGww2DEWoGg8FguGEwQs1gMBgMNwxGqBkMBoPhhsEINYPBYDDcMBihZjAYDIYbBiPUDAaDwXDDYISawWAwGG4YjFC7CWHNsCtXrqhTp06p48ePqzNnzsjaY6xDZjAYwgeL2168eFGdOHFCkrOYrSF0mPXU4hkEB4V64sQJ9p4Imja9X+XLl09WVw4V3AsBRkU6ffq02rRpk9q8ebNauXKlOnToUBQhxn1r1aqpataoqe6+5x5VokSJWC1uyTXHjx+nLl26ZO+JGRbgTJcunSpatJhORWVhTe4bSF5wvz179qhly5bae+JG8eIl1L333uvz3tzvm2+mSkfAHyluTaEyZsyocufJo/Lnz69Sp04t+RmX78tK3XPmzJaOiCfVqlVTFSpUdL02nZXZs2erCxciFj0NBc8+21qlSZPG3goM8o1VyLdu3arWr19n71XyrZ955ll166232ntiz08/XdD1aqK95U6yZMllxfGcuXLpb11cpUqVSu5NGQwlzvuyWO2uXbvUhg3r1caNm3TaKPWf34FvljXrnbru3a1qVK+u7qldW915550hyY+bCc/11BRCTe+ztFDT+WwINwcPHrTSp08vee6ZOnfqZGntyT4q7ugKZenGz5o+fbr1XOvWVtasWaPd01fSDbDVosUj1vLly+WZtGC0rxozP//8s1WwYEHX6waSChcuZH355ZeWFsKWbsTtq/rm8uXL1htvvOF6rdikxx9/3O930MLaqlixouu5vlKxYkWtzp07WWvWrJHnjS3bt22zypUrF+36b775pvX777/bR12D/GvZsmW04+OatOCw7xA4umNlTZwwIdq1tFCzBg0aZB8VN5568slo1/eXqIevdOxoLV682NKCRupMXKGuXL161dq9e7c1dOgQq0H9+q739pc+eP99S2txlhaI9lUNMTFnzhzJ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";
                    } else {
//IMAGEN A COLOR
                        $logoOnac = "@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";
                    }
                    $data['infoOnac'] = '<img src="' . $logoOnac . '" style="width: 90.75px;height: 56.66px"><br><label style="font-size:6px;font-family: century-gothic">ISO/IEC 17020:2012<br>' . $this->codigoOnac . '</label>';
                } else {
                    $data['infoOnac'] = '';
                }
            } else {
                if ($this->posicionLogoOnac == "1") {
                    $data["colOnac"] = "47px";
                    $data["colCda"] = "140px";
                    $data["colMid"] = "7.6px";
                    $data["colDatCda"] = "141px";
                    $data["logoCda"] = '<img style="width: 139px;height: 60px" src="' . $data['cda']->logo . '">';

                    if ($this->habilitarLogoOnac == "1") {
                        if ($this->logoColorOnac == "0") {
//IMAGEN MONOCROMATICA
                            $logoOnac = "@/9j/4AAQSkZJRgABAQEAlgCWAAD/4QAiRXhpZgAATU0AKgAAAAgAAQESAAMAAAABAAEAAAAAAAD/7QAsUGhvdG9zaG9wIDMuMAA4QklNA+0AAAAAABAAlgAAAAEAAQCWAAAAAQAB/+FVcGh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8APD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4NCjx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDYuMC1jMDA0IDc5LjE2NDU3MCwgMjAyMC8xMS8xOC0xNTo1MTo0NiAgICAgICAgIj4NCgk8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPg0KCQk8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wR0ltZz0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL2cvaW1nLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczpzdEV2dD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlRXZlbnQjIiB4bWxuczppbGx1c3RyYXRvcj0iaHR0cDovL25zLmFkb2JlLmNvbS9pbGx1c3RyYXRvci8xLjAvIiB4bWxuczpwZGY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vcGRmLzEuMy8iPg0KCQkJPGRjOmZvcm1hdD5pbWFnZS9qcGVnPC9kYzpmb3JtYXQ+DQoJCQk8ZGM6dGl0bGU+DQoJCQkJPHJkZjpBbHQ+DQoJCQkJCTxyZGY6bGkgeG1sOmxhbmc9IngtZGVmYXVsdCI+U2ltYm9sb19BY3JlZGl0YWRvX09OQUM8L3JkZjpsaT4NCgkJCQk8L3JkZjpBbHQ+DQoJCQk8L2RjOnRpdGxlPg0KCQkJPHhtcDpNZXRhZGF0YURhdGU+MjAyMS0wOS0wM1QyMDowNDoyMS0wNTowMDwveG1wOk1ldGFkYXRhRGF0ZT4NCgkJCTx4bXA6TW9kaWZ5RGF0ZT4yMDIxLTA5LTA0VDAxOjA0OjIzWjwveG1wOk1vZGlmeURhdGU+DQoJCQk8eG1wOkNyZWF0ZURhdGU+MjAyMS0wOS0wM1QyMDowNDoyMS0wNTowMDwveG1wOkNyZWF0ZURhdGU+DQoJCQk8eG1wOkNyZWF0b3JUb29sPkFkb2JlIElsbHVzdHJhdG9yIDI1LjIgKFdpbmRvd3MpPC94bXA6Q3JlYXRvclRvb2w+DQoJCQk8eG1wOlRodW1ibmFpbHM+DQoJCQkJPHJkZjpBbHQ+DQoJCQkJCTxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPg0KCQkJCQkJPHhtcEdJbWc6d2lkdGg+MjU2PC94bXBHSW1nOndpZHRoPg0KCQkJCQkJPHhtcEdJbWc6aGVpZ2h0PjkyPC94bXBHSW1nOmhlaWdodD4NCgkJCQkJCTx4bXBHSW1nOmZvcm1hdD5KUEVHPC94bXBHSW1nOmZvcm1hdD4NCgkJCQkJCTx4bXBHSW1nOmltYWdlPi85ai80QUFRU2taSlJnQUJBZ0VBbGdDV0FBRC83UUFzVUdodmRHOXphRzl3SURNdU1BQTRRa2xOQSswQUFBQUFBQkFBbGdBQUFBRUENCkFRQ1dBQUFBQVFBQi8rSU1XRWxEUTE5UVVrOUdTVXhGQUFFQkFBQU1TRXhwYm04Q0VBQUFiVzUwY2xKSFFpQllXVm9nQjg0QUFnQUoNCkFBWUFNUUFBWVdOemNFMVRSbFFBQUFBQVNVVkRJSE5TUjBJQUFBQUFBQUFBQUFBQUFBQUFBUGJXQUFFQUFBQUEweTFJVUNBZ0FBQUENCkFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBUlkzQnlkQUFBQVZBQUFBQXoNClpHVnpZd0FBQVlRQUFBQnNkM1J3ZEFBQUFmQUFBQUFVWW10d2RBQUFBZ1FBQUFBVWNsaFpXZ0FBQWhnQUFBQVVaMWhaV2dBQUFpd0ENCkFBQVVZbGhaV2dBQUFrQUFBQUFVWkcxdVpBQUFBbFFBQUFCd1pHMWtaQUFBQXNRQUFBQ0lkblZsWkFBQUEwd0FBQUNHZG1sbGR3QUENCkE5UUFBQUFrYkhWdGFRQUFBL2dBQUFBVWJXVmhjd0FBQkF3QUFBQWtkR1ZqYUFBQUJEQUFBQUFNY2xSU1F3QUFCRHdBQUFnTVoxUlMNClF3QUFCRHdBQUFnTVlsUlNRd0FBQkR3QUFBZ01kR1Y0ZEFBQUFBQkRiM0I1Y21sbmFIUWdLR01wSURFNU9UZ2dTR1YzYkdWMGRDMVENCllXTnJZWEprSUVOdmJYQmhibmtBQUdSbGMyTUFBQUFBQUFBQUVuTlNSMElnU1VWRE5qRTVOall0TWk0eEFBQUFBQUFBQUFBQUFBQVMNCmMxSkhRaUJKUlVNMk1UazJOaTB5TGpFQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUENCkFBQUFBQUFBQUFBQUFGaFpXaUFBQUFBQUFBRHpVUUFCQUFBQUFSYk1XRmxhSUFBQUFBQUFBQUFBQUFBQUFBQUFBQUJZV1ZvZ0FBQUENCkFBQUFiNklBQURqMUFBQURrRmhaV2lBQUFBQUFBQUJpbVFBQXQ0VUFBQmphV0ZsYUlBQUFBQUFBQUNTZ0FBQVBoQUFBdHM5a1pYTmoNCkFBQUFBQUFBQUJaSlJVTWdhSFIwY0RvdkwzZDNkeTVwWldNdVkyZ0FBQUFBQUFBQUFBQUFBQlpKUlVNZ2FIUjBjRG92TDNkM2R5NXANClpXTXVZMmdBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBWkdWell3QUENCkFBQUFBQUF1U1VWRElEWXhPVFkyTFRJdU1TQkVaV1poZFd4MElGSkhRaUJqYjJ4dmRYSWdjM0JoWTJVZ0xTQnpVa2RDQUFBQUFBQUENCkFBQUFBQUF1U1VWRElEWXhPVFkyTFRJdU1TQkVaV1poZFd4MElGSkhRaUJqYjJ4dmRYSWdjM0JoWTJVZ0xTQnpVa2RDQUFBQUFBQUENCkFBQUFBQUFBQUFBQUFBQUFBQUFBQUdSbGMyTUFBQUFBQUFBQUxGSmxabVZ5Wlc1alpTQldhV1YzYVc1bklFTnZibVJwZEdsdmJpQnANCmJpQkpSVU0yTVRrMk5pMHlMakVBQUFBQUFBQUFBQUFBQUN4U1pXWmxjbVZ1WTJVZ1ZtbGxkMmx1WnlCRGIyNWthWFJwYjI0Z2FXNGcNClNVVkROakU1TmpZdE1pNHhBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQjJhV1YzQUFBQUFBQVRwUDRBRkY4dUFCRFANCkZBQUQ3Y3dBQkJNTEFBTmNuZ0FBQUFGWVdWb2dBQUFBQUFCTUNWWUFVQUFBQUZjZjUyMWxZWE1BQUFBQUFBQUFBUUFBQUFBQUFBQUENCkFBQUFBQUFBQUFBQUFBS1BBQUFBQW5OcFp5QUFBQUFBUTFKVUlHTjFjbllBQUFBQUFBQUVBQUFBQUFVQUNnQVBBQlFBR1FBZUFDTUENCktBQXRBRElBTndBN0FFQUFSUUJLQUU4QVZBQlpBRjRBWXdCb0FHMEFjZ0IzQUh3QWdRQ0dBSXNBa0FDVkFKb0Fud0NrQUtrQXJnQ3kNCkFMY0F2QURCQU1ZQXl3RFFBTlVBMndEZ0FPVUE2d0R3QVBZQSt3RUJBUWNCRFFFVEFSa0JId0VsQVNzQk1nRTRBVDRCUlFGTUFWSUINCldRRmdBV2NCYmdGMUFYd0Jnd0dMQVpJQm1nR2hBYWtCc1FHNUFjRUJ5UUhSQWRrQjRRSHBBZklCK2dJREFnd0NGQUlkQWlZQ0x3STQNCkFrRUNTd0pVQWwwQ1p3SnhBbm9DaEFLT0FwZ0NvZ0tzQXJZQ3dRTExBdFVDNEFMckF2VURBQU1MQXhZRElRTXRBemdEUXdOUEExb0QNClpnTnlBMzREaWdPV0E2SURyZ082QThjRDB3UGdBK3dEK1FRR0JCTUVJQVF0QkRzRVNBUlZCR01FY1FSK0JJd0VtZ1NvQkxZRXhBVFQNCkJPRUU4QVQrQlEwRkhBVXJCVG9GU1FWWUJXY0Zkd1dHQlpZRnBnVzFCY1VGMVFYbEJmWUdCZ1lXQmljR053WklCbGtHYWdaN0Jvd0cNCm5RYXZCc0FHMFFiakJ2VUhCd2NaQnlzSFBRZFBCMkVIZEFlR0I1a0hyQWUvQjlJSDVRZjRDQXNJSHdneUNFWUlXZ2h1Q0lJSWxnaXENCkNMNEkwZ2puQ1BzSkVBa2xDVG9KVHdsa0NYa0pqd21rQ2JvSnp3bmxDZnNLRVFvbkNqMEtWQXBxQ29FS21BcXVDc1VLM0FyekN3c0wNCklnczVDMUVMYVF1QUM1Z0xzQXZJQytFTCtRd1NEQ29NUXd4Y0RIVU1qZ3luRE1BTTJRenpEUTBOSmcxQURWb05kQTJPRGFrTnd3M2UNCkRmZ09FdzR1RGtrT1pBNS9EcHNPdGc3U0R1NFBDUThsRDBFUFhnOTZENVlQc3cvUEQrd1FDUkFtRUVNUVlSQitFSnNRdVJEWEVQVVINCkV4RXhFVThSYlJHTUVhb1J5UkhvRWdjU0poSkZFbVFTaEJLakVzTVM0eE1ERXlNVFF4TmpFNE1UcEJQRkUrVVVCaFFuRkVrVWFoU0wNCkZLMFV6aFR3RlJJVk5CVldGWGdWbXhXOUZlQVdBeFltRmtrV2JCYVBGcklXMWhiNkZ4MFhRUmRsRjRrWHJoZlNGL2NZR3hoQUdHVVkNCmloaXZHTlVZK2hrZ0dVVVpheG1SR2JjWjNSb0VHaW9hVVJwM0dwNGF4UnJzR3hRYk94dGpHNG9ic2h2YUhBSWNLaHhTSEhzY294ek0NCkhQVWRIaDFISFhBZG1SM0RIZXdlRmg1QUhtb2VsQjYrSHVrZkV4OCtIMmtmbEIrL0grb2dGU0JCSUd3Z21DREVJUEFoSENGSUlYVWgNCm9TSE9JZnNpSnlKVklvSWlyeUxkSXdvak9DTm1JNVFqd2lQd0pCOGtUU1I4SktzazJpVUpKVGdsYUNXWEpjY2w5eVluSmxjbWh5YTMNCkp1Z25HQ2RKSjNvbnF5ZmNLQTBvUHloeEtLSW8xQ2tHS1RncGF5bWRLZEFxQWlvMUttZ3FteXJQS3dJck5pdHBLNTByMFN3RkxEa3MNCmJpeWlMTmN0REMxQkxYWXRxeTNoTGhZdVRDNkNMcmN1N2k4a0wxb3ZrUy9ITC80d05UQnNNS1F3MnpFU01Vb3hnakc2TWZJeUtqSmoNCk1wc3kxRE1OTTBZemZ6TzRNL0UwS3pSbE5KNDAyRFVUTlUwMWh6WENOZjAyTnpaeU5xNDI2VGNrTjJBM25EZlhPQlE0VURpTU9NZzUNCkJUbENPWDg1dkRuNU9qWTZkRHF5T3U4N0xUdHJPNm83NkR3blBHVThwRHpqUFNJOVlUMmhQZUErSUQ1Z1BxQSs0RDhoUDJFL29qL2kNClFDTkFaRUNtUU9kQktVRnFRYXhCN2tJd1FuSkN0VUwzUXpwRGZVUEFSQU5FUjBTS1JNNUZFa1ZWUlpwRjNrWWlSbWRHcTBid1J6VkgNCmUwZkFTQVZJUzBpUlNOZEpIVWxqU2FsSjhFbzNTbjFLeEVzTVMxTkxta3ZpVENwTWNreTZUUUpOU2syVFRkeE9KVTV1VHJkUEFFOUoNClQ1TlAzVkFuVUhGUXUxRUdVVkJSbTFIbVVqRlNmRkxIVXhOVFgxT3FVL1pVUWxTUFZOdFZLRlYxVmNKV0QxWmNWcWxXOTFkRVY1SlgNCjRGZ3ZXSDFZeTFrYVdXbFp1Rm9IV2xaYXBscjFXMFZibFZ2bFhEVmNobHpXWFNkZGVGM0pYaHBlYkY2OVh3OWZZVit6WUFWZ1YyQ3ENCllQeGhUMkdpWWZWaVNXS2NZdkJqUTJPWFkrdGtRR1NVWk9sbFBXV1NaZWRtUFdhU1p1aG5QV2VUWitsb1AyaVdhT3hwUTJtYWFmRnENClNHcWZhdmRyVDJ1bmEvOXNWMnl2YlFodFlHMjViaEp1YTI3RWJ4NXZlRy9SY0N0d2huRGdjVHB4bFhId2NrdHlwbk1CYzExenVIUVUNCmRIQjB6SFVvZFlWMTRYWStkcHQyK0hkV2Q3TjRFWGh1ZU14NUtubUplZWQ2Um5xbGV3UjdZM3ZDZkNGOGdYemhmVUY5b1g0QmZtSisNCnduOGpmNFIvNVlCSGdLaUJDb0ZyZ2MyQ01JS1NndlNEVjRPNmhCMkVnSVRqaFVlRnE0WU9obktHMTRjN2g1K0lCSWhwaU02Sk00bVoNCmlmNktaSXJLaXpDTGxvdjhqR09NeW8weGpaaU4vNDVtanM2UE5vK2VrQWFRYnBEV2tUK1JxSklSa25xUzQ1Tk5rN2FVSUpTS2xQU1YNClg1WEpsalNXbjVjS2wzV1g0SmhNbUxpWkpKbVFtZnlhYUpyVm0wS2JyNXdjbkltYzk1MWtuZEtlUUo2dW54MmZpNS82b0dtZzJLRkgNCm9iYWlKcUtXb3dhamRxUG1wRmFreDZVNHBhbW1HcWFMcHYybmJxZmdxRktveEtrM3FhbXFIS3FQcXdLcmRhdnByRnlzMEsxRXJiaXUNCkxhNmhyeGF2aTdBQXNIV3c2ckZnc2RheVM3TENzeml6cnJRbHRKeTFFN1dLdGdHMmViYnd0MmkzNExoWnVORzVTcm5DdWp1NnRic3UNCnU2ZThJYnlidlJXOWo3NEt2b1MrLzc5NnYvWEFjTURzd1dmQjQ4SmZ3dHZEV01QVXhGSEV6c1ZMeGNqR1JzYkR4MEhIdjhnOXlMekoNCk9zbTV5ampLdDhzMnk3Yk1OY3kxelRYTnRjNDJ6cmJQTjgrNDBEblF1dEU4MGI3U1A5TEIwMFRUeHRSSjFNdlZUdFhSMWxYVzJOZGMNCjErRFlaTmpvMld6WjhkcDIydnZiZ053RjNJcmRFTjJXM2h6ZW90OHAzNi9nTnVDOTRVVGh6T0pUNHR2alkrUHI1SFBrL09XRTVnM20NCmx1Y2Y1Nm5vTXVpODZVYnAwT3BiNnVYcmNPdjc3SWJ0RWUyYzdpanV0TzlBNzh6d1dQRGw4WEx4Ly9LTTh4bnpwL1EwOU1MMVVQWGUNCjltMzIrL2VLK0JuNHFQazQrY2Y2Vi9ybiszZjhCL3lZL1NuOXV2NUwvdHovYmYvLy8rNEFEa0ZrYjJKbEFHVEFBQUFBQWYvYkFJUUENCkJnUUVCQVVFQmdVRkJna0dCUVlKQ3dnR0JnZ0xEQW9LQ3dvS0RCQU1EQXdNREF3UURBNFBFQThPREJNVEZCUVRFeHdiR3hzY0h4OGYNCkh4OGZIeDhmSHdFSEJ3Y05EQTBZRUJBWUdoVVJGUm9mSHg4Zkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGYNCkh4OGZIeDhmSHg4Zkh4OGZIeDhmLzhBQUVRZ0FYQUVBQXdFUkFBSVJBUU1SQWYvRUFhSUFBQUFIQVFFQkFRRUFBQUFBQUFBQUFBUUYNCkF3SUdBUUFIQ0FrS0N3RUFBZ0lEQVFFQkFRRUFBQUFBQUFBQUFRQUNBd1FGQmdjSUNRb0xFQUFDQVFNREFnUUNCZ2NEQkFJR0FuTUINCkFnTVJCQUFGSVJJeFFWRUdFMkVpY1lFVU1wR2hCeFd4UWlQQlV0SGhNeFppOENSeWd2RWxRelJUa3FLeVkzUENOVVFuazZPek5oZFUNClpIVEQwdUlJSm9NSkNoZ1poSlJGUnFTMFZ0TlZLQnJ5NC9QRTFPVDBaWFdGbGFXMXhkWGw5V1oyaHBhbXRzYlc1dlkzUjFkbmQ0ZVgNCnA3ZkgxK2YzT0VoWWFIaUltS2k0eU5qbytDazVTVmxwZVltWnFibkoyZW41S2pwS1dtcDZpcHFxdXNyYTZ2b1JBQUlDQVFJREJRVUUNCkJRWUVDQU1EYlFFQUFoRURCQ0VTTVVFRlVSTmhJZ1p4Z1pFeW9iSHdGTUhSNFNOQ0ZWSmljdkV6SkRSRGdoYVNVeVdpWTdMQ0IzUFMNCk5lSkVneGRVa3dnSkNoZ1pKalpGR2lka2RGVTM4cU96d3lncDArUHpoSlNrdE1UVTVQUmxkWVdWcGJYRjFlWDFSbFptZG9hV3ByYkcNCjF1YjJSMWRuZDRlWHA3ZkgxK2YzT0VoWWFIaUltS2k0eU5qbytEbEpXV2w1aVptcHVjblo2ZmtxT2twYWFucUttcXE2eXRycSt2L2ENCkFBd0RBUUFDRVFNUkFEOEE3NTV5L05uOHV2Smtxd2VaZGR0N0c1Y0JoYTBlYWZpZWpHR0JaSkFwN0VyaXJGLytoby95Si82bWIvcHgNCjFEL3NueFYzL1EwZjVFLzlUTi8wNDZoLzJUNHE3L29hUDhpZitwbS82Y2RRL3dDeWZGWGY5RFIva1QvMU0zL1RqcUgvQUdUNHE3L28NCmFQOEFJbi9xWnY4QXB4MUQvc254VjMvUTBmNUUvd0RVemY4QVRqcUgvWlBpcnY4QW9hUDhpZjhBcVp2K25IVVAreWZGWGY4QVEwZjUNCkUvOEFVemY5T09vZjlrK0t1LzZHai9Jbi9xWnYrbkhVUCt5ZkZYZjlEUi9rVC8xTTMvVGpxSC9aUGlyditoby95Si82bWIvcHgxRC8NCkFMSjhWZC8wTkgrUlAvVXpmOU9Pb2Y4QVpQaXJ2K2hvL3dBaWYrcG0vd0NuSFVQK3lmRlhmOURSL2tUL0FOVE4vd0JPT29mOWsrS3UNCi93Q2hvL3lKL3dDcG0vNmNkUS83SjhWZC93QkRSL2tUL3dCVE4vMDQ2aC8yVDRxaUxIL25KYjhqNzI1UzNoODBSSkk1QURUMjkzYngNCjdtbThrME1hTDE3bkZYcFVNME04TWM4RWl5d3lxSGlsUWhsWldGVlpXR3hCSFE0cXZ4VjJLdXhWMkt2bDcvbk1mOHpQTUdsWE9sK1QNCk5KdTVMR0M4dFRmNm5KQ3hTU1ZIa2FLS0lzdENFckU1WVYrTGF2VEZYeVppcnNWZGlyc1ZkaXJzVmRpcksveTQvTWZ6SDVEOHgydXINCjZSZFNKQ2tpbStzUXhFTnpDRDhjY2lmWk5WNkhxcDNHS3BKcit1NmxyK3QzdXRhbk0wOS9xRXp6M0VyRW1yT2EwRmVpcjBVZGh0aXENClg0cTdGWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXErMC84QW5ETHpIcWVvL2w5cVdsM2tyVFE2UmU4TEZuSkoNClNHYU1QNlEveVZjTXcvMXNWZlFPS3V4VjJLdXhWOFYvODVxLytUVDByL3RoMi84QTFHWGVLdm4vQUJWSDZIb1dzYTlxdHZwT2oya2wNCjlxTjAzQ0MyaFhrekg5UUE2a25ZRHJpcjZkOGdmODRXUk5ieFhubm5WWkZtWUJtMHJUZUk0ZCtNbHk0Zmw0RUlueVk0cTlRZy93Q2MNClYveU1qaFdOL0x6VHNvb1paTDIrRE43bmhPaS9jTVZZL3dDWi93RG5EajhzTlNnYzZKTmVhRGRHdnBGSkRkUUFuK2FPY21SZ1BhVVkNCnErWnZ6VS9JN3p0K1hOd0gxU0ZidlI1VzRXMnNXMVRBeFBSSkFmaWllbjdMZGV4T0t2UE1WZGlyc1ZkaXFyYTJ0MWQzTWRyYVF2Y1gNCk16QklZSWxMdTdIWUtxcUNTVDRERlhzZmwzL25FajgzOVl0VnVaNExMUmxjY2tqMUdkbGtJUGlrQ1hETDhtb2NWVXZOSC9PS0g1djYNCkRhUGRSV2x0clVVWTVTRFRKbWtrQUFydEZLa0VqL0pGSnhWNC9ORExESzhNeU5ITEd4U1NOd1ZaV1UwS3NEdUNEaXF6RldmYVIrUXYNCjV2NnZweTZqWStWN3RyU1JlY2J5K25BektlaFZKbmpkZ2E3VUcrS3NQMW5STlkwVFVaZE4xaXltMCsvZ05KYlc0Um81RjhEeFlEWTkNCmozeFZHYUI1TjgwZVliVFVydlJOT2x2NE5JaUUrb3REUW1LSnEwWXJYazMyRDlrSHBpcVg2WnB0OXFtcFdtbVdFUm52cjZhTzJ0SUENClFDOHN6aEkwQllnZkV6QWJuRlVkNXA4cGVZdkttck5wSG1DeWF3MUZVV1ZyZDJSaUVjVlUxUm1YZjU0cWwxalpYZC9lMjlqWnhOUGQNCjNVaVFXOEticzhrakJVVmZkbU5NVlJmbUh5N3JYbHpXTGpSdGJ0SHN0VHRDb3VMYVNoWmVhaDEzVXNwcXJBN0hGVXR4VjZCb3Y1Qi8NCm5CcldtcnFXbitXTHByTjFEeHZNMFZ1enFlakpITzhic0Qyb3VLc04xblJOWTBUVVpkTjFpeW0wKy9nTkpiVzRSbzVGOER4WURZOWoNCjN4VkE0cSt3UCtjSWYrVVc4eS84eDBQL0FDWnhWOUtZcTdGWFlxN0ZYeFgvQU01cS93RGswOUsvN1lkdi93QlJsM2lyd0ZFZDNWRVUNCnM3RUJWQXFTVHNBQU1WZmZmL09QdjVNV0g1ZStWNHJtOGhWL05lcHhySnFseVFDMFN0UmhheG5zcWZ0MCswMi9RTFJWNnZpcnduODINClArY3BmTHZsUFhoNWIwWlJmNmhCTUl0V3Z3bnJRV2REU1JWakR3K3ZLbmRSSW9CMkxWcU1WWjc1YjArRHpUb3RucjFsNTAxVFU3SzgNClVTMjg5dTl0YlJpaE5WOU9HQ01qaWZoWlpDeEJGRHZpcXByWGxieklkSnVyQ1c2ajgzNlBjeG1PODBiVjQ0WVo1WXlQc3czZHVrTVkNCllkVjlTSTFhbjd4UHRZcStFUHpSOGxRK1UvTlZ4WldUU3ZwVXJNOWo5WlhoY1JBR2oyOXd2N00wSjJic3c0dVBoWmNWWWZpcnNWZGkNCnI3ZC81eGMvSnJUdkxYbFcwODM2bmJyTDVrMXFFVDI3dUttMXRKUnlqU092UjVFb3pucnZ4OGFxc3gvT244Nk5GL0xMUklwNW92cjINCnRYL0pkTTAwTng1Y0tjcFpXb2VNYVZIdVRzTzVDcWgrVW41Z1cvNW1lV3pyRmpyVjVaMzhMQ0xWTk1STE9sdE1SV2ljNEpHYU51cU0NCnpHdmZjRVlxOG4vNXl3L0tvZm9odk9zVU1iWDFxeUpmMzhDZW1aNG1ZUnA5WmpXcStzdFJTVmFLd3FwQ2tJR1ZlWmY4NHArVTlKOHgNCi9tekFOVWpTZURTYlNYVW9yZVNoVjVvbmpqanFwKzF3YWJuVC9KeFZFZm1iL3dBNUYvbXBQNSsxTWFacTgraldHbVhrMXZaYWZBcXENCnFyQklZd1oxWlQ2ak54cXdlb3JzQlRGV2Evbkhkdy9tSC96am41ZS9NYlU3VkxiekpaemkybG5WZVBySVpudHBWWHhSM1FTZ2ZzbmsNCkIzeFZULzV3bW1TQzg4NXpPT1NSV3RvN0tPcEN0T1NNVlJsLytVM2w3Vi9Pdmt2ODAveXhBdWZMTjNyZW55Nnpwc0lvMWxJTHVJdTQNCmlHNktwUDd4UDJQdEQ0RDhLckEvK2N2Zi9KeVhQL01EYWY4QUVUaXFLLzV4YThxYWV1c2F0K1kydkRob1BrKzNlZEpYSHd0ZGxDUlMNCnYyakhIVTA2OGltS3B4L3prTloySG43OHYvTG41eGFMYitrOGlDdzh3UUtlUmlZTVZRdWFML2R5OG8rUkc0Wk8yS3NQL3dDY1d2S3UNCmxlWXZ6Y3NrMU9JVDIrbVc4dW9wQTRxanl3bFZpNUR1RmVRUDh4aXFaZm1wL3dBNUUvbW8zNWlhdERwZXNUYVBZYVJmVDJ0blkyNFENCkpTM2thTGxNR1Urb1g0MUllb0hTbUtzQi9NejgxUE5INWk2cmE2anIvb0xKWndMYjI4VnRHSTBVYkYycWVUa3U5VzNhZzdVeFZoMksNCnZzRC9BSndoL3dDVVc4eS84eDBQL0puRlgwcGlyc1ZkaXJzVmZGZi9BRG1yL3dDVFQwci9BTFlkdi8xR1hlS3NPLzV4cjhzVy9tSDgNCjR0Q2h1VTlTMXNHZlVaa1BRbTFRdkZYMjliaFhGWDZDNHE4Ni9QOEE4KzNQa2o4c05UMVN5azlMVkxrcFlhYklPcVRYRlFYSHVrYXUNCjYrNHhWK2VMTXpNV1lsbVkxWmp1U1QzT0t2b3ovbkRYOHdMcXc4MlhYa3E1bExhZnJFYjNOakdUOWk3dDA1dndIL0ZrS3NXLzFCaXINCjdIeFY4ei84NW0vbDliVDZGWWVlTFNQaGVXVXkyZXBjZGhKRE50RkkzK1VqcUVyMUlZRDlrWXErUThWZGlyc1ZmcWZhdzI4TnJERGINCktxMjBTS2tLcjlrSW9BVUQycGlyNFcvNXkydjcrNS9PalVJTG5sNkZsYTJrTmtDYWowbWhFemNSMi9leXZpcWRmODRXMzk5RitabXANCldVVE1iTzUwcVdTNmozSzhvWjR2VGMwN3I2aFVIL0t4VjliZWZyR3p2L0kvbUN6dkVEMnMrblhTeXFlbFBSYmV2YW5VSEZYNTMvbHgNCjU4MVR5SjV3c1BNdW5LSlpMUmlzOXN6Y1ZtaGtIR1NKalEwNUtkalEwTkQyeFY3dnJQbUQvbkViejFxSjgwNjlOZjZIckZ6U1RVTk8NCmppdUFKcFI5b3Y4QVY0cDQ2dFQ3U3VwUFU3NHF3Yjg4ZnpzMGp6WnBtbStUdkoxZzJsK1N0R0t0Qkd5aEhua2pVb2hLQXR4UlF4b0MNCmFzVHliZm9xci84QU9ObjVtK1QvQUNOL2lyL0VkMDl0K2xMV0dLejRSU1M4blQxZVFQcHEzSDdZNjRxeG44bC96bDFyOHRQTUJ1SVYNCk4zb2Q2VlhWdE5yVG1xOUpJaWZzeXBVMDdIb2ZFS3EvL09Rdm5qeTk1MS9NYVhYZEFtZWZUNUxTM2lEeVJ0RXdlTlNHQlZ3RHRYRlgNCm9Gbi9BTTVBYUIrWFA1VytYL0xuNWN5eGFocnZOcDlldWJ1Mm1XSVNTTHlrNGh2UkxrdXdSRy9rVGZyaXFOOHFmODVRMlhtelRkZjgNCnQvbXVJTGJSdFRzbWh0cnF3dHBXS1NOVldESUdtUEw0ZzZOVFlyaXJ3N3lKNTIxSHlGNTJ0Zk1Pak9sMjFoSzZVWU1zZHhBMVVkU0MNCk9TaDBPMVJWVFE5c1ZlNjZ4NWgvNXhHODlhbC9pblg1Yi9ROVl1Q0pOUjA1STdrTFBLUHRNNXQ0NTQ2dFNoWkhRbnFkOFZlUC9uSDUNCmsvTGpYZk1zVTNrUFEyMGJTN2FGYmVSajhIMWxvd0ZXWDBRV0Vmd2lsZVZXNnQ4VmNWWUZpcjdBL3dDY0lmOEFsRnZNdi9NZEQveVoNCnhWOUtZcTdGWFlxN0ZYeFgvd0E1cS84QWswOUsvd0MySGIvOVJsM2lxVy84NGYzc0Z2OEFuRkZESzFIdk5QdW9JQjR1T0V4SC9BUk4NCmlyN214VjRCL3dBNXBXVjFQK1dHbTNFWEl3Mm1yUXRjS0swQWVDWkZjL0ptQy83TEZYeFZpcjFQL25HR3p1Ym44Ny9MaGhCcEFibWENClpoMFZGdFpRYTA4U1F2MDRxL1FIRlhrZi9PVmx6QkQrUit1eHlVNTNNdGxGRFduMnhlUlNHbGY4aU5zVmZBK0t1eFYyS3Z1ci9uSFANCjgzb2ZNWGtDMXRkWW1CMURSa1d6dTdzVllDTkJTRjdqYXNkWTEvdkcrQmlEOFhLcTRxeGovbkpqOHM5TTgrcGFlWXZLTi9hWDNtYXkNCmkrcnphWmJ6UnlTM2x1R0xMNlNvV0praUxOODFQWFlWVlpYL0FNNDIva2xkZmw1b2wxcU91QlA4VGF1RUZ4RWpCMXRvRTNXQU9OaXgNClk4cENEVG9CMHFWVy93RG5LTDh6Ykh5cCtYdDNva015blhmTVVUMmR2YmcvR2x0SU9GeE93N0x3cWkvNVIyNkhGWHc1cEdsWDJyNnINClo2VllSK3JmWDg4ZHRheEQ5cVdWZ2lEN3ppcjJiekIvempwYnArWXZsWHk1b1dvdFBvMnZHVzB1ZFVkYW1POTB3T05TVUtRbisraTgNClFQV3V4SUZjVlNXSFEveUoxM1h0QjBUeTQrdnczMTNyZGxwczR2VGJ0SGNXVnhPc1VreXNnVm9aS0dvQlU5ZW5ncXpHOS9JdnlIZFMNCkpKYVdXdTZISForWnJEUWJoTlVhUDA3K0M4dXhidkpadjZjYmNsVTg2MFlVL0JWUjh5L2s1NUYwL3dBNjZKNWFYUTlac0lkUzF4Tk4NCkdxWEYvWnpSejJ5eUZIYU9PSmZValp4eFplWTI3aXVLcEhmZmx2OEFscnJlbjZwZitVbTFTeWw4czZwWldHdVdPb3ZGTXMxdmUzZjENClJKYmFXSkZLdUpPcXNPbUtwcm92NWIvaysvbWJ6NTVmMUMwMWlTNDhtMitxYW42OE4xQ3FTMm1ueUtxeEFHSW4xQ0grMTB4VktQSnYNCjVMNlA1MDBEekZybWxHNnNFbWtlMjhqYWZjTXNrMTFjMjBSdWJpT1ZsUUszN3BDaXNLRGtmYWhWUS9sN3lkK1ZWbitXZWplWnZPTVcNCnJ0Y2F4cVZ6cHp5NmRMQ29nV0duN3owcFkzNVVCNlYzeFZNZFUvSmJ5aDVHajh5NnQ1NHU3N1VOTDBqVm85RTB1MDB2MDRwcm1hYTENClM5V1NhU1FTTEdxMjhxbWcvYTJyNHFzSy9ORHlWb09nUG9tcStXN3E0dWZMdm1TeUYvcHlYeXF0M0R4Y3h5UXpjS0l4VjErMHV4L0UNCnFzR3hWOWdmODRRLzhvdDVsLzVqb2Y4QWt6aXI2VXhWMkt1eFYyS3ZpdjhBNXpWLzhtbnBYL2JEdC84QXFNdThWZUtlVnZNbXBlV2YNCk1lbmEvcHJjYjdUWjB1SWEvWmJpZDBhbjdMclZXOWppcjlIL0FDUjUxMG56ZG9rT3BXSjlPVm80M3VySjJCa2dhVkE2aHFiTXJLZVUNCmJqNFhYNGwyeFZkNTg4bjZmNXk4b2FwNWF2end0OVJoTVlsQTVHT1JTSGlsQVBVcElxdDlHS3Z6bDg1ZVRQTUhrL3pGZGFCcmxzWUwNCjYxY3FDQVRIS2xmZ2xpWWdja2NiZy9mdnRpcjZ6LzV4Ty9KMi93REsrbDNIbS9YcmMyK3I2dkVzVmhheUNra0ZtU0hMT0Q5bHBtQ24NCmoxQ2dlSkFWZlF1S3ZrLy9BSnpTL01HM25tMHZ5TFp5QjJ0WEdwYXJ4TmVNaFFwYnhtbmZnN3VRZkZUaXI1YnhWMkt1eFZPdktQbkgNCnpKNVExcUxXZkwxODlqZnhEanpTaFYwSkJNY2lOVlhRMDNWaGlyNlA4dS84NXZNdG9zZm1QeXo2bDJvK0s0MCtmZ2puYi9kVW9Zci8NCkFNakRpcWo1by81emN1NWJONGZMSGwxYmE2ZFNGdkwrYjFRaFBjUXhoYWtkcXZUMk9Ldm5Eeko1bTE3ek5yTnhyT3Uzc2wvcVYwYXkNCjNFcEZkdWlxb29xcXZRS29BSGJGVVg1STg1YWo1Tzh3UmEvcHR2YlQ2amJ4eXBhdGRvOGl4UEtoajlaRlYwL2VJR1BIbFVBOXNWWkoNCkYrZlg1bGpUWTdTNzFOdFF1TGErZzFMVHRTdlM4MTFhWEVBWmYzRHMzSGhJamxYUjFZRUhGVVRxWDU5ZVlyeVhUNVlOQjBEVFpMSFUNCjdmV3BHc2JEMFd1cnkya0VpUGNPWkdmZGg4WHBsQ1FTSzBPS3JKdnorODkzWWcvU2d0dFZlejFxTHpCcGt0OTlZbGUwdUlaUklJWUgNCjlaV0Z1YWNQVGF0Rit5UWQ4VlZiL3dEUGpVN3JXYmZYSS9LZmx1eTFtQy9UVkRxTnJaenh6eVhDU2VxM3FTRzRZc3NqSDQrNThjVlENCjJ2OEE1NCtaZFZ0VnNyWFM5STBPeGE5aTFLOXRkSnRXdDF2TG1CL1VqYTVacEpKSEFiZW5JRDZjVlM2RDgxL01VUG1MelpyNjI5bWINCnp6alpYdW5hbkdVbDlLT0xVV1ZwV2dIcWNsWmVBNDgyWWVJT0tvMncvUFg4dzlLc2RCMC9STDBhUHB1Z1JySERZMlhxUnczTENVelANCkxkcXp2NnJ5dXg1OUY4QU1WVE96L3dDY2hkZXRyVnJVK1dmTHR6RXVvWEdyV2d1Yk9lWVcxMWN0emQ3ZFh1Q3FjVzNYWTB4Vkt0SC8NCkFEczg0MlYxclV1b3hXUG1HMTh3VGk3MVRUZFl0L3JGckpjTDlpVlkxYUlveUNnWGl3RkFCMkdLcEY1Mzg5Njk1eTFXUFVOVzlHSmINCmFGTFd4c0xTTVFXdHJieC9aaWdpRmVLaXZqaXJIY1ZmWUgvT0VQOEF5aTNtWC9tT2gvNU00cStsTVZkaXJzVmRpcjRyL3dDYzFmOEENCnlhZWxmOXNPMy82akx2Rlh6L2lyMmo4ai93QTR0RzBSN2Z5LzV3YWVIUzRpeTZUNWdzbmVPOTA3MVc1UEdXaitLUzFaL2phTWhsRGINCjhXN0t2cnZTN0x6SGY2ZkRkNk41MVRVZE11RjVXOTlKWjJ0dzdvUWFNc3RzYmVFbXYvRmYwWXFyUi9sem85eHF0cHJQbUdhWHpGcTINCm5rbXd1TlFXSDA3WXRRa3d3UVJ3eEExR3pNck9QNXNWWlhpcnlEODZ2K2NpZkxQa0d5dU5PMDJXTFZQTnJLVWhzVVBPTzJZN2M3cGwNClB3OGV2cDE1SDJCNVlxK0Z0WDFiVWRZMU82MVRVN2g3cS92WkdtdWJpUTFaM2MxSk9Lb1BGVVJmMk4xWVgxellYY1podTdTVjRMaUoNCmhSa2tqWW82a0h1R0ZNVlErS3V4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMkt2c2IvbkNXeHVvdkpXdjNra2INCkxiWE9vSWtFaEZBNWloSFBqNDA1akZYMGRpcnNWZGlyc1ZmRmYvT2F2L2swOUsvN1lkdi9BTlJsM2lyd0FBazBHNVBRWXFtT29lV3YNCk1lbTJzZDNxT2xYbGxheTBFVnhjVzhzVWJFaW80dTZnR294Vk52S0huYjh3dktjY3VvK1dkU3ZkUHRFa1ZMaDRlVFd2cU9DVUVxTUcNCmhMRUlhY2hYYkZYcGtQOEF6bFgrZTFwcFVWNWNwYlRXZHp5aXQ5Um5zU2lPNlZEY0hqTWNUTXZjVStqRldOZVl2ejkvT3p6UlkzWHENCjZ6Y3c2YkNxL1d4cHNRdG80MWtiZ3ZxU3dxSEFkangrSjZIcGlyeklDU1dRQUF2STVvQUtsbVluOFNjVlREVmZMZm1MUjBpZlZ0THYNCk5PU2YrNWE3Z2xnRDAzK0V5S3Rmb3hWTGNWZm9GK1l2L09OLzVhK2U5V09zYWhGZGFkcWtsUHJOM3AwaVJOUFFVQmxXU09hTXNCKzANCkZCUGM0cXhML29TcjhyUCtycnJuL1NSWi93RFpKaXJ2K2hLdnlzLzZ1dXVmOUpGbi93QmttS3UvNkVxL0t6L3E2NjUvMGtXZi9aSmkNCnJ2OEFvU3I4clA4QXE2NjUvd0JKRm4vMlNZcTcvb1NyOHJQK3Jycm4vU1JaL3dEWkppcnYraEt2eXMvNnV1dWY5SkZuL3dCa21LdS8NCjZFcS9Lei9xNjY1LzBrV2YvWkppcnY4QW9TcjhyUDhBcTY2NS93QkpGbi8yU1lxNy9vU3I4clArcnJybi9TUlovd0RaSmlyditoS3YNCnlzLzZ1dXVmOUpGbi93QmttS3UvNkVxL0t6L3E2NjUvMGtXZi9aSmlydjhBb1NyOHJQOEFxNjY1L3dCSkZuLzJTWXE3L29TcjhyUCsNCnJycm4vU1JaL3dEWkppcnYraEt2eXMvNnV1dWY5SkZuL3dCa21LdS82RXEvS3ovcTY2NS8wa1dmL1pKaXJ2OEFvU3I4clA4QXE2NjUNCi93QkpGbi8yU1lxcVczL09GLzVVUlR4eXlYK3MzQ0l3TFFTWEZzRWNEOWxqSGJJOUQ3TURpcjJ6UWRCMGZRTkl0dEgwYTBqc2ROczANCjlPMnRvaFJWRmFuclVra21wSk5TZHp2aXFQeFYyS3V4VjJLdml2OEE1elYvOG1ucFgvYkR0LzhBcU11OFZZZi9BTTQySm9ML0FKdjYNCk11c0NGcWliOUhMYy93QnliNzBtK3I4cS93Q1Y5bi9LcFRlbUt2V3ZKazM1eHpYWG5WUHpmUzQvd1FtbTNSMVQ5Sm9xMnduRkRDYkENCjBDazh2c2VrZVBUOXJqaXJGZnlEMXJRZEYvS0g4eGRROHdhWU5YMFZKOUppMUN3TkFYaHVKekF6SVQrM0dKT2FiajRnTngxeFZmOEENCm5mb25sM1IveVE4bDIzbHZVZjByb0V1cDMxenB0NGZ0bUdjczRqazJIN3lNa28rdzNIUWRNVlpuK1Vuay9RUExuNWQ2ZjViOHlYZWsNCldseCtZRWNrK3YyMm9Ya1Z0ZnBaVFJOSHBxMnNMbms3K3FlWTJIRmlSMUdLdlBmeUs4c3llV1B6cjF6UWRTU0QvRStsV0dvUStYRnUNCmFMRytxSncrcnVvWTArT0V1eTc5RGlxQzg5VGY4NUtTL2w1cUgrTlk3My9DMzE2UDYyMStrQ3lpYm1lSENvRXdoOVNsQ3Z3ZE9PMksNCnZHTVZmcXBpcnNWZGlyc1ZkaXJzVmRpcnNWZGlyc1ZkaXJzVmRpcnNWZGlyc1ZkaXJzVmRpcnNWZGlyc1ZkaXI0ci81elYvOG1ucFgNCi9iRHQvd0RxTXU4VmVKK1dkQTFIekI1Z3NORzA2Z3ZMNlpZbzNZOFVRZFdrZHYyVWpVRjJQWUN1S3ZRZk1mbEw4eUpQTDkrM25IemcNCkliRFNyNmZUN0hUdFV2N3VjWE05cEdzam0wajR6Snc5TmxLTzNFTlZRT294Vk9JUHlDOCtXME0yaTJubWFNYVZxU1RTYWpaMnk2a3kNCnl5YWNZWEt0WnhRRjdyMHpjb1ZhTkhBYXU5Y1ZTb2ZreDVxdS9McVEyUG1TMnZMYUY1NzVkQmRyMkNSWVlyNXRNbXZWdHJpS05RVmsNCmgrTUdrZ1dsUldneFZiclA1WmVaZFp1TGE3dXZOOFd1YTljYXBKNWZoZ25hL2x1QmMyYm9KbGFlZUxnc2NFY3l5bHVmSGowM0JHS3INCi93RGxVbm4zekRyT29TNmw1aWhsODFRYWhKcGxrdC9jWE1seGYzRnJicmNSL1Y3b3JJdEhnS21FeU90ZHFZcTE1NzhpZm1kL2cwYTENCjVpODAvcHRkTGlzN3EvMFNhL3VMcTUwK0hVZmh0WkhTYXNZNTFBSVJpUlVkc1ZlVDRxL1ZURlhZcTdGWFlxN0ZYWXE3RlhZcTdGWFkNCnE3RlhZcTdGWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXE3Rlh4Yi93QTVyUlNEOHo5SmxLa1J0b2tLcTlOaXkzZHlXQVB0eUgzNHE4UTgNCnZlWmRkOHUzemFob2w0OWhmdERKYmk3aUNpVkVtWGkvcE9RV2pZcnR6UWhoMk9Lc2h1ZnprL011NnNMbXh1OWNrdWJlOFJFdWZYaWcNCmxkekhEOVhFaGtlTnBQVk1JNE5LRzVzdXpNY1ZWUDhBbGRmNW1OUDY4MnJyZFNjWlVIMXEwczdsUkhjQ01TeEtzME1pckczb0llQUgNCkdvclN0Y1ZVNVB6aS9NUjlLR2xMcWNjRmdKRElJYmF6czdmcmN0ZGxPVU1LUDZYcnVYOUt2RC9KcHRpcURzL3pNODhXVXNrOXBxalcNCjl4SmMzdDk5WWpqaFdWTG5VbzFodTVvcEFuT0ozampVQW9SeC9aNDFPS3BrbjU0Zm1tbHZMRCtuWFpwazRTWGJ3V3ozWi9kQ0RsOWINCmVKcmptWWdFTDgrUkhVNHFsK3EvbWw1KzFieXhCNVgxSFdKYmpRN2RZMGp0V1NJRXBCdkVra3FvSlpGai9aRHVRTzJLc1lpaWtsa1MNCktKUzhrakJVUlJVbGlhQUFlK0t2MVJ4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMksNCnV4VjRSL3psbi95ckQvQ2RsL2l2MS8wNXpmOEFRUDFIaDlacnQ2dkxuOEhvL1o1MTcwNDc0cStJelNwcDA3VnhWckZYWXE3RlhZcTcNCkZYWXE5ZS81eHMvNVZmOEE0LzAvL0YzMWo5Sit1djZGNThQcUgxbW85TDF2MitmUDdIN1BLbGNWZi8vWjwveG1wR0ltZzppbWFnZT4NCgkJCQkJPC9yZGY6bGk+DQoJCQkJPC9yZGY6QWx0Pg0KCQkJPC94bXA6VGh1bWJuYWlscz4NCgkJCTx4bXBNTTpJbnN0YW5jZUlEPnhtcC5paWQ6YWY4MGY4ZTAtMWNlMy1mMzQ3LThiMjUtYmZmMjVhZTRkOGU5PC94bXBNTTpJbnN0YW5jZUlEPg0KCQkJPHhtcE1NOkRvY3VtZW50SUQ+eG1wLmRpZDphZjgwZjhlMC0xY2UzLWYzNDctOGIyNS1iZmYyNWFlNGQ4ZTk8L3htcE1NOkRvY3VtZW50SUQ+DQoJCQk8eG1wTU06T3JpZ2luYWxEb2N1bWVudElEPnV1aWQ6NUQyMDg5MjQ5M0JGREIxMTkxNEE4NTkwRDMxNTA4Qzg8L3htcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD4NCgkJCTx4bXBNTTpSZW5kaXRpb25DbGFzcz5wcm9vZjpwZGY8L3htcE1NOlJlbmRpdGlvbkNsYXNzPg0KCQkJPHhtcE1NOkRlcml2ZWRGcm9tIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4NCgkJCQk8c3RSZWY6aW5zdGFuY2VJRD54bXAuaWlkOmFkMzVhODk1LWJkZjItN2Q0NC1hMjY2LWY5NTcwOTEyOTUxYjwvc3RSZWY6aW5zdGFuY2VJRD4NCgkJCQk8c3RSZWY6ZG9jdW1lbnRJRD54bXAuZGlkOmFkMzVhODk1LWJkZjItN2Q0NC1hMjY2LWY5NTcwOTEyOTUxYjwvc3RSZWY6ZG9jdW1lbnRJRD4NCgkJCQk8c3RSZWY6b3JpZ2luYWxEb2N1bWVudElEPnV1aWQ6NUQyMDg5MjQ5M0JGREIxMTkxNEE4NTkwRDMxNTA4Qzg8L3N0UmVmOm9yaWdpbmFsRG9jdW1lbnRJRD4NCgkJCQk8c3RSZWY6cmVuZGl0aW9uQ2xhc3M+cHJvb2Y6cGRmPC9zdFJlZjpyZW5kaXRpb25DbGFzcz4NCgkJCTwveG1wTU06RGVyaXZlZEZyb20+DQoJCQk8eG1wTU06SGlzdG9yeT4NCgkJCQk8cmRmOlNlcT4NCgkJCQkJPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+DQoJCQkJCQk8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+DQoJCQkJCQk8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOjJmNWQzMjgxLTM1NDgtYzU0OC1iZWE1LTYyNDUzOTdlYzgxNjwvc3RFdnQ6aW5zdGFuY2VJRD4NCgkJCQkJCTxzdEV2dDp3aGVuPjIwMjEtMDgtMjZUMTQ6MjM6NTItMDU6MDA8L3N0RXZ0OndoZW4+DQoJCQkJCQk8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBJbGx1c3RyYXRvciAyNS4yIChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4NCgkJCQkJCTxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+DQoJCQkJCTwvcmRmOmxpPg0KCQkJCQk8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4NCgkJCQkJCTxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4NCgkJCQkJCTxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6M2I3NTA2MmEtN2M3Ny0wZjQyLTkwYzMtNWM0YzlmNTJhYmRjPC9zdEV2dDppbnN0YW5jZUlEPg0KCQkJCQkJPHN0RXZ0OndoZW4+MjAyMS0wOS0wMVQxMToyMDozNS0wNTowMDwvc3RFdnQ6d2hlbj4NCgkJCQkJCTxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIElsbHVzdHJhdG9yIDI1LjIgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50Pg0KCQkJCQkJPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4NCgkJCQkJPC9yZGY6bGk+DQoJCQkJCTxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPg0KCQkJCQkJPHN0RXZ0OmFjdGlvbj5jb252ZXJ0ZWQ8L3N0RXZ0OmFjdGlvbj4NCgkJCQkJCTxzdEV2dDpwYXJhbWV0ZXJzPmZyb20gYXBwbGljYXRpb24vcG9zdHNjcmlwdCB0byBhcHBsaWNhdGlvbi92bmQuYWRvYmUuaWxsdXN0cmF0b3I8L3N0RXZ0OnBhcmFtZXRlcnM+DQoJCQkJCTwvcmRmOmxpPg0KCQkJCQk8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4NCgkJCQkJCTxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4NCgkJCQkJCTxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6MzRlNjIwYzQtZjVhMS0zMjQzLWE4NjMtNzQ0NDUxYjJlOTkwPC9zdEV2dDppbnN0YW5jZUlEPg0KCQkJCQkJPHN0RXZ0OndoZW4+MjAyMS0wOS0wM1QxOTo0MDozMC0wNTowMDwvc3RFdnQ6d2hlbj4NCgkJCQkJCTxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIElsbHVzdHJhdG9yIDI1LjIgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50Pg0KCQkJCQkJPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4NCgkJCQkJPC9yZGY6bGk+DQoJCQkJCTxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPg0KCQkJCQkJPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPg0KCQkJCQkJPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDphZjgwZjhlMC0xY2UzLWYzNDctOGIyNS1iZmYyNWFlNGQ4ZTk8L3N0RXZ0Omluc3RhbmNlSUQ+DQoJCQkJCQk8c3RFdnQ6d2hlbj4yMDIxLTA5LTAzVDIwOjA0OjIxLTA1OjAwPC9zdEV2dDp3aGVuPg0KCQkJCQkJPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgSWxsdXN0cmF0b3IgMjUuMiAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+DQoJCQkJCQk8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPg0KCQkJCQk8L3JkZjpsaT4NCgkJCQk8L3JkZjpTZXE+DQoJCQk8L3htcE1NOkhpc3Rvcnk+DQoJCQk8aWxsdXN0cmF0b3I6U3RhcnR1cFByb2ZpbGU+UHJpbnQ8L2lsbHVzdHJhdG9yOlN0YXJ0dXBQcm9maWxlPg0KCQkJPGlsbHVzdHJhdG9yOkNyZWF0b3JTdWJUb29sPkFkb2JlIElsbHVzdHJhdG9yPC9pbGx1c3RyYXRvcjpDcmVhdG9yU3ViVG9vbD4NCgkJCTxwZGY6UHJvZHVjZXI+QWRvYmUgUERGIGxpYnJhcnkgMTUuMDA8L3BkZjpQcm9kdWNlcj4NCgkJPC9yZGY6RGVzY3JpcHRpb24+DQoJPC9yZGY6UkRGPg0KPC94OnhtcG1ldGE+DQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPD94cGFja2V0IGVuZD0ndyc/Pv/iDFhJQ0NfUFJPRklMRQABAQAADEhMaW5vAhAAAG1udHJSR0IgWFlaIAfOAAIACQAGADEAAGFjc3BNU0ZUAAAAAElFQyBzUkdCAAAAAAAAAAAAAAAAAAD21gABAAAAANMtSFAgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEWNwcnQAAAFQAAAAM2Rlc2MAAAGEAAAAbHd0cHQAAAHwAAAAFGJrcHQAAAIEAAAAFHJYWVoAAAIYAAAAFGdYWVoAAAIsAAAAFGJYWVoAAAJAAAAAFGRtbmQAAAJUAAAAcGRtZGQAAALEAAAAiHZ1ZWQAAANMAAAAhnZpZXcAAAPUAAAAJGx1bWkAAAP4AAAAFG1lYXMAAAQMAAAAJHRlY2gAAAQwAAAADHJUUkMAAAQ8AAAIDGdUUkMAAAQ8AAAIDGJUUkMAAAQ8AAAIDHRleHQAAAAAQ29weXJpZ2h0IChjKSAxOTk4IEhld2xldHQtUGFja2FyZCBDb21wYW55AABkZXNjAAAAAAAAABJzUkdCIElFQzYxOTY2LTIuMQAAAAAAAAAAAAAAEnNSR0IgSUVDNjE5NjYtMi4xAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABYWVogAAAAAAAA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";
                        } else {
//IMAGEN A COLOR
                            $logoOnac = "@/9j/4AAQSkZJRgABAQEAlgCWAAD/4QAiRXhpZgAATU0AKgAAAAgAAQESAAMAAAABAAEAAAAAAAD/7QAsUGhvdG9zaG9wIDMuMAA4QklNA+0AAAAAABAAlgAAAAEAAQCWAAAAAQAB/+FZXWh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8APD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4NCjx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDYuMC1jMDA0IDc5LjE2NDU3MCwgMjAyMC8xMS8xOC0xNTo1MTo0NiAgICAgICAgIj4NCgk8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPg0KCQk8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wR0ltZz0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL2cvaW1nLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczpzdEV2dD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlRXZlbnQjIiB4bWxuczppbGx1c3RyYXRvcj0iaHR0cDovL25zLmFkb2JlLmNvbS9pbGx1c3RyYXRvci8xLjAvIiB4bWxuczpwZGY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vcGRmLzEuMy8iPg0KCQkJPGRjOmZvcm1hdD5pbWFnZS9qcGVnPC9kYzpmb3JtYXQ+DQoJCQk8ZGM6dGl0bGU+DQoJCQkJPHJkZjpBbHQ+DQoJCQkJCTxyZGY6bGkgeG1sOmxhbmc9IngtZGVmYXVsdCI+U2ltYm9sb19BY3JlZGl0YWRvX09OQUM8L3JkZjpsaT4NCgkJCQk8L3JkZjpBbHQ+DQoJCQk8L2RjOnRpdGxlPg0KCQkJPHhtcDpNZXRhZGF0YURhdGU+MjAyMS0wOS0wM1QxOTo1MjozNS0wNTowMDwveG1wOk1ldGFkYXRhRGF0ZT4NCgkJCTx4bXA6TW9kaWZ5RGF0ZT4yMDIxLTA5LTA0VDAwOjUyOjM4WjwveG1wOk1vZGlmeURhdGU+DQoJCQk8eG1wOkNyZWF0ZURhdGU+MjAyMS0wOS0wM1QxOTo1MjozNS0wNTowMDwveG1wOkNyZWF0ZURhdGU+DQoJCQk8eG1wOkNyZWF0b3JUb29sPkFkb2JlIElsbHVzdHJhdG9yIDI1LjIgKFdpbmRvd3MpPC94bXA6Q3JlYXRvclRvb2w+DQoJCQk8eG1wOlRodW1ibmFpbHM+DQoJCQkJPHJkZjpBbHQ+DQoJCQkJCTxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPg0KCQkJCQkJPHhtcEdJbWc6d2lkdGg+MjU2PC94bXBHSW1nOndpZHRoPg0KCQkJCQkJPHhtcEdJbWc6aGVpZ2h0PjkyPC94bXBHSW1nOmhlaWdodD4NCgkJCQkJCTx4bXBHSW1nOmZvcm1hdD5KUEVHPC94bXBHSW1nOmZvcm1hdD4NCgkJCQkJCTx4bXBHSW1nOmltYWdlPi85ai80QUFRU2taSlJnQUJBZ0VBbGdDV0FBRC83UUFzVUdodmRHOXphRzl3SURNdU1BQTRRa2xOQSswQUFBQUFBQkFBbGdBQUFBRUENCkFRQ1dBQUFBQVFBQi8rSU1XRWxEUTE5UVVrOUdTVXhGQUFFQkFBQU1TRXhwYm04Q0VBQUFiVzUwY2xKSFFpQllXVm9nQjg0QUFnQUoNCkFBWUFNUUFBWVdOemNFMVRSbFFBQUFBQVNVVkRJSE5TUjBJQUFBQUFBQUFBQUFBQUFBQUFBUGJXQUFFQUFBQUEweTFJVUNBZ0FBQUENCkFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBUlkzQnlkQUFBQVZBQUFBQXoNClpHVnpZd0FBQVlRQUFBQnNkM1J3ZEFBQUFmQUFBQUFVWW10d2RBQUFBZ1FBQUFBVWNsaFpXZ0FBQWhnQUFBQVVaMWhaV2dBQUFpd0ENCkFBQVVZbGhaV2dBQUFrQUFBQUFVWkcxdVpBQUFBbFFBQUFCd1pHMWtaQUFBQXNRQUFBQ0lkblZsWkFBQUEwd0FBQUNHZG1sbGR3QUENCkE5UUFBQUFrYkhWdGFRQUFBL2dBQUFBVWJXVmhjd0FBQkF3QUFBQWtkR1ZqYUFBQUJEQUFBQUFNY2xSU1F3QUFCRHdBQUFnTVoxUlMNClF3QUFCRHdBQUFnTVlsUlNRd0FBQkR3QUFBZ01kR1Y0ZEFBQUFBQkRiM0I1Y21sbmFIUWdLR01wSURFNU9UZ2dTR1YzYkdWMGRDMVENCllXTnJZWEprSUVOdmJYQmhibmtBQUdSbGMyTUFBQUFBQUFBQUVuTlNSMElnU1VWRE5qRTVOall0TWk0eEFBQUFBQUFBQUFBQUFBQVMNCmMxSkhRaUJKUlVNMk1UazJOaTB5TGpFQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUENCkFBQUFBQUFBQUFBQUFGaFpXaUFBQUFBQUFBRHpVUUFCQUFBQUFSYk1XRmxhSUFBQUFBQUFBQUFBQUFBQUFBQUFBQUJZV1ZvZ0FBQUENCkFBQUFiNklBQURqMUFBQURrRmhaV2lBQUFBQUFBQUJpbVFBQXQ0VUFBQmphV0ZsYUlBQUFBQUFBQUNTZ0FBQVBoQUFBdHM5a1pYTmoNCkFBQUFBQUFBQUJaSlJVTWdhSFIwY0RvdkwzZDNkeTVwWldNdVkyZ0FBQUFBQUFBQUFBQUFBQlpKUlVNZ2FIUjBjRG92TDNkM2R5NXANClpXTXVZMmdBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBWkdWell3QUENCkFBQUFBQUF1U1VWRElEWXhPVFkyTFRJdU1TQkVaV1poZFd4MElGSkhRaUJqYjJ4dmRYSWdjM0JoWTJVZ0xTQnpVa2RDQUFBQUFBQUENCkFBQUFBQUF1U1VWRElEWXhPVFkyTFRJdU1TQkVaV1poZFd4MElGSkhRaUJqYjJ4dmRYSWdjM0JoWTJVZ0xTQnpVa2RDQUFBQUFBQUENCkFBQUFBQUFBQUFBQUFBQUFBQUFBQUdSbGMyTUFBQUFBQUFBQUxGSmxabVZ5Wlc1alpTQldhV1YzYVc1bklFTnZibVJwZEdsdmJpQnANCmJpQkpSVU0yTVRrMk5pMHlMakVBQUFBQUFBQUFBQUFBQUN4U1pXWmxjbVZ1WTJVZ1ZtbGxkMmx1WnlCRGIyNWthWFJwYjI0Z2FXNGcNClNVVkROakU1TmpZdE1pNHhBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQjJhV1YzQUFBQUFBQVRwUDRBRkY4dUFCRFANCkZBQUQ3Y3dBQkJNTEFBTmNuZ0FBQUFGWVdWb2dBQUFBQUFCTUNWWUFVQUFBQUZjZjUyMWxZWE1BQUFBQUFBQUFBUUFBQUFBQUFBQUENCkFBQUFBQUFBQUFBQUFBS1BBQUFBQW5OcFp5QUFBQUFBUTFKVUlHTjFjbllBQUFBQUFBQUVBQUFBQUFVQUNnQVBBQlFBR1FBZUFDTUENCktBQXRBRElBTndBN0FFQUFSUUJLQUU4QVZBQlpBRjRBWXdCb0FHMEFjZ0IzQUh3QWdRQ0dBSXNBa0FDVkFKb0Fud0NrQUtrQXJnQ3kNCkFMY0F2QURCQU1ZQXl3RFFBTlVBMndEZ0FPVUE2d0R3QVBZQSt3RUJBUWNCRFFFVEFSa0JId0VsQVNzQk1nRTRBVDRCUlFGTUFWSUINCldRRmdBV2NCYmdGMUFYd0Jnd0dMQVpJQm1nR2hBYWtCc1FHNUFjRUJ5UUhSQWRrQjRRSHBBZklCK2dJREFnd0NGQUlkQWlZQ0x3STQNCkFrRUNTd0pVQWwwQ1p3SnhBbm9DaEFLT0FwZ0NvZ0tzQXJZQ3dRTExBdFVDNEFMckF2VURBQU1MQXhZRElRTXRBemdEUXdOUEExb0QNClpnTnlBMzREaWdPV0E2SURyZ082QThjRDB3UGdBK3dEK1FRR0JCTUVJQVF0QkRzRVNBUlZCR01FY1FSK0JJd0VtZ1NvQkxZRXhBVFQNCkJPRUU4QVQrQlEwRkhBVXJCVG9GU1FWWUJXY0Zkd1dHQlpZRnBnVzFCY1VGMVFYbEJmWUdCZ1lXQmljR053WklCbGtHYWdaN0Jvd0cNCm5RYXZCc0FHMFFiakJ2VUhCd2NaQnlzSFBRZFBCMkVIZEFlR0I1a0hyQWUvQjlJSDVRZjRDQXNJSHdneUNFWUlXZ2h1Q0lJSWxnaXENCkNMNEkwZ2puQ1BzSkVBa2xDVG9KVHdsa0NYa0pqd21rQ2JvSnp3bmxDZnNLRVFvbkNqMEtWQXBxQ29FS21BcXVDc1VLM0FyekN3c0wNCklnczVDMUVMYVF1QUM1Z0xzQXZJQytFTCtRd1NEQ29NUXd4Y0RIVU1qZ3luRE1BTTJRenpEUTBOSmcxQURWb05kQTJPRGFrTnd3M2UNCkRmZ09FdzR1RGtrT1pBNS9EcHNPdGc3U0R1NFBDUThsRDBFUFhnOTZENVlQc3cvUEQrd1FDUkFtRUVNUVlSQitFSnNRdVJEWEVQVVINCkV4RXhFVThSYlJHTUVhb1J5UkhvRWdjU0poSkZFbVFTaEJLakVzTVM0eE1ERXlNVFF4TmpFNE1UcEJQRkUrVVVCaFFuRkVrVWFoU0wNCkZLMFV6aFR3RlJJVk5CVldGWGdWbXhXOUZlQVdBeFltRmtrV2JCYVBGcklXMWhiNkZ4MFhRUmRsRjRrWHJoZlNGL2NZR3hoQUdHVVkNCmloaXZHTlVZK2hrZ0dVVVpheG1SR2JjWjNSb0VHaW9hVVJwM0dwNGF4UnJzR3hRYk94dGpHNG9ic2h2YUhBSWNLaHhTSEhzY294ek0NCkhQVWRIaDFISFhBZG1SM0RIZXdlRmg1QUhtb2VsQjYrSHVrZkV4OCtIMmtmbEIrL0grb2dGU0JCSUd3Z21DREVJUEFoSENGSUlYVWgNCm9TSE9JZnNpSnlKVklvSWlyeUxkSXdvak9DTm1JNVFqd2lQd0pCOGtUU1I4SktzazJpVUpKVGdsYUNXWEpjY2w5eVluSmxjbWh5YTMNCkp1Z25HQ2RKSjNvbnF5ZmNLQTBvUHloeEtLSW8xQ2tHS1RncGF5bWRLZEFxQWlvMUttZ3FteXJQS3dJck5pdHBLNTByMFN3RkxEa3MNCmJpeWlMTmN0REMxQkxYWXRxeTNoTGhZdVRDNkNMcmN1N2k4a0wxb3ZrUy9ITC80d05UQnNNS1F3MnpFU01Vb3hnakc2TWZJeUtqSmoNCk1wc3kxRE1OTTBZemZ6TzRNL0UwS3pSbE5KNDAyRFVUTlUwMWh6WENOZjAyTnpaeU5xNDI2VGNrTjJBM25EZlhPQlE0VURpTU9NZzUNCkJUbENPWDg1dkRuNU9qWTZkRHF5T3U4N0xUdHJPNm83NkR3blBHVThwRHpqUFNJOVlUMmhQZUErSUQ1Z1BxQSs0RDhoUDJFL29qL2kNClFDTkFaRUNtUU9kQktVRnFRYXhCN2tJd1FuSkN0VUwzUXpwRGZVUEFSQU5FUjBTS1JNNUZFa1ZWUlpwRjNrWWlSbWRHcTBid1J6VkgNCmUwZkFTQVZJUzBpUlNOZEpIVWxqU2FsSjhFbzNTbjFLeEVzTVMxTkxta3ZpVENwTWNreTZUUUpOU2syVFRkeE9KVTV1VHJkUEFFOUoNClQ1TlAzVkFuVUhGUXUxRUdVVkJSbTFIbVVqRlNmRkxIVXhOVFgxT3FVL1pVUWxTUFZOdFZLRlYxVmNKV0QxWmNWcWxXOTFkRVY1SlgNCjRGZ3ZXSDFZeTFrYVdXbFp1Rm9IV2xaYXBscjFXMFZibFZ2bFhEVmNobHpXWFNkZGVGM0pYaHBlYkY2OVh3OWZZVit6WUFWZ1YyQ3ENCllQeGhUMkdpWWZWaVNXS2NZdkJqUTJPWFkrdGtRR1NVWk9sbFBXV1NaZWRtUFdhU1p1aG5QV2VUWitsb1AyaVdhT3hwUTJtYWFmRnENClNHcWZhdmRyVDJ1bmEvOXNWMnl2YlFodFlHMjViaEp1YTI3RWJ4NXZlRy9SY0N0d2huRGdjVHB4bFhId2NrdHlwbk1CYzExenVIUVUNCmRIQjB6SFVvZFlWMTRYWStkcHQyK0hkV2Q3TjRFWGh1ZU14NUtubUplZWQ2Um5xbGV3UjdZM3ZDZkNGOGdYemhmVUY5b1g0QmZtSisNCnduOGpmNFIvNVlCSGdLaUJDb0ZyZ2MyQ01JS1NndlNEVjRPNmhCMkVnSVRqaFVlRnE0WU9obktHMTRjN2g1K0lCSWhwaU02Sk00bVoNCmlmNktaSXJLaXpDTGxvdjhqR09NeW8weGpaaU4vNDVtanM2UE5vK2VrQWFRYnBEV2tUK1JxSklSa25xUzQ1Tk5rN2FVSUpTS2xQU1YNClg1WEpsalNXbjVjS2wzV1g0SmhNbUxpWkpKbVFtZnlhYUpyVm0wS2JyNXdjbkltYzk1MWtuZEtlUUo2dW54MmZpNS82b0dtZzJLRkgNCm9iYWlKcUtXb3dhamRxUG1wRmFreDZVNHBhbW1HcWFMcHYybmJxZmdxRktveEtrM3FhbXFIS3FQcXdLcmRhdnByRnlzMEsxRXJiaXUNCkxhNmhyeGF2aTdBQXNIV3c2ckZnc2RheVM3TENzeml6cnJRbHRKeTFFN1dLdGdHMmViYnd0MmkzNExoWnVORzVTcm5DdWp1NnRic3UNCnU2ZThJYnlidlJXOWo3NEt2b1MrLzc5NnYvWEFjTURzd1dmQjQ4SmZ3dHZEV01QVXhGSEV6c1ZMeGNqR1JzYkR4MEhIdjhnOXlMekoNCk9zbTV5ampLdDhzMnk3Yk1OY3kxelRYTnRjNDJ6cmJQTjgrNDBEblF1dEU4MGI3U1A5TEIwMFRUeHRSSjFNdlZUdFhSMWxYVzJOZGMNCjErRFlaTmpvMld6WjhkcDIydnZiZ053RjNJcmRFTjJXM2h6ZW90OHAzNi9nTnVDOTRVVGh6T0pUNHR2alkrUHI1SFBrL09XRTVnM20NCmx1Y2Y1Nm5vTXVpODZVYnAwT3BiNnVYcmNPdjc3SWJ0RWUyYzdpanV0TzlBNzh6d1dQRGw4WEx4Ly9LTTh4bnpwL1EwOU1MMVVQWGUNCjltMzIrL2VLK0JuNHFQazQrY2Y2Vi9ybiszZjhCL3lZL1NuOXV2NUwvdHovYmYvLy8rNEFEa0ZrYjJKbEFHVEFBQUFBQWYvYkFJUUENCkJnUUVCQVVFQmdVRkJna0dCUVlKQ3dnR0JnZ0xEQW9LQ3dvS0RCQU1EQXdNREF3UURBNFBFQThPREJNVEZCUVRFeHdiR3hzY0h4OGYNCkh4OGZIeDhmSHdFSEJ3Y05EQTBZRUJBWUdoVVJGUm9mSHg4Zkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGYNCkh4OGZIeDhmSHg4Zkh4OGZIeDhmLzhBQUVRZ0FYQUVBQXdFUkFBSVJBUU1SQWYvRUFhSUFBQUFIQVFFQkFRRUFBQUFBQUFBQUFBUUYNCkF3SUdBUUFIQ0FrS0N3RUFBZ0lEQVFFQkFRRUFBQUFBQUFBQUFRQUNBd1FGQmdjSUNRb0xFQUFDQVFNREFnUUNCZ2NEQkFJR0FuTUINCkFnTVJCQUFGSVJJeFFWRUdFMkVpY1lFVU1wR2hCeFd4UWlQQlV0SGhNeFppOENSeWd2RWxRelJUa3FLeVkzUENOVVFuazZPek5oZFUNClpIVEQwdUlJSm9NSkNoZ1poSlJGUnFTMFZ0TlZLQnJ5NC9QRTFPVDBaWFdGbGFXMXhkWGw5V1oyaHBhbXRzYlc1dlkzUjFkbmQ0ZVgNCnA3ZkgxK2YzT0VoWWFIaUltS2k0eU5qbytDazVTVmxwZVltWnFibkoyZW41S2pwS1dtcDZpcHFxdXNyYTZ2b1JBQUlDQVFJREJRVUUNCkJRWUVDQU1EYlFFQUFoRURCQ0VTTVVFRlVSTmhJZ1p4Z1pFeW9iSHdGTUhSNFNOQ0ZWSmljdkV6SkRSRGdoYVNVeVdpWTdMQ0IzUFMNCk5lSkVneGRVa3dnSkNoZ1pKalpGR2lka2RGVTM4cU96d3lncDArUHpoSlNrdE1UVTVQUmxkWVdWcGJYRjFlWDFSbFptZG9hV3ByYkcNCjF1YjJSMWRuZDRlWHA3ZkgxK2YzT0VoWWFIaUltS2k0eU5qbytEbEpXV2w1aVptcHVjblo2ZmtxT2twYWFucUttcXE2eXRycSt2L2ENCkFBd0RBUUFDRVFNUkFEOEE5TGFyNWwwTFNtQ1g5NGtNaEZSSHU3MDhlS0JtL0RJbVlITnc5VDJoZ3dHc2tnRDl2eUNYL3dES3hmSnYNCi9Wdy81SXovQVBOR1I4V0xpL3k3cFA1Lyt4bCtwMy9LeGZKdi9Wdy81SXovQVBOR1BpeFgrWGRKL1A4QTlqTDlUdjhBbFl2azMvcTQNCmY4a1ovd0Rtakh4WXIvTHVrL24vQU94bCtwMy9BQ3NYeWIvMWNQOEFralAvQU0wWStMRmY1ZDBuOC84QTJNdjFPLzVXTDVOLzZ1SC8NCkFDUm4vd0NhTWZGaXY4dTZUK2YvQUxHWDZuZjhyRjhtL3dEVncvNUl6LzhBTkdQaXhYK1hkSi9QL3dCakw5VHYrVmkrVGY4QXE0ZjgNCmtaLythTWZGaXY4QUx1ay9uLzdHWDZuZjhyRjhtLzhBVncvNUl6LzgwWStMRmY1ZDBuOC8vWXkvVTcvbFl2azMvcTRmOGtaLythTWYNCkZpdjh1NlQrZi9zWmZxZC95c1h5Yi8xY1ArU00vd0R6Umo0c1YvbDNTZnovQVBZeS9VNy9BSldMNU4vNnVIL0pHZjhBNW94OFdLL3kNCjdwUDUvd0RzWmZxZC93QXJGOG0vOVhEL0FKSXovd0ROR1BpeFgrWGRKL1AvQU5qTDlUditWaStUZityaC93QWtaLzhBbWpIeFlyL0wNCnVrL24vd0N4bCtwMy9LeGZKdjhBMWNQK1NNLy9BRFJqNHNWL2wzU2Z6LzhBWXkvVTcvbFl2azMvQUt1SC9KR2YvbWpIeFlyL0FDN3ANClA1Lyt4bCtwMy9LeGZKdi9BRmNQK1NNLy9OR1BpeFgrWGRKL1AvMk12MUw0ZlA4QTVRbWtFYWFpb1k5QzZTb3YvQk9xakQ0a2U5bEgNCnRyU1NOQ2YyRWZlRS9SMWRRNkVNakFGV0JxQ0QwSU9UZG1DQ0xEZUtYWXE3RlhZcTg3L05UekJlMjBsdnBWdEswS1N4K3RjTWhJWmcNCldLcXRSMitFMThjb3pTUEo1WDJqMXM0R09LSnF4WmVhWmp2SU94VjJLdXhWMkt1eFYyS3Bsb092WCtqWDhkMWF5TUZERDFvYS9ESXYNCmRXSFRwa295SUxsNlBXVDA4eEtKOTQ3MEplM2x4ZTNjMTNjTVhtbmN1N0h4UDhQRElrMjBaY3Nza3pLWE1xR0xXN0ZYWXE3RlhZcTcNCkZYWXE3RlhZcTdGWFlxN0ZYWXE3RlhZcTdGWFlxOVovS20vdUo5RW50NVdMcGF6VWhKTmFLNjE0L0lHdVpPRTdQYit6ZWFVc0ppZjQNClRzemJMbm9uWXE3RlhZcThtL05yL2xJN2IvbURUL2s3Sm1MbTV2RCswdjhBakVmNmcrK1RDY3FlZVZyT3p1cjI1UzJ0WW1tbmtORWoNClVWSnhBdHN4WXBaSkNNUmNpOUMwWDhwbEtMTHJGeVF4M050QlRiMmFRMXI5QStuTDQ0ZTk2blNlelcxNVpmQWZyWkVuNWNlVDFVQTINCkpjanF4bW1xZnVjRExQQ2k3VWRoYVFENlB0bCt0QmFoK1ZmbDY0US9WR2xzNVAyU0c5UkI4MWY0ai93V0E0UTQyZjJjd1NIb3VCK1kNCiszOWJ6N3pINVExZlFYcmNLSmJWalNPNmozUW53YnVwK2VVVGdZdkw2L3N2THBqNnQ0OTQvR3lSNUIxenNWZGlyc1ZYUnh5U3lMSEcNCnBlUnpSVVVFa2s5Z0JpbU1TVFEzTEtySDhzdk5GMUdKSFNLMEIzQ3p1UTMzSUhwOU9XREZJdTZ3K3orcG1MSUVmZWYxV3QxSDh0Zk4NCkZuR1pGaWp1MVVWYjZ1eFpxZjZyQkdQMERFNHBCR2ZzRFU0eGRDWDlYOEJpekt5TVZZRldVMFpUc1FSMk9WdWxJcll0WXFuVnI1TTgNCjBYVUFuaDA2VXhFVlV0eFFrZUlEbFNjbU1jajBkaGo3SzFNNDhRZ2ErWDNwWGQybDFhVHRiM1VUd1RKOXFPUUZXSDBISUVVNFdURksNCkV1R1FvcXRscFdvMzBWeExhUU5NbHFvZWNwUWxWTmQ2ZFQwN1lSRWxuaTAyVElDWUMrSG1vVzl2TmNYRVZ2Q3ZPYVoxamlRZDJZMFUNCmIrSk9BQnJoQXprSWptZGxiVWRNdnROdVRhMzBSaG5BREZDUVRROU9oT0Vnam0yWjlQUEZMaG1La29Rd3l6VEpERXBlV1Jna2FEcVcNClkwQUgwNEdxTVRJZ0RtVlMrc2J1d3VwTFM3ak1OeEZUbkcxS2lvcU9uc2NKRk04MkdXT1JqTVZJS0dCclR1MDhsK2FMdTNGeEJwOGgNCmlJcXBZcWhJUGNLNVVuSmpISTlIWVl1eWRUT1BGR0JyNWZlbFYzYVhWcE8xdmRSUEJNbjJvNUFWWWZRY2dSVGhaTVVvUzRaQ2lvNHMNCkhxUDVRLzhBSE8xRC9qTW4vRWN5TUhKN0wyWS91NS8xaDl6UHN2ZW5kaXJzVmRpcnliODJ2K1VqdHY4QW1EVC9BSk95Wmk1dWJ3L3QNCkwvakVmNmcrK1RDZ0NTQUJVbllBWlU4ODlxOGtlVklkRDA1WkpVQjFLNFVHNGtQVlFkeEdQWWQvRS9SbVhqaFFmUXV5T3pScDhkbisNCjhsei9BRk1reXgyN0R2TXY1aTJPbVh2MUMwSHJUbzNHNW1weVNMZjRnRnFuTmg0Y2hsVThvR3pvTmYyN0REUGdqdWVwNkQ3clB4VG0NCndnVFViU0s5aTFhNXVJcFJ5UjBNY2EvTGlpTDA2RU5raHYxZGhoZ01zUk1aSlNCOXcrNGZldXU5T3Y4QTZ0SkMwZzFTMWtYakxhWFMNCm9yc3Znc2tZUmErSEpmOEFaRHJoSVB2VGx3VDRTQ2ZFaWVjWlZmd0lyN1I4UThhOHhhU3VtYWs4VVJZMnpFbUgxQlIxRmQwY2RtWHYNCjQ3SG9SbUpLTkY0RFhhWVljaEErbnBmUDNIekg3ZXFWNUZ3M1lxN0ZYcnY1ZGVWWU5QMDJMVkxoQTEvZG9IUW5mMDRtM1VEM1libjcNCnN5c1VLRnZkZGhkbXh4WXhra1BYTDdBbXZtenpaYWVYN1JYZGZXdTVxaTN0d2FWcDFaajJVWktjK0Z6ZTB1MG82V0ZuZVI1Qlo1WjENCnRQTUZoOWFodTVZcGxJVzR0d0l2M2JlMVVZbFQySk9NSmNRWTluNnNhcUhFSkVIcU50dnM1TWEvTXJ5My9vcDFaVVV6UmtDYVpCeDUNCnFUeEhxS051US9tSFhwdHRXdkxEcTZqdC9RZW54ZW81bnY4QWY1K2Y3R1BmbHZwbHRmOEFtWlByQ2gwdG9tdUZSdWhaU3FydDdGcS8NClJsZUlXWFZkZzZlT1RVRGkvaEYvajVxbm1IejM1a2ZXcmo2dmRQYXcyOHJKREFnQUFDTngrTUVmRVRUZXVHV1EyeTEzYkdvT1k4TWoNCkVST3c5M2VtM21xVk5jOGlXT3UzRVlqdjRuOU5uQXB5SEl4c0I3RWpsN1pLZThiYzd0R1ExT2lobmtLbURYMjErMWIrVWpoSmRXY2kNCm9XT0lrZkl2amc2bzltVFJ5SHlINlZXYnkxWTNXcmFUNWo4dmZ2TlBsdklHdXJkUnZDM3FyVThld0g3UTdmTG84QUpCRE9mWjhKNWMNCmVvMCs4RE9OanUzSDRQZDdrbS9NL3dENVNxVC9BSXd4ZnFPUnpmVTRIdEQvQUl5ZjZvVlB5NjAyQVhWenJ0NktXV2x4bHd4NkdXbGYNCnA0citOTWNRNm5veTdDMDhlS1dlZjBZeDl2NC9RaXZQTVVPdGFKWWVhYlJPSllDRzlRYjhUV2dyMCt5MVZyN2pEazNIRTM5c1JHb3cNCncxTUI1Uy9IdjIrU1YvbHpwdHRmZVo0aGNLSFMzUnB3aDZGa29GcjhpMWNqaUZsd3V3c0VjbXBIRnlpTFJIbVR6MTVqT3VYSzIxMDENCnJEYXpQSEZDbEtVallyVnFqNHEwNzRaNURiYnIrMk5SNDBoR1hDSWtnRDNkL2Vrdm1EekhxT3UzTWM5N3dEUklFUlkxNGdEdWU1M08NCi9YSVNrVHpkZHJkZmsxTWhLZGJEb2xXUmNONmorVVAvQUJ6dFEvNHpKL3hITWpCeWV5OW1QN3VmOVlmY3o3TDNwM1lxN0ZYWXE4bS8NCk5yL2xJN2IvQUpnMC93Q1RzbVl1Ym04UDdTLzR4SCtvUHZrbFhrSFQwdnZOTm1rZzVSd2t6c1ArTVlxdi9EMHlPTVhKd3V4Y0F5YW0NCklQSWIvTDlyMjdNeDlGU0x6dHJVbWtlWHJpNWhQRzRrcERBM2c3OS9vVUU1REpLZzYzdGJWbkJnTWg5UjJIeGVIRWttcDNKNm5NTjgNCjRaMStWV3R5UTZuSnBNalZndWxNa0tudEtncWFmNnlBMStXWFlaYjA5TDdPYXN4eUhFZVV0eDd4K3o3bnFtWkwyanovQVBOZlJJM3MNCjRkWGlXa3NUQ0s0cDNSdnNzZmNFVStuS00wZXJ5L3RKcEFZREtPWTJMekRNZDQ1Mkt1eFY5R3hxaVJxa1lBalVBSUIwb0J0bWUrc1INCkFBb2NuanY1bXpUU2ViSjBrcndoamlTR3Y4cFFNYWY3SmptSmwrcDRIMmdtVHFpRDBBcjVYOTZML0tlYVpmTUU4S2srbEpiTTBpOXENCnE2OFQ5SEtuMDVMRHpiL1pxWkdjZ2NqSDlJZW02MURGTm85N0ZLS3h2QklHQjhPQnpJbHlldzFjUkxGSUhsd243bmhtZzZ6Y2FOcWsNCk9vUURrMFJJZU1tZ2RHRkdVL01aaFJsUnQ4NDBlcmxwOG95UjZmYXpLNnZmeXgxaWY5STNyeldkMUo4VThDcTRETjNyd1YxMzhRUmwNCnhNRHU3L0ptN096eThTZkZHUjVqZjlBS1QrY1BOdHJxZHZCcFdsUW0zMG0xb1VVaWhjcUtEYmVnRmY0bklUbmV3NU9CMnAybkhORVkNCnNRNGNVZnRYK1FmTUdsNlAra3ZyOGhqK3NSb3NWRlpxa2NxL1pCOGNPT1FGMnk3RjF1TEJ4OFpyaUczMnBmNVQ4MTNmbCs5OVJBWmINCk9VZ1hOdjRnZnRMNE1NakNmQ1hGN043U2xwWjJONEhtUHgxWCtlTllzZFcxNXJ5eWN2QTBTS0N5bFRWUnVLSEhKSUU3SjdYMVVNK2YNCmpoeW9KM0Y1MnN0Qzh1V05ob1RMUGVWTDNza3NiQmVUQ3JVcnhydWFBK0F5ZmlDSW9PeGoydERUYWVNTUc4LzRyQi9aL1lGWFRmekUNCmkxSzN2YkR6SndqdGJpRXBISkRHeElZN0dvcTIrOVFmYkNNdDdGc3dkdUROR1VOUlFqSWRCL2F4RFJ0WG4wWFY0NzYxSWtNTEVVTlENCnNpSFlqZmNWSDNaVkdWRzNRNlhVeTArVVRqdlgyaG1OMWZmbGpyRS82UnZXbXM3cC9pbmdDeUFPM2NuMDFkZC9FRVphVEE3bDMrVE4NCjJkbmw0aytLTWp6RysveUIvUXhmelZmNkRlWDZ0bzFtYlMzalFJeE8zcUZkZzNEZmp0OS9mS3BrRTdPbTdSellNazd3eDRZZ2ZQNEoNCkxrWFh2VWZ5aC80NTJvZjhaay80am1SZzVQWmV6SDkzUCtzUHVaOWw3MDdzVmRpcnNWZVRmbTEveWtkdC93QXdhZjhBSjJUTVhOemUNCkg5cGY4WWovQUZCOThrUCtWMHlSK2FWVmpReXdTSW51UlJ2MUtjY1AxTlhzN0lEVTEzeFA2M3NPWlQzckN2elloa2Z5OUJJdFNrZHkNCnBjZHFGSEFQMzdmVGxPYms4OTdTeEp3QTkwdjBGNUxtTThPeVA4dllwSlBOOWh3L1lNanNmQUNOdjlyTE1YMU8xN0RpVHFvVjUvY1gNCnRtWmI2SXhuOHlKRVR5aGVLM1dSb2xUNStxcmZxVTVYbCtsMC9iMGdOSkx6cjd3OFd6RWZQbllxN0ZYc1hrVHpRbDlvc2NkMC93Qy8NCnRBSXBaTnpzTmxNbjh2d2o3UjJQalhNckhPdzk3MlAyaU1tRUNYT08zNnIvQUY4a3YvTUR5L2I2MEk3N1RKb3B0UWlYZzl1akt6U3ANCldvNGdIN1MxUDBaSExHOXc0dmJlaWpxS25qSU14MDd4K3hNdklQbEdUUTdTU2U4cCtrTHFuTlFhaU5CdUVxTzlldVN4d3B5K3hlekQNCnBvR1Uvcmw5Zzd2MXQvbUo1Z2gwM1E1YlJXQnZMNVRGR2c2aU50bmMrMU5oNzQ1WlVGN2Mxd3hZVEFmWFBiNGRTOGZ0YmFhNnVZcmENCkZlVTB6ckhHdml6R2d6RkF0NFRIak01Q0k1azB5dSs4aW9OZDAyd3M1eTlwZThvcExrajdNdHZYMXhUYitXcTVhY2U0RHU4M1k0OGENCkVJSDB6MnZ6ajlYN0VJbG41TnZMeXl0TEEzcXpTM2NVRCt0NlpWNG5jS3pnZ0FxMzBZS2llVFFNV2t5VGpDSEhabUJ2VzRKNStTYXoNCmVUdEdrWU5GRGVXYXhhaEJadUxrcnhtU1dVSVdpUEZUVURmdmt1QWZhNXN1eThKNUNjYXlSajZ2NGdUVzJ5elVQS3VqUWF0YWFlTE8NCjZoV2U4RnY5WWVhSjFlTU1RU3FxT1NrOVJYRXdGMHh6OW5ZbzVZNCtHWTRwMWZFTndnNXRCMEM4aHVadE1OeEMybjNFVU41RE9WWU0NCmswdnBCbzJVQ2hyMk9SNFFlVGp5MFdDWWtjZkVPQ1FFZ2U0bXRrVGFhRDVYT29hell6eFhUUHBTWE54eldSQUdpZ0lBWGRmdGI5Y0kNCmpHeU81dXhhTFRlSmxoSVR2R0pIbU9VZmh6UTJsZVU3WFZySyt2TGIxSVE3R1BSNEpDR2FTU05USTZzUU4vaEZCNzRCQzJuVGRteHoNCnduT05qcEFkNUc1VTdIU3ZMY1hsKzAxRFZGdWpKZFhFa0JhQmxIQUorMXhaVFhFQVZaWTRkTnA0NEk1TXZGY3BFYlZzcjNIbFBTOUgNClhVTG5XSlpwN2ExdVZ0TGVLMzRxMGp2R0pnenMzSUtPREQ2Y0pnQnpiTW5adVBCeHl5a21NWmNJNGV1M0Z2OEFCS2ZNV2syVmtiUzUNCnNKSGtzYitMMW9CTUFKRW9lTEsxTmpRanFNaEtOY25CMTJtaGo0WlFKTUppeGZNZVJTZkl1QzlSL0tIL0FJNTJvZjhBR1pQK0k1a1kNCk9UMlhzeC9kei9yRDdtZlplOU83RlhZcTdGWGszNXRmOHBIYmY4d2FmOG5aTXhjM040ZjJsL3hpUDlRZmZKaVduWDgrbjM4RjdibWsNCjF1NGRQQTA3SDJQUTVXRFJ0MGVETkxGTVRqemlYdk9rYXRiYW5hSmNRL0N4VlRKQ1NDeUZoVVZwMUJHNFBRanBtYkdWdnBlbDFNYzANCk9JZkx1L0hROVhhenBjR3E2WGNhZk5zazZjUTNYaXczVnZvWUE0eUZpazZ2VHh6WTVZenlrOEoxWFNyM1M3NlN5dkl5azBacDdNT3oNCktlNE9ZVW9rR256WFU2YWVHWmhNYmg2WCtXbmxhYlRyZVRWTDFPRjFkS0ZoallmRWtYV3A4Q3hwbVJpaFc3MS9ZSFp4eFJPU1lxVXUNClhrUDJzNHk1Nk41citiR3RvNzIyanhNQ1l6Njl6VHN4QkNMOXhKKzdNZk5MbzhqN1M2c0V4d2pwdWYwZmozUE9zb2VVZGlyc1ZSZW0NCmFyZjZYZHJkMk14aG5YYW8zQkhjTURzUjg4SUpISnUwK3BuaGx4UU5GbmxqK2J4RVFXLzAvbEtCdkpBOUFUL3FzRFQvQUlMTGhuNzMNCnBzUHRQdDY0YitSL0gzcmRSL055Vm9pbW4ySWprSTJsbWJsVC9ZS0IrdkU1KzVHZjJtSkZZNFVlOC9xWUZmNmhlYWhkUGQza3JUWEUNCmhxenQrb0RvQVBBWlNUYnpPYlBQTEl5bWJrVlhTTlZuMHErVzl0MGplZU5XRVprQllLV0hIa0FDUGlGZHE0eE5HMmVsMU1zTStPSUgNCkVPLzcwZXZuUHpCNkFpa3VETzhjeVhFRnhMVjVJblNvK0FrMG93TkNDRGt2RUxranRYUHcwVFpCQkJQTUVkMzZWU2Z6cGZTdEF5V1YNCmxibUc0UzdZd3c4VEpMR2VRTG1wUFhyeHBpY2haejdWbklnaU1JMUlTMmp6STcvMlUwM25YV1plSDFuMDdreFhhMzF1MDNOakU2dHkNCjRJZWYyTzNFOXVtUGlGQjdXekg2cWxVK01YZXg3aHZ5OGwwM25PNGt1MHZGMHl3aXUwbkZ4NjhjVGgyY055UEk4enN4NjQrSjVKbjINCnJJeUUrREdKY1hGWUJ1L21wM3ZtL1VMbU1SUjI5clp3bVZaNW83YU1vSlpFUEpUSVN6TWQvZkV6TEhMMnBrbUtFWXhGMmVFVlo4MUINClBNdDhsL3FkNkk0dlYxV0dhQzRXamNWV2Nnc1UrS29JcHRVbkJ4bXllOXFHdm1Kem5RdklDRC9uZDI2ckQ1eDF5Mmhzb0xTYjZyQloNCktGU0dIa3FTSGx5TFNnazhpeE8vYkR4bHNoMnBtZ0l4Z2VHTU9nNjlkKyswUkY1NHZZNHpIK2o3R1JSTzl6RUpJbmIwNUpEVWxBWG8NCktIcGg4UnRqMnZNQ3VER2ZVWmJnN0U5MjZIdGZOdXFSU1hiVHJEZlIzeityY3dYU2VwR3pqb3dBSzBJOXNBbVduSDJubGlaY1ZURXoNClpFaFl0QmF2ck41cXR5czl6d1VSb0k0WVlsNFJ4b3ZSVVVkQmtaU3RvMVdxbm1seFM2YkFEWUFlU0J3T005Ui9LSC9qbmFoL3htVC8NCkFJam1SZzVQWmV6SDkzUCtzUHVaOWw3MDdzVmRpcnNWZVRmbTEveWtkdC96QnAveWRrekZ6YzNoL2FYL0FCaVA5UWZmSmhPVlBQTXMNCjhvZWFiVzBLV1dxRjF0MXFMYStpSldXRGthbGFydTBaTzVYZmZzY3NoT3RpN3ZzdnRHTUtobHZoNlNIT1AvSGZMN0hwOXZGZnpRSkwNCmE2c0o3ZHhWSm1pamNrZUlhUGd2L0M1a2krOTdHRVp5amNjbkZFOWFCKzZoOWk1ZEJ0WHVZcnErZHI2NWdxWVhuQ2NZNi95SWlxdjANCmtFKytQRDNzaG80bVFsUDF5SEs2Mjl3RkQ5S1paSnkyTCtiZlBXbjZMRThGdXkzR3BrVVdFR3F4bnhrSTZVL2w2NVhQSUI3M1RkcDkNCnNZOU9ER1BxeWQzZDcvMVBIYnE1bnVyaVM1dUhNazByRjVIYnFTY3hDYmVDeVpKVGtaU05rcVdMQmZOREpCTkpES3BXU0ppanFlb1oNClRRakZsT0ppU0R6Q3pGaTdGWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXE5VS9LT0dSZEp2WldVaU9TY0JHUGYNCml1OVB2ekp3Y250UFptSkdLUjZHWDZHZDVjOUs3RlhZcTdGWGszNXRmOHBIYmY4QU1Hbi9BQ2RrekZ6YzNoL2FYL0dJL3dCUWZmSmgNCk9WUFBJaWZUNyszaldXZTJsaWpiN0x1aktwcjRFakNRV3llQ2NSY29rRDNJblM5WDF6VFZhZlQ3aWFDSU1CSVVxWStSNmNnYXBYYnYNCmhFaU9UZHA5VG13K3JHU0I5bjZtUUwrWkhuS0syV1dRUnZGSlZVbmVHZ0pIV2hYaXBPVDhXVHRCMjlxeEd6Vkhyd3BmZmVkZk51b3cNCnljcnFSTGRBUFZFQ2lOVkRHZzVNZ3J1ZHR6a1Rra1hGemRyYXJLRGNqdytXMzNNZkFabW9LbGlmbVNUa0hWODFhNXNMNjFDbTV0cFkNCkEvMkRJakpYNWNnTUpCRFprd3poOVVTUGVLVU1EVzl0MTN5Rm9HczNKdXAxa2d1Ry92SllHQ2w2ZnpCbFlWOTZabHl4Z3ZvZXM3R3cNCmFpWEZLeEx5Ni9lbG4vS3BmTG4vQUMwM24vQnhmOVU4ajRJY1AvUTFwLzUwL21QK0pkL3lxWHk1L3dBdE41L3djWC9WUEh3UXYraHINClQvenAvTWY4UzcvbFV2bHovbHB2UCtEaS93Q3FlUGdoZjlEV24vblQrWS80bDMvS3BmTG4vTFRlZjhIRi93QlU4ZkJDL3dDaHJUL3oNCnAvTWY4UzcvQUpWTDVjLzVhYnovQUlPTC9xbmo0SVgvQUVOYWYrZFA1ai9pWGY4QUtwZkxuL0xUZWY4QUJ4ZjlVOGZCQy82R3RQOEENCnpwL01mOFM3L2xVdmx6L2xwdlArRGkvNnA0K0NGLzBOYWY4QW5UK1kvd0NKZC95cVh5NS95MDNuL0J4ZjlVOGZCQy82R3RQL0FEcC8NCk1mOEFFdS81Vkw1Yy93Q1dtOC80T0wvcW5qNElYL1ExcC81MC9tUCtKZC95cVh5NS93QXRONS93Y1gvVlBId1F2K2hyVC96cC9NZjgNClM3L2xVdmx6L2xwdlArRGkvd0NxZVBnaGY5RFduL25UK1kvNGwzL0twZkxuL0xUZWY4SEYvd0JVOGZCQy93Q2hyVC96cC9NZjhTNy8NCkFKVkw1Yy81YWJ6L0FJT0wvcW5qNElYL0FFTmFmK2RQNWovaVhmOEFLcGZMbi9MVGVmOEFCeGY5VThmQkMvNkd0UDhBenAvTWY4UzcNCi9sVXZsei9scHZQK0RpLzZwNCtDRi8wTmFmOEFuVCtZL3dDSmQveXFYeTUveTAzbi9CeGY5VThmQkMvNkd0UC9BRHAvTWY4QUV0eC8NCmxQNWFWMVpwcnR3RFVvengwUHNlTVlQNDQrQ0dVZlpyVGc4NW40ajlUTHJLeXRiSzFqdGJTSVEyOFFwSEd2UURMUUtkNWl4Unh4RVkNCmlvaFd3dGpzVmRpcnNWZVRmbTEveWtkdC93QXdhZjhBSjJUTVhOemVIOXBmOFlqL0FGQjk4a3I4Z2l5UG1pMUYwRVAyL1FFbjJmVzQNCm5oWDZlbnZrY2YxT0gyTHdmbVk4WG5WOS9SazJrdDVxZVRWaDVvRW42SUVFbjFqNndBSStZK3g2UDhPUDY2WlpIaTM0dVR0OU1kU1QNCmsvTTM0WENiNHVYbHcvcys5TGZKZDNaV25sZlhaNzYzK3RXZ2UyV2VIdVZkK0JJOXh5cVBmSTR6VVRiaWRrNVlRMDJXVXh4UnVOajMNCm1tL045cFkydmxEU1k3Q2Y2elpOY1RTVzh2Zmc5VFJ2OHBlaHd6QUVSU2UxTVVJYVhHSUhpaHhTSVB2VGJ5enBkbFlhSEJZWDh0ckcNCit0cXozc2M4cXh6aUYxS3dDTkR1VHkzOWprb0FBVWVybmRuNmVHUENJVE1RYzMxV2FsWDhOQkkvSjJublQvTnQ1WlhBVDlJMjBNNlcNCkFrMkJ1QlRnUlh4V3BHUWdLazYzc3ZCNFdxbENWZUpHTXVHLzUzVDdGSFdIOC9Ob2MvNldXWDlIZXN2cW1ZSUdEMTJwWDQrUEx3MjgNCk1FdUt0MkdxT3VPRStMZmgzMXIrMnZzWXBsYnBIMGZtZStzdXhWMkt1eFYyS3V4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMkt1eFYyS3UNCnhWMkt1eFYyS3V4VjJLdkp2emEvNVNPMi93Q1lOUDhBazdKbUxtNXZEKzB2K01SL3FENzVNUjAreW52YjJHMGdwNnN6QlZKMkE4V0oNCjdCUnVjckFzdWp3WXBaSmlNZVpUeS8welhtc1pqcW1xY1liYVo0SVlMbWFWL1VlSlF4OUphTUtjU0tIYXUyVElOYmwyT2JUNStBK0wNCmsyaVNBSlNPNUhkelJTZVN0WmpSclNQVUYrclRobW5pakZ3UXpRY0RReEtsWktlb0tGUWQ4UGhudmJoMlRtQTRSUDB5NWdjWDhOZEsNCjM1OUxRMytGTlNsc1FrTi9ITEdwZVlXUk1xTUVXWTI3eWlOMVViTW0vd0MxVEJ3R21yK1RjaGhRbUNOenc3aitMaE1xSTh0K3JWMzUNCmYxQzZlT1dUVkZ2TDJTNWF4VkhNelA2a1JITUYzV2dWRllOV3RLWW1KUFZHVFJaSmtFNU9LWmx3ZnhYWTU3a2NoZHJ2OE02MWZYYzcNClhGOHJha2s3VzhJbWVScEpualFPT0VoRERkS2NlUkdQQVNuK1Q4MlNSTXAvdk9LaFpOeUlGN0gzY3JhMW5Sdk1QNksrdDMyby9XeGINCnJGSk5adk04a2tDejdSc1EzdzcreHhsRTF1VjFXbHorRnhUbnhjTkV4NGlUSGk1TWF5dDFENlB6UGZXWFlxN0ZYWXE3RlhZcTdGWFkNCnE3RlhZcTdGWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXE3RlhZcTdGWGsvd0NiU3QvaUcyYW53bTBRQTlxaVdTdjY4eHMzTjRqMm1INysNClA5VDlKWWhZNmhlV014bnM1VERNVlpCS3RPUURDaDRucXBwM0crVkEwNkxEbm5qUEZBMFVkSjVxOHdTUXlReTNqU0pLQUpPYW94UEYNCk9ITGtWNWN1R3hhdFQzdzhaY2lYYVdjZ2d5c0gzZDFmT3V2TmQvaTN6QVg1dmRDUnFNUDNrVVVnQ3Z4NUtBNk1BcDREYnBqeGxQOEENCktlZTdNcjk0QjUrOGN0dVMxdk5PdW0yK3JDNENRMTVjWTRvay93QjJHV2xWUUhqek5lUFRIaktEMmptNGVHOXZJQWRiNkRsZlRrcFINCmVZTllpWm5pdUNqdEpOTnpWVURDUzRVTEl5c0JWU1FvNmRPMlBFV0VkYmxpYkVxTms5T2N0aWZMOFVyanpmNWpDTXYxd2t1S05JVWoNCk1wK0hoWDFDdk92SGF0YTQ4WmJSMnBxS3JpK3dYM2M2dmtvM1BtTFdyblRrMDZlNlo3T01LRmpJV3RFK3lHWURrd1h0VTRtUnFtdkoNCnJzMDhZeHlsY0IrajdVdVZXWmdxaXJNYUFEcVNjaTRnRnZvN005OVpkaXJzVmRpcnNWZGlyc1ZkaXJzVmRpcnNWZGlyc1ZkaXJzVmQNCmlyc1ZkaXJzVmRpcnNWZGlyc1ZZYitaZitIdjBiRCtrdWYxeXArcGVqVDFPM0t0ZHVIU3Y0WlZscXQzUWR2OEFnZUdQRXZqL0FJYTUNCi93Qmp5STBydDB6RmVGZGlyc1ZkaXJzVmRpcnNWWlI1Qi93Nyttb1AwcHorc2N4OVVyVDBmVS9aNTk2MTZkc3N4MWU3dU94ZnkvakQNCnhMNHI5UGRmbS8vWjwveG1wR0ltZzppbWFnZT4NCgkJCQkJPC9yZGY6bGk+DQoJCQkJPC9yZGY6QWx0Pg0KCQkJPC94bXA6VGh1bWJuYWlscz4NCgkJCTx4bXBNTTpJbnN0YW5jZUlEPnhtcC5paWQ6YjJlMWRlYjYtMDNkMy1lNTQyLWJmZWQtYmIzY2I5ZDY5Mzg0PC94bXBNTTpJbnN0YW5jZUlEPg0KCQkJPHhtcE1NOkRvY3VtZW50SUQ+eG1wLmRpZDpiMmUxZGViNi0wM2QzLWU1NDItYmZlZC1iYjNjYjlkNjkzODQ8L3htcE1NOkRvY3VtZW50SUQ+DQoJCQk8eG1wTU06T3JpZ2luYWxEb2N1bWVudElEPnV1aWQ6NUQyMDg5MjQ5M0JGREIxMTkxNEE4NTkwRDMxNTA4Qzg8L3htcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD4NCgkJCTx4bXBNTTpSZW5kaXRpb25DbGFzcz5wcm9vZjpwZGY8L3htcE1NOlJlbmRpdGlvbkNsYXNzPg0KCQkJPHhtcE1NOkRlcml2ZWRGcm9tIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4NCgkJCQk8c3RSZWY6aW5zdGFuY2VJRD54bXAuaWlkOmMxMzY1ZDA2LWJlYTctMjE0Zi1iMWE1LWUwNjEzZWUwNWJiZTwvc3RSZWY6aW5zdGFuY2VJRD4NCgkJCQk8c3RSZWY6ZG9jdW1lbnRJRD54bXAuZGlkOmMxMzY1ZDA2LWJlYTctMjE0Zi1iMWE1LWUwNjEzZWUwNWJiZTwvc3RSZWY6ZG9jdW1lbnRJRD4NCgkJCQk8c3RSZWY6b3JpZ2luYWxEb2N1bWVudElEPnV1aWQ6NUQyMDg5MjQ5M0JGREIxMTkxNEE4NTkwRDMxNTA4Qzg8L3N0UmVmOm9yaWdpbmFsRG9jdW1lbnRJRD4NCgkJCQk8c3RSZWY6cmVuZGl0aW9uQ2xhc3M+cHJvb2Y6cGRmPC9zdFJlZjpyZW5kaXRpb25DbGFzcz4NCgkJCTwveG1wTU06RGVyaXZlZEZyb20+DQoJCQk8eG1wTU06SGlzdG9yeT4NCgkJCQk8cmRmOlNlcT4NCgkJCQkJPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+DQoJCQkJCQk8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+DQoJCQkJCQk8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOjJmNWQzMjgxLTM1NDgtYzU0OC1iZWE1LTYyNDUzOTdlYzgxNjwvc3RFdnQ6aW5zdGFuY2VJRD4NCgkJCQkJCTxzdEV2dDp3aGVuPjIwMjEtMDgtMjZUMTQ6MjM6NTItMDU6MDA8L3N0RXZ0OndoZW4+DQoJCQkJCQk8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBJbGx1c3RyYXRvciAyNS4yIChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4NCgkJCQkJCTxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+DQoJCQkJCTwvcmRmOmxpPg0KCQkJCQk8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4NCgkJCQkJCTxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4NCgkJCQkJCTxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6M2I3NTA2MmEtN2M3Ny0wZjQyLTkwYzMtNWM0YzlmNTJhYmRjPC9zdEV2dDppbnN0YW5jZUlEPg0KCQkJCQkJPHN0RXZ0OndoZW4+MjAyMS0wOS0wMVQxMToyMDozNS0wNTowMDwvc3RFdnQ6d2hlbj4NCgkJCQkJCTxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIElsbHVzdHJhdG9yIDI1LjIgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50Pg0KCQkJCQkJPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4NCgkJCQkJPC9yZGY6bGk+DQoJCQkJCTxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPg0KCQkJCQkJPHN0RXZ0OmFjdGlvbj5jb252ZXJ0ZWQ8L3N0RXZ0OmFjdGlvbj4NCgkJCQkJCTxzdEV2dDpwYXJhbWV0ZXJzPmZyb20gYXBwbGljYXRpb24vcG9zdHNjcmlwdCB0byBhcHBsaWNhdGlvbi92bmQuYWRvYmUuaWxsdXN0cmF0b3I8L3N0RXZ0OnBhcmFtZXRlcnM+DQoJCQkJCTwvcmRmOmxpPg0KCQkJCQk8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4NCgkJCQkJCTxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4NCgkJCQkJCTxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6MzRlNjIwYzQtZjVhMS0zMjQzLWE4NjMtNzQ0NDUxYjJlOTkwPC9zdEV2dDppbnN0YW5jZUlEPg0KCQkJCQkJPHN0RXZ0OndoZW4+MjAyMS0wOS0wM1QxOTo0MDozMC0wNTowMDwvc3RFdnQ6d2hlbj4NCgkJCQkJCTxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIElsbHVzdHJhdG9yIDI1LjIgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50Pg0KCQkJCQkJPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4NCgkJCQkJPC9yZGY6bGk+DQoJCQkJCTxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPg0KCQkJCQkJPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPg0KCQkJCQkJPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDpiMmUxZGViNi0wM2QzLWU1NDItYmZlZC1iYjNjYjlkNjkzODQ8L3N0RXZ0Omluc3RhbmNlSUQ+DQoJCQkJCQk8c3RFdnQ6d2hlbj4yMDIxLTA5LTAzVDE5OjUyOjM1LTA1OjAwPC9zdEV2dDp3aGVuPg0KCQkJCQkJPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgSWxsdXN0cmF0b3IgMjUuMiAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+DQoJCQkJCQk8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPg0KCQkJCQk8L3JkZjpsaT4NCgkJCQk8L3JkZjpTZXE+DQoJCQk8L3htcE1NOkhpc3Rvcnk+DQoJCQk8aWxsdXN0cmF0b3I6U3RhcnR1cFByb2ZpbGU+UHJpbnQ8L2lsbHVzdHJhdG9yOlN0YXJ0dXBQcm9maWxlPg0KCQkJPGlsbHVzdHJhdG9yOkNyZWF0b3JTdWJUb29sPkFkb2JlIElsbHVzdHJhdG9yPC9pbGx1c3RyYXRvcjpDcmVhdG9yU3ViVG9vbD4NCgkJCTxwZGY6UHJvZHVjZXI+QWRvYmUgUERGIGxpYnJhcnkgMTUuMDA8L3BkZjpQcm9kdWNlcj4NCgkJPC9yZGY6RGVzY3JpcHRpb24+DQoJPC9yZGY6UkRGPg0KPC94OnhtcG1ldGE+DQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPD94cGFja2V0IGVuZD0ndyc/Pv/iDFhJQ0NfUFJPRklMRQABAQAADEhMaW5vAhAAAG1udHJSR0IgWFlaIAfOAAIACQAGADEAAGFjc3BNU0ZUAAAAAElFQyBzUkdCAAAAAAAAAAAAAAAAAAD21gABAAAAANMtSFAgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEWNwcnQAAAFQAAAAM2Rlc2MAAAGEAAAAbHd0cHQAAAHwAAAAFGJrcHQAAAIEAAAAFHJYWVoAAAIYAAAAFGdYWVoAAAIsAAAAFGJYWVoAAAJAAAAAFGRtbmQAAAJUAAAAcGRtZGQAAALEAAAAiHZ1ZWQAAANMAAAAhnZpZXcAAAPUAAAAJGx1bWkAAAP4AAAAFG1lYXMAAAQMAAAAJHRlY2gAAAQwAAAADHJUUkMAAAQ8AAAIDGdUUkMAAAQ8AAAIDGJUUkMAAAQ8AAAIDHRleHQAAAAAQ29weXJpZ2h0IChjKSAxOTk4IEhld2xldHQtUGFja2FyZCBDb21wYW55AABkZXNjAAAAAAAAABJz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";
                        }
                        $codOnac = $this->codigoOnac;
                        $infoOnac = <<<EOF
                <img src="$logoOnac" style="width: 45px"><label style="font-size:5pt"><font face="trebuc">ISO/ IEC 17020:2012<br>$codOnac</font></label>
EOF;
                        $data['infoOnac'] = $infoOnac;
                    } else {
                        $data['infoOnac'] = '';
                    }
                } else {
                    $data["colOnac"] = "100px";
                    $data["colCda"] = "90px";
                    $data["colMid"] = "4px";
                    $data["colDatCda"] = "141px";
                    $data["logoCda"] = '<br><br><img style="width: 90px;height: 36px" src="' . $data['cda']->logo . '">';
                    if ($this->habilitarLogoOnac == "1") {
                        if ($this->logoColorOnac == "0") {
//IMAGEN MONOCROMATICA
                            $logoOnac = "@/9j/4AAQSkZJRgABAQEAlgCWAAD/4QAiRXhpZgAATU0AKgAAAAgAAQESAAMAAAABAAEAAAAAAAD/7QAsUGhvdG9zaG9wIDMuMAA4QklNA+0AAAAAABAAlgAAAAEAAQCWAAAAAQAB/+FVcGh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8APD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4NCjx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDYuMC1jMDA0IDc5LjE2NDU3MCwgMjAyMC8xMS8xOC0xNTo1MTo0NiAgICAgICAgIj4NCgk8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPg0KCQk8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wR0ltZz0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL2cvaW1nLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczpzdEV2dD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlRXZlbnQjIiB4bWxuczppbGx1c3RyYXRvcj0iaHR0cDovL25zLmFkb2JlLmNvbS9pbGx1c3RyYXRvci8xLjAvIiB4bWxuczpwZGY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vcGRmLzEuMy8iPg0KCQkJPGRjOmZvcm1hdD5pbWFnZS9qcGVnPC9kYzpmb3JtYXQ+DQoJCQk8ZGM6dGl0bGU+DQoJCQkJPHJkZjpBbHQ+DQoJCQkJCTxyZGY6bGkgeG1sOmxhbmc9IngtZGVmYXVsdCI+U2ltYm9sb19BY3JlZGl0YWRvX09OQUM8L3JkZjpsaT4NCgkJCQk8L3JkZjpBbHQ+DQoJCQk8L2RjOnRpdGxlPg0KCQkJPHhtcDpNZXRhZGF0YURhdGU+MjAyMS0wOS0wM1QyMDowNDo0MS0wNTowMDwveG1wOk1ldGFkYXRhRGF0ZT4NCgkJCTx4bXA6TW9kaWZ5RGF0ZT4yMDIxLTA5LTA0VDAxOjA0OjQzWjwveG1wOk1vZGlmeURhdGU+DQoJCQk8eG1wOkNyZWF0ZURhdGU+MjAyMS0wOS0wM1QyMDowNDo0MS0wNTowMDwveG1wOkNyZWF0ZURhdGU+DQoJCQk8eG1wOkNyZWF0b3JUb29sPkFkb2JlIElsbHVzdHJhdG9yIDI1LjIgKFdpbmRvd3MpPC94bXA6Q3JlYXRvclRvb2w+DQoJCQk8eG1wOlRodW1ibmFpbHM+DQoJCQkJPHJkZjpBbHQ+DQoJCQkJCTxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPg0KCQkJCQkJPHhtcEdJbWc6d2lkdGg+MjU2PC94bXBHSW1nOndpZHRoPg0KCQkJCQkJPHhtcEdJbWc6aGVpZ2h0PjkyPC94bXBHSW1nOmhlaWdodD4NCgkJCQkJCTx4bXBHSW1nOmZvcm1hdD5KUEVHPC94bXBHSW1nOmZvcm1hdD4NCgkJCQkJCTx4bXBHSW1nOmltYWdlPi85ai80QUFRU2taSlJnQUJBZ0VBbGdDV0FBRC83UUFzVUdodmRHOXphRzl3SURNdU1BQTRRa2xOQSswQUFBQUFBQkFBbGdBQUFBRUENCkFRQ1dBQUFBQVFBQi8rSU1XRWxEUTE5UVVrOUdTVXhGQUFFQkFBQU1TRXhwYm04Q0VBQUFiVzUwY2xKSFFpQllXVm9nQjg0QUFnQUoNCkFBWUFNUUFBWVdOemNFMVRSbFFBQUFBQVNVVkRJSE5TUjBJQUFBQUFBQUFBQUFBQUFBQUFBUGJXQUFFQUFBQUEweTFJVUNBZ0FBQUENCkFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBUlkzQnlkQUFBQVZBQUFBQXoNClpHVnpZd0FBQVlRQUFBQnNkM1J3ZEFBQUFmQUFBQUFVWW10d2RBQUFBZ1FBQUFBVWNsaFpXZ0FBQWhnQUFBQVVaMWhaV2dBQUFpd0ENCkFBQVVZbGhaV2dBQUFrQUFBQUFVWkcxdVpBQUFBbFFBQUFCd1pHMWtaQUFBQXNRQUFBQ0lkblZsWkFBQUEwd0FBQUNHZG1sbGR3QUENCkE5UUFBQUFrYkhWdGFRQUFBL2dBQUFBVWJXVmhjd0FBQkF3QUFBQWtkR1ZqYUFBQUJEQUFBQUFNY2xSU1F3QUFCRHdBQUFnTVoxUlMNClF3QUFCRHdBQUFnTVlsUlNRd0FBQkR3QUFBZ01kR1Y0ZEFBQUFBQkRiM0I1Y21sbmFIUWdLR01wSURFNU9UZ2dTR1YzYkdWMGRDMVENCllXTnJZWEprSUVOdmJYQmhibmtBQUdSbGMyTUFBQUFBQUFBQUVuTlNSMElnU1VWRE5qRTVOall0TWk0eEFBQUFBQUFBQUFBQUFBQVMNCmMxSkhRaUJKUlVNMk1UazJOaTB5TGpFQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUENCkFBQUFBQUFBQUFBQUFGaFpXaUFBQUFBQUFBRHpVUUFCQUFBQUFSYk1XRmxhSUFBQUFBQUFBQUFBQUFBQUFBQUFBQUJZV1ZvZ0FBQUENCkFBQUFiNklBQURqMUFBQURrRmhaV2lBQUFBQUFBQUJpbVFBQXQ0VUFBQmphV0ZsYUlBQUFBQUFBQUNTZ0FBQVBoQUFBdHM5a1pYTmoNCkFBQUFBQUFBQUJaSlJVTWdhSFIwY0RvdkwzZDNkeTVwWldNdVkyZ0FBQUFBQUFBQUFBQUFBQlpKUlVNZ2FIUjBjRG92TDNkM2R5NXANClpXTXVZMmdBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBWkdWell3QUENCkFBQUFBQUF1U1VWRElEWXhPVFkyTFRJdU1TQkVaV1poZFd4MElGSkhRaUJqYjJ4dmRYSWdjM0JoWTJVZ0xTQnpVa2RDQUFBQUFBQUENCkFBQUFBQUF1U1VWRElEWXhPVFkyTFRJdU1TQkVaV1poZFd4MElGSkhRaUJqYjJ4dmRYSWdjM0JoWTJVZ0xTQnpVa2RDQUFBQUFBQUENCkFBQUFBQUFBQUFBQUFBQUFBQUFBQUdSbGMyTUFBQUFBQUFBQUxGSmxabVZ5Wlc1alpTQldhV1YzYVc1bklFTnZibVJwZEdsdmJpQnANCmJpQkpSVU0yTVRrMk5pMHlMakVBQUFBQUFBQUFBQUFBQUN4U1pXWmxjbVZ1WTJVZ1ZtbGxkMmx1WnlCRGIyNWthWFJwYjI0Z2FXNGcNClNVVkROakU1TmpZdE1pNHhBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQjJhV1YzQUFBQUFBQVRwUDRBRkY4dUFCRFANCkZBQUQ3Y3dBQkJNTEFBTmNuZ0FBQUFGWVdWb2dBQUFBQUFCTUNWWUFVQUFBQUZjZjUyMWxZWE1BQUFBQUFBQUFBUUFBQUFBQUFBQUENCkFBQUFBQUFBQUFBQUFBS1BBQUFBQW5OcFp5QUFBQUFBUTFKVUlHTjFjbllBQUFBQUFBQUVBQUFBQUFVQUNnQVBBQlFBR1FBZUFDTUENCktBQXRBRElBTndBN0FFQUFSUUJLQUU4QVZBQlpBRjRBWXdCb0FHMEFjZ0IzQUh3QWdRQ0dBSXNBa0FDVkFKb0Fud0NrQUtrQXJnQ3kNCkFMY0F2QURCQU1ZQXl3RFFBTlVBMndEZ0FPVUE2d0R3QVBZQSt3RUJBUWNCRFFFVEFSa0JId0VsQVNzQk1nRTRBVDRCUlFGTUFWSUINCldRRmdBV2NCYmdGMUFYd0Jnd0dMQVpJQm1nR2hBYWtCc1FHNUFjRUJ5UUhSQWRrQjRRSHBBZklCK2dJREFnd0NGQUlkQWlZQ0x3STQNCkFrRUNTd0pVQWwwQ1p3SnhBbm9DaEFLT0FwZ0NvZ0tzQXJZQ3dRTExBdFVDNEFMckF2VURBQU1MQXhZRElRTXRBemdEUXdOUEExb0QNClpnTnlBMzREaWdPV0E2SURyZ082QThjRDB3UGdBK3dEK1FRR0JCTUVJQVF0QkRzRVNBUlZCR01FY1FSK0JJd0VtZ1NvQkxZRXhBVFQNCkJPRUU4QVQrQlEwRkhBVXJCVG9GU1FWWUJXY0Zkd1dHQlpZRnBnVzFCY1VGMVFYbEJmWUdCZ1lXQmljR053WklCbGtHYWdaN0Jvd0cNCm5RYXZCc0FHMFFiakJ2VUhCd2NaQnlzSFBRZFBCMkVIZEFlR0I1a0hyQWUvQjlJSDVRZjRDQXNJSHdneUNFWUlXZ2h1Q0lJSWxnaXENCkNMNEkwZ2puQ1BzSkVBa2xDVG9KVHdsa0NYa0pqd21rQ2JvSnp3bmxDZnNLRVFvbkNqMEtWQXBxQ29FS21BcXVDc1VLM0FyekN3c0wNCklnczVDMUVMYVF1QUM1Z0xzQXZJQytFTCtRd1NEQ29NUXd4Y0RIVU1qZ3luRE1BTTJRenpEUTBOSmcxQURWb05kQTJPRGFrTnd3M2UNCkRmZ09FdzR1RGtrT1pBNS9EcHNPdGc3U0R1NFBDUThsRDBFUFhnOTZENVlQc3cvUEQrd1FDUkFtRUVNUVlSQitFSnNRdVJEWEVQVVINCkV4RXhFVThSYlJHTUVhb1J5UkhvRWdjU0poSkZFbVFTaEJLakVzTVM0eE1ERXlNVFF4TmpFNE1UcEJQRkUrVVVCaFFuRkVrVWFoU0wNCkZLMFV6aFR3RlJJVk5CVldGWGdWbXhXOUZlQVdBeFltRmtrV2JCYVBGcklXMWhiNkZ4MFhRUmRsRjRrWHJoZlNGL2NZR3hoQUdHVVkNCmloaXZHTlVZK2hrZ0dVVVpheG1SR2JjWjNSb0VHaW9hVVJwM0dwNGF4UnJzR3hRYk94dGpHNG9ic2h2YUhBSWNLaHhTSEhzY294ek0NCkhQVWRIaDFISFhBZG1SM0RIZXdlRmg1QUhtb2VsQjYrSHVrZkV4OCtIMmtmbEIrL0grb2dGU0JCSUd3Z21DREVJUEFoSENGSUlYVWgNCm9TSE9JZnNpSnlKVklvSWlyeUxkSXdvak9DTm1JNVFqd2lQd0pCOGtUU1I4SktzazJpVUpKVGdsYUNXWEpjY2w5eVluSmxjbWh5YTMNCkp1Z25HQ2RKSjNvbnF5ZmNLQTBvUHloeEtLSW8xQ2tHS1RncGF5bWRLZEFxQWlvMUttZ3FteXJQS3dJck5pdHBLNTByMFN3RkxEa3MNCmJpeWlMTmN0REMxQkxYWXRxeTNoTGhZdVRDNkNMcmN1N2k4a0wxb3ZrUy9ITC80d05UQnNNS1F3MnpFU01Vb3hnakc2TWZJeUtqSmoNCk1wc3kxRE1OTTBZemZ6TzRNL0UwS3pSbE5KNDAyRFVUTlUwMWh6WENOZjAyTnpaeU5xNDI2VGNrTjJBM25EZlhPQlE0VURpTU9NZzUNCkJUbENPWDg1dkRuNU9qWTZkRHF5T3U4N0xUdHJPNm83NkR3blBHVThwRHpqUFNJOVlUMmhQZUErSUQ1Z1BxQSs0RDhoUDJFL29qL2kNClFDTkFaRUNtUU9kQktVRnFRYXhCN2tJd1FuSkN0VUwzUXpwRGZVUEFSQU5FUjBTS1JNNUZFa1ZWUlpwRjNrWWlSbWRHcTBid1J6VkgNCmUwZkFTQVZJUzBpUlNOZEpIVWxqU2FsSjhFbzNTbjFLeEVzTVMxTkxta3ZpVENwTWNreTZUUUpOU2syVFRkeE9KVTV1VHJkUEFFOUoNClQ1TlAzVkFuVUhGUXUxRUdVVkJSbTFIbVVqRlNmRkxIVXhOVFgxT3FVL1pVUWxTUFZOdFZLRlYxVmNKV0QxWmNWcWxXOTFkRVY1SlgNCjRGZ3ZXSDFZeTFrYVdXbFp1Rm9IV2xaYXBscjFXMFZibFZ2bFhEVmNobHpXWFNkZGVGM0pYaHBlYkY2OVh3OWZZVit6WUFWZ1YyQ3ENCllQeGhUMkdpWWZWaVNXS2NZdkJqUTJPWFkrdGtRR1NVWk9sbFBXV1NaZWRtUFdhU1p1aG5QV2VUWitsb1AyaVdhT3hwUTJtYWFmRnENClNHcWZhdmRyVDJ1bmEvOXNWMnl2YlFodFlHMjViaEp1YTI3RWJ4NXZlRy9SY0N0d2huRGdjVHB4bFhId2NrdHlwbk1CYzExenVIUVUNCmRIQjB6SFVvZFlWMTRYWStkcHQyK0hkV2Q3TjRFWGh1ZU14NUtubUplZWQ2Um5xbGV3UjdZM3ZDZkNGOGdYemhmVUY5b1g0QmZtSisNCnduOGpmNFIvNVlCSGdLaUJDb0ZyZ2MyQ01JS1NndlNEVjRPNmhCMkVnSVRqaFVlRnE0WU9obktHMTRjN2g1K0lCSWhwaU02Sk00bVoNCmlmNktaSXJLaXpDTGxvdjhqR09NeW8weGpaaU4vNDVtanM2UE5vK2VrQWFRYnBEV2tUK1JxSklSa25xUzQ1Tk5rN2FVSUpTS2xQU1YNClg1WEpsalNXbjVjS2wzV1g0SmhNbUxpWkpKbVFtZnlhYUpyVm0wS2JyNXdjbkltYzk1MWtuZEtlUUo2dW54MmZpNS82b0dtZzJLRkgNCm9iYWlKcUtXb3dhamRxUG1wRmFreDZVNHBhbW1HcWFMcHYybmJxZmdxRktveEtrM3FhbXFIS3FQcXdLcmRhdnByRnlzMEsxRXJiaXUNCkxhNmhyeGF2aTdBQXNIV3c2ckZnc2RheVM3TENzeml6cnJRbHRKeTFFN1dLdGdHMmViYnd0MmkzNExoWnVORzVTcm5DdWp1NnRic3UNCnU2ZThJYnlidlJXOWo3NEt2b1MrLzc5NnYvWEFjTURzd1dmQjQ4SmZ3dHZEV01QVXhGSEV6c1ZMeGNqR1JzYkR4MEhIdjhnOXlMekoNCk9zbTV5ampLdDhzMnk3Yk1OY3kxelRYTnRjNDJ6cmJQTjgrNDBEblF1dEU4MGI3U1A5TEIwMFRUeHRSSjFNdlZUdFhSMWxYVzJOZGMNCjErRFlaTmpvMld6WjhkcDIydnZiZ053RjNJcmRFTjJXM2h6ZW90OHAzNi9nTnVDOTRVVGh6T0pUNHR2alkrUHI1SFBrL09XRTVnM20NCmx1Y2Y1Nm5vTXVpODZVYnAwT3BiNnVYcmNPdjc3SWJ0RWUyYzdpanV0TzlBNzh6d1dQRGw4WEx4Ly9LTTh4bnpwL1EwOU1MMVVQWGUNCjltMzIrL2VLK0JuNHFQazQrY2Y2Vi9ybiszZjhCL3lZL1NuOXV2NUwvdHovYmYvLy8rNEFEa0ZrYjJKbEFHVEFBQUFBQWYvYkFJUUENCkJnUUVCQVVFQmdVRkJna0dCUVlKQ3dnR0JnZ0xEQW9LQ3dvS0RCQU1EQXdNREF3UURBNFBFQThPREJNVEZCUVRFeHdiR3hzY0h4OGYNCkh4OGZIeDhmSHdFSEJ3Y05EQTBZRUJBWUdoVVJGUm9mSHg4Zkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGYNCkh4OGZIeDhmSHg4Zkh4OGZIeDhmLzhBQUVRZ0FYQUVBQXdFUkFBSVJBUU1SQWYvRUFhSUFBQUFIQVFFQkFRRUFBQUFBQUFBQUFBUUYNCkF3SUdBUUFIQ0FrS0N3RUFBZ0lEQVFFQkFRRUFBQUFBQUFBQUFRQUNBd1FGQmdjSUNRb0xFQUFDQVFNREFnUUNCZ2NEQkFJR0FuTUINCkFnTVJCQUFGSVJJeFFWRUdFMkVpY1lFVU1wR2hCeFd4UWlQQlV0SGhNeFppOENSeWd2RWxRelJUa3FLeVkzUENOVVFuazZPek5oZFUNClpIVEQwdUlJSm9NSkNoZ1poSlJGUnFTMFZ0TlZLQnJ5NC9QRTFPVDBaWFdGbGFXMXhkWGw5V1oyaHBhbXRzYlc1dlkzUjFkbmQ0ZVgNCnA3ZkgxK2YzT0VoWWFIaUltS2k0eU5qbytDazVTVmxwZVltWnFibkoyZW41S2pwS1dtcDZpcHFxdXNyYTZ2b1JBQUlDQVFJREJRVUUNCkJRWUVDQU1EYlFFQUFoRURCQ0VTTVVFRlVSTmhJZ1p4Z1pFeW9iSHdGTUhSNFNOQ0ZWSmljdkV6SkRSRGdoYVNVeVdpWTdMQ0IzUFMNCk5lSkVneGRVa3dnSkNoZ1pKalpGR2lka2RGVTM4cU96d3lncDArUHpoSlNrdE1UVTVQUmxkWVdWcGJYRjFlWDFSbFptZG9hV3ByYkcNCjF1YjJSMWRuZDRlWHA3ZkgxK2YzT0VoWWFIaUltS2k0eU5qbytEbEpXV2w1aVptcHVjblo2ZmtxT2twYWFucUttcXE2eXRycSt2L2ENCkFBd0RBUUFDRVFNUkFEOEE3NTV5L05uOHV2Smtxd2VaZGR0N0c1Y0JoYTBlYWZpZWpHR0JaSkFwN0VyaXJGLytoby95Si82bWIvcHgNCjFEL3NueFYzL1EwZjVFLzlUTi8wNDZoLzJUNHE3L29hUDhpZitwbS82Y2RRL3dDeWZGWGY5RFIva1QvMU0zL1RqcUgvQUdUNHE3L28NCmFQOEFJbi9xWnY4QXB4MUQvc254VjMvUTBmNUUvd0RVemY4QVRqcUgvWlBpcnY4QW9hUDhpZjhBcVp2K25IVVAreWZGWGY4QVEwZjUNCkUvOEFVemY5T09vZjlrK0t1LzZHai9Jbi9xWnYrbkhVUCt5ZkZYZjlEUi9rVC8xTTMvVGpxSC9aUGlyditoby95Si82bWIvcHgxRC8NCkFMSjhWZC8wTkgrUlAvVXpmOU9Pb2Y4QVpQaXJ2K2hvL3dBaWYrcG0vd0NuSFVQK3lmRlhmOURSL2tUL0FOVE4vd0JPT29mOWsrS3UNCi93Q2hvL3lKL3dDcG0vNmNkUS83SjhWZC93QkRSL2tUL3dCVE4vMDQ2aC8yVDRxaUxIL25KYjhqNzI1UzNoODBSSkk1QURUMjkzYngNCjdtbThrME1hTDE3bkZYcFVNME04TWM4RWl5d3lxSGlsUWhsWldGVlpXR3hCSFE0cXZ4VjJLdXhWMkt2bDcvbk1mOHpQTUdsWE9sK1QNCk5KdTVMR0M4dFRmNm5KQ3hTU1ZIa2FLS0lzdENFckU1WVYrTGF2VEZYeVppcnNWZGlyc1ZkaXJzVmRpcksveTQvTWZ6SDVEOHgydXINCjZSZFNKQ2tpbStzUXhFTnpDRDhjY2lmWk5WNkhxcDNHS3BKcit1NmxyK3QzdXRhbk0wOS9xRXp6M0VyRW1yT2EwRmVpcjBVZGh0aXENClg0cTdGWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXErMC84QW5ETHpIcWVvL2w5cVdsM2tyVFE2UmU4TEZuSkoNClNHYU1QNlEveVZjTXcvMXNWZlFPS3V4VjJLdXhWOFYvODVxLytUVDByL3RoMi84QTFHWGVLdm4vQUJWSDZIb1dzYTlxdHZwT2oya2wNCjlxTjAzQ0MyaFhrekg5UUE2a25ZRHJpcjZkOGdmODRXUk5ieFhubm5WWkZtWUJtMHJUZUk0ZCtNbHk0Zmw0RUlueVk0cTlRZy93Q2MNClYveU1qaFdOL0x6VHNvb1paTDIrRE43bmhPaS9jTVZZL3dDWi93RG5EajhzTlNnYzZKTmVhRGRHdnBGSkRkUUFuK2FPY21SZ1BhVVkNCnErWnZ6VS9JN3p0K1hOd0gxU0ZidlI1VzRXMnNXMVRBeFBSSkFmaWllbjdMZGV4T0t2UE1WZGlyc1ZkaXFyYTJ0MWQzTWRyYVF2Y1gNCk16QklZSWxMdTdIWUtxcUNTVDRERlhzZmwzL25FajgzOVl0VnVaNExMUmxjY2tqMUdkbGtJUGlrQ1hETDhtb2NWVXZOSC9PS0g1djYNCkRhUGRSV2x0clVVWTVTRFRKbWtrQUFydEZLa0VqL0pGSnhWNC9ORExESzhNeU5ITEd4U1NOd1ZaV1UwS3NEdUNEaXF6RldmYVIrUXYNCjV2NnZweTZqWStWN3RyU1JlY2J5K25BektlaFZKbmpkZ2E3VUcrS3NQMW5STlkwVFVaZE4xaXltMCsvZ05KYlc0Um81RjhEeFlEWTkNCmozeFZHYUI1TjgwZVliVFVydlJOT2x2NE5JaUUrb3REUW1LSnEwWXJYazMyRDlrSHBpcVg2WnB0OXFtcFdtbVdFUm52cjZhTzJ0SUENClFDOHN6aEkwQllnZkV6QWJuRlVkNXA4cGVZdkttck5wSG1DeWF3MUZVV1ZyZDJSaUVjVlUxUm1YZjU0cWwxalpYZC9lMjlqWnhOUGQNCjNVaVFXOEticzhrakJVVmZkbU5NVlJmbUh5N3JYbHpXTGpSdGJ0SHN0VHRDb3VMYVNoWmVhaDEzVXNwcXJBN0hGVXR4VjZCb3Y1Qi8NCm5CcldtcnFXbitXTHByTjFEeHZNMFZ1enFlakpITzhic0Qyb3VLc04xblJOWTBUVVpkTjFpeW0wKy9nTkpiVzRSbzVGOER4WURZOWoNCjN4VkE0cSt3UCtjSWYrVVc4eS84eDBQL0FDWnhWOUtZcTdGWFlxN0ZYeFgvQU01cS93RGswOUsvN1lkdi93QlJsM2lyd0ZFZDNWRVUNCnM3RUJWQXFTVHNBQU1WZmZmL09QdjVNV0g1ZStWNHJtOGhWL05lcHhySnFseVFDMFN0UmhheG5zcWZ0MCswMi9RTFJWNnZpcnduODINClArY3BmTHZsUFhoNWIwWlJmNmhCTUl0V3Z3bnJRV2REU1JWakR3K3ZLbmRSSW9CMkxWcU1WWjc1YjArRHpUb3RucjFsNTAxVFU3SzgNClVTMjg5dTl0YlJpaE5WOU9HQ01qaWZoWlpDeEJGRHZpcXByWGxieklkSnVyQ1c2ajgzNlBjeG1PODBiVjQ0WVo1WXlQc3czZHVrTVkNCllkVjlTSTFhbjd4UHRZcStFUHpSOGxRK1UvTlZ4WldUU3ZwVXJNOWo5WlhoY1JBR2oyOXd2N00wSjJic3c0dVBoWmNWWWZpcnNWZGkNCnI3ZC81eGMvSnJUdkxYbFcwODM2bmJyTDVrMXFFVDI3dUttMXRKUnlqU092UjVFb3pucnZ4OGFxc3gvT244Nk5GL0xMUklwNW92cjINCnRYL0pkTTAwTng1Y0tjcFpXb2VNYVZIdVRzTzVDcWgrVW41Z1cvNW1lV3pyRmpyVjVaMzhMQ0xWTk1STE9sdE1SV2ljNEpHYU51cU0NCnpHdmZjRVlxOG4vNXl3L0tvZm9odk9zVU1iWDFxeUpmMzhDZW1aNG1ZUnA5WmpXcStzdFJTVmFLd3FwQ2tJR1ZlWmY4NHArVTlKOHgNCi9tekFOVWpTZURTYlNYVW9yZVNoVjVvbmpqanFwKzF3YWJuVC9KeFZFZm1iL3dBNUYvbXBQNSsxTWFacTgraldHbVhrMXZaYWZBcXENCnFyQklZd1oxWlQ2ak54cXdlb3JzQlRGV2Evbkhkdy9tSC96am41ZS9NYlU3VkxiekpaemkybG5WZVBySVpudHBWWHhSM1FTZ2ZzbmsNCkIzeFZULzV3bW1TQzg4NXpPT1NSV3RvN0tPcEN0T1NNVlJsLytVM2w3Vi9Pdmt2ODAveXhBdWZMTjNyZW55Nnpwc0lvMWxJTHVJdTQNCmlHNktwUDd4UDJQdEQ0RDhLckEvK2N2Zi9KeVhQL01EYWY4QUVUaXFLLzV4YThxYWV1c2F0K1kydkRob1BrKzNlZEpYSHd0ZGxDUlMNCnYyakhIVTA2OGltS3B4L3prTloySG43OHYvTG41eGFMYitrOGlDdzh3UUtlUmlZTVZRdWFML2R5OG8rUkc0Wk8yS3NQL3dDY1d2S3UNCmxlWXZ6Y3NrMU9JVDIrbVc4dW9wQTRxanl3bFZpNUR1RmVRUDh4aXFaZm1wL3dBNUUvbW8zNWlhdERwZXNUYVBZYVJmVDJ0blkyNFENCkpTM2thTGxNR1Urb1g0MUllb0hTbUtzQi9NejgxUE5INWk2cmE2anIvb0xKWndMYjI4VnRHSTBVYkYycWVUa3U5VzNhZzdVeFZoMksNCnZzRC9BSndoL3dDVVc4eS84eDBQL0puRlgwcGlyc1ZkaXJzVmZGZi9BRG1yL3dDVFQwci9BTFlkdi8xR1hlS3NPLzV4cjhzVy9tSDgNCjR0Q2h1VTlTMXNHZlVaa1BRbTFRdkZYMjliaFhGWDZDNHE4Ni9QOEE4KzNQa2o4c05UMVN5azlMVkxrcFlhYklPcVRYRlFYSHVrYXUNCjYrNHhWK2VMTXpNV1lsbVkxWmp1U1QzT0t2b3ovbkRYOHdMcXc4MlhYa3E1bExhZnJFYjNOakdUOWk3dDA1dndIL0ZrS3NXLzFCaXINCjdIeFY4ei84NW0vbDliVDZGWWVlTFNQaGVXVXkyZXBjZGhKRE50RkkzK1VqcUVyMUlZRDlrWXErUThWZGlyc1ZmcWZhdzI4TnJERGINCktxMjBTS2tLcjlrSW9BVUQycGlyNFcvNXkydjcrNS9PalVJTG5sNkZsYTJrTmtDYWowbWhFemNSMi9leXZpcWRmODRXMzk5RitabXANCldVVE1iTzUwcVdTNmozSzhvWjR2VGMwN3I2aFVIL0t4VjliZWZyR3p2L0kvbUN6dkVEMnMrblhTeXFlbFBSYmV2YW5VSEZYNTMvbHgNCjU4MVR5SjV3c1BNdW5LSlpMUmlzOXN6Y1ZtaGtIR1NKalEwNUtkalEwTkQyeFY3dnJQbUQvbkViejFxSjgwNjlOZjZIckZ6U1RVTk8NCmppdUFKcFI5b3Y4QVY0cDQ2dFQ3U3VwUFU3NHF3Yjg4ZnpzMGp6WnBtbStUdkoxZzJsK1N0R0t0Qkd5aEhua2pVb2hLQXR4UlF4b0MNCmFzVHliZm9xci84QU9ObjVtK1QvQUNOL2lyL0VkMDl0K2xMV0dLejRSU1M4blQxZVFQcHEzSDdZNjRxeG44bC96bDFyOHRQTUJ1SVYNCk4zb2Q2VlhWdE5yVG1xOUpJaWZzeXBVMDdIb2ZFS3EvL09Rdm5qeTk1MS9NYVhYZEFtZWZUNUxTM2lEeVJ0RXdlTlNHQlZ3RHRYRlgNCm9Gbi9BTTVBYUIrWFA1VytYL0xuNWN5eGFocnZOcDlldWJ1Mm1XSVNTTHlrNGh2UkxrdXdSRy9rVGZyaXFOOHFmODVRMlhtelRkZjgNCnQvbXVJTGJSdFRzbWh0cnF3dHBXS1NOVldESUdtUEw0ZzZOVFlyaXJ3N3lKNTIxSHlGNTJ0Zk1Pak9sMjFoSzZVWU1zZHhBMVVkU0MNCk9TaDBPMVJWVFE5c1ZlNjZ4NWgvNXhHODlhbC9pblg1Yi9ROVl1Q0pOUjA1STdrTFBLUHRNNXQ0NTQ2dFNoWkhRbnFkOFZlUC9uSDUNCmsvTGpYZk1zVTNrUFEyMGJTN2FGYmVSajhIMWxvd0ZXWDBRV0Vmd2lsZVZXNnQ4VmNWWUZpcjdBL3dDY0lmOEFsRnZNdi9NZEQveVoNCnhWOUtZcTdGWFlxN0ZYeFgvd0E1cS84QWswOUsvd0MySGIvOVJsM2lxVy84NGYzc0Z2OEFuRkZESzFIdk5QdW9JQjR1T0V4SC9BUk4NCmlyN214VjRCL3dBNXBXVjFQK1dHbTNFWEl3Mm1yUXRjS0swQWVDWkZjL0ptQy83TEZYeFZpcjFQL25HR3p1Ym44Ny9MaGhCcEFibWENClpoMFZGdFpRYTA4U1F2MDRxL1FIRlhrZi9PVmx6QkQrUit1eHlVNTNNdGxGRFduMnhlUlNHbGY4aU5zVmZBK0t1eFYyS3Z1ci9uSFANCjgzb2ZNWGtDMXRkWW1CMURSa1d6dTdzVllDTkJTRjdqYXNkWTEvdkcrQmlEOFhLcTRxeGovbkpqOHM5TTgrcGFlWXZLTi9hWDNtYXkNCmkrcnphWmJ6UnlTM2x1R0xMNlNvV0praUxOODFQWFlWVlpYL0FNNDIva2xkZmw1b2wxcU91QlA4VGF1RUZ4RWpCMXRvRTNXQU9OaXgNClk4cENEVG9CMHFWVy93RG5LTDh6Ykh5cCtYdDNva015blhmTVVUMmR2YmcvR2x0SU9GeE93N0x3cWkvNVIyNkhGWHc1cEdsWDJyNnINClo2VllSK3JmWDg4ZHRheEQ5cVdWZ2lEN3ppcjJiekIvempwYnArWXZsWHk1b1dvdFBvMnZHVzB1ZFVkYW1POTB3T05TVUtRbisraTgNClFQV3V4SUZjVlNXSFEveUoxM1h0QjBUeTQrdnczMTNyZGxwczR2VGJ0SGNXVnhPc1VreXNnVm9aS0dvQlU5ZW5ncXpHOS9JdnlIZFMNCkpKYVdXdTZISForWnJEUWJoTlVhUDA3K0M4dXhidkpadjZjYmNsVTg2MFlVL0JWUjh5L2s1NUYwL3dBNjZKNWFYUTlac0lkUzF4Tk4NCkdxWEYvWnpSejJ5eUZIYU9PSmZValp4eFplWTI3aXVLcEhmZmx2OEFscnJlbjZwZitVbTFTeWw4czZwWldHdVdPb3ZGTXMxdmUzZjENClJKYmFXSkZLdUpPcXNPbUtwcm92NWIvaysvbWJ6NTVmMUMwMWlTNDhtMitxYW42OE4xQ3FTMm1ueUtxeEFHSW4xQ0grMTB4VktQSnYNCjVMNlA1MDBEekZybWxHNnNFbWtlMjhqYWZjTXNrMTFjMjBSdWJpT1ZsUUszN3BDaXNLRGtmYWhWUS9sN3lkK1ZWbitXZWplWnZPTVcNCnJ0Y2F4cVZ6cHp5NmRMQ29nV0duN3owcFkzNVVCNlYzeFZNZFUvSmJ5aDVHajh5NnQ1NHU3N1VOTDBqVm85RTB1MDB2MDRwcm1hYTENClM5V1NhU1FTTEdxMjhxbWcvYTJyNHFzSy9ORHlWb09nUG9tcStXN3E0dWZMdm1TeUYvcHlYeXF0M0R4Y3h5UXpjS0l4VjErMHV4L0UNCnFzR3hWOWdmODRRLzhvdDVsLzVqb2Y4QWt6aXI2VXhWMkt1eFYyS3ZpdjhBNXpWLzhtbnBYL2JEdC84QXFNdThWZUtlVnZNbXBlV2YNCk1lbmEvcHJjYjdUWjB1SWEvWmJpZDBhbjdMclZXOWppcjlIL0FDUjUxMG56ZG9rT3BXSjlPVm80M3VySjJCa2dhVkE2aHFiTXJLZVUNCmJqNFhYNGwyeFZkNTg4bjZmNXk4b2FwNWF2end0OVJoTVlsQTVHT1JTSGlsQVBVcElxdDlHS3Z6bDg1ZVRQTUhrL3pGZGFCcmxzWUwNCjYxY3FDQVRIS2xmZ2xpWWdja2NiZy9mdnRpcjZ6LzV4Ty9KMi93REsrbDNIbS9YcmMyK3I2dkVzVmhheUNra0ZtU0hMT0Q5bHBtQ24NCmoxQ2dlSkFWZlF1S3ZrLy9BSnpTL01HM25tMHZ5TFp5QjJ0WEdwYXJ4TmVNaFFwYnhtbmZnN3VRZkZUaXI1YnhWMkt1eFZPdktQbkgNCnpKNVExcUxXZkwxODlqZnhEanpTaFYwSkJNY2lOVlhRMDNWaGlyNlA4dS84NXZNdG9zZm1QeXo2bDJvK0s0MCtmZ2puYi9kVW9Zci8NCkFNakRpcWo1by81emN1NWJONGZMSGwxYmE2ZFNGdkwrYjFRaFBjUXhoYWtkcXZUMk9Ldm5Eeko1bTE3ek5yTnhyT3Uzc2wvcVYwYXkNCjNFcEZkdWlxb29xcXZRS29BSGJGVVg1STg1YWo1Tzh3UmEvcHR2YlQ2amJ4eXBhdGRvOGl4UEtoajlaRlYwL2VJR1BIbFVBOXNWWkoNCkYrZlg1bGpUWTdTNzFOdFF1TGErZzFMVHRTdlM4MTFhWEVBWmYzRHMzSGhJamxYUjFZRUhGVVRxWDU5ZVlyeVhUNVlOQjBEVFpMSFUNCjdmV3BHc2JEMFd1cnkya0VpUGNPWkdmZGg4WHBsQ1FTSzBPS3JKdnorODkzWWcvU2d0dFZlejFxTHpCcGt0OTlZbGUwdUlaUklJWUgNCjlaV0Z1YWNQVGF0Rit5UWQ4VlZiL3dEUGpVN3JXYmZYSS9LZmx1eTFtQy9UVkRxTnJaenh6eVhDU2VxM3FTRzRZc3NqSDQrNThjVlENCjJ2OEE1NCtaZFZ0VnNyWFM5STBPeGE5aTFLOXRkSnRXdDF2TG1CL1VqYTVacEpKSEFiZW5JRDZjVlM2RDgxL01VUG1MelpyNjI5bWINCnp6alpYdW5hbkdVbDlLT0xVV1ZwV2dIcWNsWmVBNDgyWWVJT0tvMncvUFg4dzlLc2RCMC9STDBhUHB1Z1JySERZMlhxUnczTENVelANCkxkcXp2NnJ5dXg1OUY4QU1WVE96L3dDY2hkZXRyVnJVK1dmTHR6RXVvWEdyV2d1Yk9lWVcxMWN0emQ3ZFh1Q3FjVzNYWTB4Vkt0SC8NCkFEczg0MlYxclV1b3hXUG1HMTh3VGk3MVRUZFl0L3JGckpjTDlpVlkxYUlveUNnWGl3RkFCMkdLcEY1Mzg5Njk1eTFXUFVOVzlHSmINCmFGTFd4c0xTTVFXdHJieC9aaWdpRmVLaXZqaXJIY1ZmWUgvT0VQOEF5aTNtWC9tT2gvNU00cStsTVZkaXJzVmRpcjRyL3dDYzFmOEENCnlhZWxmOXNPMy82akx2Rlh6L2lyMmo4ai93QTR0RzBSN2Z5LzV3YWVIUzRpeTZUNWdzbmVPOTA3MVc1UEdXaitLUzFaL2phTWhsRGINCjhXN0t2cnZTN0x6SGY2ZkRkNk41MVRVZE11RjVXOTlKWjJ0dzdvUWFNc3RzYmVFbXYvRmYwWXFyUi9sem85eHF0cHJQbUdhWHpGcTINCm5rbXd1TlFXSDA3WXRRa3d3UVJ3eEExR3pNck9QNXNWWlhpcnlEODZ2K2NpZkxQa0d5dU5PMDJXTFZQTnJLVWhzVVBPTzJZN2M3cGwNClB3OGV2cDE1SDJCNVlxK0Z0WDFiVWRZMU82MVRVN2g3cS92WkdtdWJpUTFaM2MxSk9Lb1BGVVJmMk4xWVgxellYY1podTdTVjRMaUoNCmhSa2tqWW82a0h1R0ZNVlErS3V4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMkt2c2IvbkNXeHVvdkpXdjNra2INCkxiWE9vSWtFaEZBNWloSFBqNDA1akZYMGRpcnNWZGlyc1ZmRmYvT2F2L2swOUsvN1lkdi9BTlJsM2lyd0FBazBHNVBRWXFtT29lV3YNCk1lbTJzZDNxT2xYbGxheTBFVnhjVzhzVWJFaW80dTZnR294Vk52S0huYjh3dktjY3VvK1dkU3ZkUHRFa1ZMaDRlVFd2cU9DVUVxTUcNCmhMRUlhY2hYYkZYcGtQOEF6bFgrZTFwcFVWNWNwYlRXZHp5aXQ5Um5zU2lPNlZEY0hqTWNUTXZjVStqRldOZVl2ejkvT3p6UlkzWHENCjZ6Y3c2YkNxL1d4cHNRdG80MWtiZ3ZxU3dxSEFkangrSjZIcGlyeklDU1dRQUF2STVvQUtsbVluOFNjVlREVmZMZm1MUjBpZlZ0THYNCk5PU2YrNWE3Z2xnRDAzK0V5S3Rmb3hWTGNWZm9GK1l2L09OLzVhK2U5V09zYWhGZGFkcWtsUHJOM3AwaVJOUFFVQmxXU09hTXNCKzANCkZCUGM0cXhML29TcjhyUCtycnJuL1NSWi93RFpKaXJ2K2hLdnlzLzZ1dXVmOUpGbi93QmttS3UvNkVxL0t6L3E2NjUvMGtXZi9aSmkNCnJ2OEFvU3I4clA4QXE2NjUvd0JKRm4vMlNZcTcvb1NyOHJQK3Jycm4vU1JaL3dEWkppcnYraEt2eXMvNnV1dWY5SkZuL3dCa21LdS8NCjZFcS9Lei9xNjY1LzBrV2YvWkppcnY4QW9TcjhyUDhBcTY2NS93QkpGbi8yU1lxNy9vU3I4clArcnJybi9TUlovd0RaSmlyditoS3YNCnlzLzZ1dXVmOUpGbi93QmttS3UvNkVxL0t6L3E2NjUvMGtXZi9aSmlydjhBb1NyOHJQOEFxNjY1L3dCSkZuLzJTWXE3L29TcjhyUCsNCnJycm4vU1JaL3dEWkppcnYraEt2eXMvNnV1dWY5SkZuL3dCa21LdS82RXEvS3ovcTY2NS8wa1dmL1pKaXJ2OEFvU3I4clA4QXE2NjUNCi93QkpGbi8yU1lxcVczL09GLzVVUlR4eXlYK3MzQ0l3TFFTWEZzRWNEOWxqSGJJOUQ3TURpcjJ6UWRCMGZRTkl0dEgwYTBqc2ROczANCjlPMnRvaFJWRmFuclVra21wSk5TZHp2aXFQeFYyS3V4VjJLdml2OEE1elYvOG1ucFgvYkR0LzhBcU11OFZZZi9BTTQySm9ML0FKdjYNCk11c0NGcWliOUhMYy93QnliNzBtK3I4cS93Q1Y5bi9LcFRlbUt2V3ZKazM1eHpYWG5WUHpmUzQvd1FtbTNSMVQ5Sm9xMnduRkRDYkENCjBDazh2c2VrZVBUOXJqaXJGZnlEMXJRZEYvS0g4eGRROHdhWU5YMFZKOUppMUN3TkFYaHVKekF6SVQrM0dKT2FiajRnTngxeFZmOEENCm5mb25sM1IveVE4bDIzbHZVZjByb0V1cDMxenB0NGZ0bUdjczRqazJIN3lNa28rdzNIUWRNVlpuK1Vuay9RUExuNWQ2ZjViOHlYZWsNCldseCtZRWNrK3YyMm9Ya1Z0ZnBaVFJOSHBxMnNMbms3K3FlWTJIRmlSMUdLdlBmeUs4c3llV1B6cjF6UWRTU0QvRStsV0dvUStYRnUNCmFMRytxSncrcnVvWTArT0V1eTc5RGlxQzg5VGY4NUtTL2w1cUgrTlk3My9DMzE2UDYyMStrQ3lpYm1lSENvRXdoOVNsQ3Z3ZE9PMksNCnZHTVZmcXBpcnNWZGlyc1ZkaXJzVmRpcnNWZGlyc1ZkaXJzVmRpcnNWZGlyc1ZkaXJzVmRpcnNWZGlyc1ZkaXI0ci81elYvOG1ucFgNCi9iRHQvd0RxTXU4VmVKK1dkQTFIekI1Z3NORzA2Z3ZMNlpZbzNZOFVRZFdrZHYyVWpVRjJQWUN1S3ZRZk1mbEw4eUpQTDkrM25IemcNCkliRFNyNmZUN0hUdFV2N3VjWE05cEdzam0wajR6Snc5TmxLTzNFTlZRT294Vk9JUHlDOCtXME0yaTJubWFNYVZxU1RTYWpaMnk2a3kNCnl5YWNZWEt0WnhRRjdyMHpjb1ZhTkhBYXU5Y1ZTb2ZreDVxdS9McVEyUG1TMnZMYUY1NzVkQmRyMkNSWVlyNXRNbXZWdHJpS05RVmsNCmgrTUdrZ1dsUldneFZiclA1WmVaZFp1TGE3dXZOOFd1YTljYXBKNWZoZ25hL2x1QmMyYm9KbGFlZUxnc2NFY3l5bHVmSGowM0JHS3INCi93RGxVbm4zekRyT29TNmw1aWhsODFRYWhKcGxrdC9jWE1seGYzRnJicmNSL1Y3b3JJdEhnS21FeU90ZHFZcTE1NzhpZm1kL2cwYTENCjVpODAvcHRkTGlzN3EvMFNhL3VMcTUwK0hVZmh0WkhTYXNZNTFBSVJpUlVkc1ZlVDRxL1ZURlhZcTdGWFlxN0ZYWXE3RlhZcTdGWFkNCnE3RlhZcTdGWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXE3Rlh4Yi93QTVyUlNEOHo5SmxLa1J0b2tLcTlOaXkzZHlXQVB0eUgzNHE4UTgNCnZlWmRkOHUzemFob2w0OWhmdERKYmk3aUNpVkVtWGkvcE9RV2pZcnR6UWhoMk9Lc2h1ZnprL011NnNMbXh1OWNrdWJlOFJFdWZYaWcNCmxkekhEOVhFaGtlTnBQVk1JNE5LRzVzdXpNY1ZWUDhBbGRmNW1OUDY4MnJyZFNjWlVIMXEwczdsUkhjQ01TeEtzME1pckczb0llQUgNCkdvclN0Y1ZVNVB6aS9NUjlLR2xMcWNjRmdKRElJYmF6czdmcmN0ZGxPVU1LUDZYcnVYOUt2RC9KcHRpcURzL3pNODhXVXNrOXBxalcNCjl4SmMzdDk5WWpqaFdWTG5VbzFodTVvcEFuT0ozampVQW9SeC9aNDFPS3BrbjU0Zm1tbHZMRCtuWFpwazRTWGJ3V3ozWi9kQ0RsOWINCmVKcmptWWdFTDgrUkhVNHFsK3EvbWw1KzFieXhCNVgxSFdKYmpRN2RZMGp0V1NJRXBCdkVra3FvSlpGai9aRHVRTzJLc1lpaWtsa1MNCktKUzhrakJVUlJVbGlhQUFlK0t2MVJ4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMksNCnV4VjRSL3psbi95ckQvQ2RsL2l2MS8wNXpmOEFRUDFIaDlacnQ2dkxuOEhvL1o1MTcwNDc0cStJelNwcDA3VnhWckZYWXE3RlhZcTcNCkZYWXE5ZS81eHMvNVZmOEE0LzAvL0YzMWo5Sit1djZGNThQcUgxbW85TDF2MitmUDdIN1BLbGNWZi8vWjwveG1wR0ltZzppbWFnZT4NCgkJCQkJPC9yZGY6bGk+DQoJCQkJPC9yZGY6QWx0Pg0KCQkJPC94bXA6VGh1bWJuYWlscz4NCgkJCTx4bXBNTTpJbnN0YW5jZUlEPnhtcC5paWQ6MTBkZWQ3Y2UtMGFjYS1lNTRhLTkzNTQtNDBhZjU4NDdkYjgzPC94bXBNTTpJbnN0YW5jZUlEPg0KCQkJPHhtcE1NOkRvY3VtZW50SUQ+eG1wLmRpZDoxMGRlZDdjZS0wYWNhLWU1NGEtOTM1NC00MGFmNTg0N2RiODM8L3htcE1NOkRvY3VtZW50SUQ+DQoJCQk8eG1wTU06T3JpZ2luYWxEb2N1bWVudElEPnV1aWQ6NUQyMDg5MjQ5M0JGREIxMTkxNEE4NTkwRDMxNTA4Qzg8L3htcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD4NCgkJCTx4bXBNTTpSZW5kaXRpb25DbGFzcz5wcm9vZjpwZGY8L3htcE1NOlJlbmRpdGlvbkNsYXNzPg0KCQkJPHhtcE1NOkRlcml2ZWRGcm9tIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4NCgkJCQk8c3RSZWY6aW5zdGFuY2VJRD54bXAuaWlkOmFmODBmOGUwLTFjZTMtZjM0Ny04YjI1LWJmZjI1YWU0ZDhlOTwvc3RSZWY6aW5zdGFuY2VJRD4NCgkJCQk8c3RSZWY6ZG9jdW1lbnRJRD54bXAuZGlkOmFmODBmOGUwLTFjZTMtZjM0Ny04YjI1LWJmZjI1YWU0ZDhlOTwvc3RSZWY6ZG9jdW1lbnRJRD4NCgkJCQk8c3RSZWY6b3JpZ2luYWxEb2N1bWVudElEPnV1aWQ6NUQyMDg5MjQ5M0JGREIxMTkxNEE4NTkwRDMxNTA4Qzg8L3N0UmVmOm9yaWdpbmFsRG9jdW1lbnRJRD4NCgkJCQk8c3RSZWY6cmVuZGl0aW9uQ2xhc3M+cHJvb2Y6cGRmPC9zdFJlZjpyZW5kaXRpb25DbGFzcz4NCgkJCTwveG1wTU06RGVyaXZlZEZyb20+DQoJCQk8eG1wTU06SGlzdG9yeT4NCgkJCQk8cmRmOlNlcT4NCgkJCQkJPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+DQoJCQkJCQk8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+DQoJCQkJCQk8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOjJmNWQzMjgxLTM1NDgtYzU0OC1iZWE1LTYyNDUzOTdlYzgxNjwvc3RFdnQ6aW5zdGFuY2VJRD4NCgkJCQkJCTxzdEV2dDp3aGVuPjIwMjEtMDgtMjZUMTQ6MjM6NTItMDU6MDA8L3N0RXZ0OndoZW4+DQoJCQkJCQk8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBJbGx1c3RyYXRvciAyNS4yIChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4NCgkJCQkJCTxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+DQoJCQkJCTwvcmRmOmxpPg0KCQkJCQk8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4NCgkJCQkJCTxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4NCgkJCQkJCTxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6M2I3NTA2MmEtN2M3Ny0wZjQyLTkwYzMtNWM0YzlmNTJhYmRjPC9zdEV2dDppbnN0YW5jZUlEPg0KCQkJCQkJPHN0RXZ0OndoZW4+MjAyMS0wOS0wMVQxMToyMDozNS0wNTowMDwvc3RFdnQ6d2hlbj4NCgkJCQkJCTxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIElsbHVzdHJhdG9yIDI1LjIgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50Pg0KCQkJCQkJPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4NCgkJCQkJPC9yZGY6bGk+DQoJCQkJCTxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPg0KCQkJCQkJPHN0RXZ0OmFjdGlvbj5jb252ZXJ0ZWQ8L3N0RXZ0OmFjdGlvbj4NCgkJCQkJCTxzdEV2dDpwYXJhbWV0ZXJzPmZyb20gYXBwbGljYXRpb24vcG9zdHNjcmlwdCB0byBhcHBsaWNhdGlvbi92bmQuYWRvYmUuaWxsdXN0cmF0b3I8L3N0RXZ0OnBhcmFtZXRlcnM+DQoJCQkJCTwvcmRmOmxpPg0KCQkJCQk8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4NCgkJCQkJCTxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4NCgkJCQkJCTxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6MzRlNjIwYzQtZjVhMS0zMjQzLWE4NjMtNzQ0NDUxYjJlOTkwPC9zdEV2dDppbnN0YW5jZUlEPg0KCQkJCQkJPHN0RXZ0OndoZW4+MjAyMS0wOS0wM1QxOTo0MDozMC0wNTowMDwvc3RFdnQ6d2hlbj4NCgkJCQkJCTxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIElsbHVzdHJhdG9yIDI1LjIgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50Pg0KCQkJCQkJPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4NCgkJCQkJPC9yZGY6bGk+DQoJCQkJCTxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPg0KCQkJCQkJPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPg0KCQkJCQkJPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDoxMGRlZDdjZS0wYWNhLWU1NGEtOTM1NC00MGFmNTg0N2RiODM8L3N0RXZ0Omluc3RhbmNlSUQ+DQoJCQkJCQk8c3RFdnQ6d2hlbj4yMDIxLTA5LTAzVDIwOjA0OjQxLTA1OjAwPC9zdEV2dDp3aGVuPg0KCQkJCQkJPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgSWxsdXN0cmF0b3IgMjUuMiAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+DQoJCQkJCQk8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPg0KCQkJCQk8L3JkZjpsaT4NCgkJCQk8L3JkZjpTZXE+DQoJCQk8L3htcE1NOkhpc3Rvcnk+DQoJCQk8aWxsdXN0cmF0b3I6U3RhcnR1cFByb2ZpbGU+UHJpbnQ8L2lsbHVzdHJhdG9yOlN0YXJ0dXBQcm9maWxlPg0KCQkJPGlsbHVzdHJhdG9yOkNyZWF0b3JTdWJUb29sPkFkb2JlIElsbHVzdHJhdG9yPC9pbGx1c3RyYXRvcjpDcmVhdG9yU3ViVG9vbD4NCgkJCTxwZGY6UHJvZHVjZXI+QWRvYmUgUERGIGxpYnJhcnkgMTUuMDA8L3BkZjpQcm9kdWNlcj4NCgkJPC9yZGY6RGVzY3JpcHRpb24+DQoJPC9yZGY6UkRGPg0KPC94OnhtcG1ldGE+DQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPD94cGFja2V0IGVuZD0ndyc/Pv/iDFhJQ0NfUFJPRklMRQABAQAADEhMaW5vAhAAAG1udHJSR0IgWFlaIAfOAAIACQAGADEAAGFjc3BNU0ZUAAAAAElFQyBzUkdCAAAAAAAAAAAAAAAAAAD21gABAAAAANMtSFAgIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEWNwcnQAAAFQAAAAM2Rlc2MAAAGEAAAAbHd0cHQAAAHwAAAAFGJrcHQAAAIEAAAAFHJYWVoAAAIYAAAAFGdYWVoAAAIsAAAAFGJYWVoAAAJAAAAAFGRtbmQAAAJUAAAAcGRtZGQAAALEAAAAiHZ1ZWQAAANMAAAAhnZpZXcAAAPUAAAAJGx1bWkAAAP4AAAAFG1lYXMAAAQMAAAAJHRlY2gAAAQwAAAADHJUUkMAAAQ8AAAIDGdUUkMAAAQ8AAAIDGJUUkMAAAQ8AAAIDHRleHQAAAAAQ29weXJpZ2h0IChjKSAxOTk4IEhld2xldHQtUGFja2FyZCBDb21wYW55AABkZXNjAAAAAAAAABJzUkdCIElFQzYxOTY2LTIuMQAAAAAAAAAAAAAAEnNSR0IgSUVDNjE5NjYtMi4xAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABYWVogAAAAAAAA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iZW/WucooA/ZH/glz/wd3fFT4B6/pvhj9oaOX4peBmYQtrsMKReItKUkfOSNsd2ijOVkCyHOfNONp/o3/Z8/aF8F/tU/CHRfHvw98Raf4q8JeIIfPsdRsn3RygEqykHDI6sCrIwDKwIIBBFfwb1+j3/BuT/wWU1L/gmd+1TZ+E/FWqSf8KV+I97Faa9BM2YdCunxHFqkY/h2fKs2PvxZJDNHHgA/rezRTVkEihlIZWGQQcgiigD+L/8A4Ls/tYXX7Y//AAVa+MniiS4+0aXpWuzeGtGCtujSx09jaxMn+zIY2mP+1M1fItXvFPiCbxb4m1LVbnH2jU7qW7lwSfnkcseTz1J61RoAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKBRRQB/Wf/wRn/4LF+CPGH/BMD4NzfEPxE8XjDT9EOj6gX2vJN9inls4pXYtlnkigjdieSzk0V/K14c+NninwjosOn6brE1rZ2+7y4ljQhdzFj1Unkkn8aKAOVora+I9hDpPxD161to1ht7bUbiKKNeiKsrAAfQACsWgAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAOg+K//JUvEv8A2Fbr/wBHNXP10HxX/wCSpeJf+wrdf+jmrn6ACiiigAooooAKKKKACiiigAooooAKKKMYoAKK+lP2bv8Agjx+1B+1vbW1z4C+B/j7VdOvArW+pXdh/ZenThuhS6uzFAw5ByHwBzX138N/+DPT9sDxxFG2qR/C/wAGtJnKax4kaUx/e6/Y4bgc4HQn7w98AH5YUV+v3/EFV+1N/wBD98AP/B5q/wD8rK4r4l/8Gfv7YngS2lk0uz+GvjRo922PRvEvlNJjGMfbIrcc5OMkdDnH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";
                        } else {
//IMAGEN A COLOR
                            $logoOnac = "@/9j/4AAQSkZJRgABAQEAlgCWAAD/4QAiRXhpZgAATU0AKgAAAAgAAQESAAMAAAABAAEAAAAAAAD/7QAsUGhvdG9zaG9wIDMuMAA4QklNA+0AAAAAABAAlgAAAAEAAQCWAAAAAQAB/+FHzWh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8APD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4NCjx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDYuMC1jMDA0IDc5LjE2NDU3MCwgMjAyMC8xMS8xOC0xNTo1MTo0NiAgICAgICAgIj4NCgk8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPg0KCQk8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wR0ltZz0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL2cvaW1nLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczpzdEV2dD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlRXZlbnQjIiB4bWxuczppbGx1c3RyYXRvcj0iaHR0cDovL25zLmFkb2JlLmNvbS9pbGx1c3RyYXRvci8xLjAvIiB4bWxuczpwZGY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vcGRmLzEuMy8iPg0KCQkJPGRjOmZvcm1hdD5pbWFnZS9qcGVnPC9kYzpmb3JtYXQ+DQoJCQk8ZGM6dGl0bGU+DQoJCQkJPHJkZjpBbHQ+DQoJCQkJCTxyZGY6bGkgeG1sOmxhbmc9IngtZGVmYXVsdCI+U2ltYm9sb19BY3JlZGl0YWRvX09OQUM8L3JkZjpsaT4NCgkJCQk8L3JkZjpBbHQ+DQoJCQk8L2RjOnRpdGxlPg0KCQkJPHhtcDpNZXRhZGF0YURhdGU+MjAyMS0wOS0wM1QxOTo1MTo0Ni0wNTowMDwveG1wOk1ldGFkYXRhRGF0ZT4NCgkJCTx4bXA6TW9kaWZ5RGF0ZT4yMDIxLTA5LTA0VDAwOjUxOjUyWjwveG1wOk1vZGlmeURhdGU+DQoJCQk8eG1wOkNyZWF0ZURhdGU+MjAyMS0wOS0wM1QxOTo1MTo0Ni0wNTowMDwveG1wOkNyZWF0ZURhdGU+DQoJCQk8eG1wOkNyZWF0b3JUb29sPkFkb2JlIElsbHVzdHJhdG9yIDI1LjIgKFdpbmRvd3MpPC94bXA6Q3JlYXRvclRvb2w+DQoJCQk8eG1wOlRodW1ibmFpbHM+DQoJCQkJPHJkZjpBbHQ+DQoJCQkJCTxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPg0KCQkJCQkJPHhtcEdJbWc6d2lkdGg+MjU2PC94bXBHSW1nOndpZHRoPg0KCQkJCQkJPHhtcEdJbWc6aGVpZ2h0PjkyPC94bXBHSW1nOmhlaWdodD4NCgkJCQkJCTx4bXBHSW1nOmZvcm1hdD5KUEVHPC94bXBHSW1nOmZvcm1hdD4NCgkJCQkJCTx4bXBHSW1nOmltYWdlPi85ai80QUFRU2taSlJnQUJBZ0VBU0FCSUFBRC83UUFzVUdodmRHOXphRzl3SURNdU1BQTRRa2xOQSswQUFBQUFBQkFBU0FBQUFBRUENCkFRQklBQUFBQVFBQi8rNEFEa0ZrYjJKbEFHVEFBQUFBQWYvYkFJUUFCZ1FFQkFVRUJnVUZCZ2tHQlFZSkN3Z0dCZ2dMREFvS0N3b0sNCkRCQU1EQXdNREF3UURBNFBFQThPREJNVEZCUVRFeHdiR3hzY0h4OGZIeDhmSHg4Zkh3RUhCd2NOREEwWUVCQVlHaFVSRlJvZkh4OGYNCkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zkh4OGZIeDhmSHg4Zi84QUFFUWdBWEFFQUF3RVINCkFBSVJBUU1SQWYvRUFhSUFBQUFIQVFFQkFRRUFBQUFBQUFBQUFBUUZBd0lHQVFBSENBa0tDd0VBQWdJREFRRUJBUUVBQUFBQUFBQUENCkFRQUNBd1FGQmdjSUNRb0xFQUFDQVFNREFnUUNCZ2NEQkFJR0FuTUJBZ01SQkFBRklSSXhRVkVHRTJFaWNZRVVNcEdoQnhXeFFpUEINClV0SGhNeFppOENSeWd2RWxRelJUa3FLeVkzUENOVVFuazZPek5oZFVaSFREMHVJSUpvTUpDaGdaaEpSRlJxUzBWdE5WS0JyeTQvUEUNCjFPVDBaWFdGbGFXMXhkWGw5V1oyaHBhbXRzYlc1dlkzUjFkbmQ0ZVhwN2ZIMStmM09FaFlhSGlJbUtpNHlOam8rQ2s1U1ZscGVZbVoNCnFibkoyZW41S2pwS1dtcDZpcHFxdXNyYTZ2b1JBQUlDQVFJREJRVUVCUVlFQ0FNRGJRRUFBaEVEQkNFU01VRUZVUk5oSWdaeGdaRXkNCm9iSHdGTUhSNFNOQ0ZWSmljdkV6SkRSRGdoYVNVeVdpWTdMQ0IzUFNOZUpFZ3hkVWt3Z0pDaGdaSmpaRkdpZGtkRlUzOHFPend5Z3ANCjArUHpoSlNrdE1UVTVQUmxkWVdWcGJYRjFlWDFSbFptZG9hV3ByYkcxdWIyUjFkbmQ0ZVhwN2ZIMStmM09FaFlhSGlJbUtpNHlOam8NCitEbEpXV2w1aVptcHVjblo2ZmtxT2twYWFucUttcXE2eXRycSt2L2FBQXdEQVFBQ0VRTVJBRDhBOUxhcjVsMExTbUNYOTRrTWhGUkgNCnU3MDhlS0JtL0RJbVlITnc5VDJoZ3dHc2tnRDl2eUNYL3dES3hmSnYvVncvNUl6L0FQTkdSOFdMaS95N3BQNS8reGwrcDMvS3hmSnYNCi9Wdy81SXovQVBOR1BpeFgrWGRKL1A4QTlqTDlUdjhBbFl2azMvcTRmOGtaL3dEbWpIeFlyL0x1ay9uL0FPeGwrcDMvQUNzWHliLzENCmNQOEFralAvQU0wWStMRmY1ZDBuOC84QTJNdjFPLzVXTDVOLzZ1SC9BQ1JuL3dDYU1mRml2OHU2VCtmL0FMR1g2bmY4ckY4bS93RFYNCncvNUl6LzhBTkdQaXhYK1hkSi9QL3dCakw5VHYrVmkrVGY4QXE0ZjhrWi8rYU1mRml2OEFMdWsvbi83R1g2bmY4ckY4bS84QVZ3LzUNCkl6LzgwWStMRmY1ZDBuOC8vWXkvVTcvbFl2azMvcTRmOGtaLythTWZGaXY4dTZUK2Yvc1pmcWQveXNYeWIvMWNQK1NNL3dEelJqNHMNClYvbDNTZnovQVBZeS9VNy9BSldMNU4vNnVIL0pHZjhBNW94OFdLL3k3cFA1L3dEc1pmcWQvd0FyRjhtLzlYRC9BSkl6L3dETkdQaXgNClgrWGRKL1AvQU5qTDlUditWaStUZityaC93QWtaLzhBbWpIeFlyL0x1ay9uL3dDeGwrcDMvS3hmSnY4QTFjUCtTTS8vQURSajRzVi8NCmwzU2Z6LzhBWXkvVTcvbFl2azMvQUt1SC9KR2YvbWpIeFlyL0FDN3BQNS8reGwrcDMvS3hmSnYvQUZjUCtTTS8vTkdQaXhYK1hkSi8NClAvMk12MUw0ZlA4QTVRbWtFYWFpb1k5QzZTb3YvQk9xakQ0a2U5bEh0clNTTkNmMkVmZUUvUjFkUTZFTWpBRldCcUNEMElPVGRtQ0MNCkxEZUtYWXE3RlhZcTg3L05UekJlMjBsdnBWdEswS1N4K3RjTWhJWmdXS3F0UjIrRTE4Y296U1BKNVgyajFzNEdPS0pxeFplYVpqdkkNCk94VjJLdXhWMkt1eFYyS3Bsb092WCtqWDhkMWF5TUZERDFvYS9ESXZkV0hUcGtveUlMbDZQV1QwOHhLSjk0NzBKZTNseGUzYzEzY00NClhtbmN1N0h4UDhQRElrMjBaY3Nza3pLWE1xR0xXN0ZYWXE3RlhZcTdGWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXE3RlhZcTlaL0ttL3UNCko5RW50NVdMcGF6VWhKTmFLNjE0L0lHdVpPRTdQYit6ZWFVc0ppZjRUc3piTG5vbllxN0ZYWXE4bS9Oci9sSTdiL21EVC9rN0ptTG0NCjV2RCswdjhBakVmNmcrK1RDY3FlZVZyT3p1cjI1UzJ0WW1tbmtORWpVVkp4QXRzeFlwWkpDTVJjaTlDMFg4cGxLTExyRnlReDNOdEINClRiMmFRMXI5QStuTDQ0ZTk2blNlelcxNVpmQWZyWkVuNWNlVDFVQTJKY2pxeG1tcWZ1Y0RMUENpN1VkaGFRRDZQdGwrdEJhaCtWZmwNCjY0US9WR2xzNVAyU0c5UkI4MWY0ai93V0E0UTQyZjJjd1NIb3VCK1krMzliejd6SDVRMWZRWHJjS0piVmpTTzZqM1Fud2J1cCtlVVQNCmdZdkw2L3N2THBqNnQ0OTQvR3lSNUIxenNWZGlyc1ZYUnh5U3lMSEdwZVJ6UlVVRWtrOWdCaW1NU1RRM0xLckg4c3ZORjFHSkhTSzANCkIzQ3p1UTMzSUhwOU9XREZJdTZ3K3orcG1MSUVmZWYxV3QxSDh0Zk5GbkdaRmlqdTFVVmI2dXhacWY2ckJHUDBERTRwQkdmc0RVNHgNCmRDWDlYOEJpekt5TVZZRldVMFpUc1FSMk9WdWxJcll0WXFuVnI1TTgwWFVBbmgwNlV4RVZVdHhRa2VJRGxTY21NY2owZGhqN0sxTTQNCjhRZ2ErWDNwWGQybDFhVHRiM1VUd1RKOXFPUUZXSDBISUVVNFdURktFdUdRb3F0bHBXbzMwVnhMYVFOTWxxb2VjcFFsVk5kNmRUMDcNCllSRWxuaTAyVElDWUMrSG1vVzl2TmNYRVZ2Q3ZPYVoxamlRZDJZMFViK0pPQUJyaEF6a0lqbWRsYlVkTXZ0TnVUYTMwUmhuQURGQ1ENClRROU9oT0Vnam0yWjlQUEZMaG1La29Rd3l6VEpERXBlV1Jna2FEcVdZMEFIMDRHcU1USWdEbVZTK3NidXd1cExTN2pNTnhGVG5HMUsNCmlvcU9uc2NKRk04MkdXT1JqTVZJS0dCclR1MDhsK2FMdTNGeEJwOGhpSXFwWXFoSVBjSzVVbkpqSEk5SFlZdXlkVE9QRkdCcjVmZWwNClYzYVhWcE8xdmRSUEJNbjJvNUFWWWZRY2dSVGhaTVVvUzRaQ2lvNHNIcVA1US84QUhPMUQvak1uL0VjeU1ISjdMMlkvdTUvMWg5elANCnN2ZW5kaXJzVmRpcnliODJ2K1VqdHY4QW1EVC9BSk95Wmk1dWJ3L3RML2pFZjZnKytUQ2dDU0FCVW5ZQVpVODg5cThrZVZJZEQwNVoNCkpVQjFLNFVHNGtQVlFkeEdQWWQvRS9SbVhqaFFmUXV5T3pScDhkbis4bHovQUZNa3l4MjdEdk12NWkyT21YdjFDMEhyVG8zRzVtcHkNClNMZjRnRnFuTmg0Y2hsVThvR3pvTmYyN0REUGdqdWVwNkQ3clB4VG13Z1RVYlNLOWkxYTV1SXBSeVIwTWNhL0xpaUwwNkVOa2h2MWQNCmhoZ01zUk1aSlNCOXcrNGZldXU5T3Y4QTZ0SkMwZzFTMWtYakxhWFNvcnN2Z3NrWVJhK0hKZjhBWkRyaElQdlRsd1Q0U0NmRWllY1oNClZmd0lyN1I4UThhOHhhU3VtYWs4VVJZMnpFbUgxQlIxRmQwY2RtWHY0N0hvUm1KS05GNERYYVlZY2hBK25wZlAzSHpIN2VxVjVGdzMNCllxN0ZYcnY1ZGVWWU5QMDJMVkxoQTEvZG9IUW5mMDRtM1VEM1libjdzeXNVS0Z2ZGRoZG14eFl4a2tQWEw3QW12bXp6WmFlWDdSWGQNCmZXdTVxaTN0d2FWcDFaajJVWktjK0Z6ZTB1MG82V0ZuZVI1Qlo1WjF0UE1GaDlhaHU1WXBsSVc0dHdJdjNiZTFVWWxUMkpPTUpjUVkNCjluNnNhcUhFSkVIcU50dnM1TWEvTXJ5My9vcDFaVVV6UmtDYVpCeDVxVHhIcUtOdVEvbUhYcHR0V3ZMRHE2anQvUWVueGVvNW52OEENCmY1K2Y3R1BmbHZwbHRmOEFtWlByQ2gwdG9tdUZSdWhaU3FydDdGcS9SbGVJV1hWZGc2ZU9UVURpL2hGL2o1cW5tSHozNWtmV3JqNnYNCmRQYXcyOHJKREFnQUFDTngrTUVmRVRUZXVHV1EyeTEzYkdvT1k4TWpFUk93OTNlbTNtcVZOYzhpV091M0VZanY0bjlObkFweUhJeHMNCkI3RWpsN1pLZThiYzd0R1ExT2lobmtLbURYMjErMWIrVWpoSmRXY2lvV09Ja2ZJdmpnNm85bVRSeUh5SDZWV2J5MVkzV3JhVDVqOHYNCmZ2TlBsdklHdXJkUnZDM3FyVThld0g3UTdmTG84QUpCRE9mWjhKNWNlbzArOERPTmp1M0g0UGQ3a20vTS93RDVTcVQvQUl3eGZxT1INCnpmVTRIdEQvQUl5ZjZvVlB5NjAyQVhWenJ0NktXV2x4bHd4NkdXbGZwNHIrTk1jUTZub3k3QzA4ZUtXZWYwWXg5djQvUWl2UE1VT3QNCmFKWWVhYlJPSllDRzlRYjhUV2dyMCt5MVZyN2pEazNIRTM5c1JHb3d3MU1CNVMvSHYyK1NWL2x6cHR0ZmVaNGhjS0hTM1Jwd2g2RmsNCm9GcjhpMWNqaUZsd3V3c0VjbXBIRnlpTFJIbVR6MTVqT3VYSzIxMDFyRGF6UEhGQ2xLVWpZclZxajRxMDc0WjVEYmJyKzJOUjQwaEcNClhDSWtnRDNkL2Vrdm1EekhxT3UzTWM5N3dEUklFUlkxNGdEdWU1M08vWElTa1R6ZGRyZGZrMU1oS2RiRG9sV1JjTjZqK1VQL0FCenQNClEvNHpKL3hITWpCeWV5OW1QN3VmOVlmY3o3TDNwM1lxN0ZYWXE4bS9Oci9sSTdiL0FKZzAvd0NUc21ZdWJtOFA3Uy80eEgrb1B2a2wNClhrSFQwdnZOTm1rZzVSd2t6c1ArTVlxdi9EMHlPTVhKd3V4Y0F5YW1JUEliL0w5cjI3TXg5RlNMenRyVW1rZVhyaTVoUEc0a3BEQTMNCmc3OS9vVUU1REpLZzYzdGJWbkJnTWg5UjJIeGVIRWttcDNKNm5NTjg0WjErVld0eVE2bkpwTWpWZ3VsTWtLbnRLZ3FhZjZ5QTErV1gNCllaYjA5TDdPYXN4eUhFZVV0eDd4K3o3bnFtWkwyanovQVBOZlJJM3M0ZFhpV2tzVENLNHAzUnZzc2ZjRVUrbktNMGVyeS90SnBBWUQNCktPWTJMekRNZDQ1Mkt1eFY5R3hxaVJxa1lBalVBSUIwb0J0bWUrc1JBQW9jbmp2NW16VFNlYkowa3J3aGppU0d2OHBRTWFmN0pqbUoNCmwrcDRIMmdtVHFpRDBBcjVYOTZML0tlYVpmTUU4S2srbEpiTTBpOXFxNjhUOUhLbjA1TER6Yi9acVpHY2djakg5SWVtNjFERk5vOTcNCkZLS3h2QklHQjhPQnpJbHlldzFjUkxGSUhsd243bmhtZzZ6Y2FOcWtPb1FEazBSSWVNbWdkR0ZHVS9NWmhSbFJ0ODQwZXJscDhveVINCjZmYXpLNnZmeXgxaWY5STNyeldkMUo4VThDcTRETjNyd1YxMzhRUmx4TUR1Ny9KbTdPenk4U2ZGR1I1amY5QUtUK2NQTnRycWR2QnANCldsUW0zMG0xb1VVaWhjcUtEYmVnRmY0bklUbmV3NU9CMnAybkhORVlzUTRjVWZ0WCtRZk1HbDZQK2t2cjhoaitzUm9zVkZacWtjcS8NClpCOGNPT1FGMnk3RjF1TEJ4OFpyaUczMnBmNVQ4MTNmbCs5OVJBWmJPVWdYTnY0Z2Z0TDRNTWpDZkNYRjdON1NscFoyTjRIbVB4MVgNCitlTllzZFcxNXJ5eWN2QTBTS0N5bFRWUnVLSEhKSUU3SjdYMVVNK2ZqaHlvSjNGNTJzdEM4dVdOaG9UTFBlVkwzc2tzYkJlVENyVXINCnhydWFBK0F5ZmlDSW9PeGoydERUYWVNTUc4LzRyQi9aL1lGWFRmekVpMUszdmJEekp3anRiaUVwSEpER3hJWTdHb3EyKzlRZmJDTXQNCjdGc3dkdUROR1VOUlFqSWRCL2F4RFJ0WG4wWFY0NzYxSWtNTEVVTlFzaUhZamZjVkgzWlZHVkczUTZYVXkwK1VUanZYMmhtTjFmZmwNCmpyRS82UnZXbXM3cC9pbmdDeUFPM2NuMDFkZC9FRVphVEE3bDMrVE4yZG5sNGsrS01qekcrL3lCL1F4ZnpWZjZEZVg2dG8xbWJTM2oNClFJeE8zcUZkZzNEZmp0OS9mS3BrRTdPbTdSellNazd3eDRZZ2ZQNEpMa1hYdlVmeWgvNDUyb2Y4WmsvNGptUmc1UFplekg5M1Arc1ANCnVaOWw3MDdzVmRpcnNWZVRmbTEveWtkdC93QXdhZjhBSjJUTVhOemVIOXBmOFlqL0FGQjk4a1ArVjB5UithVlZqUXl3U0ludVJSdjENCktjY1AxTlhzN0lEVTEzeFA2M3NPWlQzckN2elloa2Z5OUJJdFNrZHlwY2RxRkhBUDM3ZlRsT2JrODk3U3hKd0E5MHYwRjVMbU04T3kNClA4dllwSlBOOWh3L1lNanNmQUNOdjlyTE1YMU8xN0RpVHFvVjUvY1h0bVpiNkl4bjh5SkVUeWhlSzNXUm9sVDUrcXJmcVU1WGwrbDANCi9iMGdOSkx6cjd3OFd6RWZQbllxN0ZYc1hrVHpRbDlvc2NkMC93Qy90QUlwWk56c05sTW44dndqN1IyUGpYTXJIT3c5NzJQMmlNbUUNCkNYT08zNnIvQUY4a3YvTUR5L2I2MEk3N1RKb3B0UWlYZzl1akt6U3BXbzRnSDdTMVAwWkhMRzl3NHZiZWlqcUtuaklNeDA3eCt4TXYNCklQbEdUUTdTU2U4cCtrTHFuTlFhaU5CdUVxTzlldVN4d3B5K3hlekRwb0dVL3JsOWc3djF0L21KNWdoMDNRNWJSV0J2TDVURkdnNmkNCk50bmMrMU5oNzQ1WlVGN2Mxd3hZVEFmWFBiNGRTOGZ0YmFhNnVZcmFGZVUwenJIR3ZpekdnekZBdDRUSGpNNUNJNWsweXUrOGlvTmQNCjAyd3M1eTlwZThvcExrajdNdHZYMXhUYitXcTVhY2U0RHU4M1k0OGFFSUgwejJ2emo5WDdFSWxuNU52THl5dExBM3F6UzNjVUQrdDYNClpWNG5jS3pnZ0FxMzBZS2llVFFNV2t5VGpDSEhabUJ2VzRKNStTYXplVHRHa1lORkRlV2F4YWhCWnVMa3J4bVNXVUlXaVBGVFVEZnYNCmt1QWZhNXN1eThKNUNjYXlSajZ2NGdUVzJ5elVQS3VqUWF0YWFlTE82aFdlOEZ2OVllYUoxZU1NUVNxcU9TazlSWEV3RjB4ejluWW8NCjVZNCtHWTRwMWZFTndnNXRCMEM4aHVadE1OeEMybjNFVU41RE9WWU1rMHZwQm8yVUNocjJPUjRRZVRqeTBXQ1lrY2ZFT0NRRWdlNG0NCnRrVGFhRDVYT29hell6eFhUUHBTWE54eldSQUdpZ0lBWGRmdGI5Y0lqR3lPNXV4YUxUZUpsaElUdkdKSG1PVWZoelEybGVVN1hWcksNCit2TGIxSVE3R1BSNEpDR2FTU05USTZzUU4vaEZCNzRCQzJuVGRteHp3bk9OanBBZDVHNVU3SFN2TGNYbCswMURWRnVqSmRYRWtCYUINCmxIQUorMXhaVFhFQVZaWTRkTnA0NEk1TXZGY3BFYlZzcjNIbFBTOUhYVUxuV0pacDdhMXVWdExlSzM0cTBqdkdKZ3pzM0lLT0RENmMNCkpnQnpiTW5adVBCeHl5a21NWmNJNGV1M0Z2OEFCS2ZNV2syVmtiUzVzSkhrc2IrTDFvQk1BSkVvZUxLMU5qUWpxTWhLTmNuQjEybWgNCmo0WlFKTUppeGZNZVJTZkl1QzlSL0tIL0FJNTJvZjhBR1pQK0k1a1lPVDJYc3gvZHovckQ3bWZaZTlPN0ZYWXE3RlhrMzV0ZjhwSGINCmY4d2FmOG5aTXhjM040ZjJsL3hpUDlRZmZKaVduWDgrbjM4RjdibWsxdTRkUEEwN0gyUFE1V0RSdDBlRE5MRk1UanppWHZPa2F0YmENCm5hSmNRL0N4VlRKQ1NDeUZoVVZwMUJHNFBRanBtYkdWdnBlbDFNYzBPSWZMdS9IUTlYYXpwY0dxNlhjYWZOc2s2Y1EzWGl3M1Z2b1kNCkE0eUZpazZ2VHh6WTVZenlrOEoxWFNyM1M3NlN5dkl5azBacDdNT3pLZTRPWVVva0duelhVNmFlR1poTWJoNlgrV25sYWJUcmVUVkwNCjFPRjFkS0ZoallmRWtYV3A4Q3hwbVJpaFc3MS9ZSFp4eFJPU1lxVXVYa1AyczR5NTZONXIrYkd0bzcyMmp4TUNZejY5elRzeEJDTDkNCnhKKzdNZk5MbzhqN1M2c0V4d2pwdWYwZmozUE9zb2VVZGlyc1ZSZW1hcmY2WGRyZDJNeGhuWGFvM0JIY01Ec1I4OElKSEp1MCtwbmgNCmx4UU5GbmxqK2J4RVFXLzAvbEtCdkpBOUFUL3FzRFQvQUlMTGhuNzNwc1B0UHQ2NGIrUi9IM3JkUi9OeVZvaW1uMklqa0kybG1ibFQNCi9ZS0IrdkU1KzVHZjJtSkZZNFVlOC9xWUZmNmhlYWhkUGQza3JUWEVocXp0K29Eb0FQQVpTVGJ6T2JQUExJeW1ia1ZYU05WbjBxK1cNCjl0MGplZU5XRVprQllLV0hIa0FDUGlGZHE0eE5HMmVsMU1zTStPSUhFTy83MGV2blB6QjZBaWt1RE84Y3lYRUZ4TFY1SW5TbytBazANCm93TkNDRGt2RUxranRYUHcwVFpCQkJQTUVkMzZWU2Z6cGZTdEF5V1ZsYm1HNFM3WXd3OFRKTEdlUUxtcFBYcnhwaWNoWno3Vm5JZ2kNCk1JMUlTMmp6STcvMlUwM25YV1plSDFuMDdreFhhMzF1MDNOakU2dHk0SWVmMk8zRTl1bVBpRkI3V3pINnFsVStNWGV4N2h2eThsMDMNCm5PNGt1MHZGMHl3aXUwbkZ4NjhjVGgyY055UEk4enN4NjQrSjVKbjJySXlFK0RHSmNYRllCdS9tcDN2bS9VTG1NUlIyOXJad21WWjUNCm83YU1vSlpFUEpUSVN6TWQvZkV6TEhMMnBrbUtFWXhGMmVFVlo4MUJQTXQ4bC9xZDZJNHZWMVdHYUM0V2pjVldjZ3NVK0tvSXB0VW4NCkJ4bXllOXFHdm1Kem5RdklDRC9uZDI2ckQ1eDF5Mmhzb0xTYjZyQlpLRlNHSGtxU0hseUxTZ2s4aXhPL2JEeGxzaDJwbWdJeGdlR00NCk9nNjlkKyswUkY1NHZZNHpIK2o3R1JSTzl6RUpJbmIwNUpEVWxBWG9LSHBoOFJ0ajJ2TUN1REdmVVpiZzdFOTI2SHRmTnVxUlNYYlQNCnJEZlIzeityY3dYU2VwR3pqb3dBSzBJOXNBbVduSDJubGlaY1ZURXpaRWhZdEJhdnJONXF0eXM5endVUm9JNFlZbDRSeG92UlVVZEINCmtaU3RvMVdxbm1seFM2YkFEWUFlU0J3T005Ui9LSC9qbmFoL3htVC9BSWptUmc1UFplekg5M1Arc1B1WjlsNzA3c1ZkaXJzVmVUZm0NCjEveWtkdC96QnAveWRrekZ6YzNoL2FYL0FCaVA5UWZmSmhPVlBQTXM4b2VhYlcwS1dXcUYxdDFxTGEraUpXV0RrYWxhcnUwWk81WGYNCmZzY3NoT3RpN3ZzdnRHTUtobHZoNlNIT1AvSGZMN0hwOXZGZnpRSkxhNnNKN2R4VkptaWpja2VJYVBndi9DNWtpKzk3R0VaeWpjY24NCkZFOWFCKzZoOWk1ZEJ0WHVZcnErZHI2NWdxWVhuQ2NZNi95SWlxdjBrRSsrUEQzc2hvNG1RbFAxeUhLNjI5d0ZEOUtaWkp5MkwrYmYNClBXbjZMRThGdXkzR3BrVVdFR3F4bnhrSTZVL2w2NVhQSUI3M1RkcDlzWTlPREdQcXlkM2Q3LzFQSGJxNW51cmlTNXVITWswckY1SGINCnFTY3hDYmVDeVpKVGtaU05rcVdMQmZOREpCTkpES3BXU0ppanFlb1pUUWpGbE9KaVNEekN6Rmk3RlhZcTdGWFlxN0ZYWXE3RlhZcTcNCkZYWXE3RlhZcTdGWFlxN0ZYWXE5VS9LT0dSZEp2WldVaU9TY0JHUGZpdTlQdnpKd2NudFBabUpHS1I2R1g2R2Q1YzlLN0ZYWXE3RlgNCmszNXRmOHBIYmY4QU1Hbi9BQ2RrekZ6YzNoL2FYL0dJL3dCUWZmSmhPVlBQSWlmVDcrM2pXV2UybGlqYjdMdWpLcHI0RWpDUVd5ZUMNCmNSY29rRDNJblM5WDF6VFZhZlQ3aWFDSU1CSVVxWStSNmNnYXBYYnZoRWlPVGRwOVRtdytyR1NCOW42bVFMK1pIbktLMldXUVJ2RkoNClZVbmVHZ0pIV2hYaXBPVDhXVHRCMjlxeEd6Vkhyd3BmZmVkZk51b3d5Y3JxUkxkQVBWRUNpTlZER2c1TWdydWR0emtUa2tYRnpkcmENCnJLRGNqdytXMzNNZkFabW9LbGlmbVNUa0hWODFhNXNMNjFDbTV0cFlBLzJESWpKWDVjZ01KQkRaa3d6aDlVU1BlS1VNRFc5dDEzeUYNCm9HczNKdXAxa2d1Ry92SllHQ2w2ZnpCbFlWOTZabHl4Z3ZvZXM3R3dhaVhGS3hMeTYvZWxuL0twZkxuL0FDMDNuL0J4ZjlVOGo0SWMNClAvUTFwLzUwL21QK0pkL3lxWHk1L3dBdE41L3djWC9WUEh3UXYraHJUL3pwL01mOFM3L2xVdmx6L2xwdlArRGkvd0NxZVBnaGY5RFcNCm4vblQrWS80bDMvS3BmTG4vTFRlZjhIRi93QlU4ZkJDL3dDaHJUL3pwL01mOFM3L0FKVkw1Yy81YWJ6L0FJT0wvcW5qNElYL0FFTmENCmYrZFA1ai9pWGY4QUtwZkxuL0xUZWY4QUJ4ZjlVOGZCQy82R3RQOEF6cC9NZjhTNy9sVXZsei9scHZQK0RpLzZwNCtDRi8wTmFmOEENCm5UK1kvd0NKZC95cVh5NS95MDNuL0J4ZjlVOGZCQy82R3RQL0FEcC9NZjhBRXUvNVZMNWMvd0NXbTgvNE9ML3FuajRJWC9RMXAvNTANCi9tUCtKZC95cVh5NS93QXRONS93Y1gvVlBId1F2K2hyVC96cC9NZjhTNy9sVXZsei9scHZQK0RpL3dDcWVQZ2hmOURXbi9uVCtZLzQNCmwzL0twZkxuL0xUZWY4SEYvd0JVOGZCQy93Q2hyVC96cC9NZjhTNy9BSlZMNWMvNWFiei9BSU9ML3FuajRJWC9BRU5hZitkUDVqL2kNClhmOEFLcGZMbi9MVGVmOEFCeGY5VThmQkMvNkd0UDhBenAvTWY4UzcvbFV2bHovbHB2UCtEaS82cDQrQ0YvME5hZjhBblQrWS93Q0oNCmQveXFYeTUveTAzbi9CeGY5VThmQkMvNkd0UC9BRHAvTWY4QUV0eC9sUDVhVjFacHJ0d0RVb3p4MFBzZU1ZUDQ0K0NHVWZaclRnODUNCm40ajlUTHJLeXRiSzFqdGJTSVEyOFFwSEd2UURMUUtkNWl4Unh4RVlpb2hXd3Rqc1ZkaXJzVmVUZm0xL3lrZHQvd0F3YWY4QUoyVE0NClhOemVIOXBmOFlqL0FGQjk4a3I4Z2l5UG1pMUYwRVAyL1FFbjJmVzRuaFg2ZW52a2NmMU9IMkx3Zm1ZOFhuVjkvUmsya3Q1cWVUVmgNCjVvRW42SUVFbjFqNndBSStZK3g2UDhPUDY2WlpIaTM0dVR0OU1kU1RrL00zNFhDYjR1WGx3L3MrOUxmSmQzWldubGZYWjc2Myt0V2cNCmUyV2VIdVZkK0JJOXh5cVBmSTR6VVRiaWRrNVlRMDJXVXh4UnVOajNtbS9OOXBZMnZsRFNZN0NmNnpaTmNUU1c4dmZnOVRSdjhwZWgNCnd6QUVSU2UxTVVJYVhHSUhpaHhTSVB2VGJ5enBkbFlhSEJZWDh0ckcrdHF6M3NjOHF4emlGMUt3Q05EdVR5Mzlqa29BQVVlcm5kbjYNCmVHUENJVE1RYzMxV2FsWDhOQkkvSjJublQvTnQ1WlhBVDlJMjBNNldBazJCdUJUZ1JYeFdwR1FnS2s2M3N2QjRXcWxDVmVKR011Ry8NCjUzVDdGSFdIOC9Ob2MvNldXWDlIZXN2cW1ZSUdEMTJwWDQrUEx3MjhNRXVLdDJHcU91T0UrTGZoMzFyKzJ2c1lwbGJwSDBmbWUrc3UNCnhWMkt1eFYyS3V4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMkt1eFYyS3V4VjJLdXhWMkt1eFYyS3ZKdnphLzVTTzIvd0NZTlA4QWs3Sm0NCkxtNXZEKzB2K01SL3FENzVNUjAreW52YjJHMGdwNnN6QlZKMkE4V0o3QlJ1Y3JBc3Vqd1lwWkppTWVaVHkvMHpYbXNaanFtcWNZYmENClo0SVlMbWFWL1VlSlF4OUphTUtjU0tIYXUyVElOYmwyT2JUNStBK0xrMmlTQUpTTzVIZHpSU2VTdFpqUnJTUFVGK3JUaG1uaWpGd1ENCnpRY0RReEtsWktlb0tGUWQ4UGhudmJoMlRtQTRSUDB5NWdjWDhOZEszNTlMUTMrRk5TbHNRa04vSExHcGVZV1JNcU1FV1kyN3lpTjENClViTW0vd0MxVEJ3R21yK1RjaGhRbUNOenc3aitMaE1xSTh0K3JWMzVmMUM2ZU9XVFZGdkwyUzVheFZITXpQNmtSSE1GM1dnVkZZTlcNCnRLWW1KUFZHVFJaSmtFNU9LWmx3ZnhYWTU3a2NoZHJ2OE02MWZYYzdYRjhyYWtrN1c4SW1lUnBKbmpRT09FaEREZEtjZVJHUEFTbisNClQ4MlNSTXAvdk9LaFpOeUlGN0gzY3JhMW5Sdk1QNksrdDMyby9XeGJyRkpOWnZNOGtrQ3o3UnNRM3c3K3h4bEUxdVYxV2x6K0Z4VG4NCnhjTkV4NGlUSGk1TWF5dDFENlB6UGZXWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXE3RlhZcTdGWFlxN0ZYWXENCjdGWGsvd0NiU3QvaUcyYW53bTBRQTlxaVdTdjY4eHMzTjRqMm1INytQOVQ5SlloWTZoZVdNeG5zNVRETVZaQkt0T1FEQ2g0bnFwcDMNCkcrVkEwNkxEbm5qUEZBMFVkSjVxOHdTUXlReTNqU0pLQUpPYW94UEZPSExrVjVjdUd4YXRUM3c4WmNpWGFXY2dneXNIM2QxZk91dk4NCmQvaTN6QVg1dmRDUnFNUDNrVVVnQ3Z4NUtBNk1BcDREYnBqeGxQOEFLZWU3TXI5NEI1KzhjdHVTMXZOT3VtMityQzRDUTE1Y1k0b2sNCi93QjJHV2xWUUhqek5lUFRIaktEMmptNGVHOXZJQWRiNkRsZlRrcFJlWU5ZaVpuaXVDanRKTk56VlVEQ1M0VUxJeXNCVlNRbzZkTzINClBFV0VkYmxpYkVxTms5T2N0aWZMOFVyanpmNWpDTXYxd2t1S05JVWpNcCtIaFgxQ3ZPdkhhdGE0OFpiUjJwcUtyaSt3WDNjNnZrbzMNClBtTFdyblRrMDZlNlo3T01LRmpJV3RFK3lHWURrd1h0VTRtUnFtdkpyczA4WXh5bGNCK2o3VXVWV1pncWlyTWFBRHFTY2k0Z0Z2bzcNCk05OVpkaXJzVmRpcnNWZGlyc1ZkaXJzVmRpcnNWZGlyc1ZkaXJzVmRpcnNWZGlyc1ZkaXJzVmRpcnNWWWIrWmYrSHYwYkQra3VmMXkNCnArcGVqVDFPM0t0ZHVIU3Y0WlZscXQzUWR2OEFnZUdQRXZqL0FJYTUvd0JqeUkwcnQwekZlRmRpcnNWZGlyc1ZkaXJzVlpSNUIvdzcNCittb1AwcHorc2N4OVVyVDBmVS9aNTk2MTZkc3N4MWU3dU94ZnkvakR4TDRyOVBkZm0vL1o8L3htcEdJbWc6aW1hZ2U+DQoJCQkJCTwvcmRmOmxpPg0KCQkJCTwvcmRmOkFsdD4NCgkJCTwveG1wOlRodW1ibmFpbHM+DQoJCQk8eG1wTU06SW5zdGFuY2VJRD54bXAuaWlkOmMxMzY1ZDA2LWJlYTctMjE0Zi1iMWE1LWUwNjEzZWUwNWJiZTwveG1wTU06SW5zdGFuY2VJRD4NCgkJCTx4bXBNTTpEb2N1bWVudElEPnhtcC5kaWQ6YzEzNjVkMDYtYmVhNy0yMTRmLWIxYTUtZTA2MTNlZTA1YmJlPC94bXBNTTpEb2N1bWVudElEPg0KCQkJPHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD51dWlkOjVEMjA4OTI0OTNCRkRCMTE5MTRBODU5MEQzMTUwOEM4PC94bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ+DQoJCQk8eG1wTU06UmVuZGl0aW9uQ2xhc3M+cHJvb2Y6cGRmPC94bXBNTTpSZW5kaXRpb25DbGFzcz4NCgkJCTx4bXBNTTpEZXJpdmVkRnJvbSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+DQoJCQkJPHN0UmVmOmluc3RhbmNlSUQ+eG1wLmlpZDpkOTVmNjU4Yi1hNTM1LWU2NGMtODY4Yi04NGM4MzA3ZmMxNTg8L3N0UmVmOmluc3RhbmNlSUQ+DQoJCQkJPHN0UmVmOmRvY3VtZW50SUQ+eG1wLmRpZDpkOTVmNjU4Yi1hNTM1LWU2NGMtODY4Yi04NGM4MzA3ZmMxNTg8L3N0UmVmOmRvY3VtZW50SUQ+DQoJCQkJPHN0UmVmOm9yaWdpbmFsRG9jdW1lbnRJRD51dWlkOjVEMjA4OTI0OTNCRkRCMTE5MTRBODU5MEQzMTUwOEM4PC9zdFJlZjpvcmlnaW5hbERvY3VtZW50SUQ+DQoJCQkJPHN0UmVmOnJlbmRpdGlvbkNsYXNzPnByb29mOnBkZjwvc3RSZWY6cmVuZGl0aW9uQ2xhc3M+DQoJCQk8L3htcE1NOkRlcml2ZWRGcm9tPg0KCQkJPHhtcE1NOkhpc3Rvcnk+DQoJCQkJPHJkZjpTZXE+DQoJCQkJCTxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPg0KCQkJCQkJPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPg0KCQkJCQkJPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDoyZjVkMzI4MS0zNTQ4LWM1NDgtYmVhNS02MjQ1Mzk3ZWM4MTY8L3N0RXZ0Omluc3RhbmNlSUQ+DQoJCQkJCQk8c3RFdnQ6d2hlbj4yMDIxLTA4LTI2VDE0OjIzOjUyLTA1OjAwPC9zdEV2dDp3aGVuPg0KCQkJCQkJPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgSWxsdXN0cmF0b3IgMjUuMiAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+DQoJCQkJCQk8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPg0KCQkJCQk8L3JkZjpsaT4NCgkJCQkJPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+DQoJCQkJCQk8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+DQoJCQkJCQk8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOjNiNzUwNjJhLTdjNzctMGY0Mi05MGMzLTVjNGM5ZjUyYWJkYzwvc3RFdnQ6aW5zdGFuY2VJRD4NCgkJCQkJCTxzdEV2dDp3aGVuPjIwMjEtMDktMDFUMTE6MjA6MzUtMDU6MDA8L3N0RXZ0OndoZW4+DQoJCQkJCQk8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBJbGx1c3RyYXRvciAyNS4yIChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4NCgkJCQkJCTxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+DQoJCQkJCTwvcmRmOmxpPg0KCQkJCQk8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4NCgkJCQkJCTxzdEV2dDphY3Rpb24+Y29udmVydGVkPC9zdEV2dDphY3Rpb24+DQoJCQkJCQk8c3RFdnQ6cGFyYW1ldGVycz5mcm9tIGFwcGxpY2F0aW9uL3Bvc3RzY3JpcHQgdG8gYXBwbGljYXRpb24vdm5kLmFkb2JlLmlsbHVzdHJhdG9yPC9zdEV2dDpwYXJhbWV0ZXJzPg0KCQkJCQk8L3JkZjpsaT4NCgkJCQkJPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+DQoJCQkJCQk8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+DQoJCQkJCQk8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOjM0ZTYyMGM0LWY1YTEtMzI0My1hODYzLTc0NDQ1MWIyZTk5MDwvc3RFdnQ6aW5zdGFuY2VJRD4NCgkJCQkJCTxzdEV2dDp3aGVuPjIwMjEtMDktMDNUMTk6NDA6MzAtMDU6MDA8L3N0RXZ0OndoZW4+DQoJCQkJCQk8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBJbGx1c3RyYXRvciAyNS4yIChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4NCgkJCQkJCTxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+DQoJCQkJCTwvcmRmOmxpPg0KCQkJCQk8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4NCgkJCQkJCTxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4NCgkJCQkJCTxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6YzEzNjVkMDYtYmVhNy0yMTRmLWIxYTUtZTA2MTNlZTA1YmJlPC9zdEV2dDppbnN0YW5jZUlEPg0KCQkJCQkJPHN0RXZ0OndoZW4+MjAyMS0wOS0wM1QxOTo1MTo0Ni0wNTowMDwvc3RFdnQ6d2hlbj4NCgkJCQkJCTxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIElsbHVzdHJhdG9yIDI1LjIgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50Pg0KCQkJCQkJPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4NCgkJCQkJPC9yZGY6bGk+DQoJCQkJPC9yZGY6U2VxPg0KCQkJPC94bXBNTTpIaXN0b3J5Pg0KCQkJPGlsbHVzdHJhdG9yOlN0YXJ0dXBQcm9maWxlPlByaW50PC9pbGx1c3RyYXRvcjpTdGFydHVwUHJvZmlsZT4NCgkJCTxpbGx1c3RyYXRvcjpDcmVhdG9yU3ViVG9vbD5BZG9iZSBJbGx1c3RyYXRvcjwvaWxsdXN0cmF0b3I6Q3JlYXRvclN1YlRvb2w+DQoJCQk8cGRmOlByb2R1Y2VyPkFkb2JlIFBERiBsaWJyYXJ5IDE1LjAwPC9wZGY6UHJvZHVjZXI+DQoJCTwvcmRmOkRlc2NyaXB0aW9uPg0KCTwvcmRmOlJERj4NCjwveDp4bXBtZXRhPg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgIDw/eHBhY2tldCBlbmQ9J3cnPz7/4gxYSUNDX1BST0ZJTEUAAQEAAAxITGlubwIQAABtbnRyUkdCIFhZWiAHzgACAAkABgAxAABhY3NwTVNGVAAAAABJRUMgc1JHQgAAAAAAAAAAAAAAAAAA9tYAAQAAAADTLUhQICAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABFjcHJ0AAABUAAAADNkZXNjAAABhAAAAGx3dHB0AAAB8AAAABRia3B0AAACBAAAABRyWFlaAAACGAAAABRnWFlaAAACLAAAABRiWFlaAAACQAAAABRkbW5kAAACVAAAAHBkbWRkAAACxAAAAIh2dWVkAAADTAAAAIZ2aWV3AAAD1AAAACRsdW1pAAAD+AAAABRtZWFzAAAEDAAAACR0ZWNoAAAEMAAAAAxyVFJDAAAEPAAACAxnVFJDAAAEPAAACAxiVFJDAAAEPAAACAx0ZXh0AAAAAENvcHlyaWdodCAoYykgMTk5OCBIZXdsZXR0LVBhY2thcmQgQ29tcGFueQAAZGVzYwAAAAAA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ooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooADzV7w74n1PwhqS3mk6hfaZdx/dmtZ2hkX/gSkGqNFC02Kp1JQkpwdmtmtz6n/AGd/+CnfiPwVeW+n+OFbxFo5IQ3iKFvrYep6LKB6HDf7XY/d/gfxxpPxG8LWmtaJfQalpt8m+GeI5VucEHuCDkEHBBBBr8aTzXu/7CP7VNx+z98R4tN1K4b/AIRLXplivUY5WzkOFW5X0xwGx1XsSq16mDzCUZclR3Xfsfvnhp4wYzCYmGW53UdSjJpKcneUG9rvrHvfVbp2Vj9Nd1FNDAj/AOvRX0B/XR+UP7Y3xKk+Kn7SPirUWk329teNYWmD8ohgPlqR7NtL/VzXmSjFT6hfNqeoXFzJ/rLiRpW+rHJ/nUNfGzk5Scn1P81s1x9THY2rjKrvKpKUn/282woooqTgCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKRqWgjNAH6Wfsp/tT6Pqn7PPhVtcvmXVILT7LPnkv5LtErE55LKikn1NFfnHYeLdS0y0WC3umjhTO1Qo4yc+nvRXqQzSUYpWP6Cy3x4xmGwlLDToqThGMW3u2kld67u1zOWlq1rkC22uXsca7Y453VQOwDECqteWfz/KPLJxfQKKKKCQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAHSigdKKALniP/AJGHUP8Ar5k/9CNU6ueI/wDkYdQ/6+ZP/QjVOg0rfxJerCiiigzCiiigAooooAKKKKACiiigAooJxRmgAorvvAX7LfxD+Jsccmi+ENauYJQCk8kP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X5lJSa2T0fds/Uj4bfsx/Df9nJJdX0vR9P02aBCZNSvpzJJCpGDiSViIwQcHbtBzXzj+3N+39p/iLw7e+C/At0bqK8Bh1LVo+I2jPDQw923dGfpjIGc5HyH4h8c654uVBq2sarqgj+6Lu7kn2/TcTWWKVbMLw9nSjyoniLxglXy+WU5DhY4SjJNS5bXae6XKoqN9m9W+6ADFFFFecfioUUUUAFFFFABSH7wpaCOaAAdKKB0oqLAbXxJ/5KP4g/7CNx/6NasWtr4k/wDJR/EH/YRuP/RrVi1rLdnVjf8AeKn+J/mFFFFScoUUUUAFFFFABRRRQAUUUUAFIwzS0UAJg0YpaKAE28UYpaKAExzSgYoooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAAdKKB0ooA2viT/wAlH8Qf9hG4/wDRrVi1tfEn/ko3iD/sI3H/AKNasWqluzqxv+8VP8T/ADCiiipOUKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooJxQADpRSA8UUrgegftVeD5PAf7R3jTTZFZQuqzXEQIx+6lbzY/wDxx1rgAc19uf8ABVH9nma+hsfiHpduZPsqLY6uqLyqZ/dTH2BJQn3j7ZI+IlrpxVJ06riz7TxB4fq5Nn2Iwk1aLk5QfeEndW9PhfmmLRRRXOfFhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFIxxSk4roPhZ8NNU+MPxA0vw7o8JlvtTmEYODthXq0jeiquWJ9B60KLbsjbD4erXqxoUYuUpNJJbtvRJebPQPhb+xN4u+LXgSx8Q6XC7WOoeZ5REec7JGjPcd1NFfph8PPA9j8MvA2k+H9NXbZaPapbRZHzOFGCx/2mOSfcmivejlVOyvuf11gfAXJFhqf1uU/acq5rS05rLmt5X2NTV9Jtte0u4sby3hurO8iaGeGVQySowwysDwQQSCD61+eX7X3/BPrWPhJql1rvhG1uNX8KyEyNBFmW50vuVZerxjs4yQPvdNzfoqaK7cThYVo2lv3P0TjTgXLuJcKqGMXLOPwTXxRf6p9U9+lnZn4p5or9LP2wf2ZvAeveBNX8Rz+GrBdbiXf9rty9uzsTyziNlDn3YE1+a97EsN9OijCpIygegBr53FYWVGXK3c/jPjngXFcM4uOGxFSNRSV4uN1pe2qa0fkm/UjooormPhwooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAozQxwK92/YU+Dnhv4wfEVLLxHpv9pWu5v3Znli6KCOUZTVU6bnNQR62R5PWzXHU8Bh2lKbsnK9vnZN/geVfDL4T+IfjH4lj0nw3pd1ql45G7y1wkIPG53PyovuxAr9Iv2P/ANj3TP2Y/DbTzNDqPirUI9t9fAHbGuc+TFnkICASeCxAJxgAeo+C/AWi/DjQ49N0HS7HSLGM5ENrCsak+px1Y+pyTWuDzX0WEy+NL3pav8j+w/D/AMI8Fw9UWNxMvbYjo7WjDvyre/TmettktbhXJopaK9A/Xz//2Q==";
                        }
                        $codOnac = $this->codigoOnac;
                        $infoOnac = <<<EOF
                <br><br><img src="$logoOnac" style="width: 87.5px;font-family: "Trebuchet MS", sans-serif;"><br><label style="font-size:7px;"><font face="trebuc">ISO/ IEC 17020:2012<br>$codOnac</font></label>
EOF;
                        $data['infoOnac'] = $infoOnac;
                    } else {
                        $data['infoOnac'] = '';
                    }
                }
            }

            $data['titulo'] = 'FORMATO UNIFORME DE RESULTADOS - FUR';
            $data['consecutivo'] = "<strong>FUR N°: </strong>$cons";
            $data['fur_aso'] = $cons;

            if ($this->logoColorSuper == "0") {
//Monocromatica
                $data['logoSuper'] = '<img style="width: 99.213px;height: 42.52px" src="@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"  />';
            } else {
//Color
                $data['logoSuper'] = '<img style="width: 99.213px;height: 42.52px" src="@iVBORw0KGgoAAAANSUhEUgAAAa4AAACCCAIAAACRjrs0AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAADaASURBVHhe7X2HWxvH+rX/t+86PU6c3PTiJDe/mxsnuOPeu40Lrrj3ik0vpoveexFFovcmEF2A8Hdghskwu1qthIQFzHnOw8PMzhbtzpx536lr3i4T2GemB8d6my215R3ZmQ1RMcaHoeU3XpddCy69DIaUXQ+vuBltvJ9WH17WntlkqUZinEJPlpCQkNCE70qhZayntD0zsuru1cxde2O/9gv919+h/88l+oWuxYmX07dDJYvb0vpHu+ilJSQkJBbCt6Swd6Q92fQqKHvfnph/C7rmEe6O/vJ61p64mqcd1kZ6SwkJCQlfkMKZmZnG/urIyjvHE38TlEuD2yI+hsV34M13h+J+OBT3I/7ZG/PVjsh1QjINHon/GS62ua/CPmOnjyIhIbFa8S6lsHekI6ziJhRNECmeW8I/OJuy8UVJYFJdcEFLcl1PSfdQy/jkKE6Hhk7bp2zTE7ap8Sn7JIKIxP89w211PaVInFT3Mrj0yoXUTVsjPhIuy3NPzJcvSy51WJvmHkpCQmI14h1IITSrqisPXrCj5r9jCb88K76Q2RDdMlAHsRu1DVV3FxrMIeEVNx8WnLqcseN44q/+UZ8JZ4HQTZiH51P97uYdeVV2NbH2RVGrAcpon5luG6zPaox9XnzhZNLvwlmMuHJpe6Y0EiUkViGWVAphshlMIfBMBQ0CIYsw3yBeMPpg4tX2lCTUvoCiwf8VUrpB6OaVDH/44FC6oQlL/2gXHiMwfZtf6FohJQgxxa1HbcP0oSUkJFYBlkgKYZdlNkSp+sJQQNhrUCioT35LIuRve+QnQhoPEpoLsxGaC2txZGIwpynuauZOIQ24M+pzuOST0zb6AyQkJFY0lkIKS9sz4PMKWrM14qOnRedaB80TU2MZDVGX07dvClOx0bxKONqRVXe7h1u7hlqCS68oJRgWYl5zImmFlJCQWMHwrhQ29lfDBBP0BT5vcl0wbMA5Abq8I/JTIcHS83rW7srOvDHbSKo57GjCBuHo6eQ/anqK6U+SkJBYifCWFMK1DKu4KXSM7I7+ItUcOjU9Wd6RdSXDnz/kCzwS/3OK6fWIbQhe8/7Yb4SjsGFJz7WEhMTKg1ekEMbgscRfeR3ZFvFxdNV9SAnMqzMpf/KHfI07o9cnm16NTY4k1D4XBioejPu+rqeE/kgJCYkVBA9L4ZR9MqLyttAz+6z4vHW8v3XApNpB4ZuEF1/Qkjw8Mfi67JrQiPmq9KpteoL+YAkJiRUBT0ph/2jX6eQ/eNU48OY7mIHWccvDgpN8/HLhmZT/mXrLmyyikXs0YUPnUDP92RISEssfHpNCc1/F7ugveb14VnwBHnFhawpcTj5+2fFV2dUR21BIeRDf9Anfuaorj/54CQmJZQ7PSGF2Y+zmsPeZTOyP/cbYVTA0MXAn9zCLXNY8krChoa/S3Ft+OP5HFukXuja5Lpi+AgkJieWMxUqhfWb6VelVpg7gxbQtEMHS9ozd0V/w8cudEL7wilvDE4M3svfx8U+KAqbsk/R1SEhILE8sSgqn7VO3cg7yugCneHLaFlF5m49cSTyX6jcw1hdecZOPvJS+zTY1Tl+KhITEMoT7UjingweYHGwKW5tqDhufHBWMppXH/W++bR0w5TUnbAn/gEUGSjWUkFjOcFMKBR3cEflpTU9xz3D78YU9rSuV2yI+LmlLb+iv4nuKpG0oIbF84Y4UQgdvLtDBdY39xoa+yp1Rn7PI1cD4mqed1qY9Mf+o4eX07XLIoYTEcoTLUmifsfPtg3M6WF3fV+nV5WR8ljHGBx3WJt42vJyxQ/aiSEgsO7gshXyPgX/UuiZLtbmvYnXqIGGM8aGgho8Lz9KXJSEhsUzgmhTmNsezAg97sNlSY+4t3xbxMYtcnYw1PuqwNvJqmGx6RV+ZhITEcoALUtjQV8n6TP1C1xq78uEaSx0kjKt+grex8P0U0BcnISHh89Arhf2jXXz/QIrptWW020tbdC5TFram5DUnsqB/1LruoRb6+iQkJHwbuqRw2j59NmUjK+RPi87ZpsaFlRckt4Z/2GypCePaUo8mbJDDayQklgV0SWFs9SNWvC+mbZmanlwxk4s9y32xXw+M9QZl7WUxwaVX6EuUkJDwYTiXwtYB06aw90jBho88NDEQY3zAirqkwHOGv4fGBw7H/8Ri6npK6auUkJDwVTiRwin75Alu4+Cy9szanhIWlFRlcOllc18FW9HrUNwPE1Nj9IVKSEj4JJxIYUTlHVbCHxWcHp8cPRj3PYuRdERUGCHlQSz4oiSQvlAJCQmfhJYUwjVmS/Pvj/1m1Db8rPg8K96SGkSFMTwxyG+eB3Gkr1VCQsL3oCWF/KZ0xq6Cqq48FpR0yufFFxr7jawuOZ38h9xPWULCZ+FQCo1d+axUP8g/OTY5vC/2axYjqYeoP16XXWPBgpYk+nIlJCR8DOpSaJ+xn0r6P1KAt4R/0D/aJSxWKqmHRxI2WMctbAfRQ3E/yJUaJCR8E+pSmNsUx8pzaHmQZbSbX6ZUUj8z6iPja56xoMEUQl+xhISEL0FFCienbfvffEuKrn/UZ6O2oUcFp1lhlnSJe2L+PTwxwNoWdkWvH5scoS9aQkLCZ6AihTlNb1hJTqp72Tpo5je9lHSVb6ofZzXGsmBC7XP6oj2BolYDvhcN6AbM/Miqu7woT0yNRVfd7x5upWFNwM2v6SkOr7h1IXXTscRfwQDDxseFZ6q7C+0z0zSRAr0j7VFV91RrgoqO7MyGKBpYNOwzdrzzZkstDTtDaXtmSNn112XXGBGMqLxjMIe0DNThajSd6xixWUvbM16VXg0w/HUs4RfwTMr/nhYF5LckDo710kQSvgEVKWSTi2HRwEK8mrmLFWNJN7gj8lPreD8bWHPgzXeLKV0CTs4NgO8ZbqNhfXiQP7tDv6m3jIYhRp05iImsvEPDDjBtn0qqC+aXK98S/sHW8A9ZEHkGUjI0MUBP4BBjfIgE0B0a5kCG8dPAooHqAVc7k/InDTvD8cTfyMOrckfkuvv5JzqsjTS1PgyM9T4tOrcpjI4fAPGWtkV8zFsVt3IOdFib6AkS7xqiFJr7Ktinijbeh0nIgi4RmftR4RlYQNmNscVtabAXGvur2wbrmy019X2VKHi5TXEwOVH9Xsnw59e88QYPx/+Ewg9LwWAKYUwxvQaTTa8o64JBlPM5vkysfQEmUD6Pr3kWX/N0Z/T6Q3E/xlU/IUH8g2vGVj+KNT5COY8xPiDEewPv5h1lDwBrKK0+nAXLO7Lo61408Bi4IF4jDeuAddyyOex9+OzT9n8suPKObFwnovI2DasB5ZZoFk5/WHCqoCV5ZGKQHIIx2NBXiccgTSt7Y76qU4yjxDvBoZK2dBrmQMSIBhYN2Kq4GoicRqM0AasWiUdtwzwHx/tQVeDjXkjbjKOQsJcll3Tu1pDVGEMWr9sf+014xc3KzrzxyVFyCLYFLovcQnYB2hT2Hl4LOSTxbiFK4b28Y/hC5CMhN8CYJ0E9RHZBvoGg9I100svpxtCEBV5SWMVNuBJsyvMiuTv6C1xQp9PnFPDgzqf6Qc1p2Bn4dgZo/fDEIFvrG+pPEy0aECPYZf5R6/TP7SOLa0DEaXgOTqVwbm3a2Y2tr2XuhqtLYxWYmZlBRQJr6FTyf2nUPJZGCuHS4lJH4n/GX2RmGqsJIoU0oAbYB/g5SBOYvs3pe0b+R0pIIapSCB+NVQCeQW5zPFnu90VJoBxz+s6xQAoHx3qZSY9sNGKz8r6PNmEEeWp5PuQ26Air290gnJqsxlgPjlzpH+26kb1f22gSAP+If6SCliSYFSzY5bmlDB8XnsUFM/S1tcEShD0I9UTdQ6PmoC2FeFpiuYdX3KJRmkCFofyBSyOF8EVwKXghp5P/QGaGo0oPOIZTKQTw3m7nHkKyi2mbNdSQ6ODOqM+hyDRKE5bR7sPxP+KU58UXaJTEO8ICKSStOYRwLlCtsaAGYePo/PCuonOoGWadq+4zLFM3zFINoMaGdXw//wRzc/TgwJvv+Kc6l+rXaW1iwVelV2m6RYPYQScS/0PDmihuS0ViuLc0PA8NKYTzS5qPQ8uDaJRbWAIpHJoYgMofS/gF/2fPdVU5bf0E9EghwDa8fVWm/u3wIfxC10IHWwfNNEoHINZkHaPitjQaJfEusEAKSZ4AkfVhwDtdeQG17hJs4oEKObcp7lDcD8LdVQlbTKMT0z1k1EeiRBW2ptCwDqAiER4MRFG5nL6d/A/PyIOdJ6Q9S9k8p8TFtC1I2WSppuF5aEhhen0EDl1K30bD7mIJpBBeP66Tag7D/3BOd0av3xW9XsNLJdAphQDqQtjUfqH/Qq1GozgQP6agJZmGdaOxvxrX3P/mW6ePKuE9/COFcG/xIQlhD8LFYEFV7o7+Qk/Z8xRQJ6NM7tWc/IfiyncFeAS9I+3hFTdfl12jYX2AvyM8G4hIfp8snY36ekD6TOHB0bADtFsbkCzA8BcNc3AkhahX8M5h7Cy+r9PbUohPj0fdHvkJM97hzuOyTgcb6ZdCgIyLelx4hobnUddTivjzqX407CKIX5/ZEE3DEkuOf6QwgXOHe4bbXpQEsqCSMOkto930zCXExNQY3BN+RALj4fgfR2xWms5DgGv8uPAsbNIGV2QLJoNqzw9Z+JYt0ACFpScsGqQFEFfW/ihEoPNbEmmYgyMpxA9HPBxDGl4EvC2FMNtxkeDSyzQ818KLdwIXh4YdwCUpRJW8LeLjvTFf0fA8yMxUN0xCAtLKcSvnIA1LLDn+kcJzqX74GCDyJSRAw/7aH/sNMhk97V3gauZO4ZFAEzdKzlMwmENqe0oidLQ38YBxKjwbI5QlMH0r+f9owgZ6gicQV/0E19R41LHJYZRhyLFqb5IjKYw1znY3e2T8s7elEBYZLiJ010BcEGnqLadhNbgkhQDJfsKNzqT8D5FCZ5R+2GfsO6M+94/6zIPNJhIugUqhdbwfH5IwsupuY7+RBQVujfjIS50kOkEqf4EeHJ7CAOMuuuo+TEKXesZhFwjPxhMuW7LpFQt6sB+Z9Bjsjv7CUXtTiuk17gg9ouGFcCSFlzN2IF5j9Ix+eFUKmy21uIIyG5BF1+/kHqZhNbgqhaTWyWmKo+G3b21T4/BU8Cto2C2QPpl23aO1JDwLKoWZDdH4DITNlhoUCRYUmFYfTk55J0DOVh3fU91dSFN4CBAUuMZjkyNxNU9plA7g1e2MXi88G89jCb9AVlgwofYFPdMTIFPFc5vjaZgDzPwj8T+TsaI0aiEcSSFZoMgjpopXpZD89jLVqSyJ/9FuOnBVCrMaY5AeVQsNv32Lt4qYRdbHZKyVN5wbCT2gUkhabUH4xQiSzKHkwbjvp+1T5JSlh7mvggziFwj18fgI1fCKmz3DbXAMreN6XZ66nlI2iFqDsDFROMn/17N205M9AWIZqU44I8vuagw5diSF5FE1Xi8OGcwhsHZ54jo1PcU0xTy8J4VwS2ERI3OqSnZGQxQujqeiYQVclUIyeD65LpiG5wbEIOZq5i4adgvBpVdwkaXsipTgQaWQTAMCb+cegqtF/lcS9SFJv/TIa05Q1UHQ443NKK55zYkwCcmwDD3AKSiNwoOpEmb185KL5P/d0V/S8z0E0uDb2G+k4XmQ/Ug1+qwdSSG54KhtiIYVgG/IFmTkeSFtM00xD+9JIRlD48jEnpga849ah/rS0bQ5V6WQNDVk1EfS8Nu3o7ZhxJwz/E3DbuFhwSlcRDnOSWJpMCuFyCusTza+5lllZy75X+Cu6PUeH6qiB/A+SOO3I3p2bGP/aBfZvBiapTGvgKGmu+ji3LA+nXxSFMAvVOPZDijSUvkg/yQNzwEuOb6vdkeqIyl8VXoV8dqTpvGWYDszwprGKcphJV6SQjKGZmv4hxrjB0LKruP6jipyV6WQTE4V6pUDb75DXahzkrIqDsf/uCnsPT1ZTsIbmJVCMiSKEAWbn3PC83rWHnKOqxibHMYtStrSUFBxfRQVnVPiWgdMMKCcep2qi524B5SrR4VnkB1RrnK5dnElBsd681sSyYhll3gy6Xd+kQtVaXAb0/apvTFfbQ57n/frQ+f23uOb+ZVwJIUVc/EubWyPz41TlkwKSTfa48KzNKyGnuHZyuBE0u80vBCuSiHeMBwUoaUINRwu4nabNWpEnK40pSWWDLNSmFT3Ep+BEPmYOFNKxhgfkHP0Y3La9qIkUNnRgbIKSyq66j6UsXekg42JnZmZQZ5AfjKYQwIMfwlnOeKemC8Pxn0PHor7YZ4/oo49HP/TkfifQRhEMMTILbSB30jWuUs1h7Ku2Ib+qsD0bWdS/sQzX8nwx9VQGIRn0M9NYWvHJkeZN+3qSB2nIMNfYqsfkSDsFP+oz5xOunAkhagV8Kh4w3CEaZQzLLEUEhc+MH3ro4LTGiRevOq+gy5Joam3DImVzYLEHtdWZA0k1D7H6W4UMQlPYVYK2XJSR+ZGupHVMpR0Y2kp0temhyhvcHN0Nre5R6djwmEGkuHHwxODbNw/7ER+eT6PENrKhN7jw4BgD/JrcJFP4FRwHUkhQLxL/aV0KaWw2VKDE0EYfU6JZKpjxfVLIapqMrsOxjKNmgdqGjLKvd3aQKN0A/kNeQyZX8/iERJewqwUsh2d7uUds4z1kP+VrOrKI+foB8n9hMcSfgnK3ncz58A5w996elo9Tu3hkMau/MT5dvfkumBmBJl7y4XrLJ6p5jC2So1y3sLi8bBgdmXWolYD/j+V/F+ns1AADSkcsVl3R3+BggoFp1GaWEopJF0NOhcyQA7Eq1Au1aFfCkmHCVwEGl4IVKU4Cu/BpfY++4ydrHmjZ+UICe9hVgrhPeFLgGEVN02OS74brVpwfgWTCu4hvFftwXfe4NaIjzQaKKGSoeU3yP9DExa+4xhevHCpxfNV6VXWKAFrxePrRzRZqnFluPNEx29rDjAm0JBCoLQ9EyKyI/LTxn7n/ZtLJoX4UrB/98d+o7M3j6yeyz40g04pJGNvkXU1VsBEZY802gt58cCnv59/AqecSPpduwVDwttYA4HAlyA0mEJU53IQqs5ddQqYmU+KAvyjVMZb8LyVcxBOB8wxmI1OE7tBjRF88GhgozE9elP9mLVdArBhhUstnnfzjhS0JLGgo2HPiwFxwMlssLqeUhrrGNpSCOQ1J0K1d0Suc5oNSGsaHEkanofHpZCsQRtX/YSGnQHyBDX3j/pMaPd0KoUQKZhsSAOjQXvtXqQkG2DANnS6gAXcYdIuDx1U3QJBYimxBv4CPgYhHA2IEQsKTOLGlLoKeAFQnOzG2OfFF+CysQVieaKkXUzbgqob3lxFZw5r6vYIHQ246R5qeVZ8gZkV1vF+fnrJqG0Y1pBwqcUTMkEmhBHq35BIPyBY5OKOuk0FOJVCIK85gTTmwuop78gSDJ9p+5SxK/9x4VnSKqfcY8CzUjjbV07XoHVBRMgwZn5IIOBICmdmZloHzbDfYXgiwZH4n/W0A0INb2TvR3pk8qdFAea+CqGvGWUBXkh4xU0yThaiyfZFkHiHWMNvZlLfVxkyN/BClffzj9OTFg3b9ERdT0ms8dHljB1wXYUbQX0upW/LaIh6XXZNOOQ2VbfpQUZHZuUdE7jDfNFCuRWu4xEeivuxi1sSTdkGv3jA2N8T829cXOe6T/NS6KS5Co99JcOfPDYZBnAv7xgyBoxuVm/B8oJ7oZygQqSwtD2DhjmQOS2oCJ0SGaZ1wIRTyBq0qC/JFXSCrEQnLHNLpDAwfSsjbhRg2MjadmAMJtQ+d8mBRbVBBBREDr+cvn3uRZ3Ai4JZSuLxD3RWp3cv4W2sIVmKsH+0iwwfVeX+N9/SkzwK1JkNfZXxNU9RxrzUg7xvbjahABT+0PIgvsaGXyw0JLFpIZ7l1vAPxyZHWNBLq9Sl10fA/NQ56Ld3pP1k0u86RdnUW44qk00fJIQywjeEUe9odZaqrjyYqKoNbXjtqB64sVAOCdOMLDMDsxq2pBuLF+CbIpPTwBxQ4wp3Pxz/Iy4O2Y0xPsSNXBJBBpyV15z4uPCMsATy7ugv7+YdhWU6NjlMk0r4ANakmsPYR4IpgSqRBZV0Y6CAS4AYFbUakFNhWQi3BmEt3s49rOpca/ORYqFNgzkEhZYG5gFbRljNiew74Q3C9WbzCGEd0/stN4zYrD3DbVC3wfE+9/RilQDVA94SbFK5/bHPYg3XlbkW4TMpf5KgKp8VnyeneRsQ5cLWlNPzOzITBmXthTcBH420xehnQUsSve5cXR1ecUs5KwAOHbwY3q3jl5DxOGGAEwcW1FgpQEJCYmmwhmykC8I5RZiNMVTlXCu1m4tTuge4bKRNnRDiSCxTeFtkL0o9ZM1/TZbqhwUnVVcJbBusF1rKyIomXiJ0lrUlLXL7JAkJicVjDRmRAMJfQ5jXHVW+LLlEzlwywBLkO3Mgx8S3HRjrJdsbaZMsQ2CbGscvTTa9cjSIL8X0WhiDDWdcuJQHCV+JbYnnaAc1CQmJJYPLUgi+k10K4S+zxjUQxh1c3YrOnM1h77NIVUJoYN89K77QM6y1FLOwEa19xs56+rxBSCFrTZdSKCHxzuGag0y4PfITb/efqKLZUsOv7KBzTQSc4nSMMUxFYRycxpYGHmHvSMe++d1jlPMfJCQklhhr2L7vm8JIt8ns/ASn3BH5qTdGwzlFXjMdOewSg7L3CcNcBTRZqovbUmlgDmQ1UO/RMtbDlr3w+OI0EhISrmJNRn0kK5+2qXGyrY8e+oX+K8b4YOmHUMCdFJ5ED+/lHVMO+mVIqH0uLFhw0fWFCF3iyhhMIyGxYrCmrD2Tlc+e4XayqIl+7n/zbUZDlMZKBx6HbXqCrR/BeDZlY2ZDlOqmoIyRVXfpJRTAr6b/zWFiakx1I2NPcWvER2TNAkIvDbGWkJDQjzWN/bOrmBCaesvJztauEoIIR1tjBwzPAtYof3fYp2Qa7/DEoPYIG36AIY+HBafof3OA7y+c6Fkejv+p09rEghWdOfTGnsC0fcrcWw5jP7T8RnDpFfzNaYrT7jJaPWgbrC/vyEL1r5+VnXly9PhqwBo4hqxMFrUayIps7hHGzqOC0ybv714IyRNWxmbroPD+vpLwSXuG20hKhpGJQWEZArKhh/cYmL61uruQBcmk2sUDxmxYxU1HC6BdSNv8bjewfueo76skS0W4SmGinsSKxBoYEeyTQwehhizoNo/E/wxvtMlSrdE8t0go50qjAke8fWb6ZNLvwiGeAYa/hAnwdT2lZJVTBjI/33u8n388tzmeBT0yar110Hwozsk0QXj9Je9iIJSPoLQ9Q3ghOnkxbQu9hITPAL5UfM1TD874mF26lTmV8I49u2jz3tivn5dcrOrK83hjYnp9hHCvsykbyaFSrvVTlTiXpCSAIcmvEaCxjrenCKeVjWHaHPb+4isMZAg2jQ+Gz43s/cmmVzA8ofLFbWkhZdeZqbgpbG35u+j69wXgPRvMITD5g0uv8CQrDIK7otcLh0B8LKUnIfFu0TnUTD7ZTbUdGtzD3IL+yf8l19XeBHkx3B75CS6eXBfc0FfpkZYXsj6owJruIviAUDrtRQb3xnzFL94ZbbzPixHZ8NurxBM+L75A/j/w5jt640WALCwKQvKaLTU0lsPIxOCNuQWWSRq5QB4PskMTCH+CRkn4Nthyn2dS/qRRi8asFD4qOE2uS4olmxDmJcIOOp/qF1V1z+mGGxpoH6wXLusS+WVohdUQ4L0KiT1OqBVb9kJjeW39OJbwC7laqjmURimAGujEfNOBMIhylUNK4bKDt6QQ5YdcF4RVSDadWQJuCnsPxpHOXSAELNKNhdyzychwgsg/AMxD/as8uEfUBONTo2y+IGxSeu9FgK1pprpCLQPcisD0bdez9gxLq5CDlMJlB29JIZxWcl2wqisvbr4Za2mouh+jU5AttBdD1oEQXHqZ/APAvxaSeZxnUzaSbZgIPTJpZ+/8HD63tyTvsDbhXPuMnYbVALuysjNP57icUdtweUd2ZkNUVmNMXU+pq60iqCCNXQWZDdEgbsrvNqMEKjBTbxm/l0jrgCmjISqx9kVafbjTfnM9UoiLNPYbaeDt276RzqzG2KS6lwZzSE13kaPWXsS3Wxtym+PxMLiLcrM9JVBDo5zzKeFD4F64UU5THH6jo3spgZeQ35KIF5jXnIiPq/8T4Batg+bcpjg8dmFrCsoaPaAbOKWo1YCvn9P0BvKiZ6VuFAqQBuaWyEPOIW+4rqdE+NVuSOHsjxowkR+l+i1mpdA2PcHWQ31T/RhZkPyvn7ujvzye+Nu5VL8rGf7465KLvSfmS/IoLgE5TLiOqyQNrsgf/BRgsjO3V/my5BLKJwtaxz3QBcZaHvH+9RcVBuR7MqQ8rOImjVID2S97a8RHMMlplBrMveWB6VuFYStbwz+8n3+id6SDJnKMtsH6oKy9wgK9fqFrb2Tv61ZbXQ2IMT5EGvyExv5qFBtUNvy54IuSQJpUDU6lkHXEQYw6rU1B862ujBfTtgil3TY1HmN8oNxS/HD8T9AmjSrnaVEAkuElo6xmN8YiPX86iMKFeI2vjIsn1wUry+D2yE+eFZ/X3goGNVB01X2lY3QkYQPkWLumBKDjEBrlM2+L+PhJUYDGLs9sdzn80z5Yfy1zNzuXEBmb3B1lB8bT+VQ/Eu8fte5G9n7wdu5hR8P4UI9GVN5W7mZ+NGEDP3RkVgoBZAJyGAIxYrOy1BrcG/PV67JrMGpUN2xDVkBFiq+Ot4+syS8qwxOfBz+enuMKVLtNwKuZO0va0lEa8eOFQwJROPHhLaPdqHnoRd++1T/v0G2iOD0uPEP+99QeCahCWU/RndzD2lKlBJvZfTl9O41SA8oDSQa5oVEKRFbdJWlUiXeu3UwJQ0BjlfK5lTVUbk00GoTvr9pjdjDue5pUDU6lkPlJkDzVrcdwU35+Qc9w26G4H4Q0PC+lb3PULsRKIuvMVOXzkouqaohIspuoI0KdeeOLB+x97fFYgenbNMzzscnhC3P75TsiCrsjryWq6h5JgzejqhWo5/DGcHchnuc5w9/0chzwLbQts1eldF0oKoWkLgJ3Rq+HtLNGfVVezdwF3wfiDTOYtbg5BUQHrgR0CkY7cjxqD7wXt7d3gNksPBUhW06xojNHOKQkHqbZUpvXnEBOgXXspc1VeKK2PxL/M/n/du4hcuvFgy2rQXg6+Y+IyjtwT+BKOFVGvAFyFjIijVIDe2xck0YtBF/9oEjHVT9BPilpSwspD4LtT+KhdI7UkD0GCFGDX4naFE4N/Cxm6KE4KTfVvJt3hJ0I4lcge9T3VaLMIxsYTCHaCyk5l8LqJ+zi4Imk3+E8weuH64o8DDuFfyEQFLbm0Oaw9x/kn4RThsfIqI/kzcnZbWcW7kFKwLq2CFH9xNc8g7mAhwwuvczba3gGeg6H4rY0chTaASsEORxPWNaeCXufnQuZpqk5wGBnawmjFDwqPIPPgcfGr2ODjUD4fKoijsgAwz/GOEwKvHacjidHkWTb+eLKqplHqEGRe+NrnsLcwRuGk4pnIDttQOhhXULa2G/BG8Zjg/hRsFvJ1RigObA2SEq8ydjqRw39VXA7KjtzHxacZJVuQu0LJKZSyG/LiyfQWJdlb+zXxxJ/Rc4+k/K/C2mbjyX8giAyHx5dj/vjKbB91wTC4CIJoHHCISXhV1Z25rGaytiVLyTwOE8l/R8/5S7F9Jrc2iPAR4RSsIvzxMdCfhq1qVc8HpFCvEZyFIQZJdgsY5MjcDjIUVT7Sl9pcKyXmAPI3EqthHPEGgGgkjR2HrwUKsuDU7gkhfCEtKt/ZhmhcHYONdPYeeDVsT4u5f6oAC+FkZV3hHtBPdmgKNjIytaVhwWnyFFSvHkMTVhg9eMQyg6N4nBxfhVkSIZyHGVVVx4z1lSX14TUkqMQu9K5yQ48hicG2fomqCeUYspLISpUp544XiNJrN1WyOoesrwpjZ0HyjsxffAXri2VQhQSppGowx0NVYHRnmx6JXjEeG5Uv+EVN6HNeMswBJz+kkUCH9XRyEE2R4qfUOiIcAegAo391F8IdbzxqacYY3zIBleDetrRXQLcNFgfqLrZLXj6R32Gz0+TcvCIFLKNaBw1OE7ZJ5lMKNdCZy8/xviARi3EtH2aLXYrvDcmhe6NTNIvhXgA7dXe2Ox16J0jy6Cmp5ikgeGmHE/GpBC2GI1aCBRp1gSJb01j58HaCuAQ0KiFQJ2k9KzZYyOHKB+JAEYDSaN8bCgd0RTQUXsXfFuWf5IVO6ozKTyasEGPo6lHCpkxBHPN0TXDK26RNKi8qRQCrJnscPyPCAqNHaiCcFpmQzQMRngfcF5gdcN85e8BBYQL/Kjg9InE/yTVBTuyQRYPuAz8s/FEnUnSoP4UDqky1viISSFKgnDU42wdNDNfDxYiua83AHvB3FsOjUMdyzcAIR/DbaGJ5rF4KYQ2kUM7oz531AoGsLU/4FvQqHmQoZEoihqnw+cip+c1J9KoOTApVEqDHuiXwuclF2mUA8B9IylR59EoNdyaN5CVD8ykEE9FoxQIntvYHhSWEQFgFJND8NugSjpneT0uPEvOgiLQKDUEZe0lyTIaomjUHGDFk/gAw180Sg1ssqkymzEpVLWUldAjhWx9fo3maRQT0r93LXP3P1LIjy7ssDYK27HviFwHiw8PCkVPqnuJzBFcehmGAPIuHB+UJb49FVYbTANkd1j4Hp/YgAuypgclAwwbIdCoEEDIt3BUSfwoMkjCOt4vHPI4UbvAMWRBR+aPN4AqnTWvoKoTzPbFSyGbogNVolEOwNYe5zfbgllBIuGdpZrDHJHlSaHAMCnMdt07BvRLIWtidwRW66DOo1FqwHOSZMrXxaQQn4xGKQCNI2lg/tOoecBohR6RoyCK7fWsPXh+fDKNES3M3NZuVIUlRJIJ61MwacaNaJQaYM8SZw4FUzCumRTq3P1RjxSyJfvghwkZiSfZtwOG9j9SyI/Uw0/iJyO/KAlUbeIFUIfD4H9WfAG1EGSYF0Tk74jKOxBE/DxPjeltGajTs+WAfm6P/KShvwpXZr2o3mNIeVAaN4zGUwvS6ARuxwa4VHbm0tg5LF4KWeOytjUEsEZe/iKuDo0SNqH1HSlkfqK2l8es4/MKLdMjheUdWSTNmZT/0SgOUBxYr2wMP+OemH+HV9xUHe8BX4Gk0W7aQkkhyS6kbaZRc2Dvv6QtnUY5AFtpVBAEb0ihS07e1oiP/pFCgPXfH47/aWZmBn4uCeIDJ9Q+1/66MDXhix148x1cY76FctQ2BIlEPKqOxTSN4TqQEuVArcWzoa8S12ezD73HzqFm1oqnPbzDS2B7+D1fuKcVqwbclsJo431yKLrKyeQZ0nIP8sNi4IWQSIg1iqU24YNXdS2QCR+UQm3PlE1qODu/hgiDS1JIdnNUBbQGDhz8PmL1MEKMWIsQAzuqPRba3FdBkl0UpZA2UDrdAI49jOAsekMKmc0k5B8l8dVQeSyQQrw7cjKI3JaxcO0/vHenhoxltPthwaljib+aFg53hFFpMIfAfbidewhWJI3Vh1HbMExc1GmQWr5f31Osn5NCnZtGuU1YQ3ynNn4L+XWLByotuGNw8GnYMdiAG8HBYSMwlCWTBxs6qyGFQfNttaqA0cEKA9+rAGeC9Nq5txyW70jh0fmhl8TVcATmHHhPChmQPbqGWmDKsNWJjif+KvSckHjQ0ZBDAjaCTWijZN3HkZq79IxNjhC/BG67YH56QwpZg6zOYbYLpBCWFwxFcn5Q1l44v8KAUuTXp0UBTifiNPYbIclPigIE7UedU9iagioFOSbW+Aia6KiNfNo+ha+Sag69n39836yLfbPT2nTHOxsTo65b/NwVpyzvyH40P7LaL3StG5OZHCG0/AauiQ+nnY8BtkQ5Pg2NmgMb34Pv66gpA3mXZQYNKcRjaMxnQP1Kku2P/YZGzYO0cOEBlMMGncJ3pPBp0Tk9KdnIkiWQQgYoAvuCpPpnIJFgaHkQjVID82kyF25BUTE/hneur9ahi816vaAtNGoerkohLC2SXuMNsM4PndP8F0ghwL4l9Lt3pB1flAR5bg57/2XJJdVGBwaoHioimHJ4DuU4apS9rMYYXPxyxo4jCRtgbiBPwDsLMGyERTk7dDHhlxvZ+2E64avD16juLmTDVrWJwpDTFJdU91J7zD1PvFbeHPYGD8X9AKuNeU/uTbt2BOban0r+Lz/nQQDf3ST0wMJGYNOS4mue0diFyGyIIglADSkEIUyC0UGAau9wPO1V4JfAIGCfAFlCz3AKHr4jhZWduSQlNN1RzcQ6eUGPS2FFRzZkxdFsTtaTIMx8J5Egsqijx2Z9Jij+wrBQfFlmcjrKP6j7WeuW8jO5KoVwKUh6PLCjaRrwUEmD6dbwD/VMnBelEK4WuQcIc6N7qIUFBeIGSKDdH4KKCLY0XgGKCoSVxioAVW0brIcZ3zfSOWKz8hULHoCps1Py05khtcJRR0TGUs559CyT6oL5RS5cbSLQRoe1kYksKhLVqWkwe1FsSBqYBsrcwwxGlGFkeqFuL2hJYrcAtaUQfJB/UhhKhcqPPcC2iI8HFUOsJ6dtzAFXXTsHPxPe9/Ws3co62HekEGDTY1G7CGuG463CVOGHxHpWCpnbeDDue+Xe3y0DdewjCq32JJIQjy3cGjUTzJpN810rqkOsmcUHxlY/EjqImy21rBY8nvibskXSVSkE2HS6e3nH+N5aHixX7435SmiyA6DgsIJhfuF9IihKIXBhfty5f9RnKDPa5hUcIviwsJA1GlxRh0RU3tkVvR5XzmiIQqlQtRp4wLqBlrHeRp28nXuYnj+33oxw1BEz6iNZs4A3uDv6C/iMbFYTsgJ9RM8B3jfLqeQWL0oCUerS6sPx5lHeWN8xqDoIFh+aH0kKw/x5ycXoqvuvy66REX88NaSQ/UwILhQKIoKL4DtCYUk8nkTYPoGhfbCeGQ6oaGE7p9dH5LckppheX83cxX4C7Ed6wjx8SgphgLAVxck1Q8qDUBHii7A3zGaDeUkKCU8k/mf21tVPIDS3cw8xCYbdTU+YBzuFPRiuDEMHj/2s+AI/hzfA8Jdqoxbkkt9jAz7ck6IAnI7sx+axgCgLquvIuSGFfE8Gyi8y3pmUPwVzFfUrqlWWDD8KbwMuEQxzGFis2Zp0xKtIIRu1BMJFhS2q7JhXEkoHrxnVoLLCJ8BjVXXlhZRdR0FFOcH3QPoY40M4s6nmMFJoIbuoVPEe+aKrnwZzCL0ZJ+hOCUNDiPEsIUnJplcs6N4wYKdAbeS052dH5KeqOkgAF0ZjNv45w99M5pQVLJPCZ8XnYSDwuswTVkluUxw9Rw3wDJTKy/Ny+nbldhasEOZoXtwR2H4+grIwsAlCqBholCZgg/PyIRAVNuunUq4gwEZxGLsKaJQCrG1OqaQo59rDaSG1ylmP7CjMN43BaudS/TRaYKCG0E3hFJ7IXao6CKC+JGkgAjTKGWBOsZHhjJARengesE/Z6ieqhCtJpt6qSCHMeDaMBq8VL87VXdhxdRgCqEITa1/At0LJQVXJj0wcmxyBJ17anplQ+wK1JeoN97RPIFwAcn28JjbT0ymZweINwumDScha4qDytukJ8pAeB6rr+JpnqGnY3Rl3Rs/u2uG0rwaOBjIlMw0IjyZsQB2DmozkV/wi5TwiXgoR7B5uRa3GLzGCB3hYcIrfQ8YRkHfhOqBG5CtgVPs3cw6UtWeq+hOkHXN75CdwOGiUK4DHTZSLX6OIh6m3HCIOqwoPQKOcgbxJ5vKDOB35nNh6uCOptyCyJD0D2dgW+USj98k63k+azmHj0CgOIxODSXXBMN+EMgUlguuqatOxNJaxHihaZkO0kItgN6EK19OGa+6ruJVzkB/Bg8dAHYNH0sj5EKO5cS1rhWFS2kBmwFOxuhOy46jPrb6v8k7uYTaqkZA81YjNStKoSCHA5iSCT4sChicG9SuLNpGl/KPWwYNAeeOLikeIy8KzQy4hGcUXCIsDNj8LuufBuQp8L/hQ8AKgETBAuodaNPr1VAHRrOspRbYW2ptxZaEZiECQQgKUHFgBDX2V+OvqAwAQX9RtOB2OM/6nsQ6A8q9ayHViyj4pjHYQAGlzbyIp3mRDfxWsLWGSAoJK64wAeuRoRgODxukMsOBm339/FV6j9lIpLH/y407wQhr7jY391dr9AarA50a1hG/XbKlx1K0hAOaRzpRK4FHx5E5nGUI6URmTF6KsadSlEGD9/ajN8KtYc4mkfgYYNlpGu1lbNWxtNxRhWUBVCiWWC8i3A3kpXG1wKIVskg0YlLUX1fIxL+8OvMKIKgQ1KhtLCArT3VYSpBQua7AsKqVQHfx+T3nNiSjYrBNK0ikjK+/wWyNoLxC93CGlcFmD5VIpheroGW5jvVH+UZ8NjvdFzO+3K6nNk0m/8wNoUIWwLp0VCSmFyxrk24FSCh0ixfSavaag7H1T9knWuSzpiJvC3msdNPPrO+gfIrBMEWuka8M5XdFPwgfBvD3l8PXVAydSODMzw/pPwJymOFg3eoYZrma+qX5cNr9HGggL0Wnf1nJHfV/l1oiPNoW5MNxEwndwK+cgMurZlI16hsusVDiRQqBnuJ2NetkRuY7sT0qCkkqSmWF7/tnVaNZCpK9yRWNsclh7PIqEz8I+Yx8Y69VeoWvFw7kUAqnmMFbUjyf+Nj45qn9a26riscRfh8YH+K1FVLclk5CQ8DXokkK4yfxCgTey901OT7K9UCQJd0Z9DguaHz0TYPhrlde0EhLLBbqkEBi1DbFFjMGIytsjNitbbUISjnBtTwm/GfHe2K8dTceWkJDwNeiVQqBzqJkt/QgWtCR1D7V4e/Hn5cKcpjcVHdls1ucWx+u+SUhI+CBckEKgsjOPlfbNYe9XdOZ0DbXwSxKtTmY1xpp7y/kp1QWOd26UkJDwQbgmhUBS3UtW4GH7QA1hLa5mNcxujDUt1MHIqrv0ZUlISCwTuCyFwMuSS6zYQw0rO3M7rU1s+MjqIQxk+MWCDt7LO6a6kJSEhIQvwx0pRFF/URLICv+cGubBU15VvSibw97Pa0409ZYJOriaB6lKSCxfuCOFANTweclFJgHQhdymuBGb9VL6Nha5ggkTuL6vsrwjW+qghMTKgJtSCMyq4cL1u0PLb0xOT/ISuSJ5OvkPy2h3XM1TfpXg+/nHpQ5KSCxfuC+FgOApg0FZe8cnR1PNYV5dJf8d8m7e0ZGJQQgfH/kg/6TUQQmJZY1FSSFBiuk1v47h8cTfeobbG/urtffrWXaEL5xWHz4w1nsm5U8+Ptp4X/aTSEgsd3hACgFjVz4/+np75CeZDVGT07aQ8iDei1y+vJi2pXekvajVwO8UszXio+K2VPoKJCQkljM8I4VA51AzPzMPvJa5e3Cs19RbrrGlpO9za/iHMHuHxgfYZruE+998u7JXY5WQWFXwmBQCo7YhYU9h/6jP8poTJ6bGoqvu852ty4U3svd3D7eWtKWzrcoJL6RtVu7GKyEhsXzhSSkkgGssqN7VzF1tg/WD433Pis8vl91Rzqf6mXvLe4bbbuce5uO3hH+QWPtCdpJISKwweF4Kgd6RjsD0rbyC+IX+61HhGctYT6e1CaYWf8jXeCzhl9L2TOu45UVJoNAPHmDY6N6m4xISEj4Or0ghMDMzYzCFsF2iCGFShVfcGpschpH4tOiccPSdMzB9W3FbGtz8aKPozm8Oez++5qk0BiUkViq8JYUEMA/v5R3jNQXcEflpcOmV7qGWEZsVzubBuO+FBEtMqB4MwA5rY/9oV3jFzZ1cHzFhUPY+aQxKSKxseFcKCZotNaoT8q5m7qzoyJ6yT1V25j4tChC6JrxNGHrXMndnNESN2oaNXQVw25Xjfs4Z/jb1ltGfISEhsXKxFFJIAL1T3Tj0cPyPkVV3my219hm7qbc8pDzIq4Nv/KPWPcg/WdyWOj45Ckswrubp0YQNQhoQkSVt6fTRJSQkVjqWTgoBiF1pe+bl9O2C7hDuf/NtcOnlmu6iaft030gn1Cqs4mZg+rbtkZ8IKV3iprC1p5L+D1ZnRn0k7NPJaVtdT2lI2XVH6+icS/XLb0mUe5JISKwqLKkUMnRYm16UBDoaabgjct2VDP/IyjvQzYGxXghop7WpvCMLWhZVde9JUcDVzF0wMA+8+W5vzFe7otfvjF6/J+bLfbFfw5SDdN7PPx5aHpRUF1zQktzYb4T2Wcct8MRjjA+CsvYqmwIJt4Z/iCvLUdMSEqsT70YKCcYmhw2mEH6rTFVC74Ky970suRRf8zS3Ka66uxBKinOn7VP0QnOwz0yP2oZxCHZlXnNiQu2L12XXbuYcgEQKFxR4Kvm/ibUvRmxWeiEJCYnVh3cphQzW8f6Mhqhrmbs3h70v6JRTwv91Y9j2prD3YHimmkP7R7voQ0hISKxi+IQUMoxNjhS1GoJLLwcY/toS/oGgX4sk5O908h/Pis8XtCTBfqS3lJCQkPA1KeQB/7dloA7W4tOic+dT/Q7Gfe+SzQhrEa5xgGHjk6KA9PqIJkv15LSNXlpCQkJiIXxXCpWYmZkZmhhoHTRXduZmN8am1YcbTCFJdS/BZNMreLtZjbE41Dpgso5b7DN2epqEhISENt6+/f9jNy1GCGUrhQAAAABJRU5ErkJggg=="  />';
            }
            $data['escudoColombia'] = '<img src="escudocolombia.jpg" style="width:34.016px;height:42.52px;">';
            $data['tituloMinisterio'] = '<br>REPÚBLICA DE COLOMBIA<BR>MINISTERIO DE TRANSPORTE<br><br>';
            $data['tituloB'] = '<strong>B. RESULTADOS DE LA INSPECCIÓN MECANIZADA REALIZADA DE ACUERDO CON LOS MÉTODOS DEFINIDOS POR LA NTC 5375;NTC 6218; NTC 6282.</strong>';
            $data['tituloC'] = '<strong>C. DEFECTOS ENCONTRADOS EN LA INSPECCIÓN MECANIZADA DE ACUERDO CON LOS CRITERIOS DEFINIDOS EN LAS NTC 5375, NTC 6218 Y NTC6282 (según corresponda).</strong>';
            $data['tituloD'] = '<strong>D. DEFECTOS ENCONTRADOS EN LA INSPECCIÓN SENSORIAL DE ACUERDO CON LOS MÉTODOS Y CRITERIOS DEFINIDOS EN LAS NTC 5375, NTC 6218, NTC 6282 NTC, 4983, NTC 4231 Y NTC 5365 (según corresponda).</strong>';
            $data['tituloE'] = '<strong>E. CONFORMIDAD DE LAS NORMAS NTC 5375, NTC 6218, NTC 6282, NTC 4983, NTC 4231 Y NTC 5365 (según corresponda).</strong>';
            $data['tituloG'] = '<strong>G. REGISTRO FOTOGRÁFICO DE LA REVISIÓN TÉCNICO-MECÁNICA Y DE EMISIONES CONTAMINANTES</strong>';
            $data['tituloJ'] = '<strong>J. NOMBRE DE LOS INSPECTORES QUE REALIZARON LA REVISIÓN TÉCNICO-MECÁNICA Y DE EMISIONES CONTAMINANTES</strong>';
        }

        $data['vehiculo'] = $this->getvehiculo($data['hojatrabajo']->idvehiculo);
//------------------------------------------------------------------------------PRESIONES DE LLANTA
        if ($ocasion == '0' || $ocasion == '1') {
            $ti = '1';
        } elseif ($ocasion == '4444' || $ocasion == '44441') {
            $ti = '2';
        } else {
            $ti = '3';
        }
        $oc = '0';
        if ($data['ocasion'] == 'true') {
            $oc = '1';
        }

        $data['presion'] = $this->getPresiones($data['vehiculo']->numero_placa, $oc, $data['fechafur'], $ti);
        if ($this->histo_propietario !== '') {
            $data['vehiculo']->idpropietarios = $this->histo_propietario;
        }
        if ($this->histo_servicio !== '') {
            $data['vehiculo']->idservicio = $this->histo_servicio;
        }
        if ($this->histo_licencia !== '') {
            $data['vehiculo']->numero_tarjeta_propiedad = $this->histo_licencia;
        }
        if ($this->histo_color !== '') {
            $data['vehiculo']->idcolor = $this->histo_color;
        }
//        if ($this->histo_combustible !== '') {
//            $data['vehiculo']->idtipocombustible = $this->histo_combustible;
//        }
        if ($this->histo_kilometraje !== '') {
            $data['vehiculo']->kilometraje = $this->histo_kilometraje;
        }
        if ($this->histo_blindaje !== '') {
            $data['vehiculo']->blindaje = $this->histo_blindaje;
        }
        if ($this->histo_polarizado !== '') {
            $data['vehiculo']->polarizado = $this->histo_polarizado;
        }
        if ($this->usuario_registro !== '') {
            $data['vehiculo']->usuario = $this->usuario_registro;
        }
        if ($this->chk_3 !== '') {
            $data['vehiculo']->chk_3 = $this->chk_3;
        }
        if ($this->fecha_final_certgas !== '') {
            $data['vehiculo']->fecha_final_certgas = $this->fecha_final_certgas;
        }
        if ($this->fecha_vencimiento_soat !== '') {
            $data['vehiculo']->fecha_vencimiento_soat = $this->fecha_vencimiento_soat;
        }

        if ($data['vehiculo']->kilometraje == '0' || $data['vehiculo']->kilometraje == '') {
            $data['vehiculo']->kilometraje = 'NO FUNCIONAL';
        }
//        if ($data['vehiculo']->potencia_motor == '0') {
//            $data['vehiculo']->potencia_motor = '';
//        }
        $data['vehiculo']->diametro_escape = floatval(str_replace(",", ".", $data['vehiculo']->diametro_escape)) * 10;
        $data['vehiculo']->diametro_escape = $this->rdnr($data['vehiculo']->diametro_escape);
        $data['servicio'] = $this->getServicio($data['vehiculo']->idservicio);
        $data['carroceria'] = $this->getCarroceria($data['vehiculo']->diseno);
        $data['clase'] = $this->getclase($data['vehiculo']->idclase);
        $this->nombreClase = $data['clase'];
//        $this->nombreClase->nombre= "MOTOCICLETA";
        $data['linea'] = $this->getLinea($data['vehiculo']->idlinea, $data['vehiculo']);
        if ($data['vehiculo']->registrorunt == '1') {
            $data['marca'] = $this->getMarca($data['linea']->idmarcaRUNT, $data['vehiculo']);
        } else {
            $data['marca'] = $this->getMarca($data['linea']->idmarca, $data['vehiculo']);
        }
        $data['color'] = $this->getColor($data['vehiculo']->idcolor, $data['vehiculo']);
        $data['combustible'] = $this->getCombustible($data['vehiculo']->idtipocombustible);
        $data['pais'] = $this->getPais($data['vehiculo']->idpais);
        $data['propietario'] = $this->getPropietario($data['vehiculo']->idpropietarios);
        $data['tipoDocumento'] = $this->tipoDocumento($data['propietario']->tipo_identificacion);

        $data['ciudadPropietario'] = $this->getCiudad($data['propietario']->cod_ciudad);
        $data['departamentoPropietario'] = $this->getDepartamento($data['ciudadPropietario']->cod_depto);
        $data['blindaje'] = $this->getBlindaje($data['vehiculo']->blindaje);
        if (is_null($data['vehiculo']->num_pasajeros) || $data['vehiculo']->num_pasajeros == '') {
            $data['pasajeros'] = intval($data['vehiculo']->numsillas) - 1;
        } else {
            $data['pasajeros'] = intval($data['vehiculo']->num_pasajeros);
        }

        if ($data['vehiculo']->idtipocombustible !== 5) {
            if ($data['vehiculo']->scooter === '1' && $data['vehiculo']->tipo_vehiculo !== '3') {
                $data['vehiculo']->convertidorCat = "SI";
            } elseif ($data['vehiculo']->scooter === '0' && $data['vehiculo']->tipo_vehiculo !== '3') {
                $data['vehiculo']->convertidorCat = "NO";
            } elseif ($data['vehiculo']->tipo_vehiculo == '3') {
                if (is_null($data['vehiculo']->convertidor) || $data['vehiculo']->convertidor == '0')
                    $data['vehiculo']->convertidorCat = "NO";
                elseif ($data['vehiculo']->convertidor == '1')
                    $data['vehiculo']->convertidorCat = "SI";
                else
                    $data['vehiculo']->convertidorCat = "N.A.";
            } else {
                $data['vehiculo']->convertidorCat = "N.A.";
            }
        } else {
            $data['vehiculo']->convertidorCat = "N.A.";
        }
        $idpre_prerevision = $this->getIdPre_prerevision($data['vehiculo']->numero_placa, $ocasion, $data['fechafur']);
        if ($idpre_prerevision !== '' && $idpre_prerevision !== NULL) {
            if (strlen($this->getFechaSoat($idpre_prerevision)) == 10) {
                $data['vehiculo']->fecha_vencimiento_soat = $this->getFechaSoat($idpre_prerevision);
                if ($data['vehiculo']->fecha_vencimiento_soat == '0000-00-00') {
                    $data['vehiculo']->fecha_vencimiento_soat = "";
                }
            }
//            else {
//                $data['vehiculo']->fecha_vencimiento_soat = '';
//            }
            $data['vehiculo']->certificadoGas = $this->getCertificado($idpre_prerevision);
            if (strlen($this->getFechaCertificado($idpre_prerevision)) == 10) {
                $data['vehiculo']->fecha_final_certgas = $this->getFechaCertificado($idpre_prerevision);
                if ($data['vehiculo']->fecha_final_certgas == '0000-00-00') {
                    $data['vehiculo']->fecha_final_certgas = "";
                }
            } else {
                $data['vehiculo']->fecha_final_certgas = '';
            }
            $idusuario_ = $this->getUsuarioRegistro($idpre_prerevision);
        } else {
            $idusuario_ = $data['vehiculo']->usuario;
            $dat = '';
            switch ($data['vehiculo']->chk_3) {
                case 'NA':
                    $dat = "SI ( ) NO ( ) N/A (X)";
                    break;
                case 'NO':
                    $dat = "SI ( ) NO (X) N/A ( )";
                    break;
                case 'SI':
                    $dat = "SI (X) NO ( ) N/A ( )";
                    break;
                default :
                    $dat = "SI ( ) NO ( ) N/A (X)";
                    break;
            }
            $data['vehiculo']->certificadoGas = $dat;
            if ($data['vehiculo']->fecha_final_certgas == '0000-00-00') {
                $data['vehiculo']->fecha_final_certgas = "";
            }
            if ($data['vehiculo']->fecha_vencimiento_soat == '0000-00-00') {
                $data['vehiculo']->fecha_vencimiento_soat = "";
            }
        }
//        echo $idusuario_;
        $usuario = $this->getUsuario($idusuario_);
        $data['vehiculo']->usuario_registro = $usuario->nombres . " " . $usuario->apellidos;
        $data['vehiculo']->documento_usuario = $usuario->identificacion;

//------------------------------------------------------------------------------RESULTADOS
//------------------------------------------------------------------------------LUCES
        $data['luces'] = $this->getLuces($idhojaprueba, $data['vehiculo']);
//        var_dump($data['luces']);
        if ($data['vehiculo']->tipo_vehiculo !== '3' || $this->evalLucFullMotos == "0") {
            $data['luces']->intensidad_minimaAD = '';
            $data['luces']->intensidad_minimaAI = '';
        } else {
            $data['luces']->intensidad_minimaAD = '';
            $data['luces']->intensidad_minimaAI = '';
            if ($data['luces']->valor_alta_derecha_1 == "") {
                $data['luces']->intensidad_minimaAD = '';
            }
            if ($data['luces']->valor_alta_izquierda_1 == "") {
                $data['luces']->intensidad_minimaAI = '';
            }
            if ($data['luces']->valor_baja_izquierda_1 == "") {
                $data['luces']->intensidad_minimaBI = '';
                $data['luces']->inclinacion_rangoBI = '';
            }

            if ($data['luces']->valor_baja_derecha_1 == "") {
                $data['luces']->intensidad_minima = '';
                $data['luces']->inclinacion_rango = '';
            }
        }
//        if ($data['vehiculo']->tipo_vehiculo !== '3' || $this->evalLucFullMotos == "0") {
//            
//        }

        if ($data['luces']->configLuces !== '') {
            array_push($this->observaciones, (object) array(
                        "codigo" => '- ',
                        "descripcion" => $data['luces']->configLuces
            ));
        }
//------------------------------------------------------------------------------SUSPENSION
        $data['suspension'] = $this->getSuspension($idhojaprueba);
//------------------------------------------------------------------------------FRENOS
        $data['frenos'] = $this->getFrenos($idhojaprueba, $data['vehiculo'], $ocasion);
//------------------------------------------------------------------------------ALINEACION
        $data['alineacion'] = $this->getAlineacion($idhojaprueba);
//------------------------------------------------------------------------------TAXIMETRO
        $data['taximetro'] = $this->getTaximetro($idhojaprueba);

//------------------------------------------------------------------------------SONOMETRO
        $data['sonometro'] = $this->getSonometro($idhojaprueba);
        if ($data['sonometro']->valor_ruido_motor1 !== '') {
            array_push($this->observaciones, (object) array(
                        "codigo" => 'Valor sonometría',
                        "descripcion" => $data['sonometro']->valor_ruido_motor1 . " dB"
            ));
        }
//------------------------------------------------------------------------------GASES
        $data['gases'] = $this->getGases($idhojaprueba, $data['vehiculo']);
        if ($data['vehiculo']->tipo_vehiculo == '3' && $this->obseCorrOxigeno == '1') {
            if ((floatval($data['gases']->o2_ralenti) > 6 && $data['vehiculo']->tiempos == '4') || (floatval($data['gases']->o2_ralenti) > 11 && $data['vehiculo']->tiempos == '2') || (floatval($data['gases']->o2_ralenti) > 6 && $data['vehiculo']->tiempos == '2' && $data['vehiculo']->ano_modelo >= 2010)) {
                array_push($this->observaciones, (object) array(
                            "codigo" => 'Hidrocarburo (HC) anterior exosto 1',
                            "descripcion" => $data['gases']->hc_anterior . " ppm"
                ));
                if ($data['gases']->hc_anterior1 !== "")
                    array_push($this->observaciones, (object) array(
                                "codigo" => 'Hidrocarburo (HC) anterior exosto 2',
                                "descripcion" => $data['gases']->hc_anterior1 . " ppm"
                    ));
                if ($data['gases']->hc_anterior2 !== "")
                    array_push($this->observaciones, (object) array(
                                "codigo" => 'Hidrocarburo (HC) anterior exosto 3',
                                "descripcion" => $data['gases']->hc_anterior2 . " ppm"
                    ));
                if ($data['gases']->hc_anterior3 !== "")
                    array_push($this->observaciones, (object) array(
                                "codigo" => 'Hidrocarburo (HC) anterior exosto 4',
                                "descripcion" => $data['gases']->hc_anterior3 . " ppm"
                    ));

                array_push($this->observaciones, (object) array(
                            "codigo" => 'Monóxido de carbono (CO) anterior exosto 1',
                            "descripcion" => $data['gases']->co_anterior . " %"
                ));
                if ($data['gases']->co_anterior1 !== "")
                    array_push($this->observaciones, (object) array(
                                "codigo" => 'Monóxido de carbono (CO) anterior exosto 2',
                                "descripcion" => $data['gases']->co_anterior1 . " %"
                    ));
                if ($data['gases']->co_anterior2 !== "")
                    array_push($this->observaciones, (object) array(
                                "codigo" => 'Monóxido de carbono (CO) anterior exosto 3',
                                "descripcion" => $data['gases']->co_anterior2 . " %"
                    ));
                if ($data['gases']->co_anterior3 !== "")
                    array_push($this->observaciones, (object) array(
                                "codigo" => 'Monóxido de carbono (CO) anterior exosto 4',
                                "descripcion" => $data['gases']->co_anterior3 . " %"
                    ));
            }
        } else {
            if ($data['gases']->dilusion == 'true') {
                array_push($this->observaciones, (object) array(
                            "codigo" => '1.1.6.16.1',
                            "descripcion" => "Dilución excesiva."
                ));
            }
        }
        if ($this->observacionHumos !== "") {
            array_push($this->observaciones, (object) array(
                        "codigo" => '-',
                        "descripcion" => $this->observacionHumos
            ));
        }

//------------------------------------------------------------------------------OPACIDAD        
        $data['opacidad'] = $this->getOpacidad($idhojaprueba, $data['vehiculo']);
        if ($this->fechares762_K1 !== "" && $this->fechares762_K1 <= $this->fechaGlobal) {
            if ($data['opacidad']->op_ciclo1k !== "") {
                array_push($this->observaciones, (object) array(
                            "codigo" => 'Densidad de humo 1 (K)',
                            "descripcion" => $data['opacidad']->op_ciclo1k . " 1/m"
                ));
            }
            if ($data['opacidad']->op_ciclo2k !== "") {
                array_push($this->observaciones, (object) array(
                            "codigo" => 'Densidad de humo 2 (K)',
                            "descripcion" => $data['opacidad']->op_ciclo2k . " 1/m"
                ));
            }
            if ($data['opacidad']->op_ciclo3k !== "") {
                array_push($this->observaciones, (object) array(
                            "codigo" => 'Densidad de humo 3 (K)',
                            "descripcion" => $data['opacidad']->op_ciclo3k . " 1/m"
                ));
            }
            if ($data['opacidad']->op_ciclo4k !== "") {
                array_push($this->observaciones, (object) array(
                            "codigo" => 'Densidad de humo 4 (K)',
                            "descripcion" => $data['opacidad']->op_ciclo4k . " 1/m"
                ));
            }
            if ($data['opacidad']->op_cicloTk !== "") {
                array_push($this->observaciones, (object) array(
                            "codigo" => 'Densidad de humo total (K)',
                            "descripcion" => $data['opacidad']->op_cicloTk . " 1/m"
                ));
            }
        }
        if ($this->observacion405 !== "") {
            array_push($this->observaciones, (object) array(
                        "codigo" => '-',
                        "descripcion" => $this->observacion405
            ));
        }
        if ($this->observacionDifAri !== "") {
            array_push($this->observaciones, (object) array(
                        "codigo" => '-',
                        "descripcion" => $this->observacionDifAri
            ));
        }
//------------------------------------------------------------------------------LABRADO        
        $data['labrado'] = $this->getLabrados($idhojaprueba);
//------------------------------------------------------------------------------FOTOS
        if ($this->espejoImagenes == '1') {
            $this->Mimagenes->deleteImagenes();
            $this->MEventosindra->deleteEventos();
        }
        if ($this->input->post('desdeConsulta') !== NULL) {
            $this->desdeConsulta = $this->input->post('desdeConsulta');
        }
        $data['fotografia'] = $this->getFotografias($idhojaprueba);
//------------------------------------------------------------------------------MAQUINA
        $data['maquinas'] = $this->getMaquinas($idhojaprueba, $fechaMaquinas, $data['vehiculo']);
//------------------------------------------------------------------------------INSPECTORES
        $data['inspectores'] = $data['maquinas'];
//------------------------------------------------------------------------------SENSORIAL
        $this->setDefectos();
        $data['sensorial'] = $this->getSensorial($idhojaprueba);
//------------------------------------------------------------------------------FIRMA
        if ($data['hojatrabajo']->jefelinea !== '' && $data['hojatrabajo']->jefelinea !== NULL) {
            if ($this->input->post('envioSicov') !== 'true' && $this->jefe_pista !== '') {
                $data['hojatrabajo']->jefelinea = $this->jefe_pista;
            }
            $data['firmaJefe'] = $this->getFirmaJefe($this->Musuarios->getXnombreID($data['hojatrabajo']->jefelinea));
        } else {
            $data['firmaJefe'] = '';
        }
//        echo "firma " . $data['firmaJefe'];
//------------------------------------------------------------------------------DEFECTOS
        $data['defectosMecanizadosA'] = $this->defectosMA;
        $data['defectosMecanizadosB'] = $this->defectosMB;
        $data['defectosSensorialesA'] = $this->defectosSA;
        $data['defectosSensorialesB'] = $this->defectosSB;
        $data['defectosEnsenanzaA'] = $this->defectosEA;
        $data['defectosEnsenanzaB'] = $this->defectosEB;
//------------------------------------------------------------------------------NÚMEROS DE LOS FUR ASOCIADOS AL VEHÍCULO PARA LA REVISIÓN
        if ($this->virtualRunt == "0" && ($ocasion == '0' || $ocasion == '1')) {
            if ($ocasion == "0") {
                $data['num_fur_aso'] = "No: $consecutivo-0";
            } else {
                $fechafur_ant = $data['fechafur_ant'];
                $data['num_fur_aso'] = "Primera inspección el $fechafur_ant No: $consecutivo-0 No: $consecutivo-1";
            }
        } else {
            $data['num_fur_aso'] = $cons;
        }
//------------------------------------------------------------------------------I. SOFTWARE Y/O APLICATIVOS CON LA VERSIÓN UTILIZADA:
        $data['software'] = $this->software;
//------------------------------------------------------------------------------F. COMENTARIOS U OBSERVACIONES ADICIONALES:
        $data['observaciones'] = $this->observaciones;
//------------------------------------------------------------------------------DIAGNOSTICO
        $data['apro'] = $this->getDiagnostico($data['vehiculo']);
        $data['aproE'] = $this->getDiagnosticoE($data['vehiculo']);

        if ($data['hojatrabajo']->estadototal !== '7' && $data['hojatrabajo']->estadototal !== '4') {
            $this->segundo_envio = false;
            if ($this->input->post('envio') !== NULL && $this->input->post('envio') == '2') {
                $this->segundo_envio = true;
            }
            if ($this->segundo_envio) {
                $data['numero_consecutivo'] = $this->getNumero_consecutivo($idhojaprueba);
                $data['numero_sustrato'] = substr($data['numero_consecutivo'], 1);
            } else {
                $data['numero_sustrato'] = '';
                $data['numero_consecutivo'] = '';
            }
        } else {
            $data['numero_consecutivo'] = $this->getNumero_consecutivo($idhojaprueba);
            $data['numero_sustrato'] = substr($data['numero_consecutivo'], 1);
        }
        if ($this->mostrarFecha == '0') {
            $data['fechainicioprueba'] = "";
            $data['fechafinalprueba'] = "";
        }

        if ($data['vehiculo']->idtipocombustible == '1') {
            $data['vehiculo']->tiempos = 'Diésel';
        } else {
            if ($data['vehiculo']->tipo_vehiculo !== '3' && ($data['vehiculo']->idtipocombustible == '2' || $data['vehiculo']->idtipocombustible == '4' || $data['vehiculo']->idtipocombustible == '3')) {
                $data['vehiculo']->tiempos = 'OTTO';
            } elseif ($data['vehiculo']->tipo_vehiculo == '3' && $data['vehiculo']->idtipocombustible == '2') {
                $data['vehiculo']->tiempos = $data['vehiculo']->tiempos . ' T';
            } elseif ($data['vehiculo']->idtipocombustible == '6') {
                $data['vehiculo']->tiempos = 'Hidrogeno';
            } else {
                $data['vehiculo']->tiempos = 'Eléctrico';
            }
        }
        if ($this->input->post('desdeVisor') === 'car') {
            if ($this->CARinformeActivo == "1") {
//                echo $this->idprueba_gases;
                $rta = $this->Mambientales->getEnvioCar($this->idprueba_gases);
                if (count($rta) == 0) {
                    $r['idprueba'] = $this->idprueba_gases;
                    $r['tipo'] = 'Iniciando envio_' . $data['vehiculo']->numero_placa;
                    $r['estado'] = 0;
                    $r['usuario'] = $this->session->userdata("IdUsuario");
                    $r['fecharegistro'] = date("Y-m-d H:i:s");
                    $this->Mambientales->insertControlCar($r);
                    $this->getInformeCarNew($data['hojatrabajo']->idhojapruebas);
                }
            }
        } elseif ($this->input->post('desdeVisor') === 'true') {
            if ($this->rechazadoCB) {
                echo "APROBADO: SI_____ NO__X__|APROBADO: SI_____ NO__X__|" . $data['sensorial']->idprueba;
            } else {
                echo "APROBADO: SI__X__ NO_____|APROBADO: SI__X__ NO_____|" . $data['sensorial']->idprueba;
            }
//            echo "APROBADO: SI__X__ NO_____|APROBADO: SI__X__ NO_____|" . $data['sensorial']->idprueba;
        } else {
            if ($this->input->post('envioSicov') !== 'true') {
                if ($this->jefe_pista !== '') {
                    $data["hojatrabajo"]->jefelinea = $this->jefe_pista;
                }
                $data['horarioAtencion'] = $this->horarioAtencion;
                $data['envioEmail'] = $envioEmailFur;
                $fecha1 = explode(" ", $data['fechafur']);
                $fecha_ = str_replace("-", "", $fecha1[0]);
                if ($envioEmailFur == 1) {
                    if (!is_dir('C:\PDF')) {
                        mkdir('C:\PDF', 0777, true);
                    }
                    if (!is_dir('C:\PDF\fur')) {
                        mkdir('C:\PDF\fur', 0777, true);
                    }
                    $data ['tipopdfenvio'] = 1;
                    $data ['url_'] = "C:/PDF/fur/";
                    $data ['file_'] = "Fur_" . $data['vehiculo']->numero_placa . '_' . $fecha_ . ".pdf";
                    $url = "C:/PDF/fur/Fur_" . $data['vehiculo']->numero_placa . '_' . $fecha_ . ".pdf";
                    $this->load->view('oficina/fur/VFURPDF', $data, true);
                    $email = $this->input->post('email');
                    $r = $this->enviarEmail($email, $url, $data['vehiculo']->numero_placa);
                    echo $r;
//            redirect('oficina/informes/CPrerevision');
                } else {

//                    if ($this->CARinformeActivo == "1") {
//                        $rta = $this->Mambientales->getEnvioCar($this->idprueba_gases);
//                        if (count($rta) == 0) {
//                            $this->getInformeCarNew($data['hojatrabajo']->idhojapruebas);
//                        }
//                    }
                    $data ['tipopdfenvio'] = 0;
                    $data ['url'] = "C:/PDF/fur/";
                    $data ['file'] = "Fur_" . $data['vehiculo']->numero_placa . '_' . $fecha_ . ".pdf";
                    if ($this->input->post('tamano') == NULL) {
                        $data['tamano'] = "oficio";
                    } else {
                        $data['tamano'] = $this->input->post('tamano');
                    }
                    $this->load->view('oficina/fur/VFURPDF', $data);
                }
            } else {
                if ($this->input->post('envioSicov') === 'true') {
                    $data['idhojapruebas'] = $idhojaprueba;
                    $this->guardarJefePista($data, $oc, $ti);
                    if ($this->nombreSicov == "CI2") {
                        $this->buildCi2($data);
                    } else {
                        $data['sicovModoAlternativo'] = $this->input->post('sicovModoAlternativo');
                        $this->buildIndra($data);
                    }
                }
            }
        }
    }

    public function guardarJefePista($data, $ocasion, $ti) {
        $id = 'jefe_pista';
        $numero_placa_ref = $data['vehiculo']->numero_placa;
        $reinspeccion = $ocasion;
        $tipo_inspeccion = $ti;
        $valor = $data['hojatrabajo']->jefelinea;
        $pre_prerevision['numero_placa_ref'] = $numero_placa_ref;
        $pre_prerevision['reinspeccion'] = $reinspeccion;
        $pre_prerevision['tipo_inspeccion'] = $tipo_inspeccion;
        $idpre_prerevision = $this->Mpre_prerevision->getXidPre($pre_prerevision);
        $pre_atributo['id'] = $id;
        $idpre_atributo = $this->Mpre_atributo->getXid($pre_atributo);
        $rta_pre = $idpre_atributo->result();
        $pre_dato['idpre_atributo'] = $rta_pre[0]->idpre_atributo;
        $pre_dato['idpre_zona'] = '0';
        $pre_dato['idpre_prerevision'] = $idpre_prerevision;
        $pre_dato['valor'] = $valor;
        $this->Mpre_dato->guardar($pre_dato);
    }

    var $llanta_1_I = '';
    var $llanta_1_D = '';
    var $llanta_2_IE = '';
    var $llanta_2_DE = '';
    var $llanta_2_II = '';
    var $llanta_2_DI = '';
    var $llanta_3_II = '';
    var $llanta_3_IE = '';
    var $llanta_3_DI = '';
    var $llanta_3_DE = '';
    var $llanta_4_II = '';
    var $llanta_4_IE = '';
    var $llanta_4_DI = '';
    var $llanta_4_DE = '';
    var $llanta_5_II = '';
    var $llanta_5_IE = '';
    var $llanta_5_DI = '';
    var $llanta_5_DE = '';
    var $llanta_R = '';
    var $llanta_R2 = '';
//Datos versionados del vehiculo
    var $histo_propietario = '';
    var $histo_servicio = '';
    var $histo_licencia = '';
    var $histo_color = '';
    var $histo_combustible = '';
    var $histo_kilometraje = '';
    var $histo_blindaje = '';
    var $histo_polarizado = '';
    var $usuario_registro = '';
    var $histo_cliente = '';
    var $chk_3 = '';
    var $fecha_final_certgas = '';
    var $fecha_vencimiento_soat = '';
    var $jefe_pista = '';

    private function getPresiones($placa, $reinspeccion, $fecha, $tipo) {
        $data['numero_placa_ref'] = $placa;
        $data['reinspeccion'] = $reinspeccion;
        $data['tipo_inspeccion'] = $tipo;
        $data['fecha_prerevision'] = $fecha;
        $dat_pre = $this->Mpre_prerevision->getDatos($data);
        if ($dat_pre) {
            foreach ($dat_pre->result() as $d) {
                switch ($d->id) {
                    case 'llanta-1-1-a':
                        $this->llanta_1_D = $d->valor;
                        break;
                    case 'llanta-2-1-a':
                        $this->llanta_2_DE = $d->valor;
                        break;
                    case 'llanta-1-D-a':
                        $this->llanta_1_D = $d->valor;
                        break;
                    case 'llanta-1-D-a':
                        $this->llanta_1_D = $d->valor;
                        break;
                    case 'llanta-1-I-a':
                        $this->llanta_1_I = $d->valor;
                        break;
                    case 'llanta-2-D-a':
                        $this->llanta_2_DE = $d->valor;
                        break;
                    case 'llanta-2-I-a':
                        $this->llanta_2_IE = $d->valor;
                        break;
                    case 'llanta-R-a':
                        $this->llanta_R = $d->valor;
                        break;
                    case 'llanta-R2-a':
                        $this->llanta_R2 = $d->valor;
                        break;
                    case 'llanta-2-DI-a':
                        $this->llanta_2_DI = $d->valor;
                        break;
                    case 'llanta-2-II-a':
                        $this->llanta_2_II = $d->valor;
                        break;
                    case 'llanta-2-DE-a':
                        $this->llanta_2_DE = $d->valor;
                        break;
                    case 'llanta-2-IE-a':
                        $this->llanta_2_IE = $d->valor;
                        break;
                    case 'llanta-3-DI-a':
                        $this->llanta_3_DI = $d->valor;
                        break;
                    case 'llanta-3-II-a':
                        $this->llanta_3_II = $d->valor;
                        break;
                    case 'llanta-3-DE-a':
                        $this->llanta_3_DE = $d->valor;
                        break;
                    case 'llanta-3-IE-a':
                        $this->llanta_3_IE = $d->valor;
                        break;
                    case 'llanta-4-DI-a':
                        $this->llanta_4_DI = $d->valor;
                        break;
                    case 'llanta-4-II-a':
                        $this->llanta_4_II = $d->valor;
                        break;
                    case 'llanta-4-DE-a':
                        $this->llanta_4_DE = $d->valor;
                        break;
                    case 'llanta-4-IE-a':
                        $this->llanta_4_IE = $d->valor;
                        break;
                    case 'llanta-5-DI-a':
                        $this->llanta_5_DI = $d->valor;
                        break;
                    case 'llanta-5-II-a':
                        $this->llanta_5_II = $d->valor;
                        break;
                    case 'llanta-5-DE-a':
                        $this->llanta_5_DE = $d->valor;
                        break;
                    case 'llanta-5-IE-a':
                        $this->llanta_5_IE = $d->valor;
                        break;
                    case 'histo_propietario':
                        $this->histo_propietario = $d->valor;
                        break;
                    case 'histo_servicio':
                        $this->histo_servicio = $d->valor;
                        break;
                    case 'histo_licencia':
                        $this->histo_licencia = $d->valor;
                        break;
                    case 'histo_color':
                        $this->histo_color = $d->valor;
                        break;
                    case 'histo_combustible':
                        $this->histo_combustible = $d->valor;
                        break;
                    case 'histo_kilometraje':
                        $this->histo_kilometraje = $d->valor;
                        break;
                    case 'histo_blindaje':
                        $this->histo_blindaje = $d->valor;
                        break;
                    case 'histo_polarizado':
                        $this->histo_polarizado = $d->valor;
                        break;
                    case 'usuario_registro':
                        $this->usuario_registro = $d->valor;
                        break;
                    case 'histo_cliente':
                        $this->histo_cliente = $d->valor;
                        break;
                    case 'chk-3':
                        $this->chk_3 = $d->valor;
                        break;
                    case 'fecha_final_certgas':
                        $this->fecha_final_certgas = $d->valor;
                        break;
                    case 'fecha_vencimiento_soat':
                        $this->fecha_vencimiento_soat = $d->valor;
                        break;
                    case 'jefe_pista':
                        $this->jefe_pista = $d->valor;
                        break;
                    default:
                        break;
                }
            }
        }

        $presiones = (object)
                array(
                    'llanta_1_I' => $this->rdnr($this->llanta_1_I),
                    'llanta_1_D' => $this->rdnr($this->llanta_1_D),
                    'llanta_2_IE' => $this->rdnr($this->llanta_2_IE),
                    'llanta_2_DE' => $this->rdnr($this->llanta_2_DE),
                    'llanta_2_II' => $this->rdnr($this->llanta_2_II),
                    'llanta_2_DI' => $this->rdnr($this->llanta_2_DI),
                    'llanta_3_II' => $this->rdnr($this->llanta_3_II),
                    'llanta_3_IE' => $this->rdnr($this->llanta_3_IE),
                    'llanta_3_DI' => $this->rdnr($this->llanta_3_DI),
                    'llanta_3_DE' => $this->rdnr($this->llanta_3_DE),
                    'llanta_4_II' => $this->rdnr($this->llanta_4_II),
                    'llanta_4_IE' => $this->rdnr($this->llanta_4_IE),
                    'llanta_4_DI' => $this->rdnr($this->llanta_4_DI),
                    'llanta_4_DE' => $this->rdnr($this->llanta_4_DE),
                    'llanta_5_II' => $this->rdnr($this->llanta_5_II),
                    'llanta_5_IE' => $this->rdnr($this->llanta_5_IE),
                    'llanta_5_DI' => $this->rdnr($this->llanta_5_DI),
                    'llanta_5_DE' => $this->rdnr($this->llanta_5_DE),
                    'llanta_R' => $this->rdnr($this->llanta_R),
                    'llanta_R2' => $this->rdnr($this->llanta_R2)
        );
        return $presiones;
    }

    private function setConf() {
        $conf = @file_get_contents("system/oficina.json");
        if (isset($conf)) {
            $encrptopenssl = New Opensslencryptdecrypt();
            $json = $encrptopenssl->decrypt($conf, true);
            $dat = json_decode($json, true);
            $nomSoftware = "";
            if ($dat) {
                foreach ($dat as $d) {
                    if ($d['nombre'] == "virtualRUNT") {
                        $this->virtualRunt = $d['valor'];
                    }
                    if ($d['nombre'] == "idCdaRUNT") {
                        $this->idCdaRUNT = $d['valor'];
                    }
                    if ($d['nombre'] == "idSoftwareRunt") {
                        $this->idSoftwareRunt = $d['valor'];
                    }
                    if ($d['nombre'] == "idConsecutivoRunt") {
                        $this->idConsecutivoRunt = $d['valor'];
                    }
                    if ($d['nombre'] == "codigoOnac") {
                        $this->codigoOnac = $d['valor'];
                    }
                    if ($d['nombre'] == "software") {
                        $nomSoftware = $nomSoftware . $d['valor'] . " - ";
                    }
                    if ($d['nombre'] == "sicov") {
                        $this->nombreSicov = $d['valor'];
                    }
                    if ($d['nombre'] == "ipSicov") {
                        $this->ipSicov = $d['valor'];
                    }
                    if ($d['nombre'] == "sicovModoAlternativo") {
                        $this->sicovModoAlternativo = $d['valor'];
                    }
                    if ($d['nombre'] == "ipSicovAlternativo") {
                        $this->ipSicovAlternativo = $d['valor'];
                    }
                    if ($d['nombre'] == "usuarioSicov") {
                        $this->usuarioSicov = $d['valor'];
                    }
                    if ($d['nombre'] == "claveSicov") {
                        $this->claveSicov = $d['valor'];
                    }
                    if ($d['nombre'] == "mostrarFecha") {
                        $this->mostrarFecha = $d['valor'];
                    }
                    if ($d['nombre'] == "espejoImagenes") {
                        $this->espejoImagenes = $d['valor'];
                    }
                    if ($d['nombre'] == "habilitarPerifericos") {
                        $this->habilitarPerifericos = $d['valor'];
                    }
                    if ($d['nombre'] == "horarioAtencion") {
                        $this->horarioAtencion = $d['valor'];
                    }
                    if ($d['nombre'] == "modoDobleExt") {
                        $this->modoDobleExt = $d['valor'];
                    }
                    if ($d['nombre'] == "logoColorOnac") {
                        $this->logoColorOnac = $d['valor'];
                    }
                    if ($d['nombre'] == "logoColorSuper") {
                        $this->logoColorSuper = $d['valor'];
                    }
                    if ($d['nombre'] == "desquilibrioBmulti") {
                        $this->desquilibrioBmulti = $d['valor'];
                    }
                    if ($d['nombre'] == "noMostrarDepFur") {
                        $this->noMostrarDepFur = $d['valor'];
                    }
                    if ($d['nombre'] == "obseConfLuces") {
                        $this->obseConfLuces = $d['valor'];
                    }
                    if ($d['nombre'] == "obseCorrOxigeno") {
                        $this->obseCorrOxigeno = $d['valor'];
                    }
                    if ($d['nombre'] == "evalLucFullMotos") {
                        $this->evalLucFullMotos = $d['valor'];
                    }
                    if ($d['nombre'] == "habilitarLogoOnac") {
                        $this->habilitarLogoOnac = $d['valor'];
                    }
                    if ($d['nombre'] == "mostrarO2motos") {
                        $this->mostrarO2motos = $d['valor'];
                    }
                    if ($d['nombre'] == "dirCARinforme") {
                        $this->dirCARinforme = $d['valor'];
                    }
                    if ($d['nombre'] == "fechaLogoOnac") {
                        $this->fechaLogoOnac = $d['valor'];
                    }
                    if ($d['nombre'] == "posicionLogoOnac") {
                        $this->posicionLogoOnac = $d['valor'];
                    }
                    if ($d['nombre'] == "tipo_informe_fugas_cal_lin") {
                        $this->tipo_informe_fugas_cal_lin = $d['valor'];
                    }
                    if ($d['nombre'] == "CARinformeActivo") {
                        $this->CARinformeActivo = $d['valor'];
                    }
                    if ($d['nombre'] == "salaEspera2") {
                        $this->salaEspera2 = $d['valor'];
                    }
                    if ($d['nombre'] == "ajustarGrupos") {
                        $this->ajustarGrupos = $d['valor'];
                    }
                    if ($d['nombre'] == 'envioCorreo') {
                        $this->envioCorreo = $d['valor'];
                    }
                    if ($d['nombre'] == 'emailCDA') {
                        $this->emailCDA = $d['valor'];
                    }
                    if ($d['nombre'] == 'pwEmailCDA') {
                        $this->pwEmailCDA = $d['valor'];
                    }
                    if ($d['nombre'] == 'asuntoEmailCDA') {
                        $this->asuntoEmailCDA = $d['valor'];
                    }
                    if ($d['nombre'] == 'vistaEmailCDA') {
                        $this->vistaEmailCDA = $d['valor'];
                    }
                    if ($d['nombre'] == 'nombreCDA') {
                        $this->nombreCDA = $d['valor'];
                    }
                    if ($d['nombre'] == 'observacionesExtra') {
                        $this->observacionesExtra = $d['valor'];
                    }
                    if ($d['nombre'] == 'generarLogGases') {
                        $this->generarLogGases = $d['valor'];
                    }
                    if ($d['nombre'] == 'fechares762') {
                        $this->fechares762 = $d['valor'];
                    }
                    if ($d['nombre'] == 'fechares762_K1') {
                        $this->fechares762_K1 = $d['valor'];
                    }
                    if ($d['nombre'] == 'kCruda') {
                        $this->kCruda = $d['valor'];
                    }
                    if ($d['nombre'] == 'fechares762_Chispa') {
                        $this->fechares762_Chispa = $d['valor'];
                    }
                }
                $this->software = substr($nomSoftware, 0, strlen($nomSoftware) - 2);
            }
        } else {
            $this->software = "EasyTecmmas v1.0";
        }
    }

    private function getNumero_consecutivo($idhojapruebas) {
        $data['idhojapruebas'] = $idhojapruebas;
        if ($this->aprobado) {
            $data['estado'] = '1';
        } else {
            $data['estado'] = '2';
        }
        $result = $this->Mcertificados->getHT($data);

        if ($result->num_rows() > 0) {
            $r = $result->result();
            if ($this->aprobado) {
                return "A" . $r[0]->consecutivo_runt;
            } else {
                return "R" . $r[0]->consecutivo_runt_rechazado;
            }
        } else {
            return '';
        }
    }

    private function getNumero_sustrato($idhojapruebas) {
        $data['idhojapruebas'] = $idhojapruebas;
        if ($this->aprobado) {
            $data['estado'] = '1';
        } else {
            $data['estado'] = '2';
        }
        $result = $this->Mcertificados->getHT($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0]->numero_certificado;
        } else {
            return '';
        }
    }

    private function setDefectos() {
        $this->defectos = array();
        if ($this->nombreClase->nombre === "MOTOCICLETA" || $this->nombreClase->nombre === "MOTOCICLO") {
            $nombre_defecto = "motocicletas y motociclos";
        } else if ($this->nombreClase->nombre === "CICLOMOTOR") {
            $nombre_defecto = "ciclomotor";
        } else if ($this->nombreClase->nombre === "TRICIMOTO") {
            $nombre_defecto = "tricimoto";
        } else if ($this->nombreClase->nombre === "CUATRIMOTO") {
            $nombre_defecto = "cuatrimotos";
        } else if ($this->nombreClase->nombre === "MOTOTRICICLO") {
            $nombre_defecto = "mototriciclos";
        } else if ($this->nombreClase->nombre === "CUADRICICLO") {
            $nombre_defecto = "cuadriciclo";
        } else if ($this->nombreClase->nombre === "MOTOCARRO") {
            $nombre_defecto = "motocarro";
        } else if ($this->nombreClase->nombre === "REMOLQUE") {
            $nombre_defecto = "remolque";
        } else {
            $nombre_defecto = "liviano pesado";
        }
//        $def = json_decode(utf8_encode(file_get_contents('recursos/defectos.json', true)));
        $def = json_decode(utf8_encode(file_get_contents('application/libraries/defectos.json', true)));
//        var_dump($def);
        foreach ($def as $d) {
            if ($nombre_defecto == $d->nombre_defecto || $d->nombre_defecto == 'ensenanza') {
                if ($this->ajustarGrupos == "1") {
                    if ($nombre_defecto == "liviano pesado") {
                        if ($d->nombre_grupo == "LUCES") {
                            $d->nombre_grupo = "ALUMBRADO Y SEÑALIZACION";
                        }
                        if ($d->nombre_grupo == "VIDRIOS" || $d->codigo == "1.1.1.5.1" || $d->codigo == "1.1.1.5.2") {
                            $d->nombre_grupo = "REVISION EXTERIOR";
                        }
                        if ($d->codigo == "1.1.12.38.1" ||
                                $d->codigo == "1.1.12.38.2" ||
                                $d->codigo == "1.1.12.38.3" ||
                                $d->codigo == "1.1.12.38.4" ||
                                $d->codigo == "1.1.12.38.5") {
                            $d->nombre_grupo = "MOTOR";
                        }
                        if (
                                $d->codigo == "1.1.3.11.1" ||
                                $d->codigo == "1.1.3.11.2" ||
                                $d->codigo == "1.1.3.12.1" ||
                                $d->codigo == "1.1.3.11.3"
                        ) {
                            $d->nombre_grupo = "ELEMENTOS PARA PRODUCIR RUIDO";
                        }
                        if ($d->codigo == "1.1.13.39.1") {
                            $d->nombre_grupo = "SISTEMA DE COMBUSTIBLE";
                        }
                        if ($d->codigo == "1.1.14.40.1" ||
                                $d->codigo == "1.1.14.40.2" ||
                                $d->codigo == "1.1.14.40.3" ||
                                $d->codigo == "1.1.14.40.4" ||
                                $d->codigo == "1.1.14.40.5") {
                            $d->nombre_grupo = "TRANSMISION";
                        }
                        if ($d->nombre_grupo == "FRENOS") {
                            $d->nombre_grupo = "SISTEMA DE FRENOS";
                        }
                        if ($d->nombre_grupo == "SUSPENSION (SUSPENSION, RINES Y LLANTAS)") {
                            $d->nombre_grupo = "SUSPENSION";
                        }

                        if ($d->nombre_grupo == "REVISION EXTERIOR") {
                            $d->nombre_grupo = "6.1 REVISION EXTERIOR";
                        }
                        if ($d->nombre_grupo == "REVISION INTERIOR") {
                            $d->nombre_grupo = "6.2 REVISION INTERIOR";
                        }
                        if ($d->nombre_grupo == "ELEMENTOS PARA PRODUCIR RUIDO") {
                            $d->nombre_grupo = "6.3 ELEMENTOS PARA PRODUCIR RUIDO";
                        }
                        if ($d->nombre_grupo == "ALUMBRADO Y SEÑALIZACION") {
                            $d->nombre_grupo = "6.4 ALUMBRADO Y SEÑALIZACION";
                        }
                        if ($d->nombre_grupo == "SALIDA DE EMERGENCIA") {
                            $d->nombre_grupo = "6.5 SALIDA DE EMERGENCIA";
                        }
                        if ($d->nombre_grupo == "EMISIONES CONTAMINANTES") {
                            $d->nombre_grupo = "6.6 EMISIONES CONTAMINANTES";
                        }
                        if ($d->nombre_grupo == "SISTEMA DE FRENOS") {
                            $d->nombre_grupo = "6.7 SISTEMA DE FRENOS";
                        }
                        if ($d->nombre_grupo == "SUSPENSION") {
                            $d->nombre_grupo = "6.8 SUSPENSION";
                        }
                        if ($d->nombre_grupo == "TAXIMETROS") {
                            $d->nombre_grupo = "6.9 TAXIMETROS";
                        }
                        if ($d->nombre_grupo == "DIRECCION") {
                            $d->nombre_grupo = "6.10 DIRECCION";
                        }
                        if ($d->nombre_grupo == "RINES Y LLANTAS") {
                            $d->nombre_grupo = "6.11 RINES Y LLANTAS";
                        }
                        if ($d->nombre_grupo == "MOTOR") {
                            $d->nombre_grupo = "6.12 MOTOR";
                        }
                        if ($d->nombre_grupo == "SISTEMA DE COMBUSTIBLE") {
                            $d->nombre_grupo = "6.13 SISTEMA DE COMBUSTIBLE";
                        }
                        if ($d->nombre_grupo == "TRANSMISION") {
                            $d->nombre_grupo = "6.14 TRANSMISION";
                        }
                    }
                    if ($nombre_defecto == "motocicletas y motociclos") {
                        if ($d->nombre_grupo == "REVISION EXTERIOR" || $d->codigo == "1.1.1.5.1" || $d->codigo == "1.1.1.5.2" || $d->codigo == "1.2.1.2.1" || $d->codigo == "1.2.1.2.2") {
                            $d->nombre_grupo = "7.1 ACONDICIONAMIENTO EXTERIOR";
                        }
                        if ($d->codigo == "1.2.2.3.1") {
                            $d->nombre_grupo = "7.2 SILLIN Y REPOSAPIES";
                        }

                        if ($d->codigo == "1.2.3.4.1" || $d->codigo == "1.2.3.5.1") {
                            $d->nombre_grupo = "7.3 ELEMENTOS PARA PRODUCIR RUIDO";
                        }
                        if ($d->nombre_grupo == "LUCES") {
                            $d->nombre_grupo = "7.4 ALUMBRADO Y SEÑALIZACION";
                        }
                        if ($d->nombre_grupo == "FRENOS") {
                            $d->nombre_grupo = "7.6 SISTEMA DE FRENOS";
                        }
                        if ($d->nombre_grupo == "SUSPENSION (SUSPENSION, RINES Y LLANTAS)") {
                            $d->nombre_grupo = "7.7 SUSPENSION";
                        }
                        if ($d->nombre_grupo == "DIRECCION") {
                            $d->nombre_grupo = "7.8 DIRECCION";
                        }
                        if ($d->nombre_grupo == "RINES Y LLANTAS") {
                            $d->nombre_grupo = "7.9 RINES Y LLANTAS";
                        }
//                        if ($d->codigo == "1.2.5.8.1" || $d->codigo == "1.2.3.4.1" || $d->codigo == "1.2.3.5.1") {
//                            $d->nombre_grupo = "7.5 EMISIONES CONTAMINANTES EN LOS GASES DE ESCAPE";
//                        }
                        if ($d->nombre_grupo == "EMISIONES CONTAMINANTES") {
                            $d->nombre_grupo = "7.5 EMISIONES CONTAMINANTES EN LOS GASES DE ESCAPE";
                        }
                        if ($d->codigo == "1.2.10.19.1") {
                            $d->nombre_grupo = "7.10 SOPORTE DE ESTACIONAMIENTO";
                        }
                        if ($d->nombre_grupo == "MOTOR, TRANSMISION Y COMBUSTIBLE") {
                            $d->nombre_grupo = "7.11 MOTOR Y CAJA";
                        }
                    }

                    if ($nombre_defecto == "motocarro") {
                        if ($d->nombre_grupo == "REVISION EXTERIOR" ||
                                $d->codigo == "1.4.1.2.1" ||
                                $d->codigo == "1.4.1.2.2" ||
                                $d->codigo == "1.4.1.2.3" ||
                                $d->codigo == "1.4.1.2.4" ||
                                $d->codigo == "1.4.1.2.5" ||
                                $d->codigo == "1.4.1.2.6" ||
                                $d->codigo == "1.4.1.3.1" ||
                                $d->codigo == "1.4.1.3.2" ||
                                $d->codigo == "1.4.1.4.1" ||
                                $d->codigo == "1.4.1.4.3"
                        ) {
                            $d->nombre_grupo = "9.1 ACONDICIONAMIENTO EXTERIOR";
                        }
                        if ($d->nombre_grupo == "REVISION INTERIOR") {
                            $d->nombre_grupo = "9.2 REVISION INTERIOR";
                        }
                        if ($d->codigo == "1.4.3.10.1" || $d->codigo == "1.4.3.9.1") {
                            $d->nombre_grupo = "9.3 ELEMENTOS PARA PRODUCIR RUIDO";
                        }
                        if ($d->nombre_grupo == "LUCES") {
                            $d->nombre_grupo = "9.4 ALUMBRADO Y SEÑALIZACION";
                        }
                        if ($d->codigo == "1.4.5.13.1") {
                            $d->nombre_grupo = "9.5 EMISIONES CONTAMINANTES EN LOS GASES DE ESCAPE";
                        }
                        if ($d->nombre_grupo == "FRENOS") {
                            $d->nombre_grupo = "9.6 SISTEMA DE FRENOS";
                        }
                        if ($d->nombre_grupo == "SUSPENSION (SUSPENSION, RINES Y LLANTAS)") {
                            $d->nombre_grupo = "9.7 SUSPENSION";
                        }
                        if ($d->nombre_grupo == "DIRECCION") {
                            $d->nombre_grupo = "9.8 DIRECCION";
                        }
                        if ($d->nombre_grupo == "RINES Y LLANTAS") {
                            $d->nombre_grupo = "9.9 RINES Y LLANTAS";
                        }
                        if ($d->nombre_grupo == "RINES Y LLANTAS") {
                            $d->nombre_grupo = "9.9 RINES Y LLANTAS";
                        }
                        if ($d->codigo == "1.4.10.24.1" ||
                                $d->codigo == "1.4.10.24.2" ||
                                $d->codigo == "1.4.10.24.3" ||
                                $d->codigo == "1.4.10.24.4") {
                            $d->nombre_grupo = "9.10 MOTOR Y CAJA";
                        }
                    }
                    if ($d->nombre_grupo == "ENSEÑANZA") {
                        $d->nombre_grupo = "ANEXO A (Normativo) ADAPTACIONES DE LOS VEHÍCULOS UTILIZADOS PARA IMPARTIR LA ENSEÑANZA AUTOMOVILÍSTICA";
                    }
                    if ($d->nombre_grupo == "PLACAS LATERALES") {
                        $d->nombre_grupo = "ANEXO B (Normativo) TERCERA PLACA PARA VEHÍCULOS DE SERVICIO PÚBLICO";
                    }
                }
                array_push($this->defectos, $d);
            }
        }
    }

//------------------------------------------------------------------------------Obtener datos del cda
    public function getCda() {
        $result = $this->Mcda->get();
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

//------------------------------------------------------------------------------Obtener consecutivo
    public function getConsecutivo($idhojapruebas) {
        $data['idhojapruebas'] = $idhojapruebas;
        $result = $this->MconsecutivoTC->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0]->idconsecutivotc;
        } else {
            return $idhojapruebas;
        }
    }

//------------------------------------------------------------------------------Obtener número ONAC
    public function getNumeroOnac() {
        $data['idconfig_prueba'] = '183';
        $result = $this->Mconfig_prueba->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0]->valor;
        } else {
            return '';
        }
    }

    public function getSede() {
        $result = $this->Msede->get();
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getHojatrabajo($idhojapruebas) {
        $data['idhojapruebas'] = $idhojapruebas;
        $result = $this->Mhojatrabajo->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getFechaReins($idhojapruebas) {
        $data['idhojapruebas'] = $idhojapruebas;
        $result = $this->Mprueba->getLast($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getFechaLastReins($idhojapruebas, $order) {
        $data['idhojapruebas'] = $idhojapruebas;
        $result = $this->Mprueba->getLastFecha($data, $order);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getVehiculo($idvehiculo) {
        $data['idvehiculo'] = $idvehiculo;
        $result = $this->Mvehiculo->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getPropietario($idpropietario) {
        $data['idcliente'] = $idpropietario;
        $result = $this->Mpropietario->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getCiudad($cod_ciudad) {
        $data['cod_ciudad'] = $cod_ciudad;
        $result = $this->Mciudad->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getDepartamento($cod_depto) {
        $data['cod_depto'] = $cod_depto;
        $result = $this->Mdepartamento->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getPais($idpais) {
        $data['idpais'] = $idpais;
        $result = $this->Mpais->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getLinea($idlinea, $vehiculo) {
        if ($vehiculo->registrorunt == '1') {
            $data['idlineaRUNT'] = $idlinea;
            $result = $this->Mlinea->get($data);
        } else {
            $data['idlinea'] = $idlinea;
            $result = $this->Mlinea->get2($data);
        }
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getMarca($idmarca, $vehiculo) {
        if ($vehiculo->registrorunt == '1') {
            $data['idmarcaRUNT'] = $idmarca;
            $result = $this->Mmarca->get($data);
        } else {
            $data['idmarca'] = $idmarca;
            $result = $this->Mmarca->get2($data);
        }
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getServicio($idservicio) {
        $data['idservicio'] = $idservicio;
        $result = $this->Mservicio->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getClase($idclase) {
        $data['idclase'] = $idclase;
        $result = $this->Mclase->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getColor($idcolor, $vehiculo) {
        if ($vehiculo->registrorunt == '1') {
            $data['idcolorRUNT'] = $idcolor;
            $result = $this->Mcolor->get($data);
        } else {
            $data['idcolor'] = $idcolor;
            $result = $this->Mcolor->get2($data);
        }
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getCombustible($idcombustible) {
        $data['idtipocombustible'] = $idcombustible;
        $result = $this->Mcombustible->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getCarroceria($idcarroceria) {
        $data['idcarroceria'] = $idcarroceria;
        $result = $this->Mcarroceria->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

    public function getUsuario($IdUsuario) {
        $data['IdUsuario'] = $IdUsuario;
        $result = $this->Musuarios->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return 'NA';
        }
    }

//------------------------------------------------------------------------------LUCES
    var $ifdefLuzMinBaja;
    var $ifdefInclinacionLuces;
    var $ifdefIntensidadTotal;

    public function getLuces($idhojapruebas, $vehiculo) {
        $this->ifLuzMinBaja = false;
        $this->ifdefInclinacionLuces = false;
        $this->ifdefIntensidadTotal = false;
        $data['idhojapruebas'] = $idhojapruebas;
        $data['idtipo_prueba'] = "1";
        $data['order'] = $this->order;
        $result = $this->Mprueba->get($data);
        $configLuces = "";
        $simultaneaBaja = '';
        $simultaneaAlta = '';
        $simultaneaAntiniebla = '';
        $max_luz_total = '';
        $intensidad_total = '';
//        $aprobadoLuces = true;
        if ($result->num_rows() > 0) {
            $r = $result->result();
            $data['idprueba'] = $r[0]->idprueba;
            $operario = $this->getUsuario($r[0]->idusuario);
            if ($r[0]->estado == '1' || $r[0]->estado == '3') {
                $this->aprobado = false;
//                $aprobadoLuces = false;
            }
//            $lucesParam = new luces_param();
            $rango_min_inc = $this->getRango_min_inc();
            $rango_max_inc = $this->getRango_max_inc();
            $min_luz_baja = $this->getMin_luz_baja();
            $max_luz_total = $this->getMax_luz_total();
            $simultanea = $this->getResultado($data, 'independiente', '');
            $configLuces = $this->getResultado($data, 'optSimul', '');
            $conmuLux = $this->getResultado($data, 'conmuLux', '');
//            $valor_antiniebla_izquierda_1 = $this->getResultadoIDCF($data, 191);
            $intensidad_total = $this->evalLuzMaxTotal($this->getResultado($data, 'intensidad_total', ''), $max_luz_total);
            if ($vehiculo->idclase === 10) {
                $intensidad_total = "";
            }
            if ($vehiculo->tipo_vehiculo !== '3') {
                $alta_izquierda = $this->rdnr($this->getResultado($data, '1', 'alta_izquierda'));
                $alta_derecha = $this->rdnr($this->getResultado($data, '1', 'alta_derecha'));
                $valor_antiniebla_derecha_1 = $this->rdnr($this->getResultado($data, '1', 'antis_derecha'));
                $valor_antiniebla_izquierda_1 = $this->rdnr($this->getResultado($data, '1', 'antis_izquierda'));
            } else {
//                $alta_izquierda = $this->evalLuzMinBaja($this->rdnr($this->getResultado($data, '1', 'alta_izquierda')), $min_luz_baja);
                $alta_izquierda = $this->rdnr($this->getResultado($data, '1', 'alta_izquierda'));
                $alta_derecha = $this->rdnr($this->getResultado($data, '1', 'alta_derecha'));
//                $alta_derecha = $this->evalLuzMinBaja($this->rdnr($this->getResultado($data, '1', 'alta_derecha')), $min_luz_baja);
                $valorAD = $this->getResultado($data, '1', 'antis_derecha');
                $valorAI = $this->getResultado($data, '1', 'antis_izquierda');
                if ($valorAD !== '') {
                    $valor_antiniebla_derecha_1 = $this->evalLuzMinBaja($this->rdnr($valorAD), $min_luz_baja);
                } else {
                    $valor_antiniebla_derecha_1 = '';
                }
                if ($valorAI !== '') {
                    $valor_antiniebla_izquierda_1 = $this->evalLuzMinBaja($this->rdnr($valorAI), $min_luz_baja);
                } else {
                    $valor_antiniebla_izquierda_1 = '';
                }
                if ($this->evalLucFullMotos == "0") {
                    $alta_izquierda = '';
                    $alta_derecha = '';
                    $valor_antiniebla_izquierda_1 = '';
                    $valor_antiniebla_derecha_1 = '';
                }
            }
            if ($simultanea == '0') {
                if ($conmuLux !== "") {
                    if (substr($conmuLux, 0, 1) == "1") {
                        $simultaneaBaja = 'SI';
                    } else {
                        $simultaneaBaja = 'NO';
                    }
                    if (substr($conmuLux, 1, 1) == "1") {
                        $simultaneaAlta = 'SI';
                    } else {
                        $simultaneaAlta = 'NO';
                    }
                    if (substr($conmuLux, 2, 1) == "1") {
                        $simultaneaAntiniebla = 'SI';
                    } else {
                        $simultaneaAntiniebla = 'NO';
                    }
                }

                if ($configLuces == "1" && $this->obseConfLuces == '1') {
                    $configLuces = "<strong>Configuración de encendido de las luces:</strong><br>->ALTAS Y ANTINIEBLAS SON SIMULTANEAS<br>->BAJAS Y ANTINIEBLAS SON SIMULTANEAS<br>->BAJAS Y ALTAS NO SON SIMULTANEAS";
                } else if ($configLuces == "2" && $this->obseConfLuces == '1') {
                    $configLuces = "<strong>Configuración de encendido de las luces:</strong><br>->ALTAS Y BAJAS SON SIMULTANEAS<br>->BAJAS Y ANTINIEBLAS SON SIMULTANEAS<br>->ALTAS Y ANTINIEBLAS NO SON SIMULTANEAS";
                } else if ($configLuces == "3" && $this->obseConfLuces == '1') {
                    $configLuces = "<strong>Configuración de encendido de las luces:</strong><br>->ALTAS Y ANTINIEBLAS SON SIMULTANEAS<br>->BAJAS Y ALTAS SON SIMULTANEAS<br>->BAJAS Y ANTINIEBLAS NO SON SIMULTANEAS";
                } else {
                    $configLuces = "";
//                    $simultaneaAlta = 'NO';
//                    $simultaneaBaja = 'NO';
//                    $simultaneaAntiniebla = 'SI';
                }
            } else {
                $configLuces = "";
                $simultaneaAlta = 'SI';
                $simultaneaBaja = 'SI';
                $simultaneaAntiniebla = 'SI';
            }
            if ($this->nombreClase->nombre == 'MOTOCICLETA' || $this->nombreClase->nombre == 'MOTOCARRO') {
                $configLuces = "";
                $simultaneaBaja = '';
                $simultaneaAlta = '';
                $simultaneaAntiniebla = '';
                $max_luz_total = '';
                $intensidad_total = '';
//                $alta_izquierda = '';
//                $alta_derecha = '';
            }
            $valor_antiniebla_derecha_2 = $this->rdnr($this->getResultado($data, '2', 'antis_derecha'));
            $valor_antiniebla_derecha_3 = $this->rdnr($this->getResultado($data, '3', 'antis_derecha'));
            $valor_antiniebla_derecha_4 = $this->rdnr($this->getResultado($data, '4', 'antis_derecha'));
            $valor_antiniebla_derecha_5 = $this->rdnr($this->getResultado($data, '5', 'antis_derecha'));
            $valor_antiniebla_izquierda_2 = $this->rdnr($this->getResultado($data, '2', 'antis_izquierda'));
            $valor_antiniebla_izquierda_3 = $this->rdnr($this->getResultado($data, '3', 'antis_izquierda'));
            $valor_antiniebla_izquierda_4 = $this->rdnr($this->getResultado($data, '4', 'antis_izquierda'));
            $valor_antiniebla_izquierda_5 = $this->rdnr($this->getResultado($data, '5', 'antis_izquierda'));
            if ($valor_antiniebla_derecha_1 == '' &&
                    $valor_antiniebla_derecha_2 == '' &&
                    $valor_antiniebla_derecha_3 == '' &&
                    $valor_antiniebla_derecha_4 == '' &&
                    $valor_antiniebla_derecha_5 == '' &&
                    $valor_antiniebla_izquierda_1 == '' &&
                    $valor_antiniebla_izquierda_2 == '' &&
                    $valor_antiniebla_izquierda_3 == '' &&
                    $valor_antiniebla_izquierda_4 == '' &&
                    $valor_antiniebla_izquierda_5 == '') {
                $simultaneaAntiniebla = "";
                if ($simultanea == '0') {
                    $simultaneaAlta = 'NO';
                    $simultaneaBaja = 'NO';
                }
            }
//            $intensidad = "321" . "*";
//            $asterisco = false;

            if ($intensidad_total !== "") {
                if (substr($intensidad_total, strlen($intensidad_total) - 1) == "*") {
//                $asterisco = true;
                    $intensidad_total = substr($intensidad_total, 0, strlen($intensidad_total) - 1);
                    $intensidad_total = $this->rdnr($intensidad_total) . "*";
                } else {
                    $intensidad_total = $this->rdnr($intensidad_total);
                }
            }
            $luces = (object)
                    array(
                        'idprueba' => $r[0]->idprueba,
                        'valor_baja_derecha_1' => $this->evalLuzMinBaja($this->rdnr($this->getResultado($data, '1', 'baja_derecha')), $min_luz_baja),
                        'valor_baja_derecha_2' => $this->evalLuzMinBaja($this->rdnr($this->getResultado($data, '2', 'baja_derecha')), $min_luz_baja),
                        'valor_baja_derecha_3' => $this->evalLuzMinBaja($this->rdnr($this->getResultado($data, '3', 'baja_derecha')), $min_luz_baja),
                        'valor_baja_derecha_4' => $this->evalLuzMinBaja($this->rdnr($this->getResultado($data, '4', 'baja_derecha')), $min_luz_baja),
                        'valor_baja_derecha_5' => $this->evalLuzMinBaja($this->rdnr($this->getResultado($data, '5', 'baja_derecha')), $min_luz_baja),
                        'inclinacion_baja_derecha_1' => $this->evalInclinacionLuces($this->rdnr($this->getResultado($data, '1', 'inclinacion_derecha')), $rango_min_inc, $rango_max_inc),
                        'inclinacion_baja_derecha_2' => $this->evalInclinacionLuces($this->rdnr($this->getResultado($data, '2', 'inclinacion_derecha')), $rango_min_inc, $rango_max_inc),
                        'inclinacion_baja_derecha_3' => $this->evalInclinacionLuces($this->rdnr($this->getResultado($data, '3', 'inclinacion_derecha')), $rango_min_inc, $rango_max_inc),
                        'inclinacion_baja_derecha_4' => $this->evalInclinacionLuces($this->rdnr($this->getResultado($data, '4', 'inclinacion_derecha')), $rango_min_inc, $rango_max_inc),
                        'inclinacion_baja_derecha_5' => $this->evalInclinacionLuces($this->rdnr($this->getResultado($data, '5', 'inclinacion_derecha')), $rango_min_inc, $rango_max_inc),
                        'valor_alta_derecha_1' => $alta_derecha,
                        'valor_alta_derecha_2' => $this->rdnr($this->getResultado($data, '2', 'alta_derecha')),
                        'valor_alta_derecha_3' => $this->rdnr($this->getResultado($data, '3', 'alta_derecha')),
                        'valor_alta_derecha_4' => $this->rdnr($this->getResultado($data, '4', 'alta_derecha')),
                        'valor_alta_derecha_5' => $this->rdnr($this->getResultado($data, '5', 'alta_derecha')),
                        'valor_antiniebla_derecha_1' => $valor_antiniebla_derecha_1,
                        'valor_antiniebla_derecha_2' => $valor_antiniebla_derecha_2,
                        'valor_antiniebla_derecha_3' => $valor_antiniebla_derecha_3,
                        'valor_antiniebla_derecha_4' => $valor_antiniebla_derecha_4,
                        'valor_antiniebla_derecha_5' => $valor_antiniebla_derecha_5,
                        'valor_baja_izquierda_1' => $this->evalLuzMinBaja($this->rdnr($this->getResultado($data, '1', 'baja_izquierda')), $min_luz_baja),
                        'valor_baja_izquierda_2' => $this->evalLuzMinBaja($this->rdnr($this->getResultado($data, '2', 'baja_izquierda')), $min_luz_baja),
                        'valor_baja_izquierda_3' => $this->evalLuzMinBaja($this->rdnr($this->getResultado($data, '3', 'baja_izquierda')), $min_luz_baja),
                        'valor_baja_izquierda_4' => $this->evalLuzMinBaja($this->rdnr($this->getResultado($data, '4', 'baja_izquierda')), $min_luz_baja),
                        'valor_baja_izquierda_5' => $this->evalLuzMinBaja($this->rdnr($this->getResultado($data, '5', 'baja_izquierda')), $min_luz_baja),
                        'inclinacion_baja_izquierda_1' => $this->evalInclinacionLuces($this->rdnr($this->getResultado($data, '1', 'inclinacion_izquierda')), $rango_min_inc, $rango_max_inc),
                        'inclinacion_baja_izquierda_2' => $this->evalInclinacionLuces($this->rdnr($this->getResultado($data, '2', 'inclinacion_izquierda')), $rango_min_inc, $rango_max_inc),
                        'inclinacion_baja_izquierda_3' => $this->evalInclinacionLuces($this->rdnr($this->getResultado($data, '3', 'inclinacion_izquierda')), $rango_min_inc, $rango_max_inc),
                        'inclinacion_baja_izquierda_4' => $this->evalInclinacionLuces($this->rdnr($this->getResultado($data, '4', 'inclinacion_izquierda')), $rango_min_inc, $rango_max_inc),
                        'inclinacion_baja_izquierda_5' => $this->evalInclinacionLuces($this->rdnr($this->getResultado($data, '5', 'inclinacion_izquierda')), $rango_min_inc, $rango_max_inc),
                        'valor_alta_izquierda_1' => $alta_izquierda,
                        'valor_alta_izquierda_2' => $this->rdnr($this->getResultado($data, '2', 'alta_izquierda')),
                        'valor_alta_izquierda_3' => $this->rdnr($this->getResultado($data, '3', 'alta_izquierda')),
                        'valor_alta_izquierda_4' => $this->rdnr($this->getResultado($data, '4', 'alta_izquierda')),
                        'valor_alta_izquierda_5' => $this->rdnr($this->getResultado($data, '5', 'alta_izquierda')),
                        'valor_antiniebla_izquierda_1' => $valor_antiniebla_izquierda_1,
                        'valor_antiniebla_izquierda_2' => $valor_antiniebla_izquierda_2,
                        'valor_antiniebla_izquierda_3' => $valor_antiniebla_izquierda_3,
                        'valor_antiniebla_izquierda_4' => $valor_antiniebla_izquierda_4,
                        'valor_antiniebla_izquierda_5' => $valor_antiniebla_izquierda_5,
                        'intensidad_minima' => $min_luz_baja,
                        'intensidad_minimaBI' => $min_luz_baja,
                        'intensidad_minimaAD' => $min_luz_baja,
                        'intensidad_minimaAI' => $min_luz_baja,
                        'inclinacion_rango' => "$rango_min_inc a $rango_max_inc",
                        'inclinacion_rangoBI' => "$rango_min_inc a $rango_max_inc",
                        'intensidad_total' => $intensidad_total,
                        'intensidad_maxima' => $max_luz_total,
                        'simultaneaBaja' => $simultaneaBaja,
                        'simultaneaAlta' => $simultaneaAlta,
                        'simultaneaAntiniebla' => $simultaneaAntiniebla,
                        'configLuces' => $configLuces,
                        'operario' => $operario->nombres . " " . $operario->apellidos,
                        'documento' => $operario->identificacion
            );
        } else {
            $luces = (object)
                    array(
                        'idprueba' => '',
                        'valor_baja_derecha_1' => '',
                        'valor_baja_derecha_2' => '',
                        'valor_baja_derecha_3' => '',
                        'valor_baja_derecha_4' => '',
                        'valor_baja_derecha_5' => '',
                        'inclinacion_baja_derecha_1' => '',
                        'inclinacion_baja_derecha_2' => '',
                        'inclinacion_baja_derecha_3' => '',
                        'inclinacion_baja_derecha_4' => '',
                        'inclinacion_baja_derecha_5' => '',
                        'valor_alta_derecha_1' => '',
                        'valor_alta_derecha_2' => '',
                        'valor_alta_derecha_3' => '',
                        'valor_alta_derecha_4' => '',
                        'valor_alta_derecha_5' => '',
                        'valor_antiniebla_derecha_1' => '',
                        'valor_antiniebla_derecha_2' => '',
                        'valor_antiniebla_derecha_3' => '',
                        'valor_antiniebla_derecha_4' => '',
                        'valor_antiniebla_derecha_5' => '',
                        'valor_baja_izquierda_1' => '',
                        'valor_baja_izquierda_2' => '',
                        'valor_baja_izquierda_3' => '',
                        'valor_baja_izquierda_4' => '',
                        'valor_baja_izquierda_5' => '',
                        'inclinacion_baja_izquierda_1' => '',
                        'inclinacion_baja_izquierda_2' => '',
                        'inclinacion_baja_izquierda_3' => '',
                        'inclinacion_baja_izquierda_4' => '',
                        'inclinacion_baja_izquierda_5' => '',
                        'valor_alta_izquierda_1' => '',
                        'valor_alta_izquierda_2' => '',
                        'valor_alta_izquierda_3' => '',
                        'valor_alta_izquierda_4' => '',
                        'valor_alta_izquierda_5' => '',
                        'valor_antiniebla_izquierda_1' => '',
                        'valor_antiniebla_izquierda_2' => '',
                        'valor_antiniebla_izquierda_3' => '',
                        'valor_antiniebla_izquierda_4' => '',
                        'valor_antiniebla_izquierda_5' => '',
                        'intensidad_minima' => '',
                        'intensidad_minimaBI' => '',
                        'intensidad_minimaAD' => '',
                        'intensidad_minimaAI' => '',
                        'inclinacion_rango' => '',
                        'inclinacion_rangoBI' => '',
                        'intensidad_total' => '',
                        'intensidad_maxima' => '',
                        'simultaneaBaja' => '',
                        'simultaneaAlta' => '',
                        'simultaneaAntiniebla' => '',
                        'configLuces' => '',
                        'operario' => '',
                        'documento' => ''
            );
        }
//        if (!$aprobadoLuces) {
//            
//        }

        return $luces;
    }

//..............................................................................EVAL LUCES

    function evalInclinacionLuces($dato, $rangoMin, $rangoMax) {
        if ($dato !== '') {
            if (floatval($rangoMin) <= floatval($dato) && floatval($rangoMax) >= floatval($dato)) {
                return $dato;
            } else {
                $this->setDefLuz();
                return $dato . "*";
            }
        }
    }

    function setDefLuz() {
        if (!$this->ifdefInclinacionLuces) {
            $this->ifdefInclinacionLuces = true;
            $grupo = 'LUCES';
            switch ($this->nombreClase->nombre) {
                case "MOTOCICLETA":
                    if ($this->ajustarGrupos == "1") {
                        $grupo = '7.4 ALUMBRADO Y SEÑALIZACION';
                    }
                    array_push($this->defectosMA, (object) array(
                                "codigo" => '1.2.4.7.2',
                                "descripcion" => 'La desviación de cualquier haz de luz en posición de bajas está por fuera del rango 0.5 y 3.5%, siendo 0 el horizonte y 3.5% la desviación hacia el piso.',
                                "grupo" => $grupo,
                                "tipo" => 'A'
                    ));
                    break;
                case "MOTOCARRO":
                    if ($this->ajustarGrupos == "1") {
                        $grupo = '9.4 ALUMBRADO Y SEÑALIZACION';
                    }
                    array_push($this->defectosMA, (object) array(
                                "codigo" => '1.4.4.12.2',
                                "descripcion" => 'La desviación de cualquier haz de luz en posición de bajas está por fuera del rango 0.5 y 3.5%, siendo 0 el horizonte y 3.5% la desviación hacia el piso.',
                                "grupo" => $grupo,
                                "tipo" => 'A'
                    ));
                    break;
                case "CUATRIMOTO":
                    if ($this->ajustarGrupos == "1") {
                        $grupo = 'ALUMBRADO Y SEÑALIZACION';
                    }
                    array_push($this->defectosMA, (object) array(
                                "codigo" => '5.1.3.2.3',
                                "descripcion" => 'La desviación de cualquier haz de luz en posición de bajas esta por fuera de rango 0.5 y 3.5%, siendo 0 el horizonte y 3.5% la desviación hacia el piso.',
                                "grupo" => $grupo,
                                "tipo" => 'A'
                    ));
                    break;

                default:
                    if ($this->ajustarGrupos == "1") {
                        $grupo = '6.4 ALUMBRADO Y SEÑALIZACION';
                    }
                    array_push($this->defectosMA, (object) array(
                                "codigo" => '1.4.4.12.2',
                                "descripcion" => 'La desviación de cualquier haz de luz en posición de bajas está por fuera del rango 0.5 y 3.5%, siendo 0 el horizonte y 3.5% la desviación hacia el piso.',
                                "grupo" => $grupo,
                                "tipo" => 'A'
                    ));
                    break;
            }
        }
    }

    function evalLuzMinBaja($dato, $luzMin) {
        if ($dato !== '') {
            if (floatval($luzMin) <= floatval($dato)) {
                return $dato;
            } else {
                if (!$this->ifdefLuzMinBaja) {
                    $this->ifdefLuzMinBaja = true;
                    $grupo = 'LUCES';
                    switch ($this->nombreClase->nombre) {
                        case "MOTOCICLETA":
                            if ($this->ajustarGrupos == "1") {
                                $grupo = '7.4 ALUMBRADO Y SEÑALIZACION';
                            }
                            array_push($this->defectosMA, (object) array(
                                        "codigo" => '1.2.4.7.1',
                                        "descripcion" => 'La intensidad de la luz menor a 2,5 klux a 1 m o 4 lux a 25 m. Se debe acelerar la moto hasta lograr la mayor intensidad de luz.',
                                        "grupo" => $grupo,
                                        "tipo" => 'A'
                            ));
                            break;
                        case "MOTOCARRO":
                            if ($this->ajustarGrupos == "1") {
                                $grupo = '9.4 ALUMBRADO Y SEÑALIZACION';
                            }
                            array_push($this->defectosMA, (object) array(
                                        "codigo" => '1.4.4.12.1',
                                        "descripcion" => 'La intensidad de la luz menor a 2.5 klux a 1 m o 4 lux a 25 m. NOTA: Cuando sea necesario, se debe acelerar al motocarro hasta lograr la mayor intensidad de luz.',
                                        "grupo" => $grupo,
                                        "tipo" => 'A'
                            ));
                            break;
                        case "CUATRIMOTO":
                            if ($this->ajustarGrupos == "1") {
                                $grupo = 'ALUMBRADO Y SEÑALIZACION';
                            }
                            array_push($this->defectosMA, (object) array(
                                        "codigo" => '5.1.3.2.1',
                                        "descripcion" => 'La intensidad en algún haz de luz baja, es inferior a los 2,5 klux a 1m o 4 lux a 25m.',
                                        "grupo" => $grupo,
                                        "tipo" => 'A'
                            ));
                            break;
                        default:
                            if ($this->ajustarGrupos == "1") {
                                $grupo = '6.4 ALUMBRADO Y SEÑALIZACION';
                            }
                            array_push($this->defectosMA, (object) array(
                                        "codigo" => '1.1.4.14.1',
                                        "descripcion" => 'La intensidad en algún haz de luz baja, es inferior a los 2,5 Klux a 1 m ó 4 lux a 25 m.',
                                        "grupo" => $grupo,
                                        "tipo" => 'A'
                            ));
                            break;
                    }
                }
                return $dato . "*";
            }
        }
    }

    function evalLuzMaxTotal($dato, $luzMax) {
        if ($dato !== '' && $this->nombreClase->nombre !== 'MOTOCICLETA' && $this->nombreClase->nombre !== 'MOTOCARRO') {
            if (floatval($luzMax) >= floatval($dato)) {
                return $dato;
            } else {
                $grupo = 'LUCES';
                $codigo = '1.1.4.14.2';
                if ($this->nombreClase->nombre == 'CUATRIMOTO') {
                    $codigo = '5.1.3.2.2';
                }
                if ($this->ajustarGrupos == "1") {
                    $grupo = '6.4 ALUMBRADO Y SEÑALIZACION';
                }
                if (!$this->ifdefIntensidadTotal) {
                    $this->ifdefIntensidadTotal = true;
                    array_push($this->defectosMA, (object) array(
                                "codigo" => $codigo,
                                "descripcion" => 'La intensidad sumada de todas las luces que se puedan encender simultáneamente, no puede ser superior a los 225 klux a 1 m de distancia o 360 lux a 25 m.',
                                "grupo" => $grupo,
                                "tipo" => 'A'
                    ));
                }
                return $dato . "*";
            }
        }
    }

//------------------------------------------------------------------------------SUSPENSION
    var $ifdefSuspension;

    public function getSuspension($idhojapruebas) {
        $this->ifdefSuspension = false;
        $data['idhojapruebas'] = $idhojapruebas;
        $data['idtipo_prueba'] = "9";
        $data['order'] = $this->order;
        $result = $this->Mprueba->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            $data['idprueba'] = $r[0]->idprueba;
            $operario = $this->getUsuario($r[0]->idusuario);
            if ($r[0]->estado == '1' || $r[0]->estado == '3') {
                $this->aprobado = false;
            }
//            $suspensionParam = new suspension_param();
            $min = $this->min();
            $suspension = (object)
                    array(
                        'idprueba' => $r[0]->idprueba,
                        'delantera_derecha' => $this->evalSuspension($this->rdnr($this->getResultadoIDCF($data, 142)), $min),
                        'trasera_derecha' => $this->evalSuspension($this->rdnr($this->getResultadoIDCF($data, 144)), $min),
                        'delantera_izquierda' => $this->evalSuspension($this->rdnr($this->getResultadoIDCF($data, 143)), $min),
                        'trasera_izquierda' => $this->evalSuspension($this->rdnr($this->getResultadoIDCF($data, 145)), $min),
                        'minima' => $min,
                        'operario' => $operario->nombres . " " . $operario->apellidos,
                        'documento' => $operario->identificacion);
        } else {
            $suspension = (object)
                    array(
                        'idprueba' => '',
                        'delantera_derecha' => '',
                        'trasera_derecha' => '',
                        'delantera_izquierda' => '',
                        'trasera_izquierda' => '',
                        'minima' => '',
                        'operario' => '',
                        'documento' => '');
        }
        return $suspension;
    }

//..............................................................................EVAL SUSPENSION
    function evalSuspension($dato, $min) {
        if ($dato !== '' && $this->nombreClase->nombre !== 'MOTOCICLETA' && $this->nombreClase->nombre !== 'MOTOCARRO') {
            if (floatval($min) <= floatval($dato)) {
                return $dato;
            } else {
                $this->setDefSus();
                return $dato . "*";
            }
        }
    }

    function setDefSus() {
        if (!$this->ifdefSuspension) {
            $grupo = "SUSPENSION (SUSPENSION, RINES Y LLANTAS)";
            if ($this->ajustarGrupos == "1") {
                $grupo = "6.8 SUSPENSION";
            }
            $this->ifdefSuspension = true;
            array_push($this->defectosMA, (object) array(
                        "codigo" => '1.1.8.33.1',
                        "descripcion" => 'Adherencia registrada en cualquier rueda inferior al 40%.',
                        "grupo" => $grupo,
                        "tipo" => 'A'
            ));
        }
    }

//------------------------------------------------------------------------------FRENOS
    var $ifDefDesequilibrioB;
    var $ifDefDesequilibrioA;
    var $ifDefEficaciaTotalA;
    var $ifDefEficaciaAuxilB;

    public function getFrenos($idhojapruebas, $vehiculo, $ocasion) {
        $this->ifDefDesequilibrioB = false;
        $this->ifDefDesequilibrioA = false;
        $this->ifDefEficaciaTotalA = false;
        $this->ifDefEficaciaAuxilB = false;
        $data['idhojapruebas'] = $idhojapruebas;
        $data['idtipo_prueba'] = "7";
        $data['order'] = $this->order;
        $result = $this->Mprueba->get($data);
//-----------------------------------------BUSCAR PESOS EN SUSPENSION
        $data2['idhojapruebas'] = $idhojapruebas;
        $data2['idtipo_prueba'] = "9";
        $data2['order'] = $this->order;
        $result2 = $this->Mprueba->get($data2);
        if ($result2->num_rows() > 0) {
            $r = $result2->result();
            $data2['idprueba'] = $r[0]->idprueba;
        } else {
            $data2['idprueba'] = "";
        }
        if ($result->num_rows() > 0) {
            $r = $result->result();
            $data['idprueba'] = $r[0]->idprueba;
            $operario = $this->getUsuario($r[0]->idusuario);
            if ($r[0]->estado == '1' || $r[0]->estado == '3') {
                $this->aprobado = false;
            }
//            $frenosParam = new frenos_param();
            $min_des_B = $this->min_des_B($vehiculo->tipo_vehiculo);
            $max_des_B = $this->max_des_B($vehiculo->tipo_vehiculo);
            $min_des_A = $this->min_des_A($vehiculo->tipo_vehiculo);
            $max_des_A = $this->max_des_A($vehiculo->tipo_vehiculo);
            $efi_min_total = $this->efi_min($vehiculo->tipo_vehiculo, 'total');
            $efi_min_auxil = $this->efi_min($vehiculo->tipo_vehiculo, 'auxil');
            $peso_1_derecho = $this->getResultadoIDCF_tipo($data, '1', 146);
            $peso_2_derecho = $this->getResultadoIDCF_tipo($data, '2', 146);
            $peso_3_derecho = $this->getResultadoIDCF_tipo($data, '3', 146);
            $peso_4_derecho = $this->getResultadoIDCF_tipo($data, '4', 146);
            $peso_5_derecho = $this->getResultadoIDCF_tipo($data, '5', 146);
            $peso_1_izquierdo = $this->getResultadoIDCF_tipo($data, '1', 147);
            $peso_2_izquierdo = $this->getResultadoIDCF_tipo($data, '2', 147);
            $peso_3_izquierdo = $this->getResultadoIDCF_tipo($data, '3', 147);
            $peso_4_izquierdo = $this->getResultadoIDCF_tipo($data, '4', 147);
            $peso_5_izquierdo = $this->getResultadoIDCF_tipo($data, '5', 147);
//Si no encuentra pesos en la prueba de frenos, los busca en suspensión
            if ($data2['idprueba'] !== "" &&
                    $peso_1_derecho == "" &&
                    $peso_2_derecho == "" &&
                    $peso_1_izquierdo == "" &&
                    $peso_2_izquierdo == "") {
                $peso_1_derecho = $this->getResultadoIDCF_tipo($data2, '1', 146);
                $peso_2_derecho = $this->getResultadoIDCF_tipo($data2, '2', 146);
                $peso_1_izquierdo = $this->getResultadoIDCF_tipo($data2, '1', 147);
                $peso_2_izquierdo = $this->getResultadoIDCF_tipo($data2, '2', 147);
            }

            $sum_peso_derecho = intval($peso_1_derecho) +
                    intval($peso_2_derecho) +
                    intval($peso_3_derecho) +
                    intval($peso_4_derecho) +
                    intval($peso_5_derecho);
            if ($sum_peso_derecho == 0) {
                $sum_peso_derecho = "";
            }
            $sum_peso_izquierdo = intval($peso_1_izquierdo) +
                    intval($peso_2_izquierdo) +
                    intval($peso_3_izquierdo) +
                    intval($peso_4_izquierdo) +
                    intval($peso_5_izquierdo);
            if ($sum_peso_izquierdo == 0) {
                $sum_peso_izquierdo = "";
            }
            $freno_1_derecho = "";
            $freno_2_derecho = "";
            $freno_3_derecho = "";
            $freno_4_derecho = "";
            $freno_5_derecho = "";
            $freno_1_izquierdo = "";
            $freno_2_izquierdo = "";
            $freno_3_izquierdo = "";
            $freno_4_izquierdo = "";
            $freno_5_izquierdo = "";
            if (intval($vehiculo->numejes) == 2) {
                $freno_1_derecho = $this->getResultadoIDCF_tipo($data, '1', 148);
                $freno_1_izquierdo = $this->getResultadoIDCF_tipo($data, '1', 149);
                $freno_2_derecho = $this->getResultadoIDCF_tipo($data, '2', 148);
                $freno_2_izquierdo = $this->getResultadoIDCF_tipo($data, '2', 149);
                $freno_3_derecho = '';
                $freno_3_izquierdo = '';
                $freno_4_derecho = '';
                $freno_4_izquierdo = '';
                $freno_5_derecho = '';
                $freno_5_izquierdo = '';
            } else if (intval($vehiculo->numejes) == 3) {
                $freno_1_derecho = $this->getResultadoIDCF_tipo($data, '1', 148);
                $freno_1_izquierdo = $this->getResultadoIDCF_tipo($data, '1', 149);
                $freno_2_derecho = $this->getResultadoIDCF_tipo($data, '2', 148);
                $freno_2_izquierdo = $this->getResultadoIDCF_tipo($data, '2', 149);
                $freno_3_derecho = $this->getResultadoIDCF_tipo($data, '3', 148);
                $freno_3_izquierdo = $this->getResultadoIDCF_tipo($data, '3', 149);
                $freno_4_derecho = '';
                $freno_4_izquierdo = '';
                $freno_5_derecho = '';
                $freno_5_izquierdo = '';
            } else if (intval($vehiculo->numejes) == 4) {
                $freno_1_derecho = $this->getResultadoIDCF_tipo($data, '1', 148);
                $freno_1_izquierdo = $this->getResultadoIDCF_tipo($data, '1', 149);
                $freno_2_derecho = $this->getResultadoIDCF_tipo($data, '2', 148);
                $freno_2_izquierdo = $this->getResultadoIDCF_tipo($data, '2', 149);
                $freno_3_derecho = $this->getResultadoIDCF_tipo($data, '3', 148);
                $freno_3_izquierdo = $this->getResultadoIDCF_tipo($data, '3', 149);
                $freno_4_derecho = $this->getResultadoIDCF_tipo($data, '4', 148);
                $freno_4_izquierdo = $this->getResultadoIDCF_tipo($data, '4', 149);
                $freno_5_derecho = '';
                $freno_5_izquierdo = '';
            } else if (intval($vehiculo->numejes) == 5) {
                $freno_1_derecho = $this->getResultadoIDCF_tipo($data, '1', 148);
                $freno_1_izquierdo = $this->getResultadoIDCF_tipo($data, '1', 149);
                $freno_2_derecho = $this->getResultadoIDCF_tipo($data, '2', 148);
                $freno_2_izquierdo = $this->getResultadoIDCF_tipo($data, '2', 149);
                $freno_3_derecho = $this->getResultadoIDCF_tipo($data, '3', 148);
                $freno_3_izquierdo = $this->getResultadoIDCF_tipo($data, '3', 149);
                $freno_4_derecho = $this->getResultadoIDCF_tipo($data, '4', 148);
                $freno_4_izquierdo = $this->getResultadoIDCF_tipo($data, '4', 149);
                $freno_5_derecho = $this->getResultadoIDCF_tipo($data, '5', 148);
                $freno_5_izquierdo = $this->getResultadoIDCF_tipo($data, '5', 149);
            }

            $desequilibrio1 = $this->getResultadoIDCF_tipo($data, '1', 150);
            $desequilibrio2 = $this->getResultadoIDCF_tipo($data, '2', 150);
            $desequilibrio3 = $this->getResultadoIDCF_tipo($data, '3', 150);
            $desequilibrio4 = $this->getResultadoIDCF_tipo($data, '4', 150);
            $desequilibrio5 = $this->getResultadoIDCF_tipo($data, '5', 150);
            $eficacia_total = $this->getResultadoIDCF_tipo($data, 'eficacia_total', 151);
            $eficacia_auxiliar = $this->getResultadoIDCF_tipo($data, 'eficacia_auxiliar', 152);

            $sum_freno_aux_derecho = $this->getSumFuerzaAux($data, $vehiculo->numejes, 148);
            $sum_freno_aux_izquierdo = $this->getSumFuerzaAux($data, $vehiculo->numejes, 149);
//            if ($sum_freno_aux_derecho == 0.00) {
//                $sum_freno_aux_derecho = "";
//            }
//            if ($sum_freno_aux_izquierdo == 0.00) {
//                $sum_freno_aux_izquierdo = "";
//            }
//            if ($vehiculo->tipo_vehiculo === '3' ) {
            if ($this->nombreClase->nombre === 'MOTOCICLETA') {
                $efi_min_auxil = '';
                $sum_freno_aux_derecho = '';
                $sum_freno_aux_izquierdo = '';
                $sum_peso_derecho = '';
                $sum_peso_izquierdo = '';
                $min_des_A = '';
                $min_des_B = '';
            }


            $frenos = (object)
                    array(
                        'idprueba' => $r[0]->idprueba,
                        'peso_1_derecho' => $this->rdnr($peso_1_derecho),
                        'peso_2_derecho' => $this->rdnr($peso_2_derecho),
                        'peso_3_derecho' => $this->rdnr($peso_3_derecho),
                        'peso_4_derecho' => $this->rdnr($peso_4_derecho),
                        'peso_5_derecho' => $this->rdnr($peso_5_derecho),
                        'peso_1_izquierdo' => $this->rdnr($peso_1_izquierdo),
                        'peso_2_izquierdo' => $this->rdnr($peso_2_izquierdo),
                        'peso_3_izquierdo' => $this->rdnr($peso_3_izquierdo),
                        'peso_4_izquierdo' => $this->rdnr($peso_4_izquierdo),
                        'peso_5_izquierdo' => $this->rdnr($peso_5_izquierdo),
                        'freno_1_derecho' => $this->rdnr($freno_1_derecho),
                        'freno_2_derecho' => $this->rdnr($freno_2_derecho),
                        'freno_3_derecho' => $this->rdnr($freno_3_derecho),
                        'freno_4_derecho' => $this->rdnr($freno_4_derecho),
                        'freno_5_derecho' => $this->rdnr($freno_5_derecho),
                        'freno_1_izquierdo' => $this->rdnr($freno_1_izquierdo),
                        'freno_2_izquierdo' => $this->rdnr($freno_2_izquierdo),
                        'freno_3_izquierdo' => $this->rdnr($freno_3_izquierdo),
                        'freno_4_izquierdo' => $this->rdnr($freno_4_izquierdo),
                        'freno_5_izquierdo' => $this->rdnr($freno_5_izquierdo),
                        'desequilibrio_1' => $this->evalFrenosDesequilibrio($this->rdnr($desequilibrio1), $min_des_B, $max_des_B, $min_des_A, $max_des_A, $ocasion),
                        'desequilibrio_2' => $this->evalFrenosDesequilibrio($this->rdnr($desequilibrio2), $min_des_B, $max_des_B, $min_des_A, $max_des_A, $ocasion),
                        'desequilibrio_3' => $this->evalFrenosDesequilibrio($this->rdnr($desequilibrio3), $min_des_B, $max_des_B, $min_des_A, $max_des_A, $ocasion),
                        'desequilibrio_4' => $this->evalFrenosDesequilibrio($this->rdnr($desequilibrio4), $min_des_B, $max_des_B, $min_des_A, $max_des_A, $ocasion),
                        'desequilibrio_5' => $this->evalFrenosDesequilibrio($this->rdnr($desequilibrio5), $min_des_B, $max_des_B, $min_des_A, $max_des_A, $ocasion),
                        'eficacia_total' => $this->evalEfiTotal($this->rdnr($eficacia_total), $efi_min_total),
                        'eficacia_auxiliar' => $this->evalEfiAuxil($this->rdnr($eficacia_auxiliar), $efi_min_auxil),
                        'sum_peso_derecho' => $this->rdnr($sum_peso_derecho),
                        'sum_peso_izquierdo' => $this->rdnr($sum_peso_izquierdo),
                        'sum_freno_aux_derecho' => $this->rdnr($sum_freno_aux_derecho),
                        'sum_freno_aux_izquierdo' => $this->rdnr($sum_freno_aux_izquierdo),
                        'n_desequilibrio_A' => $min_des_A,
                        'n_desequilibrio_B' => $min_des_B,
                        'n_eficacia_total' => $efi_min_total,
                        'n_eficacia_auxiliar' => $efi_min_auxil,
                        'operario' => $operario->nombres . " " . $operario->apellidos,
                        'documento' => $operario->identificacion);
        } else {
            $frenos = (object)
                    array(
                        'idprueba' => '',
                        'peso_1_derecho' => '',
                        'peso_2_derecho' => '',
                        'peso_3_derecho' => '',
                        'peso_4_derecho' => '',
                        'peso_5_derecho' => '',
                        'peso_1_izquierdo' => '',
                        'peso_2_izquierdo' => '',
                        'peso_3_izquierdo' => '',
                        'peso_4_izquierdo' => '',
                        'peso_5_izquierdo' => '',
                        'freno_1_derecho' => '',
                        'freno_2_derecho' => '',
                        'freno_3_derecho' => '',
                        'freno_4_derecho' => '',
                        'freno_5_derecho' => '',
                        'freno_1_izquierdo' => '',
                        'freno_2_izquierdo' => '',
                        'freno_3_izquierdo' => '',
                        'freno_4_izquierdo' => '',
                        'freno_5_izquierdo' => '',
                        'desequilibrio_1' => '',
                        'desequilibrio_2' => '',
                        'desequilibrio_3' => '',
                        'desequilibrio_4' => '',
                        'desequilibrio_5' => '',
                        'eficacia_total' => '',
                        'eficacia_auxiliar' => '',
                        'sum_peso_derecho' => '',
                        'sum_peso_izquierdo' => '',
                        'sum_freno_aux_derecho' => '',
                        'sum_freno_aux_izquierdo' => '',
                        'n_desequilibrio_A' => '',
                        'n_desequilibrio_B' => '',
                        'n_eficacia_total' => '',
                        'n_eficacia_auxiliar' => '',
                        'operario' => '',
                        'documento' => '');
        }
        return $frenos;
    }

//..............................................................................EVAL FRENOS
    function evalFrenosDesequilibrio($dato, $minB, $maxB, $minA, $maxA, $ocasion) {
        if ($dato !== '' && $this->nombreClase->nombre !== 'MOTOCICLETA') {
            if (floatval($dato) < floatval($minB)) {
                return $dato;
            } else {
                $grupo = "FRENOS";
                if (floatval($dato) >= floatval($minB) && floatval($dato) <= floatval($maxB)) {
                    if ($this->desquilibrioBmulti == "0" || $ocasion !== "8888") {
//                    if ($this->desquilibrioBmulti == "0") {
                        if (!$this->ifDefDesequilibrioB) {
                            $this->ifDefDesequilibrioB = true;
                            if ($this->nombreClase->nombre == "MOTOCARRO") {
                                if ($this->ajustarGrupos == "1") {
                                    $grupo = "9.6 SISTEMA DE FRENOS";
                                }
                                array_push($this->defectosMB, (object) array(
                                            "codigo" => '1.4.6.20.2',
                                            "descripcion" => 'Desequilibrio de las fuerzas de frenado entre las ruedas de un mismo eje, en cualquiera de sus ejes, entre el 20% y 30%.',
                                            "grupo" => $grupo,
                                            "tipo" => 'B'
                                ));
                            } else {
                                if ($this->ajustarGrupos == "1") {
                                    $grupo = "6.7 SISTEMA DE FRENOS";
                                }
                                array_push($this->defectosMB, (object) array(
                                            "codigo" => '1.1.7.31.2',
                                            "descripcion" => 'Desequilibrio de las fuerzas de frenado entre las ruedas de un mismo eje, en cualquiera de sus ejes, entre el 20% y 30%.',
                                            "grupo" => $grupo,
                                            "tipo" => 'B'
                                ));
                            }
                        }
                    } else {
                        if ($this->nombreClase->nombre == "MOTOCARRO") {
                            if ($this->ajustarGrupos == "1") {
                                $grupo = "9.6 SISTEMA DE FRENOS";
                            }
                            array_push($this->defectosMB, (object) array(
                                        "codigo" => '1.4.6.20.2',
                                        "descripcion" => 'Desequilibrio de las fuerzas de frenado entre las ruedas de un mismo eje, en cualquiera de sus ejes, entre el 20% y 30%.',
                                        "grupo" => $grupo,
                                        "tipo" => 'B'
                            ));
                        } else {
                            if ($this->ajustarGrupos == "1") {
                                $grupo = "6.7 SISTEMA DE FRENOS";
                            }
                            array_push($this->defectosMB, (object) array(
                                        "codigo" => '1.1.7.31.2',
                                        "descripcion" => 'Desequilibrio de las fuerzas de frenado entre las ruedas de un mismo eje, en cualquiera de sus ejes, entre el 20% y 30%.',
                                        "grupo" => $grupo,
                                        "tipo" => 'B'
                            ));
                        }
                    }
                } elseif (floatval($dato) > floatval($minA)) {
                    if (!$this->ifDefDesequilibrioA) {
                        $this->ifDefDesequilibrioA = true;
                        if ($this->nombreClase->nombre == "MOTOCARRO") {
                            if ($this->ajustarGrupos == "1") {
                                $grupo = "9.6 SISTEMA DE FRENOS";
                            }
                            array_push($this->defectosMA, (object) array(
                                        "codigo" => '1.4.6.20.1',
                                        "descripcion" => 'Desequilibrio de las fuerzas de frenado entre las ruedas de un mismo eje, en cualquiera de sus ejes, superior el 30%.',
                                        "grupo" => $grupo,
                                        "tipo" => 'A'
                            ));
                        } else {
                            if ($this->ajustarGrupos == "1") {
                                $grupo = "6.7 SISTEMA DE FRENOS";
                            }
                            array_push($this->defectosMA, (object) array(
                                        "codigo" => '1.1.7.31.1',
                                        "descripcion" => 'Desequilibrio de las fuerzas de frenado entre las ruedas de un mismo eje, en cualquiera de sus ejes, superior el 30%.',
                                        "grupo" => $grupo,
                                        "tipo" => 'A'
                            ));
                        }
                    }
                }
                return $dato . "*";
            }
        }
    }

    function evalEfiTotal($dato, $min) {
        if ($dato !== '') {
            if (floatval($dato) >= floatval($min)) {
                return $dato;
            } else {
                $this->setDefFren();
                return $dato . "*";
            }
        }
    }

    private function setDefFren() {
        if (!$this->ifDefEficaciaTotalA) {
            $this->ifDefEficaciaTotalA = true;
            $grupo = 'FRENOS';
            switch ($this->nombreClase->nombre) {
                case "MOTOCICLETA":
                    if ($this->ajustarGrupos == "1") {
                        $grupo = "7.6 SISTEMA DE FRENOS";
                    }
                    array_push($this->defectosMA, (object) array(
                                "codigo" => '1.2.6.15.1',
                                "descripcion" => 'Eficacia de frenado inferior el 30%.',
                                "grupo" => $grupo,
                                "tipo" => 'A'
                    ));
                    break;
                case "MOTOCARRO":
                    if ($this->ajustarGrupos == "1") {
                        $grupo = "9.6 SISTEMA DE FRENOS";
                    }
                    array_push($this->defectosMA, (object) array(
                                "codigo" => '1.4.6.19.1',
                                "descripcion" => 'Eficacia de frenado inferior el 30%.',
                                "grupo" => $grupo,
                                "tipo" => 'A'
                    ));
                    break;
                default:
                    if ($this->ajustarGrupos == "1") {
                        $grupo = "6.7 SISTEMA DE FRENOS";
                    }
                    array_push($this->defectosMA, (object) array(
                                "codigo" => '1.1.7.30.1',
                                "descripcion" => 'Eficacia de frenado inferior al 50%.',
                                "grupo" => $grupo,
                                "tipo" => 'A'
                    ));
                    break;
            }
        }
    }

    function evalEfiAuxil($dato, $min) {
        if ($dato !== '' && $this->nombreClase->nombre !== 'MOTOCICLETA') {
            if (floatval($dato) >= floatval($min)) {
                return $dato;
            } else {
                if (!$this->ifDefEficaciaAuxilB) {
                    $this->ifDefEficaciaAuxilB = true;
                    $grupo = 'FRENOS';
                    switch ($this->nombreClase->nombre) {
                        case "MOTOCARRO":
                            if ($this->ajustarGrupos == "1") {
                                $grupo = "9.6 SISTEMA DE FRENOS";
                            }
                            array_push($this->defectosMB, (object) array(
                                        "codigo" => '1.4.6.20.3',
                                        "descripcion" => 'Freno de estacionamiento (de parqueo de mano) con una eficacia inferior el 18%.',
                                        "grupo" => $grupo,
                                        "tipo" => 'B'
                            ));
                            break;
                        default:
                            if ($this->ajustarGrupos == "1") {
                                $grupo = "6.7 SISTEMA DE FRENOS";
                            }
                            array_push($this->defectosMB, (object) array(
                                        "codigo" => '1.1.7.30.2',
                                        "descripcion" => 'Freno de estacionamiento (de parqueo de mano) con una eficacia inferior el 18%.',
                                        "grupo" => $grupo,
                                        "tipo" => 'B'
                            ));
                            break;
                    }
                }
                return $dato . "*";
            }
        }
    }

//------------------------------------------------------------------------------ALINEADOR
    var $ifDefAlineadorA;
    var $ifDefAlineadorB;

    public function getAlineacion($idhojapruebas) {
        $this->ifDefAlineadorA = false;
        $this->ifDefAlineadorB = false;
        $data['idhojapruebas'] = $idhojapruebas;
        $data['idtipo_prueba'] = "10";
        $data['order'] = $this->order;
        $result = $this->Mprueba->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            $data['idprueba'] = $r[0]->idprueba;
            if ($r[0]->estado == '1' || $r[0]->estado == '3') {
                $this->aprobado = false;
            }
            $operario = $this->getUsuario($r[0]->idusuario);
//            $alineacionParam = new alineacion_param();
            $minmax = $this->minmaxAli();
            if ($this->nombreClase->nombre == 'CUATRIMOTO') {
                $minmax = "";
            }
            $alineacion = (object)
                    array(
                        'idprueba' => $r[0]->idprueba,
                        'alineacion_1' => $this->evalAlineacion($this->rdnr($this->getResultadoIDCF_tipo($data, '1', 141)), $minmax),
                        'alineacion_2' => $this->evalAlineacionT($this->rdnr($this->getResultadoIDCF_tipo($data, '2', 141)), $minmax),
                        'alineacion_3' => $this->evalAlineacionT($this->rdnr($this->getResultadoIDCF_tipo($data, '3', 141)), $minmax),
                        'alineacion_4' => $this->evalAlineacionT($this->rdnr($this->getResultadoIDCF_tipo($data, '4', 141)), $minmax),
                        'alineacion_5' => $this->evalAlineacionT($this->rdnr($this->getResultadoIDCF_tipo($data, '5', 141)), $minmax),
                        'minmax' => "(+/-)" . $minmax,
                        'operario' => $operario->nombres . " " . $operario->apellidos,
                        'documento' => $operario->identificacion);
        } else {
            $alineacion = (object)
                    array(
                        'idprueba' => '',
                        'alineacion_1' => '',
                        'alineacion_2' => '',
                        'alineacion_3' => '',
                        'alineacion_4' => '',
                        'alineacion_5' => '',
                        'minmax' => '',
                        'operario' => '',
                        'documento' => '');
        }
        return $alineacion;
    }

//..............................................................................EVAL ALINEACION
    function evalAlineacion($dato, $minMax) {
        if ($dato !== '' && $this->nombreClase->nombre !== 'MOTOCICLETA' && $this->nombreClase->nombre !== 'MOTOCARRO') {
            if ((floatval($minMax) * -1 <= floatval($dato) && floatval($minMax) >= floatval($dato)) || $this->nombreClase->nombre == 'CUATRIMOTO') {
                return $dato;
            } else {
                $this->setDefAli();
                return $dato . "*";
            }
        }
    }

    private function setDefAli() {
        if (!$this->ifDefAlineadorA) {
            $this->ifDefAlineadorA = true;
            $grupo = 'DIRECCION';
            if ($this->ajustarGrupos == "1") {
                $grupo = "6.10 DIRECCION";
            }
            array_push($this->defectosMA, (object) array(
                        "codigo" => '1.1.10.36.1',
                        "descripcion" => 'Desviación lateral en el primer eje superior a ±10 [m/km]',
                        "grupo" => $grupo,
                        "tipo" => 'A'
            ));
        }
    }

    function evalAlineacionT($dato, $minMax) {
        if ($dato !== '' && $this->nombreClase->nombre !== 'MOTOCICLETA' && $this->nombreClase->nombre !== 'MOTOCARRO') {
            if (floatval($minMax) * -1 <= floatval($dato) && floatval($minMax) >= floatval($dato) || $this->nombreClase->nombre == 'CUATRIMOTO') {
                return $dato;
            } else {
                if (!$this->ifDefAlineadorB) {
                    $this->ifDefAlineadorB = true;
                    $grupo = 'DIRECCION';
                    if ($this->ajustarGrupos == "1") {
                        $grupo = "6.10 DIRECCION";
                    }
                    array_push($this->defectosMB, (object) array(
                                "codigo" => '1.1.10.36.2',
                                "descripcion" => 'Desviación lateral para los demás ejes superior a ±10 [m/km].',
                                "grupo" => $grupo,
                                "tipo" => 'B'
                    ));
                }
                return $dato . "*";
            }
        }
    }

//------------------------------------------------------------------------------TAXIMETRO
    var $ifDefTaximetroT;
    var $ifDefTaximetroD;

    public function getTaximetro($idhojapruebas) {
        $this->ifDefTaximetroT = false;
        $this->ifDefTaximetrodD = false;
        $data['idhojapruebas'] = $idhojapruebas;
        $data['idtipo_prueba'] = "6";
        $data['order'] = $this->order;
        $result = $this->Mprueba->get($data);
//        var_dump($result->result());
        if ($result->num_rows() > 0) {
            $r = $result->result();
            $data['idprueba'] = $r[0]->idprueba;
            if ($r[0]->estado == '1' || $r[0]->estado == '3') {
                $this->aprobado = false;
            }
            $operario = $this->getUsuario($r[0]->idusuario);
            $minmax = $this->minmaxTax();

            $tieneTaximetro = $this->getResultadoDefecto($data, 89, 'Inspeccion visual taximetro');
            $taximetroVisible = $this->getResultadoDefecto($data, 92, 'Inspeccion visual taximetro');
            $grupo = 'TAXIMETROS';
            if ($this->ajustarGrupos == "1") {
                $grupo = "6.9 TAXIMETROS";
            }
            if ($tieneTaximetro == 'true') {
                $tieneTaximetro = 'false';
                array_push($this->defectosSA, (object) array(
                            "codigo" => '1.1.9.34.1',
                            "descripcion" => 'La inexistencia del Taxímetro, para los vehículos que estén obligados a usarlo.',
                            "grupo" => $grupo,
                            "tipo" => 'A'
                ));
            } else {
                $tieneTaximetro = 'true';
            }
            if ($taximetroVisible == 'true') {
                $taximetroVisible = 'false';
                array_push($this->defectosSA, (object) array(
                            "codigo" => '1.1.9.34.4',
                            "descripcion" => 'El Taxímetro está ubicado en un sitio donde no es visible para cualquier pasajero.',
                            "grupo" => $grupo,
                            "tipo" => 'A'
                ));
            } else {
                $taximetroVisible = 'true';
            }

            $taximetro = (object)
                    array(
                        'idprueba' => $r[0]->idprueba,
                        'r_llanta' => $this->getResultado($data, 'Rllanta', ''),
                        'tiempo' => $this->evalTaximetroTiempo($this->rdnr($this->getResultado($data, 'error_tiempo_nuevo', '')), $minmax),
                        'distancia' => $this->evalTaximetroDistancia($this->rdnr($this->getResultado($data, 'error_distancia_nuevo', '')), $minmax),
                        'minmax' => "(+/-)" . $minmax,
                        'aplicaTaximetro' => 'true',
                        'tieneTaximetro' => $tieneTaximetro,
                        'taximetroVisible' => $taximetroVisible,
                        'operario' => $operario->nombres . " " . $operario->apellidos,
                        'documento' => $operario->identificacion);
        } else {
            $taximetro = (object)
                    array(
                        'idprueba' => '',
                        'r_llanta' => '',
                        'tiempo' => '',
                        'distancia' => '',
                        'minmax' => '',
                        'aplicaTaximetro' => 'false',
                        'tieneTaximetro' => '',
                        'taximetroVisible' => '',
                        'operario' => '',
                        'documento' => '');
        }
        return $taximetro;
    }

//------------------------------------------------------------------------------SONOMETRO


    public function getSonometro($idhojapruebas) {
        $data['idhojapruebas'] = $idhojapruebas;
        $data['idtipo_prueba'] = "4";
        $data['order'] = $this->order;
        $result = $this->Mprueba->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            $data['idprueba'] = $r[0]->idprueba;
            $operario = $this->getUsuario($r[0]->idusuario);
            $sonometro = (object)
                    array(
                        'idprueba' => $r[0]->idprueba,
                        'valor_ruido_motor1' => $this->rdnr($this->getResultado($data, 'valor_ruido_motor1', '')),
                        'maximo_ruido_motor' => $this->rdnr($this->getResultado($data, 'maximo_ruido_motor', '')),
                        'operario' => $operario->nombres . " " . $operario->apellidos,
                        'documento' => $operario->identificacion);
        } else {
            $sonometro = (object)
                    array(
                        'idprueba' => '',
                        'valor_ruido_motor1' => '',
                        'maximo_ruido_motor' => '',
                        'operario' => '',
                        'documento' => '');
        }
        return $sonometro;
    }

//..............................................................................EVAL TAXIMETRO


    function evalTaximetroTiempo($dato, $minMax) {
        if ($dato !== '') {
            if (floatval($minMax) * -1 <= floatval($dato) && floatval($minMax) >= floatval($dato)) {
                return $dato;
            } else {
                $this->setDefTax();
                return $dato . "*";
            }
        }
    }

    private function setDefTax() {
        if (!$this->ifDefTaximetroT) {
            $this->ifDefTaximetroT = true;
            $grupo = 'TAXIMETROS';
            if ($this->ajustarGrupos == "1") {
                $grupo = "6.9 TAXIMETROS";
            }
            array_push($this->defectosMA, (object) array(
                        "codigo" => '1.1.9.34.3',
                        "descripcion" => 'Error en la medida de tiempo, por fuera de ± 2%, tomada en un tiempo cualquiera entre 60 s a 180 s.',
                        "grupo" => $grupo,
                        "tipo" => 'A'
            ));
        }
    }

    function evalTaximetroDistancia($dato, $minMax) {
        if ($dato !== '') {
            if (floatval($minMax) * -1 <= floatval($dato) && floatval($minMax) >= floatval($dato)) {
                return $dato;
            } else {
                if (!$this->ifDefTaximetroD) {
                    $this->ifDefTaximetroD = true;
                    $grupo = 'TAXIMETROS';
                    if ($this->ajustarGrupos == "1") {
                        $grupo = "6.9 TAXIMETROS";
                    }
                    array_push($this->defectosMA, (object) array(
                                "codigo" => '1.1.9.34.2',
                                "descripcion" => 'Error en la medida de distancia, por fuera de ± 2%, tomada en una distancia cualquiera entre 300 m y 1 km.',
                                "grupo" => $grupo,
                                "tipo" => 'A'
                    ));
                }
                return $dato . "*";
            }
        }
    }

//------------------------------------------------------------------------------GASES
    var $ifDefGases;
    var  $fecha_inicialG;
    public function getGases($idhojapruebas, $vehiculo) {
        $this->ifDefGases = false;
        $data['idhojapruebas'] = $idhojapruebas;
        $data['idtipo_prueba'] = "3";
        $data['order'] = $this->order;
        $result = $this->Mprueba->get($data);
        $aprobadoGases = true;
//        $rpmGases332 = false;
        if ($result->num_rows() > 0) {
            $r = $result->result();
            $this->idprueba_gases = $r[0]->idprueba;
            $data['idprueba'] = $r[0]->idprueba;
            $operario = $this->getUsuario($r[0]->idusuario);
            $this->fecha_inicialG = $r[0]->fechainicial;
            if ($r[0]->estado == '1' || $r[0]->estado == '3') {
                $this->aprobado = false;
                $aprobadoGases = false;
            }

//            $gasesParam = new gases_param();
            $CoFlag = $this->getCoFlag($vehiculo->ano_modelo, $vehiculo->tipo_vehiculo, $vehiculo->tiempos);
            $CoFlag_ = $this->getCoFlag($vehiculo->ano_modelo, $vehiculo->tipo_vehiculo, $vehiculo->tiempos);
            $Co2Flag = $this->getCo2Flag($vehiculo->tipo_vehiculo);
            $Co2Flag_ = $this->getCo2Flag($vehiculo->tipo_vehiculo);
            $O2Flag = $this->getO2Flag($vehiculo->tipo_vehiculo);
            $O2Flag_ = $this->getO2Flag($vehiculo->tipo_vehiculo);
            $HcFlag = $this->getHcFlag($vehiculo->ano_modelo, $vehiculo->tipo_vehiculo, $vehiculo->tiempos);
            $HcFlag_ = $this->getHcFlag($vehiculo->ano_modelo, $vehiculo->tipo_vehiculo, $vehiculo->tiempos);
            if ($r[0]->estado !== '0') {
                $rpm_ralenti = $this->rdnr($this->getResultado($data, 'rpm_ralenti', ''));
//                $mod1 = intval($rpm_ralenti) % 10;
//                $rpm_ralenti = intval($rpm_ralenti) - intval($mod1);
                if ($vehiculo->tipo_vehiculo !== "3") {
                    $co_ralenti = $this->evalGases($this->getResultado($data, 'co_ralenti', ''), $CoFlag, "<=", 2);
                    $hc_ralenti = $this->evalGases($this->getResultado($data, 'hc_ralenti', ''), $HcFlag, "<=", 0);
                    $hc_crucero = $this->evalGases($this->getResultado($data, 'hc_crucero', ''), $HcFlag, "<=", 0);
                    $co_crucero = $this->evalGases($this->getResultado($data, 'co_crucero', ''), $CoFlag, "<=", 2);
                    if ($vehiculo->idtipocombustible == "2") {
                        $co2_crucero = $this->evalGases($this->getResultado($data, 'co2_crucero', ''), $Co2Flag, ">=", 1);
                        $o2_crucero = $this->evalGases($this->getResultado($data, 'o2_crucero', ''), $O2Flag, "<=", 1);
                        $o2_ralenti = $this->evalGases($this->getResultado($data, 'o2_ralenti', ''), $O2Flag, "<=", 1);
                        $co2_ralenti = $this->evalGases($this->getResultado($data, 'co2_ralenti', ''), $Co2Flag, ">=", 1);
                    } else {
                        $o2_crucero = $this->rdnr($this->getResultado($data, 'o2_crucero', ''));
                        $o2_ralenti = $this->rdnr($this->getResultado($data, 'o2_ralenti', ''));
                        if (($this->fechares762_Chispa !== "" && $this->fechares762_Chispa <= $this->fecha_inicialG)) {
                            $co2_crucero = $this->evalGases($this->getResultado($data, 'co2_crucero', ''), $Co2Flag, ">=", 1);
                            $co2_ralenti = $this->evalGases($this->getResultado($data, 'co2_ralenti', ''), $Co2Flag, ">=", 1);
                        } else {
                            $co2_crucero = $this->rdnr($this->getResultado($data, 'co2_crucero', ''));
                            $co2_ralenti = $this->rdnr($this->getResultado($data, 'co2_ralenti', ''));
                        }
                    }
                    $this->getBitacoraGases($data['idprueba']);
                    $hc_anterior = "";
                    $hc_anterior1 = "";
                    $hc_anterior2 = "";
                    $hc_anterior3 = "";
                    $co_anterior = "";
                    $co_anterior1 = "";
                    $co_anterior2 = "";
                    $co_anterior3 = "";
                    $rpm_crucero = $this->rdnr($this->getResultado($data, 'rpm_crucero', ''));
//                    $mod1 = intval($rpm_crucero) % 10;
//                    $rpm_crucero = intval($rpm_crucero) - intval($mod1);
                } else {
                    if (intval($vehiculo->numero_exostos) > 1) {
                        $idP = intval($data['idprueba']);
                        if ($this->modoDobleExt == "1") {
                            $data['idprueba'] = intval($data['idprueba']) - (intval($vehiculo->numero_exostos) - 1);
                        }
                        $co_ralenti = $this->evalGases($this->getResultadoMaxGases($data, 'co_ralenti'), $CoFlag, "<=", 2);
                        $hc_ralenti = $this->evalGases($this->getResultadoMaxGases($data, 'hc_ralenti'), $HcFlag, "<=", 0);
                        $co2_ralenti = $this->rdnr($this->getResultadoMaxGases($data, 'co2_ralenti'));
                        $o2_ralenti = $this->rdnr($this->getResultadoMaxGases($data, 'o2_ralenti'));
                        $rpm_ralenti = $this->rdnr($this->getResultadoMaxGases($data, 'rpm_ralenti'));
                        $hc_anterior = $this->rdnr($this->getResultadoId($idP, 'promhcra_ant', ''));
                        $co_anterior = $this->rdnr($this->getResultadoId($idP, 'promcora_ant', ''));
                        $hc_anterior1 = $this->rdnr($this->getResultadoId($idP + 1, 'promhcra_ant', ''));
                        $co_anterior1 = $this->rdnr($this->getResultadoId($idP + 1, 'promcora_ant', ''));
                        $this->getBitacoraGases($idP);
                        $this->getBitacoraGases($idP + 1);
                        if (intval($vehiculo->numero_exostos) > 2) {
                            $hc_anterior2 = $this->rdnr($this->getResultadoId($idP + 2, 'promhcra_ant', ''));
                            $co_anterior2 = $this->rdnr($this->getResultadoId($idP + 2, 'promcora_ant', ''));
                            $this->getBitacoraGases($idP + 2);
                        } else {
                            $hc_anterior2 = "";
                            $co_anterior2 = "";
                        }
                        if (intval($vehiculo->numero_exostos) > 3) {
                            $hc_anterior3 = $this->rdnr($this->getResultadoId($idP + 3, 'promhcra_ant', ''));
                            $co_anterior3 = $this->rdnr($this->getResultadoId($idP + 3, 'promcora_ant', ''));
                            $this->getBitacoraGases($idP + 3);
                        } else {
                            $hc_anterior3 = "";
                            $co_anterior3 = "";
                        }
//                        $rpmGases332 = $this->getResultadoMaxRpmGases($data);
//                        $mod1 = intval($rpm_ralenti) % 10;
//                        $rpm_ralenti = intval($rpm_ralenti) - intval($mod1);
                        if ($this->modoDobleExt == "1") {
                            $data['idprueba'] = intval($data['idprueba']) + (intval($vehiculo->numero_exostos) - 1);
                        }
                    } else {
                        $this->getBitacoraGases($data['idprueba']);
                        $co_ralenti = $this->evalGases($this->getResultado($data, 'co_ralenti', ''), $CoFlag, "<=", 2);
                        $hc_ralenti = $this->evalGases($this->getResultado($data, 'hc_ralenti', ''), $HcFlag, "<=", 0);
                        $co2_ralenti = $this->rdnr($this->getResultado($data, 'co2_ralenti', ''));
                        $o2_ralenti = $this->rdnr($this->getResultado($data, 'o2_ralenti', ''));
                        $hc_anterior = $this->rdnr($this->getResultado($data, 'promhcra_ant', ''));
                        $hc_anterior1 = "";
                        $hc_anterior2 = "";
                        $hc_anterior3 = "";
                        $co_anterior = $this->rdnr($this->getResultado($data, 'promcora_ant', ''));
                        $co_anterior1 = "";
                        $co_anterior2 = "";
                        $co_anterior3 = "";
                    }
                    $co2_crucero = '';
                    $o2_crucero = '';
                    $hc_crucero = '';
                    $co_crucero = '';
                    $rpm_crucero = '';
                }
            } else {
                $co_ralenti = '';
                $hc_ralenti = '';
                $co2_ralenti = '';
                $hc_crucero = '';
                $co_crucero = '';
                $co2_crucero = '';
                $o2_crucero = '';
                $o2_ralenti = '';
                $rpm_ralenti = '';
                $rpm_crucero = '';
                $hc_anterior = '';
                $hc_anterior1 = '';
                $hc_anterior2 = '';
                $hc_anterior3 = '';
                $co_anterior = '';
                $co_anterior1 = '';
                $co_anterior2 = '';
                $co_anterior3 = '';
            }
            $CoFlag = '&lt;= ' . $CoFlag;
//            echo $this->fechares762_Chispa;
//            echo $this->fechaGlobal;
            if ($vehiculo->tipo_vehiculo !== "3" && $vehiculo->idtipocombustible == "2") {
                $Co2Flag = " &gt;= " . $Co2Flag;
                $O2Flag = "&lt;= " . $O2Flag;
            } elseif ($vehiculo->tipo_vehiculo !== "3" && ($vehiculo->idtipocombustible == "3" || $vehiculo->idtipocombustible == "4" || $vehiculo->idtipocombustible == "9") && ($this->fechares762_Chispa !== "" && $this->fechares762_Chispa <= $this->fecha_inicialG)) {
                $Co2Flag = " &gt;= " . $Co2Flag;
            } else {
                $Co2Flag = "";
                $Co2Flag_ = "";
                if ($this->mostrarO2motos === "1" && $vehiculo->tipo_vehiculo === "3") {
                    if (($vehiculo->tiempos === "4") || ($vehiculo->tiempos === "2" && $vehiculo->ano_modelo >= 2010)) {
                        $O2Flag = "6";
                    } else {
                        $O2Flag = "11";
                    }
                } else {
                    $O2Flag = "";
                }
                $O2Flag_ = "";
            }
            $HcFlag = "&lt;= " . $HcFlag;
//            echo($vehiculo->scooter);
            $temperatura_ambiente = $this->rdnr($this->getResultado($data, 'temperatura_ambiente', ''));
            $humedad = $this->rdnr($this->getResultado($data, 'humedad', ''));
            if ($vehiculo->tipo_vehiculo !== "3") {
                if ($vehiculo->convertidorCat == "SI") {
                    $temperatura = "";
                } elseif ($vehiculo->convertidorCat == "N.A.") {
                    $temperatura = "0";
                } else {
                    $temperatura = round($this->getResultado($data, 'temperatura_aceite', ''));
                }
            } else {
                if ($vehiculo->scooter == "1") {
                    $temperatura = "0";
                } else {
                    if (intval($vehiculo->numero_exostos) > 1) {
                        if ($this->modoDobleExt == "1") {
                            $data['idprueba'] = intval($data['idprueba']) - (intval($vehiculo->numero_exostos) - 1);
                        }
//                        $data['idprueba'] = intval($data['idprueba']) - (intval($vehiculo->numero_exostos) - 1);
                        $temperatura = $this->rdnr($this->getResultadoMaxGases($data, 'temperatura_aceite'));
                        $temperatura_ambiente = $this->rdnr($this->getResultadoMaxGases($data, 'temperatura_ambiente'));
                        $humedad = $this->rdnr($this->getResultadoMaxGases($data, 'humedad'));
                        $data['idprueba'] = intval($data['idprueba']) + (intval($vehiculo->numero_exostos) - 1);
                        if ($this->modoDobleExt == "1") {
                            $data['idprueba'] = intval($data['idprueba']) + (intval($vehiculo->numero_exostos) - 1);
                        }
                    } else {
                        $temperatura = round($this->getResultadoTmpMot($data, 'temperatura_aceite', ''));
                    }
                }
            }


            $gases = (object)
                    array(
                        'idprueba' => $r[0]->idprueba,
                        'rpm_ralenti' => $rpm_ralenti,
                        'co_ralenti' => $co_ralenti,
                        'co2_ralenti' => $co2_ralenti,
                        'o2_ralenti' => $o2_ralenti,
                        'hc_ralenti' => $hc_ralenti,
                        'rpm_crucero' => $rpm_crucero,
                        'co_crucero' => $co_crucero,
                        'co2_crucero' => $co2_crucero,
                        'o2_crucero' => $o2_crucero,
                        'hc_crucero' => $hc_crucero,
                        'hc_anterior' => $hc_anterior,
                        'hc_anterior1' => $hc_anterior1,
                        'hc_anterior2' => $hc_anterior2,
                        'hc_anterior3' => $hc_anterior3,
                        'co_anterior' => $co_anterior,
                        'co_anterior1' => $co_anterior1,
                        'co_anterior2' => $co_anterior2,
                        'co_anterior3' => $co_anterior3,
                        'temperatura' => $this->rdnr($temperatura),
                        'temperatura_ambiente' => $temperatura_ambiente,
                        'humedad' => $humedad,
                        'CoFlag' => $CoFlag,
                        'Co2Flag' => $Co2Flag,
                        'O2Flag' => $O2Flag,
                        'HcFlag' => $HcFlag,
                        'CoFlag_' => $CoFlag_,
                        'Co2Flag_' => $Co2Flag_,
                        'O2Flag_' => $O2Flag_,
                        'HcFlag_' => $HcFlag_,
                        'fugasTuboEscape' => $this->getResultadoDefecto($data, 328, 'T'),
                        'fugasSilenciador' => $this->getResultadoDefecto($data, 378, 'T'),
                        'tapaCombustible' => $this->getResultadoDefecto($data, 331, 'T'),
                        'tapaAceite' => $this->getResultadoDefecto($data, 330, 'T'),
                        'salidasAdicionales' => $this->getResultadoDefecto($data, 329, 'T'),
                        'instalacionAccesorios' => $this->getResultadoDefecto($data, 335, 'T'),
                        'fallaSistemaRefrigeracion' => $this->getResultadoDefecto($data, 336, 'T'),
                        'filtroAire' => $this->getResultadoDefecto($data, 334, 'T'),
                        'sistemaRecirculacion' => $this->getResultadoDefecto($data, 337, 'T'),
                        'presenciaHumos' => $this->getResultadoDefecto($data, 333, 'T'),
                        'revolucionesFueraRango' => $this->getResultadoDefecto($data, 332, 'T'),
                        'lucesNoEncienden' => $this->getResultadoDefecto($data, 750, 'T'),
                        'soporteCentral' => $this->getResultadoDefecto($data, 751, 'T'),
                        'salidasAdicionalesM' => $this->getResultadoDefecto($data, 369, 'T'),
                        'dilusion' => $this->getResultadoDefecto($data, 'DILUSION EXCESIVA', 'observaciones'),
                        'operario' => $operario->nombres . " " . $operario->apellidos,
                        'documento' => $operario->identificacion
            );
            if ($r[0]->estado == '0') {
                $gases = $this->getGasesEmpty();
            }
            if ($vehiculo->tipo_vehiculo !== "3") {
                $ifFugas = "false";
                if ($gases->fugasTuboEscape == 'true' || $gases->fugasSilenciador == 'true') {
                    $ifFugas = "true";
                }
                $this->setDefAnormalesGas($ifFugas, '3.1.1.1.1', "Existencia de fugas en el tubo, uniones del múltiple y silenciador del sistema de escape del vehículo.");
                $this->setDefAnormalesGas($gases->salidasAdicionales, '3.1.1.1.2', "Salidas adicionales en el sistema de escape diferentes a las de diseño original del vehículo.");
                $this->setDefAnormalesGas($gases->tapaAceite, '3.1.1.1.3', "Ausencia de tapones de aceite o fugas en el mismo.");
                $this->setDefAnormalesGas($gases->tapaCombustible, '3.1.1.1.4', "Ausencia de tapas o tapones de combustible o fugas del mismo.");
                $this->setDefAnormalesGas($gases->filtroAire, '3.1.1.1.5', "Sistema de admisión de aire en mal estado (filtro roto o deformado) o ausencia del filtro de aire.");
                $this->setDefAnormalesGas($gases->sistemaRecirculacion, '3.1.1.1.6', "Desconexión del sistema de recirculación de gases provenientes del Cárter del Motor. (Por ejemplo válvula de ventilación positiva del Cárter).");
                $this->setDefAnormalesGas($gases->instalacionAccesorios, '3.1.1.1.7', "Instalación de accesorios o deformaciones en el tubo de escape que no permitan la introducción de la sonda.");
                $this->setDefAnormalesGas($gases->fallaSistemaRefrigeracion, '3.1.1.1.8', "Incorrecta operación del sistema de refrigeración, cuya verificación se hará por medio de inspección.");
                $this->setDefAnormalesGas($gases->presenciaHumos, '3.1.1.1.9', "Presencia de humo negro o azul.");
                $this->setDefAnormalesGas($gases->revolucionesFueraRango, '3.1.1.1.10', "Revoluciones fuera de rango.");
                $this->setDefAnormalesGas($gases->lucesNoEncienden, '1.1.6.16.1', "Las luces del vehículo no encienden.");

                if ($this->defAnormaGases) {
                    $gases->temperatura = '';
                    $gases->rpm_ralenti = '';
                    $gases->co_ralenti = '';
                    $gases->co2_ralenti = '';
                    $gases->o2_ralenti = '';
                    $gases->hc_ralenti = '';
                    $gases->rpm_crucero = '';
                    $gases->co_crucero = '';
                    $gases->co2_crucero = '';
                    $gases->o2_crucero = '';
                    $gases->hc_crucero = '';
                }
            } else {
                $ifFugas = "false";
                if ($gases->fugasTuboEscape == 'true' || $gases->fugasSilenciador == 'true') {
                    $ifFugas = "true";
                }

//                if ($rpmGases332) {
//                    $gases->revolucionesFueraRango = "true";
//                }

                $this->setDefAnormalesGas($ifFugas, '4.1.1.1.1', "Existencia de fugas en el tubo, uniones del múltiple y silenciador del sistema de escape del vehículo.");
                $this->setDefAnormalesGas($gases->salidasAdicionales, '4.1.1.1.2', "Salidas adicionales en el sistema de escape diferentes a las de diseño original del vehículo.");
                if ($gases->salidasAdicionales == 'true') {
                    $gases->salidasAdicionalesM = "true";
                }
                $this->setDefAnormalesGas($gases->tapaAceite, '4.1.1.1.3', "Ausencia de tapones de aceite o fugas en el mismo.");
                $this->setDefAnormalesGas($gases->tapaCombustible, '4.1.1.1.4', "Presencia tapa llenado combustible.");
                $this->setDefAnormalesGas($gases->revolucionesFueraRango, '4.1.1.1.5', "Revoluciones fuera de rango.");
                $this->setDefAnormalesGas($gases->salidasAdicionalesM, '4.1.1.1.6', "Salidas adicionales a las del diseño.");
                $this->setDefAnormalesGas($gases->presenciaHumos, '4.1.1.1.7', "Presencia de humo negro o azul (solo para motores 4T).");
                if ($this->nombreClase->nombre == 'MOTOCARRO') {
                    $this->setDefAnormalesGas($gases->lucesNoEncienden, '1.4.5.13.1', "Las luces del vehículo no encienden.");
                } else {
                    $this->setDefAnormalesGas($gases->lucesNoEncienden, '1.2.5.8.1', "Las luces del vehículo no encienden.");
                    $this->setDefAnormalesGas($gases->soporteCentral, '1.2.5.8.1', "El vehículo no tiene soporte central.");
                }
                if ($this->defAnormaGases) {
                    $gases->rpm_ralenti = '';
                    $gases->co_ralenti = '';
                    $gases->co2_ralenti = '';
                    $gases->o2_ralenti = '';
                    $gases->hc_ralenti = '';
                }
            }
        } else {
            $gases = $this->getGasesEmpty();
        }
        if (!$aprobadoGases) {
            $this->setDefGas();
        }
        return $gases;
    }

    var $defAnormaGases = false;

    private function setDefAnormalesGas($defecto_, $codigo, $descripcion) {
        if ($defecto_ == 'true') {
            $this->defAnormaGases = true;
            array_push($this->observaciones, (object) array(
                        "codigo" => $codigo,
                        "descripcion" => $descripcion
            ));
        }
    }

    private function getGasesEmpty() {
        $gases = (object)
                array(
                    'idprueba' => '',
                    'rpm_ralenti' => '',
                    'co_ralenti' => '',
                    'co2_ralenti' => '',
                    'o2_ralenti' => '',
                    'hc_ralenti' => '',
                    'rpm_crucero' => '',
                    'co_crucero' => '',
                    'co2_crucero' => '',
                    'o2_crucero' => '',
                    'hc_crucero' => '',
                    'hc_anterior' => '',
                    'hc_anterior1' => '',
                    'hc_anterior2' => '',
                    'hc_anterior3' => '',
                    'co_anterior' => '',
                    'co_anterior1' => '',
                    'co_anterior2' => '',
                    'co_anterior3' => '',
                    'temperatura' => '',
                    'temperatura_ambiente' => '',
                    'humedad' => '',
                    'CoFlag' => '',
                    'Co2Flag' => '',
                    'O2Flag' => '',
                    'HcFlag' => '',
                    'CoFlag_' => '',
                    'Co2Flag_' => '',
                    'O2Flag_' => '',
                    'HcFlag_' => '',
                    'fugasTuboEscape' => '',
                    'fugasSilenciador' => '',
                    'tapaCombustible' => '',
                    'tapaAceite' => '',
                    'salidasAdicionales' => '',
                    'instalacionAccesorios' => '',
                    'fallaSistemaRefrigeracion' => '',
                    'filtroAire' => '',
                    'sistemaRecirculacion' => '',
                    'presenciaHumos' => '',
                    'revolucionesFueraRango' => '',
                    'lucesNoEncienden' => '',
                    'soporteCentral' => '',
                    'salidasAdicionalesM' => '',
                    'dilusion' => '',
                    'operario' => '',
                    'documento' => '');
        return $gases;
    }

//..............................................................................EVAL GASES
    function evalGases($dato, $flag, $cond, $numDec) {
        if ($cond == "<=") {
            if (floatval($dato) <= floatval($flag)) {
                return $this->rdnr(floatval($dato));
            } else {
                $this->setDefGas();
                return $this->rdnr(floatval($dato)) . "*";
            }
        } else {
            if (floatval($dato) >= floatval($flag)) {
                return $this->rdnr(floatval($dato));
            } else {
                $this->setDefGas();
                return $this->rdnr(floatval($dato)) . "*";
            }
        }
    }

    function setDefGas() {
        if (!$this->ifDefGases) {
            $this->ifDefGases = true;
            $grupo = 'EMISIONES CONTAMINANTES';
            switch ($this->nombreClase->nombre) {
                case "MOTOCICLETA":
                    if ($this->ajustarGrupos == "1") {
                        $grupo = "7.5 EMISIONES CONTAMINANTES EN LOS GASES DE ESCAPE";
                    }
                    array_push($this->defectosMA, (object) array(
                                "codigo" => '1.2.5.8.1',
                                "descripcion" => 'Concentraciones de gases y sustancias contaminantes mayores a las establecidas por la autoridad competente.',
                                "grupo" => $grupo,
                                "tipo" => 'A'
                    ));
                    break;
                case "MOTOCARRO":
                    if ($this->ajustarGrupos == "1") {
                        $grupo = "9.5 EMISIONES CONTAMINANTES EN LOS GASES DE ESCAPE";
                    }
                    array_push($this->defectosMA, (object) array(
                                "codigo" => '1.4.5.13.1',
                                "descripcion" => 'Concentraciones de gases y sustancias contaminantes mayores a las establecidas por la autoridad competente. NOTA Las emisiones de gases contaminantes se verificaran según el tipo de motor y de combustible.',
                                "grupo" => $grupo,
                                "tipo" => 'A'
                    ));
                    break;
                default:
                    if ($this->ajustarGrupos == "1") {
                        $grupo = "6.6 EMISIONES CONTAMINANTES";
                    }
                    array_push($this->defectosMA, (object) array(
                                "codigo" => '1.1.6.16.1',
                                "descripcion" => 'Los vehículos cuyas emisiones de gases de escape tengan concentración de gases y sustancias contaminantes mayores a las establecidas por los requisitos legales ambientales definidas por las autoridades competentes',
                                "grupo" => $grupo,
                                "tipo" => 'A'
                    ));
                    break;
            }
        }
    }

//------------------------------------------------------------------------------OPACIDAD
    var $ifDefopacidad;

    public function getOpacidad($idhojapruebas, $vehiculo) {
        $this->ifDefopacidad = false;
        $data['idhojapruebas'] = $idhojapruebas;
        $data['idtipo_prueba'] = "2";
        $data['order'] = $this->order;
        $result = $this->Mprueba->get($data);
        $aprobadoOpacidad = true;
//        var_dump($result->result());
        if ($result->num_rows() > 0) {
            $r = $result->result();
            $this->idprueba_gases = $r[0]->idprueba;
            $data['idprueba'] = $r[0]->idprueba;
            $ltoe = '';
            $conf = @file_get_contents("system/" . $r[0]->idmaquina . ".json");
            if (isset($conf)) {
                $encrptopenssl = New Opensslencryptdecrypt();
                $json = $encrptopenssl->decrypt($conf, true);
                $dat = json_decode($json, true);
                if ($dat) {
                    foreach ($dat as $d) {
                        if ($d['nombre'] == "ltoe") {
                            $ltoe = $d['valor'];
                        }
                    }
                }
            }
            $operario = $this->getUsuario($r[0]->idusuario);
            if ($r[0]->estado == '1' || $r[0]->estado == '3') {
                $this->aprobado = false;
                $aprobadoOpacidad = false;
            }
//            $opacidadParam = new opacidad_param();
            $max = $this->max_opacidad($vehiculo->ano_modelo);
            $rpm_ciclo1 = $this->rdnr($this->getResultadoIDCF($data, 41));
            $mod1 = intval($rpm_ciclo1) % 10;
            $rpm_ciclo1 = intval($rpm_ciclo1) - $mod1;
            $rpm_ciclo2 = $this->rdnr($this->getResultadoIDCF($data, 63));
            $mod2 = intval($rpm_ciclo2) % 10;
            $rpm_ciclo2 = intval($rpm_ciclo2) - $mod2;
            $rpm_ciclo3 = $this->rdnr($this->getResultadoIDCF($data, 64));
            $mod3 = intval($rpm_ciclo3) % 10;
            $rpm_ciclo3 = intval($rpm_ciclo3) - $mod3;
            $rpm_ciclo4 = $this->rdnr($this->getResultadoIDCF($data, 65));
            $mod4 = intval($rpm_ciclo4) % 10;
            $rpm_ciclo4 = intval($rpm_ciclo4) - $mod4;
            $tmp_inicial = $this->rdnr($this->getResultadoIDCF($data, 224));
            $tmp_final = $this->rdnr($this->getResultadoIDCF($data, 39));
//            $opa1=0;
//            $opa2=0;
//            $opa3=0;
//            $opa4=0;

            $opa1 = $this->rdnr($this->getResultadoIDCF($data, 34));
            $opa2 = $this->rdnr($this->getResultadoIDCF($data, 35));
            $opa3 = $this->rdnr($this->getResultadoIDCF($data, 36));
            $opa4 = $this->rdnr($this->getResultadoIDCF($data, 37));
            $opaTotal = $this->rdnr($this->getResultadoIDCF($data, 61));

            if ($this->fechares762_K1 !== "" && $this->fechares762_K1 <= $this->fechaGlobal && $r[0]->estado != '0') {
                if ($opa1 !== "") {
                    $opa1c = $opa1;
                    if ($this->kCruda == "1") {
                        $opa1c = round(100 * (1 - (pow((1 - (floatval($opa1) / 100)), (215 / floatval($vehiculo->diametro_escape))))), 1);
                        if ($opa1c == 100) {
                            $opa1c = 99.9;
                        }
                    }
                    $opa1K = $this->rdnr(abs(-(1 / 0.43) * log(1 - (($opa1c) / 100))));
                } else {
                    $opa1K = "";
                }
                if ($opa2 !== "") {
                    $opa2c = $opa2;
                    if ($this->kCruda == "1") {
                        $opa2c = round(100 * (1 - (pow((1 - (floatval($opa2) / 100)), (215 / floatval($vehiculo->diametro_escape))))), 1);
                        if ($opa2c == 100) {
                            $opa2c = 99.9;
                        }
                    }
                    $opa2K = $this->rdnr(abs(-(1 / 0.43) * log(1 - (($opa2c) / 100))));
                } else {
                    $opa2K = "";
                }
                if ($opa3 !== "") {
                    $opa3c = $opa3;
                    if ($this->kCruda == "1") {
                        $opa3c = round(100 * (1 - (pow((1 - (floatval($opa3) / 100)), (215 / floatval($vehiculo->diametro_escape))))), 1);
                        if ($opa3c == 100) {
                            $opa3c = 99.9;
                        }
                    }
                    $opa3K = $this->rdnr(abs(-(1 / 0.43) * log(1 - (($opa3c) / 100))));
                } else {
                    $opa3K = "";
                }
                if ($opa4 !== "") {
                    $opa4c = $opa4;
                    if ($this->kCruda == "1") {
                        $opa4c = round(100 * (1 - (pow((1 - (floatval($opa4) / 100)), (215 / floatval($vehiculo->diametro_escape))))), 1);
                        if ($opa4c == 100) {
                            $opa4c = 99.9;
                        }
                    }
                    $opa4K = $this->rdnr(abs(-(1 / 0.43) * log(1 - (($opa4c) / 100))));
                } else {
                    $opa4K = "";
                }
                if ($opaTotal !== "") {
                    $opaTotalc = $opaTotal;
                    if ($this->kCruda == "1") {
                        $opaTotalc = round(100 * (1 - (pow((1 - (floatval($opaTotal) / 100)), (215 / floatval($vehiculo->diametro_escape))))), 1);
                        if ($opaTotalc == 100) {
                            $opaTotalc = 99.9;
                        }
                    }
//                    $opaTotalK = $vehiculo->diametro_escape;
                    $opaTotalK = $this->rdnr(abs(-(1 / 0.43) * log(1 - (($opaTotalc) / 100))));
                } else {
                    $opaTotalK = "";
                }
            } else {
                $opa1K = "";
                $opa2K = "";
                $opa3K = "";
                $opa4K = "";
                $opaTotalK = "";
            }

//            if (intval($tmp_final) < intval($tmp_inicial)) {
//                $tmp_inicial_ = $tmp_inicial;
//                $tmp_inicial = $tmp_final;
//                $tmp_final = $tmp_inicial_;
//            }
            $opacidad = (object)
                    array(
                        'idprueba' => $r[0]->idprueba,
                        'op_ciclo1' => $opa1,
                        'op_ciclo2' => $opa2,
                        'op_ciclo3' => $opa3,
                        'op_ciclo4' => $opa4,
                        'op_ciclo1k' => $opa1K,
                        'op_ciclo2k' => $opa2K,
                        'op_ciclo3k' => $opa3K,
                        'op_ciclo4k' => $opa4K,
                        'op_cicloTk' => $opaTotalK,
                        'rpm_ciclo1' => $rpm_ciclo1,
                        'rpm_ciclo2' => $rpm_ciclo2,
                        'rpm_ciclo3' => $rpm_ciclo3,
                        'rpm_ciclo4' => $rpm_ciclo4,
                        'opacidad_total' => $this->evalOpacidad($opaTotal, $max),
                        'rpm_ralenti' => $this->rdnr($this->getResultadoIDCF($data, 38)),
                        'temp_inicial' => $tmp_inicial,
                        'temp_final' => $tmp_final,
                        'temp_ambiente' => $this->rdnr($this->getResultadoIDCF($data, 200)),
                        'humedad' => $this->rdnr($this->getResultadoIDCF($data, 201)),
                        'ltoe' => $ltoe,
                        'fugasTuboEscape' => $this->getResultadoDefecto($data, 348, 'defecto'),
                        'fugasSilenciador' => $this->getResultadoDefecto($data, 349, 'defecto'),
                        'tapaCombustible' => $this->getResultadoDefecto($data, 350, 'defecto'),
                        'tapaAceite' => $this->getResultadoDefecto($data, 351, 'defecto'),
                        'sistemaMuestreo' => $this->getResultadoDefecto($data, 352, 'defecto'),
                        'salidasAdicionales' => $this->getResultadoDefecto($data, 353, 'defecto'),
                        'filtroAire' => $this->getResultadoDefecto($data, 354, 'defecto'),
                        'sistemaRefrigeracion' => $this->getResultadoDefecto($data, 355, 'defecto'),
                        'revolucionesFueraRango' => $this->getResultadoDefecto($data, 356, 'defecto'),
                        'velocidadGiro' => $this->getResultadoDefecto($data, 379, 'defecto'),
                        'malFuncionamientoMotor' => $this->getResultadoDefecto($data, 405, 'defecto'),
                        'malFuncionamientoMotor2' => $this->getResultadoDefecto($data, 341, 'defecto'),
                        'gobernadaNoAlcanzada' => $this->getResultadoDefecto($data, 358, 'defecto'),
                        'diferenciaAritmetica1' => $this->getResultadoDefecto($data, 149, 'defecto'),
                        'diferenciaAritmetica2' => $this->getResultadoDefecto($data, 340, 'defecto'),
                        'fallaSubitaMotor' => $this->getResultadoDefecto($data, 357, 'T'),
                        'malasCondiciones' => $this->getResultadoDefecto($data, 505, 'defecto'),
                        'max' => $max,
                        'operario' => $operario->nombres . " " . $operario->apellidos,
                        'documento' => $operario->identificacion
            );
            $ifFugas = "false";
            if ($opacidad->fugasTuboEscape == 'true' || $opacidad->fugasSilenciador == 'true') {
                $ifFugas = "true";
            }
            $this->setDefAnormalesGas($ifFugas, '2.1.1.1.1', "Existencia de fugas en el tubo, uniones del múltiple y silenciador del sistema de escape del vehículo.");
            $this->setDefAnormalesGas($opacidad->salidasAdicionales, '2.1.1.1.2', "Salidas adicionales en el sistema de escape diferentes a las de diseño original del vehículo.");
            $this->setDefAnormalesGas($opacidad->tapaAceite, '2.1.1.1.3', "Ausencia de tapones de aceite o fugas en el mismo.");
            $this->setDefAnormalesGas($opacidad->tapaCombustible, '2.1.1.1.4', "Ausencia de tapones de combustible o fugas en el mismo.");
            $this->setDefAnormalesGas($opacidad->sistemaMuestreo, '2.1.1.1.5', "Instalación de accesorios o deformaciones en el tubo de escape que no permitan la introducción del acople.");
            $this->setDefAnormalesGas($opacidad->sistemaRefrigeracion, '2.1.1.1.6', "Incorrecta operación del sistema de refrigeración, cuya verificación se hará por medio de inspección. NOTA 1 esta inspección puede consistir en verificación fugas, verificación del estado del ventilador del sistema, vibraciones o posibles contactos por deflexión de los alabes del ventilador a altas revoluciones o elementos con sujeción inadecuada, entre otros.");
            $this->setDefAnormalesGas($opacidad->filtroAire, '2.1.1.1.7', "Ausencia o incorrecta instalación del filtro de aire.");
            $this->setDefAnormalesGas($opacidad->velocidadGiro, '2.1.1.1.8', "Activación de dispositivos instalados en el Motor o en el vehículo que alteren las características normales de velocidad de giro y que tengan como efecto la modificación de los resultados de la prueba de opacidad o que impidan su ejecución adecuada. Si no pueden ser desactivados antes de la siguiente prueba, el vehículo es rechazado por operación inadecuada.");
            $this->setDefAnormalesGas($opacidad->gobernadaNoAlcanzada, '2.1.1.1.9', "Durante la medición no se alcanza la velocidad gobernada antes de 5 segundos.");
            $this->setDefAnormalesGas($opacidad->malFuncionamientoMotor, '2.1.1.1.10', "Indicación de mal funcionamiento del motor.");
            $this->setDefAnormalesGas($opacidad->malFuncionamientoMotor2, '->', "Operación incorrecta, diferencia de temperatura inicial y final mayor a 10 °C");
            $this->setDefAnormalesGas($opacidad->diferenciaAritmetica1, '2.1.1.1.12', "La diferencia aritmética entre el valor mayor y menor de opacidad de las tres (3) aceleraciones, especificados en el numeral 3.2.4. (NTC4231)");
            $this->setDefAnormalesGas($opacidad->diferenciaAritmetica2, '2.1.1.1.12', "La diferencia aritmética entre el valor mayor y menor de opacidad de las tres (3) aceleraciones, especificados en el numeral 3.2.4. (NTC4231)");
            $this->setDefAnormalesGas($opacidad->fallaSubitaMotor, '2.1.1.1.13', "Falla súbita del motor y /o sus accesorios.");
            $this->setDefAnormalesGas($opacidad->malasCondiciones, '2.1.1.1.10', "Indicación de mal funcionamiento del motor.");
//            $this->setDefAnormalesGas($opacidad->malasCondiciones, '->', "El vehículo presenta malas condiciones de operación, no cumple con los intervalos de aceleración de RPM ralenti o gobernada establecidas en la ficha técnica del fabricante.");
            if ($ifFugas == "true" ||
                    $opacidad->salidasAdicionales == "true" ||
                    $opacidad->tapaAceite == "true" ||
                    $opacidad->tapaCombustible == "true" ||
                    $opacidad->sistemaMuestreo == "true" ||
                    $opacidad->sistemaRefrigeracion == "true" ||
                    $opacidad->filtroAire == "true" ||
                    $opacidad->velocidadGiro == "true" ||
                    $opacidad->malFuncionamientoMotor == "true" ||
                    $opacidad->malFuncionamientoMotor2 == "true" ||
                    $opacidad->fallaSubitaMotor == "true" ||
                    $opacidad->malasCondiciones == "true") {
                $opacidad->op_ciclo1 = '';
                $opacidad->op_ciclo2 = '';
                $opacidad->op_ciclo3 = '';
                $opacidad->op_ciclo4 = '';
                $opacidad->op_ciclo1k = '';
                $opacidad->op_ciclo2k = '';
                $opacidad->op_ciclo3k = '';
                $opacidad->op_ciclo4k = '';
                $opacidad->op_cicloTk = '';
                $opacidad->rpm_ciclo1 = '';
                $opacidad->rpm_ciclo2 = '';
                $opacidad->rpm_ciclo3 = '';
                $opacidad->rpm_ciclo4 = '';
                $opacidad->opacidad_total = '';
                $opacidad->rpm_ralenti = '';
//                $opacidad->temp_inicial = '';
//                $opacidad->temp_final = '';
            }
        } else {
            $opacidad = (object)
                    array(
                        'idprueba' => '',
                        'op_ciclo1' => '',
                        'op_ciclo2' => '',
                        'op_ciclo3' => '',
                        'op_ciclo4' => '',
                        'op_ciclo1k' => '',
                        'op_ciclo2k' => '',
                        'op_ciclo3k' => '',
                        'op_ciclo4k' => '',
                        'op_cicloTk' => '',
                        'rpm_ciclo1' => '',
                        'rpm_ciclo2' => '',
                        'rpm_ciclo3' => '',
                        'rpm_ciclo4' => '',
                        'opacidad_total' => '',
                        'rpm_ralenti' => '',
                        'temp_inicial' => '',
                        'temp_final' => '',
                        'temp_ambiente' => '',
                        'humedad' => '',
                        'ltoe' => '',
                        'fugasTuboEscape' => '',
                        'fugasSilenciador' => '',
                        'tapaCombustible' => '',
                        'tapaAceite' => '',
                        'sistemaMuestreo' => '',
                        'salidasAdicionales' => '',
                        'filtroAire' => '',
                        'sistemaRefrigeracion' => '',
                        'revolucionesFueraRango' => '',
                        'velocidadGiro' => '',
                        'malFuncionamientoMotor' => '',
                        'gobernadaNoAlcanzada' => '',
                        'diferenciaAritmetica1' => '',
                        'diferenciaAritmetica2' => '',
                        'fallaSubitaMotor' => '',
                        'malasCondiciones' => '',
                        'max' => '',
                        'operario' => '',
                        'documento' => '');
        }
        if (!$aprobadoOpacidad) {
            $this->setDefOpa();
        }
        return $opacidad;
    }

//..............................................................................EVAL OPACIDAD

    function evalOpacidad($dato, $max) {
        if ($dato !== '') {
            if (floatval($max) >= floatval($dato)) {
                return $dato;
            } else {
                $this->setDefAnormalesGas("true", '2.1.1.1.11', "Incumplimiento de niveles máximos permitidos por la autoridad competente.");
                $this->setDefOpa();
                return $dato . "*";
            }
        }
    }

    private function setDefOpa() {
        if (!$this->ifDefopacidad) {
            $this->ifDefopacidad = true;
            $grupo = 'EMISIONES CONTAMINANTES';
            if ($this->ajustarGrupos == "1") {
                $grupo = "6.6 EMISIONES CONTAMINANTES";
            }
            array_push($this->defectosMA, (object) array(
                        "codigo" => '1.1.6.16.1',
                        "descripcion" => 'Los vehículos cuyas emisiones de gases de escape tengan concentración de gases y sustancias contaminantes mayores a las establecidas por los requisitos legales ambientales definidas por las autoridades competentes.',
                        "grupo" => $grupo,
                        "tipo" => 'A'
            ));
        }
    }

//------------------------------------------------------------------------------SENSORIAL
    public function getSensorial($idhojapruebas) {
        $data['idhojapruebas'] = $idhojapruebas;
        $data['idtipo_prueba'] = "8";
        $data['order'] = $this->order;
        $result = $this->Mprueba->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            $data['idprueba'] = $r[0]->idprueba;
            $operario = $this->getUsuario($r[0]->idusuario);
            if ($r[0]->estado == '1' || $r[0]->estado == '3') {
                $this->aprobado = false;
            }
            $visual = (object) array(
                        'operario' => $operario->nombres . " " . $operario->apellidos,
                        'documento' => $operario->identificacion,
                        'idprueba' => $r[0]->idprueba);
            $rta = $this->getResultadoAll($data, 'defecto', 153);
            if ($rta !== '') {
                foreach ($rta as $r) {
                    foreach ($this->defectos as $d) {
                        if ($r->valor == $d->codigo) {
                            if ($d->ensenanza == '0') {
                                if ($d->tipo == "A") {
                                    array_push($this->defectosSA, (object) array(
                                                "codigo" => $d->codigo,
                                                "descripcion" => $d->descripcion,
                                                "grupo" => $d->nombre_grupo,
                                                "tipo" => 'A'
                                    ));
                                } else {
                                    array_push($this->defectosSB, (object) array(
                                                "codigo" => $d->codigo,
                                                "descripcion" => $d->descripcion,
                                                "grupo" => $d->nombre_grupo,
                                                "tipo" => 'B'
                                    ));
                                }
                            } else {
                                if ($d->tipo == "A") {
                                    array_push($this->defectosEA, (object) array(
                                                "codigo" => $d->codigo,
                                                "descripcion" => $d->descripcion,
                                                "grupo" => $d->nombre_grupo,
                                                "tipo" => 'A'
                                    ));
                                } else {
                                    array_push($this->defectosEB, (object) array(
                                                "codigo" => $d->codigo,
                                                "descripcion" => $d->descripcion,
                                                "grupo" => $d->nombre_grupo,
                                                "tipo" => 'B'
                                    ));
                                }
                            }

                            if ($r->observacion !== '') {
                                array_push($this->observaciones, (object) array(
                                            "codigo" => $d->codigo,
                                            "descripcion" => $r->observacion
                                ));
                            }

                            break;
                        }
                    }
                    if ($r->tiporesultado == 'COMENTARIOSADICIONALES') {
                        array_push($this->observaciones, (object) array(
                                    "codigo" => 'Comentarios adicionales',
                                    "descripcion" => $r->valor
                        ));
                    }
                }
            }

            if ($this->observacionesExtra == "1") {
                $rta2 = $this->getResultadoObses($data['idprueba']);
                if ($rta2 !== '') {
                    foreach ($rta2 as $r) {
                        switch ($r->tiporesultado) {
                            case 'obse_num_sillas':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Número de sillas que se contaron en la inspección',
                                            "descripcion" => $r->valor . " silla(s)"
                                ));
                                break;
                            case 'obse_salidas_10eme':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Número de salidas de emergencia para vehículos con capacidad superior a 10 pasajeros sin incluir el conductor',
                                            "descripcion" => $r->valor . " salida(s)"
                                ));
                                break;
                            case 'obse_salidas_15eme':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Número de salidas de emergencia para vehículos con capacidad superior a 15 pasajeros sin incluir el conductor',
                                            "descripcion" => $r->valor . " salida(s)"
                                ));
                                break;
                            case 'obse_distancia_suelo':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Distancia de altura desde el suelo con respecto al tubo de escape de descarga horizontal para vehículos diésel con capacidad de carga superior a tres (3) toneladas o para transportar más de diecinueve (19) pasajeros, modelos anteriores a 2001',
                                            "descripcion" => $r->valor . " metros"
                                ));
                                break;
                            case 'obse_distancia_cabina':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Distancia por encima del techo de la cabina con respecto al tubo de escape de descarga horizontal para vehículos diésel con capacidad de carga superior a tres (3) toneladas o para transportar más de diecinueve (19) pasajeros, modelos anteriores a 2001',
                                            "descripcion" => $r->valor . " centimetros"
                                ));
                                break;
                            case 'obse_cardan_pesados':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Longitud del cardan entre ejes para vehículos pesados',
                                            "descripcion" => $r->valor . " metros"
                                ));
                                break;
                            case 'obse_cardan_livianos':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Longitud del cardan para vehículos livianos',
                                            "descripcion" => $r->valor . " metros"
                                ));
                                break;
                            case 'obse_palabra_ense':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Dimensión de la palabra enseñanza, ancho x alto',
                                            "descripcion" => $r->valor . " centimetros"
                                ));
                                break;
                            case 'obse_freno_ense_moto':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Porcentaje eficacia de frenado doble mando de freno en motocicletas de enseñanza',
                                            "descripcion" => $r->valor . " %"
                                ));
                                break;
                            case 'obse_palabra_escolar':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Altura de la palabra ESCOLAR',
                                            "descripcion" => $r->valor . " centimetros"
                                ));
                                break;
                            case 'obse_cinta_total':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Longitud total de la cinta reflectiva en la parte lateral del vehículo',
                                            "descripcion" => $r->valor . " metros"
                                ));
                                break;
                            case 'obse_cinta_retroflectiva':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Longitud de la cinta retroreflectiva en la parte lateral del vehículo',
                                            "descripcion" => $r->valor . " metros"
                                ));
                                break;
                            case 'obse_cinta_contorno':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-  Longitud de la cinta reflectiva en la parte posterior del vehículo aplicable cuando es contorno parcial',
                                            "descripcion" => $r->valor . " metros"
                                ));
                                break;
                            case 'obse_tiempo_calentamiento_moto':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-  Tiempo de calentamiento motocicletas automáticas',
                                            "descripcion" => $r->valor . " minutos"
                                ));
                                break;
                            case 'obse_acople_moto':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Porcentaje reducción sección transversal con acople interno en motocicletas',
                                            "descripcion" => $r->valor . " %"
                                ));
                                break;
                            case 'obse_presion_inflado':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Se debe medir la presion de inflado para confirmar que el vehiculo esta preparado Ver Criterios presion de inflado',
                                            "descripcion" => $r->valor . " psi"
                                ));
                                break;
                            case 'obse_longitud_ancho_posterior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Longitud ancho parte posterior vehículo aplicable cuando es contorno parcial',
                                            "descripcion" => $r->valor . " metros"
                                ));
                                break;
                            case 'obse_diametro_exosto':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Diametro exosto',
                                            "descripcion" => $r->valor . " milimetros"
                                ));
                                break;
                            case 'obse_registro_temperatura':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Registro temperatura gases "0"',
                                            "descripcion" => $r->valor . " °C"
                                ));
                                break;
                            case 'obse_sensor_luz_der':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Determinacion sensor de luz derecho',
                                            "descripcion" => $r->valor . " "
                                ));
                                break;
                            case 'obse_tipo_bombillo_der':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Determinacion tipo bombillo derecho',
                                            "descripcion" => $r->valor . " "
                                ));
                                break;
                            case 'obse_distancia_luces_der':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Distancia a la cual se realizo la prueba de luces derecho',
                                            "descripcion" => $r->valor . " M"
                                ));
                                break;
                            case 'obse_sensor_luz_izq':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Determinacion sensor de luz izquierdo',
                                            "descripcion" => $r->valor . " "
                                ));
                                break;
                            case 'obse_tipo_bombillo_izq':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Determinacion tipo bombillo izquierdo',
                                            "descripcion" => $r->valor . " "
                                ));
                                break;
                            case 'obse_distancia_luces_izq':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Distancia a la cual se realizo la prueba de luces izquierdo',
                                            "descripcion" => $r->valor . " M"
                                ));
                                break;
                            case 'obse_sensor_luz_2der':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Determinacion sensor de luz 2 derecho',
                                            "descripcion" => $r->valor . " "
                                ));
                                break;
                            case 'obse_tipo_bombillo_2der':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Determinacion tipo bombillo 2 derecho',
                                            "descripcion" => $r->valor . " "
                                ));
                                break;
                            case 'obse_distancia_luces_2der':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Distancia a la cual se realizo la prueba de luces 2 derecho',
                                            "descripcion" => $r->valor . " M"
                                ));
                                break;
                            case 'obse_sensor_luz_2izq':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Determinacion sensor de luz 2 izquierdo',
                                            "descripcion" => $r->valor . " "
                                ));
                                break;
                            case 'obse_tipo_bombillo_2izq':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Determinacion tipo bombillo 2 izquierdo',
                                            "descripcion" => $r->valor . " "
                                ));
                                break;
                            case 'obse_distancia_luces_2izq':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Distancia a la cual se realizo la prueba de luces 2 izquierdo',
                                            "descripcion" => $r->valor . " M"
                                ));
                                break;
                            case 'obse_sensor_luz_3der':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Determinacion sensor de luz 3 derecho',
                                            "descripcion" => $r->valor . " "
                                ));
                                break;
                            case 'obse_tipo_bombillo_3der':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Determinacion tipo bombillo 3 derecho',
                                            "descripcion" => $r->valor . " "
                                ));
                                break;
                            case 'obse_distancia_luces_3der':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Distancia a la cual se realizo la prueba de luces 3 derecho',
                                            "descripcion" => $r->valor . " M"
                                ));
                                break;
                            case 'obse_sensor_luz_3izq':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Determinacion sensor de luz 3 izquierdo',
                                            "descripcion" => $r->valor . " "
                                ));
                                break;
                            case 'obse_tipo_bombillo_3izq':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Determinacion tipo bombillo 3 izquierdo',
                                            "descripcion" => $r->valor . " "
                                ));
                                break;
                            case 'obse_distancia_luces_3izq':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '-   Distancia a la cual se realizo la prueba de luces 3 izquierdo',
                                            "descripcion" => $r->valor . " M"
                                ));
                                break;
                            case 'Labrado_llanta_eje1_izquierdo1':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 1 lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje1_izquierdo2':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 1 lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje2_izquierdo1':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 2 lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje2_izquierdo2':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 2 lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje2_izquierdo1_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 2 interior lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje2_izquierdo2_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 2 interior lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje3_izquierdo1':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 3 lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje3_izquierdo2':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 3 lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje3_izquierdo1_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 3 interior lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje3_izquierdo2_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 3 interior lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje4_izquierdo1':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 4 lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje4_izquierdo2':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 4 lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje4_izquierdo1_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 4 interior lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje4_izquierdo2_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 4 interior lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje5_izquierdo1':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 5 lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje5_izquierdo2':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 5 lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje5_izquierdo1_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 5 interior lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje5_izquierdo2_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta izquierda en eje 5 interior lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje1_derecho1':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 1 lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje1_derecho2':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 1 lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje2_derecho1':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 2 lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje2_derecho2':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 2 lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje2_derecho1_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 2 interior lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje2_derecho2_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 2 interior lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje3_derecho1':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 3 lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje3_derecho2':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 3 lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje3_derecho1_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 3 interior lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje3_derecho2_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 3 interior lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje4_derecho1':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 4 lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje4_derecho2':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 4 lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje4_derecho1_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 4 interior lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje4_derecho2_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 4 interior lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje5_derecho1':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 5 lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje5_derecho2':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 5 lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje5_derecho1_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 5 interior lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_llanta_eje5_derecho2_interior':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta derecha en eje 5 interior lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_repuesto_1':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta repuesto 1 lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_repuesto_2':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta repuesto 1 lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_repuesto2_1':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta repuesto 2 lectura 1',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                            case 'Labrado_repuesto2_2':
                                array_push($this->observaciones, (object) array(
                                            "codigo" => '- Profundidad de labrado llanta repuesto 2 lectura 2',
                                            "descripcion" => $this->rdnr($r->valor) . " mm"
                                ));
                                break;
                        }
                    }
                }
            }
        } else {
            $visual = (object)
                    array(
                        'operario' => "",
                        'documento' => "",
                        'idprueba' => "");
        }
        return $visual;

//            var $defectosSA;
//    var $defectosSB;
    }

//------------------------------------------------------------------------------LABRADO
    public function getLabrados($idhojapruebas) {
        $data['idhojapruebas'] = $idhojapruebas;
        $data['idtipo_prueba'] = "8";
        $data['order'] = $this->order;
        $result = $this->Mprueba->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            $data['idprueba'] = $r[0]->idprueba;

            if ($this->nombreClase->nombre !== "MOTOCICLETA") {

                $labrados = (object)
                        array(
                            'eje1_derecho' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje1_derecho', '')),
                            'eje1_derecho1' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje1_derecho1', '')),
                            'eje1_derecho2' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje1_derecho2', '')),
                            'eje2_derecho' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_derecho', '')),
                            'eje2_derecho1' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_derecho1', '')),
                            'eje2_derecho2' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_derecho2', '')),
                            'eje3_derecho' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje3_derecho', '')),
                            'eje3_derecho1' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje3_derecho1', '')),
                            'eje3_derecho2' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje3_derecho2', '')),
                            'eje4_derecho' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje4_derecho', '')),
                            'eje4_derecho1' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje4_derecho1', '')),
                            'eje4_derecho2' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje4_derecho2', '')),
                            'eje5_derecho' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje5_derecho', '')),
                            'eje5_derecho1' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje5_derecho1', '')),
                            'eje5_derecho2' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje5_derecho2', '')),
                            'eje2_derecho_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_derecho_interior', '')),
                            'eje2_derecho1_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_derecho1_interior', '')),
                            'eje2_derecho2_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_derecho2_interior', '')),
                            'eje3_derecho_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje3_derecho_interior', '')),
                            'eje3_derecho1_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje3_derecho_1interior', '')),
                            'eje3_derecho2_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje3_derecho_2interior', '')),
                            'eje4_derecho_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje4_derecho_interior', '')),
                            'eje4_derecho1_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje4_derecho1_interior', '')),
                            'eje4_derecho2_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje4_derecho2_interior', '')),
                            'eje5_derecho_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje5_derecho_interior', '')),
                            'eje5_derecho1_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje5_derecho1_interior', '')),
                            'eje5_derecho2_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje5_derecho2_interior', '')),
                            'eje1_izquierdo' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje1_izquierdo', '')),
                            'eje1_izquierdo1' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje1_izquierdo1', '')),
                            'eje1_izquierdo2' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje1_izquierdo2', '')),
                            'eje2_izquierdo' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_izquierdo', '')),
                            'eje2_izquierdo1' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_izquierdo1', '')),
                            'eje2_izquierdo2' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_izquierdo2', '')),
                            'eje3_izquierdo' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje3_izquierdo', '')),
                            'eje3_izquierdo1' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje3_izquierdo1', '')),
                            'eje3_izquierdo2' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje3_izquierdo2', '')),
                            'eje4_izquierdo' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje4_izquierdo', '')),
                            'eje4_izquierdo1' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje4_izquierdo1', '')),
                            'eje4_izquierdo2' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje4_izquierdo2', '')),
                            'eje5_izquierdo' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje5_izquierdo', '')),
                            'eje5_izquierdo1' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje5_izquierdo1', '')),
                            'eje5_izquierdo2' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje5_izquierdo2', '')),
                            'eje2_izquierdo_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_izquierdo_interior', '')),
                            'eje2_izquierdo1_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_izquierdo1_interior', '')),
                            'eje2_izquierdo2_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_izquierdo2_interior', '')),
                            'eje3_izquierdo_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje3_izquierdo_interior', '')),
                            'eje3_izquierdo1_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje3_izquierdo1_interior', '')),
                            'eje3_izquierdo2_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje3_izquierdo2_interior', '')),
                            'eje4_izquierdo_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje4_izquierdo_interior', '')),
                            'eje4_izquierdo1_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje4_izquierdo1_interior', '')),
                            'eje4_izquierdo2_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje4_izquierdo2_interior', '')),
                            'eje5_izquierdo_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje5_izquierdo_interior', '')),
                            'eje5_izquierdo1_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje5_izquierdo1_interior', '')),
                            'eje5_izquierdo2_interior' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje5_izquierdo2_interior', '')),
                            'repuesto' => $this->rdnr($this->getResultado($data, 'Labrado_repuesto', '')),
                            'repuesto_1' => $this->rdnr($this->getResultado($data, 'Labrado_repuesto_1', '')),
                            'repuesto_2' => $this->rdnr($this->getResultado($data, 'Labrado_repuesto_2', '')),
                            'repuesto2' => $this->rdnr($this->getResultado($data, 'Labrado_repuesto2', '')),
                            'repuesto2_1' => $this->rdnr($this->getResultado($data, 'Labrado_repuesto2_1', '')),
                            'repuesto2_2' => $this->rdnr($this->getResultado($data, 'Labrado_repuesto2_2', '')));
            } else {
                $labrados = (object)
                        array(
                            'eje1_derecho' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje1_derecho', '')),
                            'eje1_derecho1' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje1_derecho1', '')),
                            'eje1_derecho2' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje1_derecho2', '')),
                            'eje2_derecho' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_derecho', '')),
                            'eje2_derecho1' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_derecho1', '')),
                            'eje2_derecho2' => $this->rdnr($this->getResultado($data, 'Labrado_llanta_eje2_derecho2', '')),
                            'eje3_derecho' => '',
                            'eje3_derecho1' => '',
                            'eje3_derecho2' => '',
                            'eje4_derecho' => '',
                            'eje4_derecho1' => '',
                            'eje4_derecho2' => '',
                            'eje5_derecho' => '',
                            'eje5_derecho1' => '',
                            'eje5_derecho2' => '',
                            'eje2_derecho_interior' => '',
                            'eje2_derecho1_interior' => '',
                            'eje2_derecho2_interior' => '',
                            'eje3_derecho_interior' => '',
                            'eje3_derecho1_interior' => '',
                            'eje3_derecho2_interior' => '',
                            'eje4_derecho_interior' => '',
                            'eje4_derecho1_interior' => '',
                            'eje4_derecho2_interior' => '',
                            'eje5_derecho_interior' => '',
                            'eje5_derecho1_interior' => '',
                            'eje5_derecho2_interior' => '',
                            'eje1_izquierdo' => '',
                            'eje1_izquierdo1' => '',
                            'eje1_izquierdo2' => '',
                            'eje2_izquierdo' => '',
                            'eje2_izquierdo1' => '',
                            'eje2_izquierdo2' => '',
                            'eje3_izquierdo' => '',
                            'eje3_izquierdo1' => '',
                            'eje3_izquierdo2' => '',
                            'eje4_izquierdo' => '',
                            'eje4_izquierdo1' => '',
                            'eje4_izquierdo2' => '',
                            'eje5_izquierdo' => '',
                            'eje5_izquierdo1' => '',
                            'eje5_izquierdo2' => '',
                            'eje2_izquierdo_interior' => '',
                            'eje2_izquierdo1_interior' => '',
                            'eje2_izquierdo2_interior' => '',
                            'eje3_izquierdo_interior' => '',
                            'eje3_izquierdo1_interior' => '',
                            'eje3_izquierdo2_interior' => '',
                            'eje4_izquierdo_interior' => '',
                            'eje4_izquierdo1_interior' => '',
                            'eje4_izquierdo2_interior' => '',
                            'eje5_izquierdo_interior' => '',
                            'eje5_izquierdo1_interior' => '',
                            'eje5_izquierdo2_interior' => '',
                            'repuesto' => '',
                            'repuesto_1' => '',
                            'repuesto_2' => '',
                            'repuesto2' => '',
                            'repuesto2_1' => '',
                            'repuesto2_2' => '');
            }
        } else {
            $labrados = (object)
                    array(
                        'eje1_derecho' => '',
                        'eje1_derecho1' => '',
                        'eje1_derecho2' => '',
                        'eje2_derecho' => '',
                        'eje2_derecho1' => '',
                        'eje2_derecho2' => '',
                        'eje3_derecho' => '',
                        'eje3_derecho1' => '',
                        'eje3_derecho2' => '',
                        'eje4_derecho' => '',
                        'eje4_derecho1' => '',
                        'eje4_derecho2' => '',
                        'eje5_derecho' => '',
                        'eje5_derecho1' => '',
                        'eje5_derecho2' => '',
                        'eje2_derecho_interior' => '',
                        'eje2_derecho1_interior' => '',
                        'eje2_derecho2_interior' => '',
                        'eje3_derecho_interior' => '',
                        'eje3_derecho1_interior' => '',
                        'eje3_derecho2_interior' => '',
                        'eje4_derecho_interior' => '',
                        'eje4_derecho1_interior' => '',
                        'eje4_derecho2_interior' => '',
                        'eje5_derecho_interior' => '',
                        'eje5_derecho1_interior' => '',
                        'eje5_derecho2_interior' => '',
                        'eje1_izquierdo' => '',
                        'eje1_izquierdo1' => '',
                        'eje1_izquierdo2' => '',
                        'eje2_izquierdo' => '',
                        'eje2_izquierdo1' => '',
                        'eje2_izquierdo2' => '',
                        'eje3_izquierdo' => '',
                        'eje3_izquierdo1' => '',
                        'eje3_izquierdo2' => '',
                        'eje4_izquierdo' => '',
                        'eje4_izquierdo1' => '',
                        'eje4_izquierdo2' => '',
                        'eje5_izquierdo' => '',
                        'eje5_izquierdo1' => '',
                        'eje5_izquierdo2' => '',
                        'eje2_izquierdo_interior' => '',
                        'eje2_izquierdo1_interior' => '',
                        'eje2_izquierdo2_interior' => '',
                        'eje3_izquierdo_interior' => '',
                        'eje3_izquierdo1_interior' => '',
                        'eje3_izquierdo2_interior' => '',
                        'eje4_izquierdo_interior' => '',
                        'eje4_izquierdo1_interior' => '',
                        'eje4_izquierdo2_interior' => '',
                        'eje5_izquierdo_interior' => '',
                        'eje5_izquierdo1_interior' => '',
                        'eje5_izquierdo2_interior' => '',
                        'repuesto' => '',
                        'repuesto_1' => '',
                        'repuesto_2' => '',
                        'repuesto2' => '',
                        'repuesto2_1' => '',
                        'repuesto2_2' => '');
        }
        return $labrados;
    }

//------------------------------------------------------------------------------FOTOGRAFIA  
    public function getFotografias($idhojapruebas) {
        $data['idhojapruebas'] = $idhojapruebas;
        $data['idtipo_prueba'] = "5";
        $data['order'] = $this->order;
        $result = $this->Mprueba->get5($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            if (isset($r[0]->idprueba)) {
                $data['idprueba'] = $r[0]->idprueba;
                $imagen1 = $this->getimagen($data);
                if ($imagen1 !== '') {
                    $imagen = explode(",", $imagen1);
                    $imagen1 = $imagen[1];
                }
            } else {
                $imagen1 = '';
            }

            if (isset($r[1]->idprueba)) {
                $data['idprueba'] = $r[1]->idprueba;
                $imagen2 = $this->getimagen($data);
                if ($imagen2 !== '') {
                    $imagen = explode(",", $imagen2);
                    $imagen2 = $imagen[1];
                }
            } else {
                $imagen2 = '';
            }
//            if (substr($imagen1, 0, 23) !== 'data:image/jpeg;base64,') {
//            $imagen1 = base64_encode($imagen1);
//            } else {
//                $imagen = explode(",", $imagen1);
//                $imagen1 = $imagen[1];
//            }
//            if (substr($imagen2, 0, 23) !== 'data:image/jpeg;base64,') {
//            $imagen2 = base64_encode($imagen2);
//            } else {
//                $imagen = explode(",", $imagen2);
//                $imagen2 = $imagen[1];
//            }
            if ($imagen1 !== '') {
                $imagen1 = '@' . $imagen1;
            }
            if ($imagen2 !== '') {
                $imagen2 = '@' . $imagen2;
            }
            $imagenes = (object)
                    array(
                        'imagen1' => $imagen1,
                        'imagen2' => $imagen2);
        } else {
            $imagenes = (object)
                    array(
                        'imagen1' => '',
                        'imagen2' => '');
        }
        return $imagenes;
    }

    var $perEncontrado = true;

//------------------------------------------------------------------------------MAQUINAS
    public function getMaquinas($idhojapruebas, $fechainicial, $v) {
        $data['idhojapruebas'] = $idhojapruebas;
        $data['fechainicial'] = $fechainicial;
        $data['order'] = $this->order;
//------------------------------------------------------------------------------luxometro        
        $data['idtipo_prueba'] = "1";
        $luxometro = $this->getNombreMaquina($data, "Luxometro");
//------------------------------------------------------------------------------opacimetro        
        $data['idtipo_prueba'] = "2";
        $opacimetro = $this->getNombreMaquina($data, "Opacimetro");
//------------------------------------------------------------------------------gases
        $data['idtipo_prueba'] = "3";
        $gases = $this->getNombreMaquina($data, "Analizador de gases");
//------------------------------------------------------------------------------sonometria
        $data['idtipo_prueba'] = "4";
        $sonometro = $this->getNombreMaquina($data, "Sonómetro");
//------------------------------------------------------------------------------fotos
        $data['idtipo_prueba'] = "5";
        $fotos = $this->getNombreMaquina($data, "Cámara");
//------------------------------------------------------------------------------taximetro
        $data['idtipo_prueba'] = "6";
        $taximetro = $this->getNombreMaquina($data, "Taxímetro");
//------------------------------------------------------------------------------frenos
        $data['idtipo_prueba'] = "7";
        $frenos = $this->getNombreMaquina($data, "Frenómetro");
//------------------------------------------------------------------------------frenos
        $bascula = "";
        if ($v->tipo_vehiculo == "2" && $frenos !== "") {
//            var_dump($frenos);
            $bascula = $this->getBascula($this->idLinea);
        }
//        $data['idtipo_prueba'] = "23";
//        $bascula = $this->getNombreMaquina($data, "Báscula");
//------------------------------------------------------------------------------visual
        $data['idtipo_prueba'] = "8";
        $visual = $this->getNombreMaquina($data, "Visual");
//------------------------------------------------------------------------------suspension
        $data['idtipo_prueba'] = "9";
        $suspension = $this->getNombreMaquina($data, "Banco de suspensión");
//------------------------------------------------------------------------------alineador
        $data['idtipo_prueba'] = "10";
        $alineador = $this->getNombreMaquina($data, "Alineador");
//------------------------------------------------------------------------------termohigrometro
        $data['idtipo_prueba'] = "12";
        $termohigrometro = $this->getNombreMaquina($data, "Termohigómetro");
//------------------------------------------------------------------------------profundimetro
        $data['idtipo_prueba'] = "13";
        $profundimetro = $this->getNombreMaquina($data, "Profundímetro");
//------------------------------------------------------------------------------captador
        $data['idtipo_prueba'] = "14";
        $captador = $this->getNombreMaquina($data, "Captador");
//------------------------------------------------------------------------------pie de rey
        $data['idtipo_prueba'] = "15";
        $piederey = $this->getNombreMaquina($data, "Pie de rey");
//------------------------------------------------------------------------------detector de holguras
        $data['idtipo_prueba'] = "16";
        $detector = $this->getNombreMaquina($data, "Detector de holguras");
//------------------------------------------------------------------------------elevador de motos
        $data['idtipo_prueba'] = "17";
        $elevador = $this->getNombreMaquina($data, "Elevador");
        if ($this->habilitarPerifericos == '1') {
//------------------------------------------------------------------------------Sensor rpm
            $data['idtipo_prueba'] = "21";
            $sensorRPM = $this->getNombreMaquina($data, "Sensor RPM");
//------------------------------------------------------------------------------Sonda temperatura
            $data['idtipo_prueba'] = "22";
            $sondaTMP = $this->getNombreMaquina($data, "Sonda Temperatura");
        } else {
            $sensorRPM = "";
            $sondaTMP = "";
        }

        if ($opacimetro == "" && $gases == "") {
            $sensorRPM = "";
            $sondaTMP = "";
            $termohigrometro = "";
            $captador = "";
        }
        if ($v->scooter === '1') {
            $sondaTMP = "";
        }
        $maquinas = (object)
                array(
                    'nombreLuxometro' => $luxometro,
                    'nombreOpacimetro' => $opacimetro,
                    'nombreGases' => $gases,
                    'nombreSonometro' => $sonometro,
                    'nombreFotos' => $fotos,
                    'nombreTaximetro' => $taximetro,
                    'nombreFrenos' => $frenos,
                    'nombreBascula' => $bascula,
                    'nombreVisual' => $visual,
                    'nombreSuspension' => $suspension,
                    'nombreAlineador' => $alineador,
                    'nombreTermohigrometro' => $termohigrometro,
                    'nombreProfundimetro' => $profundimetro,
                    'nombreCaptador' => $captador,
                    'nombreDetector' => $detector,
                    'nombreElevador' => $elevador,
                    'nombrePiederey' => $piederey,
                    'nombreSensorRPM' => $sensorRPM,
                    'nombreSondaTMP' => $sondaTMP
        );
        return $maquinas;
    }

//------------------------------------------------------------------------------NOMBRE MAQUINA
    public function getNombreMaquina($data, $nombrePrueba) {
        $rta = $this->Mprueba->getMaq($data);
        if ($rta->num_rows() > 0) {
            $r = $rta->result();
            $data['idmaquina'] = $r[0]->idmaquina;
            $data['IdUsuario'] = $r[0]->idusuario;
            if (($nombrePrueba == "Sensor RPM" || $nombrePrueba == "Sonda Temperatura") && $data['idmaquina'] == '') {
                $data['idmaquina'] = "0";
                $data['IdUsuario'] = "0";
                $nombre = $this->getMaquinaDat($data);
            } else {
                if ($data['idmaquina'] == '') {
                    $nombre = "";
                } else {
                    $nombre = $this->getMaquinaDat($data);
                }
            }
        } else {
            $nombre = "";
        }
        return $nombre;
    }

//------------------------------------------------------------------------------BASCULA
//    public function getBascula($data, $nombrePrueba) {
//        $rta = $this->Mprueba->getMaq($data);
//        if ($rta->num_rows() > 0) {
//            $r = $rta->result();
//            $data['idmaquina'] = $r[0]->idmaquina;
//            $data['IdUsuario'] = $r[0]->idusuario;
//            if (($nombrePrueba == "Sensor RPM" || $nombrePrueba == "Sonda Temperatura") && $data['idmaquina'] == '') {
//                $data['idmaquina'] = "0";
//                $data['IdUsuario'] = "0";
//                $nombre = $this->getMaquinaDat($data);
//            } else {
//                if ($data['idmaquina'] == '') {
//                    $nombre = "";
//                } else {
//                    $nombre = $this->getMaquinaDat($data);
//                }
//            }
//        } else {
//            $nombre = "";
//        }
//        return $nombre;
//    }

    public function getNombreMaquina55($data, $nombrePrueba) {
        $rta = $this->Mprueba->get55maquina($data);
        if ($rta->num_rows() > 0) {
            $r = $rta->result();
            $nombre = $this->getMaquinaDat($r[0]->idmaquina);
//            $nombre = $nombrePrueba . " -> " . $r[0]->nombrereal . "<br>";
        } else {
            $nombre = "";
        }
        return $nombre;
    }

    var $idLinea;

    private function getMaquinaDat($data) {
        $nombreMaquina = "";
        $marcaMaquina = "";
        $serialMaquina = "";
        $referenciaMaquina = "";
        $pefMaquina = "";
        $ltoeMaquina = "";
        $noSerieBench = "";
        $periferico = "";
        $encontrada = false;
        $per = "";
        $this->perEncontrado = false;
        if ($conf = @file_get_contents("system/lineas.json")) {
            $encrptopenssl = New Opensslencryptdecrypt();
            $json = $encrptopenssl->decrypt($conf, true);
            $dat = json_decode($json, true);
            foreach ($dat as $d) {
                if ($d['conf_idtipo_prueba'] == $data['idtipo_prueba'] && ($d['conf_idtipo_prueba'] == '21' || $d['conf_idtipo_prueba'] == '22') && !$this->perEncontrado) {
                    $this->perEncontrado = true;
                    $per = $d;
                }
                if ($d['idconf_maquina'] == $data['idmaquina']) {
                    if ($d['conf_idtipo_prueba'] == '21' || $d['conf_idtipo_prueba'] == '22') {
                        $encontrada = true;
                    }
                    if ($d['conf_idtipo_prueba'] == '7') {
//                       echo $d['conf_idtipo_prueba'];
                        $this->idLinea = $d['idconf_linea_inspeccion'];

//                       echo $d['idconf_linea_inspeccion'];
                    }
                    $nombreMaquina = $d['nombre'];
                    $serialMaquina = $d['serie_maquina'];
                    $referenciaMaquina = $d['serie_banco'];
                    if (intval($d['conf_idtipo_prueba']) > 10) {
                        $periferico = "S";
                    } else {
                        $periferico = "N";
                    }
                    if ($conf = @file_get_contents("system/" . $data['idmaquina'] . ".json")) {
                        $json = $encrptopenssl->decrypt($conf, true);
                        $dat = json_decode($json, true);
                        foreach ($dat as $d) {
                            switch ($d['nombre']) {
                                case 'nombreMarca':
                                    $marcaMaquina = $d['valor'];
                                    break;
                                case 'ltoe':
                                    $ltoeMaquina = $d['valor'];
                                    break;
                                case 'pef':
                                    $pefMaquina = $d['valor'];
                                    break;
                                case 'noSerieBench':
                                    $noSerieBench = $d['valor'];
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                    break;
                }
            }
        }
        if ($encontrada == false && ($data['idtipo_prueba'] == '22' || $data['idtipo_prueba'] == '21')) {
//            var_dump($data);
            $nombreMaquina = $per['nombre'];
            $serialMaquina = $per['serie_maquina'];
            $referenciaMaquina = $per['serie_banco'];
            $periferico = "S";
            if ($conf = @file_get_contents("system/" . $per['idconf_maquina'] . ".json")) {
                $json = $encrptopenssl->decrypt($conf, true);
                $dat = json_decode($json, true);
                foreach ($dat as $d) {
                    switch ($d['nombre']) {
                        case 'nombreMarca':
                            $marcaMaquina = $d['valor'];
                            break;
                        default:
                            break;
                    }
                }
            }
        }
        $ru = $this->Musuarios->get($data);
        if ($ru->num_rows()) {
            $r = $ru->result();
            $usuario = $r[0]->nombres . ' ' . $r[0]->apellidos;
        } else {
            $usuario = '';
        }
        if ($data['idtipo_prueba'] == '7') {
            $nombreMaquina = "FRENOMETRO";
        }
        if ($data['idtipo_prueba'] == '10') {
            $nombreMaquina = "ALINEADOR";
        }
        if ($data['idtipo_prueba'] == '9') {
            $nombreMaquina = "BANCO SUSPENSIÓN";
        }
        $maquina = $nombreMaquina . "$" . $marcaMaquina . "$" . $serialMaquina . "$" .
                $referenciaMaquina . "$" . $pefMaquina . "$" . $ltoeMaquina . "$" .
                $noSerieBench . "$" . $periferico . "$" . $usuario . "^";
        return strtoupper($maquina);
    }

    private function getBascula($idlineaIns) {
        $nombreMaquina = "";
        $marcaMaquina = "";
        $serialMaquina = "";
        $referenciaMaquina = "";
        $pefMaquina = "";
        $ltoeMaquina = "";
        $noSerieBench = "";
        $periferico = "";
        if ($conf = @file_get_contents("system/lineas.json")) {
            $encrptopenssl = New Opensslencryptdecrypt();
            $json = $encrptopenssl->decrypt($conf, true);
            $dat = json_decode($json, true);
            foreach ($dat as $d) {
                if ($d['idconf_linea_inspeccion'] == $idlineaIns && $d['conf_idtipo_prueba'] == '23') {
                    $nombreMaquina = $d['nombre'];
                    $serialMaquina = $d['serie_maquina'];
                    $referenciaMaquina = $d['serie_banco'];
                    $periferico = "N";
                    if ($conf = @file_get_contents("system/" . $d['idconf_maquina'] . ".json")) {
                        $json = $encrptopenssl->decrypt($conf, true);
                        $dat = json_decode($json, true);
                        foreach ($dat as $d) {
                            switch ($d['nombre']) {
                                case 'nombreMarca':
                                    $marcaMaquina = $d['valor'];
                                    break;
                                case 'ltoe':
                                    $ltoeMaquina = $d['valor'];
                                    break;
                                case 'pef':
                                    $pefMaquina = $d['valor'];
                                    break;
                                case 'noSerieBench':
                                    $noSerieBench = $d['valor'];
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                    break;
                }
            }
        }

        $maquina = $nombreMaquina . "$" . $marcaMaquina . "$" . $serialMaquina . "$" .
                $referenciaMaquina . "$" . $pefMaquina . "$" . $ltoeMaquina . "$" .
                $noSerieBench . "$" . $periferico . "$^";
        return strtoupper($maquina);
    }

//------------------------------------------------------------------------------INSPECTORES
    public function getInspectores($idhojapruebas) {
        $data['idhojapruebas'] = $idhojapruebas;
        $rta = $this->Mprueba->getInspectores($data);
        $inspectores = "";
        if ($rta->num_rows() > 0) {
            foreach ($rta->result() as $r) {
                $inspectores = $inspectores . "- " . $r->operarios . "<br>";
            }
        }
        return $inspectores;
    }

//------------------------------------------------------------------------------JEFE TECNICO
    public function getJefeTecnico0() {
        $data['idconfig_prueba'] = '182';
        $result = $this->Mconfig_prueba->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0]->valor;
        } else {
            return '';
        }
    }

    public function getJefeTecnico1($IDCP) {
        $data['idhojapruebas'] = $IDCP;
        $result = $this->Mresultado->getIDCP($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return str_replace(",", ".", $r[0]->valor);
        } else {
            return '';
        }
    }

//------------------------------------------------------------------------------RESULTADO
    public function getResultado($data, $tiporesultado, $observacion) {
        $data['tiporesultado'] = $tiporesultado;
        if ($observacion <> '') {
            $data['observacion'] = "and observacion='$observacion'";
        } else {
            $data['observacion'] = '';
        }
        $result = $this->Mresultado->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return str_replace(",", ".", $r[0]->valor);
        } else {
            return '';
        }
    }

    public function getResultadoId($idprueba, $tiporesultado) {
//        $data['tiporesultado'] = $tiporesultado;
//        if ($observacion <> '') {
//            $data['observacion'] = "and observacion='$observacion'";
//        } else {
//            $data['observacion'] = '';
//        }
        $result = $this->Mresultado->getIdprueba($idprueba, $tiporesultado);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return str_replace(",", ".", $r[0]->valor);
        } else {
            return '';
        }
    }

    public function getResultadoTmpMot($data, $tiporesultado, $observacion) {
        $data['tiporesultado'] = $tiporesultado;
        if ($observacion <> '') {
            $data['observacion'] = "and observacion='$observacion'";
        } else {
            $data['observacion'] = '';
        }
        $result = $this->Mresultado->getTmpMot($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return str_replace(",", ".", $r[0]->valor);
        } else {
            return '0';
        }
    }

    var $observacionHumos = "";
    var $observacion405 = "";
    var $observacionDifAri = "";

//------------------------------------------------------------------------------RESULTADO
    public function getResultadoDefecto($data, $valor, $tiporesultado) {
        $data['tiporesultado'] = $tiporesultado;
        $data['valor'] = $valor;
        $result = $this->Mresultado->getDefT($data);
//        var_dump($result->result());
        if ($result->num_rows() > 0) {
            if ($valor == 333) {
                $r = $result->result();
                $this->observacionHumos = $r[0]->observacion;
            }
            if ($valor == 405) {
                $r = $result->result();
                $this->observacion405 = $r[0]->observacion;
            }
            if ($valor == 340) {
                $r = $result->result();
                $this->observacionDifAri = $r[0]->observacion;
            }
            return 'true';
        } else {
            return 'false';
        }
    }

//------------------------------------------------------------------------------RESULTADO MAX GASES
    public function getResultadoMaxGases($data, $tiporesultado) {
        $data['tiporesultado'] = $tiporesultado;
        $result = $this->Mresultado->getMaxGases($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
//            var_dump($r);
            return str_replace(",", ".", $r[0]->valor);
        } else {
            return '';
        }
    }

//------------------------------------------------------------------------------RESULTADO MAX RPM GASES
    public function getResultadoMaxRpmGases($data) {
        $result = $this->Mresultado->getMaxRpmGases($data);
        if ($result->num_rows() > 0) {
//            $r = $result->result();
            return TRUE;
        } else {
            return FALSE;
        }
    }

//------------------------------------------------------------------------------RESULTADO idconfig_prueba
    public function getResultadoIDCF($data, $IDCP) {
        $data['idconfig_prueba'] = $IDCP;
        $result = $this->Mresultado->getIDCP($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return str_replace(",", ".", $r[0]->valor);
        } else {
            return '';
        }
    }

//------------------------------------------------------------------------------RESULTADO idconfig_prueba_tipo
    public function getResultadoIDCF_tipo($data, $tipo, $IDCP) {
        $data['idconfig_prueba'] = $IDCP;
        $data['tiporesultado'] = $tipo;
        $result = $this->Mresultado->getIDCP_tipo($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return str_replace(",", ".", $r[0]->valor);
        } else {
            return '';
        }
    }

//------------------------------------------------------------------------------RESULTADO idconfig_prueba_tipo
    public function getSumFuerzaAux($data, $tipo, $IDCP) {
        $data['idconfig_prueba'] = $IDCP;
        $data['tiporesultado'] = $tipo;
        $result = $this->Mresultado->getSumFuerzaAux($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return str_replace(",", ".", $r[0]->valor);
        } else {
            return '';
        }
    }

//------------------------------------------------------------------------------RESULTADO sensorial
    public function getResultadoAll($data, $tipo, $IDCP) {
        $data['idconfig_prueba'] = $IDCP;
        $data['tiporesultado'] = $tipo;
        $result = $this->Mresultado->getDef($data);
        if ($result->num_rows() > 0) {
            return $result->result();
        } else {
            return '';
        }
    }

//------------------------------------------------------------------------------RESULTADO obses
    public function getResultadoObses($idprueba) {
        $result = $this->Mresultado->getObses($idprueba);
        if ($result->num_rows() > 0) {
            return $result->result();
        } else {
            return '';
        }
    }

//------------------------------------------------------------------------------IMAGEN
    public function getImagen($data) {
        if ($this->espejoImagenes == '1') {
            if ($this->desdeConsulta !== "true") {
                $result = $this->Mimagenes->get($data);
            } else {
                $result = $this->Mimagenes->get2($data);
            }
        } else {
            $result = $this->Mimagenes->get($data);
        }
        if ($result->num_rows() > 0) {
            $r = $result->result();
//            $imagen = explode(",", $r[0]->imagen);
            return $r[0]->imagen;
        } else {
            return '';
        }
    }

//------------------------------------------------------------------------------MAQUINA
    public function getMaquina($data) {
        $result = $this->Mmaquina->get($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0];
        } else {
            return '';
        }
    }

    public function subChar() {
        
    }

    public function tipoDocumento($tipoDoc) {
        if ($tipoDoc === "1" || $tipoDoc === "3") {
            if ($tipoDoc === "3") {
                array_push($this->observaciones, (object) array(
                            "codigo" => '- ',
                            "descripcion" => 'PROPIETARIO, TENEDOR O POSEEDOR DEL VEHÍCULO CON DOCUMENTO DE IDENTIDAD EXTRANJERO'
                ));
            }
            return "CC(X) NIT( )";
        } else {
            return "CC( ) NIT(X)";
        }
    }

    public function getBlindaje($blindaje) {
        if ($blindaje === "1") {
            return "SI(X) NO( )";
        } else {
            return "SI( ) NO(X)";
        }
    }

    private function getIdPre_prerevision($numero_placa, $reins, $fecha) {
        $data['fecha'] = $fecha;
        $data['numero_placa_ref'] = $numero_placa;
        $data['reinspeccion'] = $reins;
        $result = $this->Mpre_prerevision->getXofi($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0]->idpre_prerevision;
        } else {
            return '';
        }
    }

    private function getIdPre_atributo($id) {
        $data['id'] = $id;
        $result = $this->Mpre_atributo->getXid($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0]->idpre_atributo;
        } else {
            return '';
        }
    }

    private function getFechaSoat($idpre_prerevision) {
        $data['idpre_prerevision'] = $idpre_prerevision;
        $data['idpre_atributo'] = $this->getIdPre_atributo("fecha_vencimiento_soat");
        $result = $this->Mpre_dato->getXatripre($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            $r[0]->valor = str_replace("-", "", $r[0]->valor);
            return substr($r[0]->valor, 0, 4) . "-" . substr($r[0]->valor, 4, 2) . "-" . substr($r[0]->valor, 6, 2);
        } else {
            return '';
        }
    }

    private function getFechaCertificado($idpre_prerevision) {
        $data['idpre_prerevision'] = $idpre_prerevision;
        $data['idpre_atributo'] = $this->getIdPre_atributo("fecha_final_certgas");
        $result = $this->Mpre_dato->getXatripre($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            $r[0]->valor = str_replace("-", "", $r[0]->valor);
            return substr($r[0]->valor, 0, 4) . "-" . substr($r[0]->valor, 4, 2) . "-" . substr($r[0]->valor, 6, 2);
        } else {
            return '';
        }
    }

    private function getUsuarioRegistro($idpre_prerevision) {
        $data['idpre_prerevision'] = $idpre_prerevision;
        $data['idpre_atributo'] = $this->getIdPre_atributo("usuario_registro");
        $result = $this->Mpre_dato->getXatripre($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            return $r[0]->valor;
        } else {
            return '';
        }
    }

    private function getCertificado($idpre_prerevision) {
        $data['idpre_prerevision'] = $idpre_prerevision;
        $data['idpre_atributo'] = $this->getIdPre_atributo("chk-3");
        $result = $this->Mpre_dato->getXatripre($data);
        if ($result->num_rows() > 0) {
            $r = $result->result();
            switch ($r[0]->valor) {
                case 'NA':
                    $dat = "SI ( ) NO ( ) N/A (X)";
                    break;
                case 'NO':
                    $dat = "SI ( ) NO (X) N/A ( )";
                    break;
                case 'SI':
                    $dat = "SI (X) NO ( ) N/A ( )";
                    break;
                default :
                    $dat = "SI ( ) NO ( ) N/A (X)";
                    break;
            }
            return $dat;
//            return substr($r[0]->valor, 0, 4) . "-" . substr($r[0]->valor, 4, 2) . "-" . substr($r[0]->valor, 6, 2);
        } else {
            return '';
        }
    }

    var $rechazadoCB = false;

    private function getDiagnostico($vehiculo) {
        $totalB = count($this->defectosMB) + count($this->defectosSB) + count($this->defectosEB);
        if (count($this->defectosMA) > 0 || count($this->defectosSA) > 0) {
            $this->aprobado = false;
            $this->rechazadoCB = true;
        }
        if (count($this->defectosEA) > 0) {
            $this->aprobadoE = false;
        }
        if ($vehiculo->idservicio == '2' && $vehiculo->tipo_vehiculo !== '3') {
            if ($totalB >= 5) {
                $this->aprobado = false;
                $this->rechazadoCB = true;
            }
        } else {
            if (($this->nombreClase->nombre == "MOTOCARRO" ||
                    $this->nombreClase->nombre == "CUATRIMOTO" ||
                    $this->nombreClase->nombre == "MOTOTRICICLO" ||
                    $this->nombreClase->nombre == "CUADRICICLO") && $totalB >= 7) {
                $this->aprobado = false;
                $this->rechazadoCB = true;
            }
            if (($this->nombreClase->nombre == "MOTOCICLETA" ||
                    $this->nombreClase->nombre == "CICLOMOTOR" ||
                    $this->nombreClase->nombre == "TRICIMOTO") && $totalB >= 5) {
                $this->aprobado = false;
                $this->rechazadoCB = true;
            }
            if ($vehiculo->ensenanza == '1' &&
                    $this->nombreClase->nombre !== "CUATRIMOTO" &&
                    $this->nombreClase->nombre !== "MOTOTRICICLO" &&
                    $this->nombreClase->nombre !== "CUADRICICLO" &&
                    $this->nombreClase->nombre !== "CICLOMOTOR" &&
                    $this->nombreClase->nombre !== "TRICIMOTO" &&
                    $totalB >= 5) {
                $this->aprobado = false;
//                $this->aprobadoE = false;
                $this->rechazadoCB = true;
            }
            if ($vehiculo->ensenanza == '1' &&
                    ($this->nombreClase->nombre == "CUATRIMOTO" ||
                    $this->nombreClase->nombre == "MOTOTRICICLO" ||
                    $this->nombreClase->nombre == "CUADRICICLO" ||
                    $this->nombreClase->nombre == "CICLOMOTOR" ||
                    $this->nombreClase->nombre == "TRICIMOTO") &&
                    $totalB >= 1) {
                $this->aprobado = false;
//                $this->aprobadoE = false;
                $this->rechazadoCB = true;
            }
            if ($vehiculo->ensenanza == '0' &&
                    $this->nombreClase->nombre !== "MOTOCARRO" &&
                    $this->nombreClase->nombre !== "CUATRIMOTO" &&
                    $this->nombreClase->nombre !== "MOTOTRICICLO" &&
                    $this->nombreClase->nombre !== "CUADRICICLO" &&
                    $this->nombreClase->nombre !== "MOTOCICLETA" &&
                    $this->nombreClase->nombre !== "CICLOMOTOR" &&
                    $this->nombreClase->nombre !== "TRICIMOTO" &&
                    $totalB >= 10
            ) {
                $this->aprobado = false;
                $this->rechazadoCB = true;
            }
        }
        if ($this->aprobado) {
            $apro = "APROBADO: SI__X__ NO_____";
        } else {
            $apro = "APROBADO: SI_____ NO__X__";
        }
        if ($this->enpista) {
            $apro = "APROBADO: SI_____ NO_____";
        }

        return $apro;
    }

    private function getDiagnosticoE($vehiculo) {
        if ($vehiculo->ensenanza == '1') {
            if (count($this->defectosEA) > 0) {
                $this->aprobadoE = false;
                $aproE = "APROBADO: SI_____ NO__X__";
            } else if ($this->aprobadoE) {
                $aproE = "APROBADO: SI__X__ NO_____";
            } else {
                $aproE = "APROBADO: SI_____ NO__X__";
            }
        } else {
            $aproE = "APROBADO: SI____ NO____";
        }
        if ($this->enpista) {
            $aproE = "APROBADO: SI_____ NO_____";
        }
        return $aproE;
    }

//--------------------------------------------------------------------------
    private function getFirmaJefe($documento) {
        $encrptopenssl = New Opensslencryptdecrypt();
        $file = 'C:/tcm/usuarios/' . $documento . '/sig.dat';
        if (file_exists($file)) {
            $firma = $encrptopenssl->decrypt(file_get_contents($file, true));
            $firma = explode(",", $firma);
            $firma = $firma[1];
        } else {
            $firma = '';
        }
        return $firma;
    }

    var $segundo_envio;

//______________________________________________________________________________TRAMA CI2
    private function buildCi2($data) {
//______________________________________________________________________DATOS DE VALIDACION
        $this->setDatCi2("usuario", $this->usuarioSicov);
        $this->setDatCi2("clave", $this->claveSicov);
        $this->setDatCi2("p_pin", $data['hojatrabajo']->pin0);
        $this->setDatCi2("p_3_plac", $data['vehiculo']->numero_placa);
        $this->setDatCi2("p_e_con_run", $data['numero_consecutivo']);
        $this->setDatCi2("p_tw01", $data['numero_sustrato']);
        if ($data['apro'] == 'APROBADO: SI__X__ NO_____') {
            $aprobado = "SI";
        } else {
            $aprobado = "NO";
        }
        $this->setDatCi2("p_e_apr", $aprobado);
        $this->setDatCi2("p_fur_num", $data['fur_aso']); //.......................PENDIENTE
//______________________________________________________________________DATOS DEL CDA
        $this->setDatCi2("p_fur_aso", $data['fur_aso']);
        $this->setDatCi2("p_cda", $data['cda']->nombre_cda);
        $this->setDatCi2("p_nit", $data['cda']->numero_cda);
        $this->setDatCi2("p_dir", $data['sede']->direccion);
        $this->setDatCi2("p_div", $data['sede']->cod_ciudad . '000');
        $this->setDatCi2("p_ciu", $data['ciudadCDA']->nombre);
        $this->setDatCi2("p_tel", $data['sede']->telefono_uno);
        $this->setDatCi2("p_ema", $data['sede']->email);
//______________________________________________________________________FECHA PRUEBA
        $fechaFur = date_format(date_create($data['fechafur']), 'd/m/Y H:i');
//        $fechaFur = date_format(date_create($data['fechafur']), 'd/m/Y H:i');
        $this->setDatCi2("p_1_fec_pru", str_replace("/", "", $fechaFur));
//______________________________________________________________________DATOS DEL PROPIETARIO
        $this->setDatCi2("p_2_nom_raz", $data['propietario']->nombre1 . " " .
                $data['propietario']->nombre2 . " " .
                $data['propietario']->apellido1 . " " .
                $data['propietario']->apellido2);
        if ($data['propietario']->tipo_identificacion == 6) {
            $data['propietario']->tipo_identificacion = 5;
        }
        $this->setDatCi2("p_2_doc_tip", $data['propietario']->tipo_identificacion);
        $this->setDatCi2("p_2_doc", $data['propietario']->numero_identificacion);
        $this->setDatCi2("p_2_dir", $data['propietario']->direccion);
        $this->setDatCi2("p_2_tel", $data['propietario']->telefono1);
        $this->setDatCi2("p_2_ciu", $data['ciudadPropietario']->nombre);
        $this->setDatCi2("p_2_dep", $data['departamentoPropietario']->nombre);
        $this->setDatCi2("p_2_ema", $data['propietario']->correo);

        if ($data['vehiculo']->kilometraje === 'NO FUNCIONAL') {
            $data['vehiculo']->kilometraje = "0";
        }

        if ($data['vehiculo']->potencia_motor === 'No aplica') {
            $data['vehiculo']->potencia_motor = "0";
        }
//______________________________________________________________________DATOS DEL VEHICULO
        $this->setDatCi2("p_3_mar", strtoupper($data['marca']->nombre));
        $this->setDatCi2("p_3_lin", strtoupper($data['linea']->nombre));
        $this->setDatCi2("p_3_cla", strtoupper($data['clase']->nombre));
        $this->setDatCi2("p_3_mod", $data['vehiculo']->ano_modelo);
        $this->setDatCi2("p_3_cil", $data['vehiculo']->cilindraje);
        $this->setDatCi2("p_3_ser", strtoupper($data['servicio']->nombre));
        $this->setDatCi2("p_3_vin", $data['vehiculo']->numero_vin);
        $this->setDatCi2("p_3_mot", $data['vehiculo']->numero_motor);
        $this->setDatCi2("p_3_lic", $data['vehiculo']->numero_tarjeta_propiedad);
        $this->setDatCi2("p_3_com", strtoupper($data['combustible']->nombre));
        $this->setDatCi2("p_3_col", strtoupper($data['color']->nombre));
        $this->setDatCi2("p_3_nac", strtoupper($data['pais']->nombre));
        $fechaMat = date_format(date_create($data['vehiculo']->fecha_matricula), 'd/m/Y');
        $this->setDatCi2("p_3_fec_lic", str_replace("/", "", $fechaMat));
        $this->setDatCi2("p_3_tip_mot", $data['vehiculo']->tiempos);
        $this->setDatCi2("p_3_kil", $data['vehiculo']->kilometraje);
        $this->setDatCi2("p_3_sil", $data['pasajeros']);
        if ($data['vehiculo']->blindaje == "1") {
            $blindaje = "SI";
        } else {
            $blindaje = "NO";
        }
//       $data['vehiculo']->tipo_vehiculo
//       $data['gases']->idprueba
//       strtoupper($data['combustible']->nombre)
        $this->setDatCi2("p_3_vid_pol", '');
        $this->setDatCi2("p_3_bli", $blindaje);
        $this->setDatCi2("p_3_pot", $data['vehiculo']->potencia_motor);
        $this->setDatCi2("p_3_tip_car", $data['carroceria']->nombre);
        $fechaSoat = date_format(date_create($data['vehiculo']->fecha_vencimiento_soat), 'd/m/Y');
        $this->setDatCi2("p_3_fec_ven_soa", str_replace("/", "", $fechaSoat));
        switch ($data['vehiculo']->certificadoGas) {
            case 'SI ( ) NO ( ) N/A (X)':
                $dat = "NA";
                break;
            case 'SI ( ) NO (X) N/A ( )':
                $dat = "NO";
                break;
            case 'SI (X) NO ( ) N/A ( )':
                $dat = "SI";
                break;
            default :
                $dat = "NA";
                break;
        }

        $this->setDatCi2("p_3_con_gnv", $dat);

        if ($dat == 'NA' || $dat == 'NO') {
            $this->setDatCi2("p_3_fec_ven_gnv", "");
        } else {
            $fechaGNV = date_format(date_create($data['vehiculo']->fecha_final_certgas), 'd/m/Y');
            $this->setDatCi2("p_3_fec_ven_gnv", str_replace("/", "", $fechaGNV));
        }
//______________________________________________________________________SONOMETRO
        $this->setDatCi2("p_4_rui_val", $data['sonometro']->valor_ruido_motor1);
        $this->setDatCi2("p_4_rui_max", $data['sonometro']->maximo_ruido_motor);
//______________________________________________________________________LUCES BAJAS
        $this->setDatCi2("p_5_der_int_b1", $data['luces']->valor_baja_derecha_1);
        $this->setDatCi2("p_5_der_int_b2", $data['luces']->valor_baja_derecha_2);
        $this->setDatCi2("p_5_der_int_b3", $data['luces']->valor_baja_derecha_3);
        $this->setDatCi2("p_5_der_min", $data['luces']->intensidad_minima);
        $this->setDatCi2("p_5_der_inc_b1", $data['luces']->inclinacion_baja_derecha_1);
        $this->setDatCi2("p_5_der_inc_b2", $data['luces']->inclinacion_baja_derecha_2);
        $this->setDatCi2("p_5_der_inc_b3", $data['luces']->inclinacion_baja_derecha_3);
        $this->setDatCi2("p_5_der_ran", $data['luces']->inclinacion_rango);
        $this->setDatCi2("p_5_sim_der_b", $data['luces']->simultaneaBaja);
        $this->setDatCi2("p_5_izq_int_b1", $data['luces']->valor_baja_izquierda_1);
        $this->setDatCi2("p_5_izq_int_b2", $data['luces']->valor_baja_izquierda_2);
        $this->setDatCi2("p_5_izq_int_b3", $data['luces']->valor_baja_izquierda_3);
        $this->setDatCi2("p_5_izq_min", $data['luces']->intensidad_minima);
        $this->setDatCi2("p_5_izq_inc_b1", $data['luces']->inclinacion_baja_izquierda_1);
        $this->setDatCi2("p_5_izq_inc_b2", $data['luces']->inclinacion_baja_izquierda_2);
        $this->setDatCi2("p_5_izq_inc_b3", $data['luces']->inclinacion_baja_izquierda_3);
        $this->setDatCi2("p_5_izq_ran", $data['luces']->inclinacion_rango);
        $this->setDatCi2("p_5_sim_izq_b", $data['luces']->simultaneaBaja);
//______________________________________________________________________LUCES ALTAS
        $this->setDatCi2("p_5_der_int_a1", $data['luces']->valor_alta_derecha_1);
        $this->setDatCi2("p_5_der_int_a2", $data['luces']->valor_alta_derecha_2);
        $this->setDatCi2("p_5_der_int_a3", $data['luces']->valor_alta_derecha_3);
//        $this->setDatCi2("p_5_der_min_a", ''); //..................................PENDIENTE
        $this->setDatCi2("p_5_der_min_a", $data['luces']->intensidad_minima); //..................................PENDIENTE
        $this->setDatCi2("p_5_sim_der_a", $data['luces']->simultaneaAlta);
        $this->setDatCi2("p_5_izq_int_a1", $data['luces']->valor_alta_izquierda_1);
        $this->setDatCi2("p_5_izq_int_a2", $data['luces']->valor_alta_izquierda_2);
        $this->setDatCi2("p_5_izq_int_a3", $data['luces']->valor_alta_izquierda_3);
//        $this->setDatCi2("p_5_izq_min_a", ''); //..................................PENDIENTE
        $this->setDatCi2("p_5_izq_min_a", $data['luces']->intensidad_minima); //..................................PENDIENTE
        $this->setDatCi2("p_5_sim_izq_a", $data['luces']->simultaneaAlta);
//______________________________________________________________________LUCES ANTINIEBLAS
        $this->setDatCi2("p_5_der_int_e1  ", $data['luces']->valor_antiniebla_derecha_1);
        $this->setDatCi2("p_5_der_int_e2", $data['luces']->valor_antiniebla_derecha_2);
        $this->setDatCi2("p_5_der_int_e3", $data['luces']->valor_antiniebla_derecha_3);
//        $this->setDatCi2("p_5_der_min_e", ''); //..................................PENDIENTE
        $this->setDatCi2("p_5_der_min_e", $data['luces']->intensidad_minima); //..................................PENDIENTE
        $this->setDatCi2("p_5_sim_der_e", $data['luces']->simultaneaAntiniebla);
        $this->setDatCi2("p_5_izq_int_e1", $data['luces']->valor_antiniebla_izquierda_1);
        $this->setDatCi2("p_5_izq_int_e2", $data['luces']->valor_antiniebla_izquierda_2);
        $this->setDatCi2("p_5_izq_int_e3", $data['luces']->valor_antiniebla_izquierda_3);
//        $this->setDatCi2("p_5_izq_min_e", ''); //..................................PENDIENTE
        $this->setDatCi2("p_5_izq_min_e", $data['luces']->intensidad_minima); //..................................PENDIENTE
        $this->setDatCi2("p_5_sim_izq_e", $data['luces']->simultaneaAntiniebla);
//______________________________________________________________________SUMA LUCES
        $this->setDatCi2("p_6_int", $data['luces']->intensidad_total);
        $this->setDatCi2("p_6_max", $data['luces']->intensidad_maxima);
//______________________________________________________________________SUSPENSION
        $this->setDatCi2("p_7_del_der_val", $data['suspension']->delantera_derecha);
        $this->setDatCi2("p_7_del_izq_val", $data['suspension']->delantera_izquierda);
        $this->setDatCi2("p_7_tra_der_val", $data['suspension']->trasera_derecha);
        $this->setDatCi2("p_7_tra_izq_val", $data['suspension']->trasera_izquierda);
        $this->setDatCi2("p_7_min", $data['suspension']->minima);
//______________________________________________________________________FRENO
        $this->setDatCi2("p_8_efi_tot", $data['frenos']->eficacia_total);
        $this->setDatCi2("p_8_efi_tot_min", $data['frenos']->n_eficacia_total);
        $this->setDatCi2("p_8_ej1_izq_fue", $data['frenos']->freno_1_izquierdo);
        $this->setDatCi2("p_8_ej1_izq_pes", $data['frenos']->peso_1_izquierdo);
        $this->setDatCi2("p_8_ej1_der_fue", $data['frenos']->freno_1_derecho);
        $this->setDatCi2("p_8_ej1_der_pes", $data['frenos']->peso_1_derecho);
        $this->setDatCi2("p_8_ej1_des", $data['frenos']->desequilibrio_1);
        $this->setDatCi2("p_8_ej1_ran", $data['frenos']->n_desequilibrio_B);
        $this->setDatCi2("p_8_ej1_max", $data['frenos']->n_desequilibrio_A);
        $this->setDatCi2("p_8_ej2_izq_fue", $data['frenos']->freno_2_izquierdo);
        $this->setDatCi2("p_8_ej2_izq_pes", $data['frenos']->peso_2_izquierdo);
        $this->setDatCi2("p_8_ej2_der_fue", $data['frenos']->freno_2_derecho);
        $this->setDatCi2("p_8_ej2_der_pes", $data['frenos']->peso_2_derecho);
        $this->setDatCi2("p_8_ej2_des", $data['frenos']->desequilibrio_2);
        $this->setDatCi2("p_8_ej2_ran", $data['frenos']->n_desequilibrio_B);
        $this->setDatCi2("p_8_ej2_max", $data['frenos']->n_desequilibrio_A);
        $this->setDatCi2("p_8_ej3_izq_fue", $data['frenos']->freno_3_izquierdo);
        $this->setDatCi2("p_8_ej3_izq_pes", $data['frenos']->peso_3_izquierdo);
        $this->setDatCi2("p_8_ej3_der_fue", $data['frenos']->freno_3_derecho);
        $this->setDatCi2("p_8_ej3_der_pes", $data['frenos']->peso_3_derecho);
        $this->setDatCi2("p_8_ej3_des", $data['frenos']->desequilibrio_3);
        $this->setDatCi2("p_8_ej3_ran", $data['frenos']->n_desequilibrio_B);
        $this->setDatCi2("p_8_ej3_max", $data['frenos']->n_desequilibrio_A);
        $this->setDatCi2("p_8_ej4_izq_fue", $data['frenos']->freno_4_izquierdo);
        $this->setDatCi2("p_8_ej4_izq_pes", $data['frenos']->peso_4_izquierdo);
        $this->setDatCi2("p_8_ej4_der_fue", $data['frenos']->freno_4_derecho);
        $this->setDatCi2("p_8_ej4_der_pes", $data['frenos']->peso_4_derecho);
        $this->setDatCi2("p_8_ej4_des", $data['frenos']->desequilibrio_4);
        $this->setDatCi2("p_8_ej4_ran", $data['frenos']->n_desequilibrio_B);
        $this->setDatCi2("p_8_ej4_max", $data['frenos']->n_desequilibrio_A);
        $this->setDatCi2("p_8_ej5_izq_fue", $data['frenos']->freno_5_izquierdo);
        $this->setDatCi2("p_8_ej5_izq_pes", $data['frenos']->peso_5_izquierdo);
        $this->setDatCi2("p_8_ej5_der_fue", $data['frenos']->freno_5_derecho);
        $this->setDatCi2("p_8_ej5_der_pes", $data['frenos']->peso_5_derecho);
        $this->setDatCi2("p_8_ej5_des", $data['frenos']->desequilibrio_5);
        $this->setDatCi2("p_8_ej5_ran", $data['frenos']->n_desequilibrio_B);
        $this->setDatCi2("p_8_ej5_max", $data['frenos']->n_desequilibrio_A);
//______________________________________________________________________FRENO AUXILIAR
        $this->setDatCi2("p_8_efi_aux", $data['frenos']->eficacia_auxiliar);
        $this->setDatCi2("p_8_efi_aux_min", $data['frenos']->n_eficacia_auxiliar);
        $this->setDatCi2("p_8_sum_izq_aux_fue", $data['frenos']->sum_freno_aux_izquierdo);
        $this->setDatCi2("p_8_sum_izq_aux_pes", $data['frenos']->sum_peso_izquierdo);
        $this->setDatCi2("p_8_sum_der_aux_fue", $data['frenos']->sum_freno_aux_derecho);
        $this->setDatCi2("p_8_sum_der_aux_pes", $data['frenos']->sum_peso_derecho);
//______________________________________________________________________DESVIACION LATERAL
        $this->setDatCi2("p_9_ej1", $data['alineacion']->alineacion_1);
        $this->setDatCi2("p_9_ej2", $data['alineacion']->alineacion_2);
        $this->setDatCi2("p_9_ej3", $data['alineacion']->alineacion_3);
        $this->setDatCi2("p_9_ej4", $data['alineacion']->alineacion_4);
        $this->setDatCi2("p_9_ej5", $data['alineacion']->alineacion_5);
        $this->setDatCi2("p_9_max", $data['alineacion']->minmax);
//______________________________________________________________________DISPOSITIVOS DE COBRO
        $this->setDatCi2("p_10_ref_com_lla", $data['taximetro']->r_llanta);
        $this->setDatCi2("p_10_err_dis", $data['taximetro']->distancia);
        $this->setDatCi2("p_10_err_tie", $data['taximetro']->tiempo);
        $this->setDatCi2("p_10_max", $data['taximetro']->minmax);
//______________________________________________________________________GASES
        $this->setDatCi2("p_11_co_ral_val", $data['gases']->co_ralenti);
        $this->setDatCi2("p_11_co_ral_nor", $data['gases']->CoFlag_);
        $this->setDatCi2("p_11_co2_ral_val", $data['gases']->co2_ralenti);
        $this->setDatCi2("p_11_co2_ral_nor", $data['gases']->Co2Flag_);
        $this->setDatCi2("p_11_o2_ral_val", $data['gases']->o2_ralenti);
        $this->setDatCi2("p_11_o2_ral_nor", $data['gases']->O2Flag_);
        $this->setDatCi2("p_11_hc_ral_val", $data['gases']->hc_ralenti);
        $this->setDatCi2("p_11_hc_ral_nor", $data['gases']->HcFlag_);
        $this->setDatCi2("p_11_co_cru_val", $data['gases']->co_crucero);
        $this->setDatCi2("p_11_co_cru_nor", $data['gases']->CoFlag_);
        $this->setDatCi2("p_11_co2_cru_val", $data['gases']->co2_crucero);
        $this->setDatCi2("p_11_co2_cru_nor", $data['gases']->Co2Flag_);
        $this->setDatCi2("p_11_o2_cru_val", $data['gases']->o2_crucero);
        $this->setDatCi2("p_11_o2_cru_nor", $data['gases']->O2Flag_);
        $this->setDatCi2("p_11_hc_cru_val", $data['gases']->hc_crucero);
        $this->setDatCi2("p_11_hc_cru_nor", $data['gases']->HcFlag_);
        if ($data['gases']->temperatura == "") {
            $data['gases']->temperatura = "0";
        }
        $this->setDatCi2("p_11_tem_ral", $data['gases']->temperatura);
        $this->setDatCi2("p_11_rpm_ral", $data['gases']->rpm_ralenti);
        $this->setDatCi2("p_11_tem_cru", $data['gases']->temperatura);
        $this->setDatCi2("p_11_rpm_cru", $data['gases']->rpm_crucero);
        $this->setDatCi2("p_11_no_ral_val", '');
        $this->setDatCi2("p_11_no_ral_nor", '');
        $this->setDatCi2("p_11_no_cru_val", '');
        $this->setDatCi2("p_11_no_cru_nor", '');
        $this->setDatCi2("p_11_cat", $data['vehiculo']->convertidorCat);
        $this->setDatCi2("p_11_hum_amb", $data['gases']->temperatura_ambiente);
        $this->setDatCi2("p_11_hum_rel", $data['gases']->humedad);
//______________________________________________________________________OPACIDAD
        $this->setDatCi2("p_11_b_ci1", $data['opacidad']->op_ciclo1);
        $this->setDatCi2("p_11_b_ci2", $data['opacidad']->op_ciclo2);
        $this->setDatCi2("p_11_b_ci3", $data['opacidad']->op_ciclo3);
        $this->setDatCi2("p_11_b_ci4", $data['opacidad']->op_ciclo4);
        $this->setDatCi2("p_11_b_c1_gob", $data['opacidad']->rpm_ciclo1);
        $this->setDatCi2("p_11_b_c2_gob", $data['opacidad']->rpm_ciclo2);
        $this->setDatCi2("p_11_b_c3_gob", $data['opacidad']->rpm_ciclo3);
        $this->setDatCi2("p_11_b_c4_gob", $data['opacidad']->rpm_ciclo4);
        $this->setDatCi2("p_11_b_res_val", $data['opacidad']->opacidad_total);
        $this->setDatCi2("p_11_b_res_nor", $data['opacidad']->max);
        $this->setDatCi2("p_11_b_rpm", $data['opacidad']->rpm_ralenti);
        $this->setDatCi2("p_11_b_tem_ini", $data['opacidad']->temp_final);
        $this->setDatCi2("p_11_b_tem_fin", $data['opacidad']->temp_inicial);
        $this->setDatCi2("p_11_b_tem_amb", $data['opacidad']->temp_ambiente);
        $this->setDatCi2("p_11_b_hum", $data['opacidad']->humedad);
        $this->setDatCi2("p_11_b_lot", $data['vehiculo']->diametro_escape);
        $this->setDatCi2("p_v01", '');
        $this->setDatCi2("p_v02", '');
        $this->setDatCi2("p_v03", '');
//______________________________________________________________________DEFECTOS MECANIZADOS
        $c_cod = "";
        $c_des = "";
        $c_gru = "";
        $c_tip_def_a = "";
        $c_tip_def_b = "";
        $c_tip_def_a_tot = "0";
        $c_tip_def_b_tot = "0";
        if (count($data['defectosMecanizadosA']) > 0) {
            $c_tip_def_a_tot = count($data['defectosMecanizadosA']);
            foreach ($data['defectosMecanizadosA'] as $def) {
                $c_cod = $c_cod . $def->codigo . ";";
                $c_des = $c_des . $def->descripcion . ";";
                $c_gru = $c_gru . $def->grupo . ";";
                $c_tip_def_a = $c_tip_def_a . "X;";
                $c_tip_def_b = $c_tip_def_b . ";";
            }
        }
        if (count($data['defectosMecanizadosB']) > 0) {
            $c_tip_def_b_tot = count($data['defectosMecanizadosB']);
            foreach ($data['defectosMecanizadosB'] as $def) {
                $c_cod = $c_cod . $def->codigo . ";";
                $c_des = $c_des . $def->descripcion . ";";
                $c_gru = $c_gru . $def->grupo . ";";
                $c_tip_def_a = $c_tip_def_a . ";";
                $c_tip_def_b = $c_tip_def_b . "X;";
            }
        }
        $this->setDatCi2("p_c_cod", $c_cod);
        $this->setDatCi2("p_c_des", $c_des);
        $this->setDatCi2("p_c_gru", $c_gru);
        $this->setDatCi2("p_c_tip_def_a", $c_tip_def_a);
        $this->setDatCi2("p_c_tip_def_b", $c_tip_def_b);
        $this->setDatCi2("p_c_tip_def_a_tot", $c_tip_def_a_tot);
        $this->setDatCi2("p_c_tip_def_b_tot", $c_tip_def_b_tot);
//______________________________________________________________________DEFECTOS SENSORIALES
        $d_cod = "";
        $d_des = "";
        $d_gru = "";
        $d_tip_def_a = "";
        $d_tip_def_b = "";
        $d_tip_def_a_tot = "0";
        $d_tip_def_b_tot = "0";
        if (count($data['defectosSensorialesA']) > 0) {
            $d_tip_def_a_tot = count($data['defectosSensorialesA']);
            foreach ($data['defectosSensorialesA'] as $def) {
                $d_cod = $d_cod . $def->codigo . ";";
                $d_des = $d_des . $def->descripcion . ";";
                $d_gru = $d_gru . $def->grupo . ";";
                $d_tip_def_a = $d_tip_def_a . "X;";
                $d_tip_def_b = $d_tip_def_b . ";";
            }
        }
        if (count($data['defectosSensorialesB']) > 0) {
            $d_tip_def_b_tot = count($data['defectosSensorialesB']);
            foreach ($data['defectosSensorialesB'] as $def) {
                $d_cod = $d_cod . $def->codigo . ";";
                $d_des = $d_des . $def->descripcion . ";";
                $d_gru = $d_gru . $def->grupo . ";";
                $d_tip_def_a = $d_tip_def_a . ";";
                $d_tip_def_b = $d_tip_def_b . "X;";
            }
        }
        $this->setDatCi2("p_d_cod", $d_cod);
        $this->setDatCi2("p_d_des", $d_des);
        $this->setDatCi2("p_d_gru", $d_gru);
        $this->setDatCi2("p_d_tip_def_a", $d_tip_def_a);
        $this->setDatCi2("p_d_tip_def_b", $d_tip_def_b);
        $this->setDatCi2("p_d_tip_def_a_tot", $d_tip_def_a_tot);
        $this->setDatCi2("p_d_tip_def_b_tot", $d_tip_def_b_tot);
//______________________________________________________________________DEFECTOS ENSENANZA
        $d1_cod = "";
        $d1_des = "";
        $d1_gru = "";
        $d1_tip_def_a = "";
        $d1_tip_def_b = "";
        $d1_tip_def_a_tot = "0";
        $d1_tip_def_b_tot = "0";
        if (count($data['defectosEnsenanzaA']) > 0) {
            $d1_tip_def_a_tot = count($data['defectosEnsenanzaA']);
            foreach ($data['defectosEnsenanzaA'] as $def) {
                $d1_cod = $d1_cod . $def->codigo . ";";
                $d1_des = $d1_des . $def->descripcion . ";";
                $d1_gru = $d1_gru . $def->grupo . ";";
                $d1_tip_def_a = $d1_tip_def_a . "X;";
                $d1_tip_def_b = $d1_tip_def_b . ";";
            }
        }
        if (count($data['defectosEnsenanzaB']) > 0) {
            $d1_tip_def_b_tot = count($data['defectosEnsenanzaB']);
            foreach ($data['defectosEnsenanzaB'] as $def) {
                $d1_cod = $d1_cod . $def->codigo . ";";
                $d1_des = $d1_des . $def->descripcion . ";";
                $d1_gru = $d1_gru . $def->grupo . ";";
                $d1_tip_def_a = $d1_tip_def_a . ";";
                $d1_tip_def_b = $d1_tip_def_b . "X;";
            }
        }
        $this->setDatCi2("p_d1_cod", $d1_cod);
        $this->setDatCi2("p_d1_des", $d1_des);
        $this->setDatCi2("p_d1_gru", $d1_gru);
        $this->setDatCi2("p_d1_tip_def_a", $d1_tip_def_a);
        $this->setDatCi2("p_d1_tip_def_b", $d1_tip_def_b);
        $this->setDatCi2("p_d1_tip_def_a_tot", $d1_tip_def_a_tot);
        $this->setDatCi2("p_d1_tip_def_b_tot", $d1_tip_def_b_tot);
//______________________________________________________________________PROFUNDIDAD DE LABRADO
        $this->setDatCi2("p_d2_ej1_izq", $data['labrado']->eje1_izquierdo);
        $this->setDatCi2("p_d2_ej2_izq_r1", $data['labrado']->eje2_izquierdo);
        $this->setDatCi2("p_d2_ej2_izq_r2", $data['labrado']->eje2_izquierdo_interior);
        $this->setDatCi2("p_d2_ej3_izq_r1", $data['labrado']->eje3_izquierdo);
        $this->setDatCi2("p_d2_ej3_izq_r2", $data['labrado']->eje3_izquierdo_interior);
        $this->setDatCi2("p_d2_ej4_izq_r1", $data['labrado']->eje4_izquierdo);
        $this->setDatCi2("p_d2_ej4_izq_r2", $data['labrado']->eje4_izquierdo_interior);
        $this->setDatCi2("p_d2_ej5_izq_r1", $data['labrado']->eje5_izquierdo);
        $this->setDatCi2("p_d2_ej5_izq_r2", $data['labrado']->eje5_izquierdo_interior);
        $this->setDatCi2("p_d2_ej1_der", $data['labrado']->eje1_derecho);
        $this->setDatCi2("p_d2_ej2_der_r1", $data['labrado']->eje2_derecho);
        $this->setDatCi2("p_d2_ej2_der_r2", $data['labrado']->eje2_derecho_interior);
        $this->setDatCi2("p_d2_ej3_der_r1", $data['labrado']->eje3_derecho);
        $this->setDatCi2("p_d2_ej3_der_r2", $data['labrado']->eje3_derecho_interior);
        $this->setDatCi2("p_d2_ej4_der_r1", $data['labrado']->eje4_derecho);
        $this->setDatCi2("p_d2_ej4_der_r2", $data['labrado']->eje4_derecho_interior);
        $this->setDatCi2("p_d2_ej5_der_r1", $data['labrado']->eje5_derecho);
        $this->setDatCi2("p_d2_ej5_der_r2", $data['labrado']->eje5_derecho_interior);
        $this->setDatCi2("p_d2_rep_r1", $data['labrado']->repuesto);
        $this->setDatCi2("p_d2_rep_r2", $data['labrado']->repuesto2);
//______________________________________________________________________ENSENANZA RESUL
        if ($data['vehiculo']->ensenanza == '1') {
            if ($data['aproE'] == 'APROBADO: SI__X__ NO_____') {
                $aprobadoE = "SI";
            } else {
                $aprobadoE = "NO";
            }
        } else {
            $aprobadoE = "";
        }
        $this->setDatCi2("p_e1_apr", $aprobadoE);
//______________________________________________________________________OBSERVACIONES
        $obs = '';
        if (count($data['observaciones']) > 0) {
            foreach ($data['observaciones'] as $o) {
                $obs = $obs . "$o->codigo: $o->descripcion;";
            }
        }
        $this->setDatCi2("p_f_com_obs", $obs);

        $luxometro = explode("$", $data['maquinas']->nombreLuxometro);
        $luxometroOperario = '';
        if (count($luxometro) > 1) {
            $luxometroOperario = $luxometro[0] . "_" . str_replace('^', '', $luxometro[8]) . ";";
            $luxometro = $luxometro[0] . "_" . $luxometro[1] . "_" . $luxometro[2] . "_" . $luxometro[3] . "_" . $luxometro[4] . "_" . $luxometro[5] . ";";
        } else {
            $luxometro = '';
        }
        $opacimetro = explode("$", $data['maquinas']->nombreOpacimetro);
        $opacimetroOperario = '';
//        var_dump($opacimetro);
        if (count($opacimetro) > 1) {
            $opacimetroOperario = $opacimetro[0] . "_" . str_replace('^', '', $opacimetro[8]) . ";";
            $opacimetro = $opacimetro[0] . "_" . $opacimetro[1] . "_" . $opacimetro[2] . "_" . $opacimetro[3] . "_" . $opacimetro[4] . "_" . $opacimetro[5] . ";";
        } else {
            $opacimetro = '';
        }
        $gases = explode("$", $data['maquinas']->nombreGases);
        $gasesOperario = '';
        if (count($gases) > 1) {
            $gasesOperario = $gases[0] . "_" . str_replace('^', '', $gases[8]) . ";";
            $gases = $gases[0] . "_" . $gases[1] . "_" . $gases[2] . "_" . $gases[3] . "_" . $gases[4] . "_" . $gases[5] . ";";
        } else {
            $gases = '';
        }
        $fotos = explode("$", $data['maquinas']->nombreFotos);
        $fotosOperario = '';
        if (count($fotos) > 1) {
            $fotosOperario = $fotos[0] . "_" . str_replace('^', '', $fotos[8]) . ";";
            $fotos = $fotos[0] . "_" . $fotos[1] . "_" . $fotos[2] . "_" . $fotos[3] . "_" . $fotos[4] . "_" . $fotos[5] . ";";
        } else {
            $fotos = '';
        }
        $taximetro = explode("$", $data['maquinas']->nombreTaximetro);
        $taximetroOperario = '';
        if (count($taximetro) > 1) {
            $taximetroOperario = $taximetro[0] . "_" . str_replace('^', '', $taximetro[8]) . ";";
            $taximetro = $taximetro[0] . "_" . $taximetro[1] . "_" . $taximetro[2] . "_" . $taximetro[3] . "_" . $taximetro[4] . "_" . $taximetro[5] . ";";
        } else {
            $taximetro = '';
        }
        $frenos = explode("$", $data['maquinas']->nombreFrenos);
        $frenosOperario = '';
        if (count($frenos) > 1) {
            $frenosOperario = $frenos[0] . "_" . str_replace('^', '', $frenos[8]) . ";";
            $frenos = $frenos[0] . "_" . $frenos[1] . "_" . $frenos[2] . "_" . $frenos[3] . "_" . $frenos[4] . "_" . $frenos[5] . ";";
        } else {
            $frenos = '';
        }
        $bascula = explode("$", $data['maquinas']->nombreBascula);
        if (count($bascula) > 1) {
            $bascula = $bascula[0] . "_" . $bascula[1] . "_" . $bascula[2] . "_" . $bascula[3] . "_" . $bascula[4] . "_" . $bascula[5] . ";";
        } else {
            $bascula = '';
        }
        $visual = explode("$", $data['maquinas']->nombreVisual);
        $visualOperario = '';
        if (count($visual) > 1) {
            $visualOperario = $visual[0] . "_" . str_replace('^', '', $visual[8]) . ";";
            $visual = $visual[0] . "_" . $visual[1] . "_" . $visual[2] . "_" . $visual[3] . "_" . $visual[4] . "_" . $visual[5] . ";";
        } else {
            $visual = '';
        }
        $suspension = explode("$", $data['maquinas']->nombreSuspension);
        $suspensionOperario = '';
        if (count($suspension) > 1) {
            $suspensionOperario = $suspension[0] . "_" . str_replace('^', '', $suspension[8]) . ";";
            $suspension = $suspension[0] . "_" . $suspension[1] . "_" . $suspension[2] . "_" . $suspension[3] . "_" . $suspension[4] . "_" . $suspension[5] . ";";
        } else {
            $suspension = '';
        }
        $alineador = explode("$", $data['maquinas']->nombreAlineador);
        $alineadorOperario = '';
        if (count($alineador) > 1) {
            $alineadorOperario = $alineador[0] . "_" . str_replace('^', '', $alineador[8]) . ";";
            $alineador = $alineador[0] . "_" . $alineador[1] . "_" . $alineador[2] . "_" . $alineador[3] . "_" . $alineador[4] . "_" . $alineador[5] . ";";
        } else {
            $alineador = '';
        }
        $th = explode("$", $data['maquinas']->nombreTermohigrometro);
        if (count($th) > 1) {
            $th = $th[0] . "_" . $th[1] . "_" . $th[2] . "_" . $th[3] . "_" . $th[4] . "_" . $th[5] . ";";
        } else {
            $th = '';
        }
        $profundimetro = explode("$", $data['maquinas']->nombreProfundimetro);
        if (count($profundimetro) > 1) {
            $profundimetro = $profundimetro[0] . "_" . $profundimetro[1] . "_" . $profundimetro[2] . "_" . $profundimetro[3] . "_" . $profundimetro[4] . "_" . $profundimetro[5] . ";";
        } else {
            $profundimetro = '';
        }
        $captador = explode("$", $data['maquinas']->nombreCaptador);
        if (count($captador) > 1) {
            $captador = $captador[0] . "_" . $captador[1] . "_" . $captador[2] . "_" . $captador[3] . "_" . $captador[4] . "_" . $captador[5] . ";";
        } else {
            $captador = '';
        }
        $piederey = explode("$", $data['maquinas']->nombrePiederey);
        if (count($piederey) > 1) {
            $piederey = $piederey[0] . "_" . $piederey[1] . "_" . $piederey[2] . "_" . $piederey[3] . "_" . $piederey[4] . "_" . $piederey[5] . ";";
        } else {
            $piederey = '';
        }
        $sensorRPM = explode("$", $data['maquinas']->nombreSensorRPM);
        if (count($sensorRPM) > 1) {
            $sensorRPM = $sensorRPM[0] . "_" . $sensorRPM[1] . "_" . $sensorRPM[2] . "_" . $sensorRPM[3] . "_" . $sensorRPM[4] . "_" . $sensorRPM[5] . ";";
        } else {
            $sensorRPM = '';
        }
        $sondaTMP = explode("$", $data['maquinas']->nombreSensorRPM);
        if (count($sondaTMP) > 1) {
            $sondaTMP = $sondaTMP[0] . "_" . $sondaTMP[1] . "_" . $sondaTMP[2] . "_" . $sondaTMP[3] . "_" . $sondaTMP[4] . "_" . $sondaTMP[5] . ";";
        } else {
            $sondaTMP = '';
        }

        $operarios = $luxometroOperario . $opacimetroOperario . $gasesOperario . $fotosOperario . $taximetroOperario . $frenosOperario . $visualOperario . $suspensionOperario . $alineadorOperario;
//        $this->setDatCi2("h_nom_ope_rea_rev_tec", str_replace("<br>", ";", $data['inspectores']));
        $this->setDatCi2("p_h_nom_ope_rea_rev_tec", $operarios);
//______________________________________________________________________PERISFERICOS

        $maquinas = $luxometro . $opacimetro . $gases . $taximetro . $frenos . $bascula . $suspension . $alineador . $th . $profundimetro . $captador . $piederey;
//        $maquinas = $luxometro . $opacimetro . $gases . $fotos . $taximetro . $frenos . $visualOperario . $suspension . $alineador . $th . $profundimetro . $captador . $piederey;
        $this->setDatCi2("p_h_equ_rev", $maquinas);
//______________________________________________________________________SOFTWARE
        $this->setDatCi2("p_i_sof_rev", $data["software"]);
//______________________________________________________________________JEFE DE PISTA
        $this->setDatCi2("p_g_nom_fir_dir_tec", $data['hojatrabajo']->jefelinea);
//______________________________________________________________________CAUSA RECHAZO
        if ($aprobado == 'NO') {
            $this->setDatCi2("p_causa_rechazo", $c_cod . $d_cod . $d1_cod);
        } else {
            $this->setDatCi2("p_causa_rechazo", '');
        }
//______________________________________________________________________FOTO
        $fotos = str_replace("@", "", $data['fotografia']->imagen1) . ";" . str_replace("@", "", $data['fotografia']->imagen2);
        $this->setDatCi2("p_foto", $fotos);
//______________________________________________________________________________ENVIAR A CI2

        $url = 'http://' . $this->ipSicov . '/ci2_cda_ws/sincrofur.asmx?wsdl';
        $datos_conexion = explode(":", $this->ipSicov);
        if ($this->sicovModoAlternativo == '1') {
            $url = 'http://' . $this->ipSicovAlternativo . '/ci2_cda_ws/sincrofur.asmx?wsdl';
            $datos_conexion = explode(":", $this->ipSicovAlternativo);
        }
        $host = $datos_conexion[0];
        if (count($datos_conexion) > 1) {
            $port = $datos_conexion[1];
        } else {
            $port = 80;
        }
        $waitTimeoutInSeconds = 2;
        error_reporting(0);
        if ($data['ocasion'] === 'true') {
            $reins = '1';
            $ocasion = '2';
        } else {
            $reins = '0';
            $ocasion = '1';
        }
        if ($fp = fsockopen($host, $port, $errCode, $errStr, $waitTimeoutInSeconds)) {
            $client = new SoapClient($url);
            $fur = array(
                'fur' => $this->arrayCi2
            );
            $respuesta = $client->ingresar_fur_v2($fur);
            $rtaCP = $respuesta->ingresar_fur_v2Result;
            $tipo = 'f';
            if ($rtaCP->CodigoRespuesta == '0000') {
                $estado = 'exito';
                $datos['idhojapruebas'] = $data['hojatrabajo']->idhojapruebas;
                $datos['reinspeccion'] = $reins;
                $datos['sicov'] = '1';
                if (!$this->segundo_envio) {
                    if ($aprobado == 'NO') {
                        $datos['estadototal'] = '3';
                    } else {
                        $datos['estadototal'] = '2';
                    }
                } else {
                    if ($aprobado == 'NO') {
                        $datos['estadototal'] = '7';
                    } else {
                        $datos['estadototal'] = '4';
                    }
                    $tipo = 'r';
                    if ($this->salaEspera2 == "1") {
                        $sala['idhojaprueba'] = $data['hojatrabajo']->idhojapruebas;
                        $sala['idtipo_prueba'] = "20";
                        $sala['estado'] = "1";
                        $sala['actualizado'] = "0";
                        $this->Mcontrol_salae->insertar($sala);
                    }
//                    if ($this->CARinformeActivo == "1") {
//                        $rta = $this->Mambientales->getEnvioCar($this->idprueba_gases);
//                        if (count($rta) == 0) {
//                            $msgCAR = $this->getInformeCarNew($data['hojatrabajo']->idhojapruebas);
//                            $mensaje = $mensaje . " - Respuesta CAR: " . $msgCAR;
//                        }
//                    }
                }
                $this->Mhojatrabajo->update_x($datos);
            } else {
                if ($this->segundo_envio) {
                    $tipo = 'r';
                }
                $estado = 'error';
            }
            $mensaje = $this->mensajesCI2($rtaCP->CodigoRespuesta, $rtaCP->CodigoRespuesta . '|' . $ocasion . '|' . $estado . '|' . $rtaCP->MensajeRespuesta);
            $this->insertarEvento($data['vehiculo']->numero_placa, json_encode($fur), $tipo, '1', $mensaje);
        } else {
            $mensaje = $this->mensajesCI2('1000', '1000' . '|' . $ocasion . '|1|No hay conexión con sicov');
            $this->insertarEvento($data['vehiculo']->numero_placa, '', 'f', '1', $mensaje);
        }

        echo $mensaje;
    }

    private function insertarEvento($idelemento, $cadena, $tipo, $enviado, $respuesta) {
        $data['idelemento'] = $idelemento;
        $data['cadena'] = $cadena;
        $data['tipo'] = $tipo;
        $data['enviado'] = $enviado;
        $data['respuesta'] = $respuesta;
        $this->MEventosindra->insert($data);
    }

    private function setDatCi2($campo, $valor) {
        $dato = $this->formato_texto($valor);
        if ($this->segundo_envio) {
            switch ($campo) {
                case 'usuario':
                    break;
                case 'clave':
                    break;
                case 'p_pin':
                    break;
                case 'p_3_plac':
                    break;
                case 'p_e_con_run':
                    break;
                case 'p_tw01':
                    break;
                default:
                    $dato = '';
                    break;
            }
        }
        $this->arrayCi2[$campo] = $dato;
    }

    private function mensajesCI2($codigo, $detalle) {
        $msg = 'Código de respuesta no válido: Revise la conexión con CI2';
        switch ($codigo) {
            case '0000':
                $msg = 'Transacción exitosa|' . $detalle;
                break;
            case '1000':
                $msg = 'Transacción Fallida|' . $detalle;
                break;
            case '1001':
                $msg = 'Dato no puede ser nulo|' . $detalle;
                break;
            case '1002':
                $msg = 'Valor no válido|' . $detalle;
                break;
            case '1003':
                $msg = 'Formato no válido|' . $detalle;
                break;
            case '1004':
                $msg = 'Campo obligatorio|' . $detalle;
                break;
            case '1005':
                $msg = 'Longitud no permitida|' . $detalle;
                break;
            case '1006':
                $msg = 'Dato no existe|' . $detalle;
                break;
            case '2001':
                $msg = 'Usuario y clave no válidos|' . $detalle;
                break;
            case '2002':
                $msg = 'Usuario no permitido|' . $detalle;
                break;
            case '2003':
                $msg = 'CDA no permitido|' . $detalle;
                break;
            case '2004':
                $msg = 'Vehículo no permitido|' . $detalle;
                break;
            case '2005':
                $msg = 'PIN ANULADO|' . $detalle;
                break;
            case '2006':
                $msg = 'PIN DISPONIBLE|' . $detalle;
                break;
            case '2007':
                $msg = 'PIN UTILIZADO|' . $detalle;
                break;
            case '2008':
                $msg = 'PIN REPORTADO CON FUR|' . $detalle;
                break;
            case '2009':
                $msg = 'PIN NO VALIDO|' . $detalle;
                break;
            case '2010':
                $msg = 'Pendiente por procesar|' . $detalle;
                break;
            case '2011':
                $msg = 'La solicitud está pendiente por reportar resultado|' . $detalle;
                break;
            case '2012':
                $msg = 'Código RUNT ya está registrado|' . $detalle;
                break;
        }
        return $msg;
    }

//______________________________________________________________________________TRAMA INDRA
    private function buildIndra($data) {
//------------------------------------------------------------------------------IdProveedor
        $IdProveedor = "862";
//------------------------------------------------------------------------------Propietario
        switch ($data['propietario']->tipo_identificacion) {
            case '1':
                $tipoDocumento = "C";
                break;
            case '2':
                $tipoDocumento = "N";
                break;
            case '3':
                $tipoDocumento = "E";
                break;
            case '4':
                $tipoDocumento = "T";
                break;
            case '5':
                $tipoDocumento = "U";
                break;
            case '6':
                $tipoDocumento = "P";
                break;
        }
        $propietario = $data['propietario']->nombre1 . " " .
                $data['propietario']->nombre2 . " " .
                $data['propietario']->apellido1 . " " .
                $data['propietario']->apellido2 . ";" .
                $tipoDocumento . ";" .
                $data['propietario']->numero_identificacion . ";" .
                $data['propietario']->direccion . ";" .
                $data['propietario']->telefono1 . ";" .
                $data['ciudadPropietario']->nombre . ";" .
                $data['departamentoPropietario']->nombre . ";" .
                $data['propietario']->correo;
//------------------------------------------------------------------------------Vehiculos
        if ($data['vehiculo']->idservicio == 3) {
            $servicio = 1;
        } else if ($data['vehiculo']->idservicio == 4) {
            $servicio = 3;
        } else if ($data['vehiculo']->idservicio == 1) {
            $servicio = 4;
        } else {
            $servicio = $data['vehiculo']->idservicio;
        }

        if ($data['vehiculo']->idclase == 13) {
            $clase = 41;
        } else if ($data['vehiculo']->idclase == 16) {
            $clase = 43;
        } else if ($data['vehiculo']->idclase == 9) {
            $clase = 42;
        } else if ($data['vehiculo']->idclase == 15) {
            $clase = 24;
        } else {
            $clase = $data['vehiculo']->idclase;
        }

        if ($data['vehiculo']->idtipocombustible == 1) {
            $combustible = 3;
        } else if ($data['vehiculo']->idtipocombustible == 2) {
            $combustible = 1;
        } else if ($data['vehiculo']->idtipocombustible == 3) {
            $combustible = 2;
        } else {
            $combustible = $data['vehiculo']->idtipocombustible;
        }
        if ($data['vehiculo']->tiempos == 2) {
            $tiempos = 1;
        } else if ($data['vehiculo']->tiempos == 4) {
            $tiempos = 2;
        } else {
            $tiempos = -1;
        }

        if ($data['vehiculo']->blindaje == "1") {
            $blindaje = "true";
        } else {
            $blindaje = "false";
        }
        if ($data['apro'] == 'APROBADO: SI__X__ NO_____') {
            $aprobado = "1";
        } else {
            $aprobado = "2";
        }
        switch ($data['vehiculo']->certificadoGas) {
            case 'SI ( ) NO ( ) N/A (X)':
                $conversionGas = "NA";
                break;
            case 'SI ( ) NO (X) N/A ( )':
                $conversionGas = "NO";
                break;
            case 'SI (X) NO ( ) N/A ( )':
                $conversionGas = "SI";
                break;
            default :
                $conversionGas = "NA";
                break;
        }
        $fechacer = '';
        if ($conversionGas == 'NA' || $conversionGas == 'NO') {
            $fechacer = '';
        } else {
            $fechacer = date_format(date_create($data['vehiculo']->fecha_final_certgas), 'Y-m-d');
        }

        if ($data['vehiculo']->kilometraje === 'NO FUNCIONAL') {
            $data['vehiculo']->kilometraje = "0";
        }

        if ($data['vehiculo']->potencia_motor === 'No aplica') {
            $data['vehiculo']->potencia_motor = "0";
        }

        $vehiculo = $data['vehiculo']->usuario_registro . ";" .
                $data['vehiculo']->documento_usuario . ";" .
                $data['vehiculo']->numero_placa . ";" .
                $data['pais']->nombre . ";" .
                $servicio . ";" .
                $clase . ";" .
                $data['marca']->nombre . ";" .
                $data['linea']->nombre . ";" .
                $data['vehiculo']->ano_modelo . ";" .
                $data['vehiculo']->numero_tarjeta_propiedad . ";" .
                $data['vehiculo']->fecha_matricula . " 00:00:00;" .
                $data['color']->nombre . ";" .
                $combustible . ";" .
                $data['vehiculo']->numero_vin . ";" .
                $data['vehiculo']->numero_motor . ";" .
                $tiempos . ";" .
                $data['vehiculo']->cilindraje . ";" .
                $data['vehiculo']->kilometraje . ";" .
                $data['pasajeros'] . ";" .
                $blindaje . ";" .
                $data['ocasion'] . ";" .
                date_format(date_create($data['fechafur']), 'Y-m-d H:i:s') . ";" .
                $aprobado . ";" .
                $data['vehiculo']->potencia_motor . ";" .
                $data['vehiculo']->diseno . ";" .
                date_format(date_create($data['vehiculo']->fecha_vencimiento_soat), 'Y-m-d') . ";" .
                $conversionGas . ";" .
                $fechacer;

//------------------------------------------------------------------------------Fotos
        $fotos = $data['vehiculo']->numero_placa . ";" .
                str_replace("@", "", $data['fotografia']->imagen1) . ";" .
                str_replace("@", "", $data['fotografia']->imagen2);
//------------------------------------------------------------------------------Gases
        $diesel = "";
        $gasesCarro = "";
        $gasesMoto = "";
        if ($combustible == 3) {
            $diesel = $data['opacidad']->operario . ";" .
                    $data['opacidad']->documento . ";" .
                    $data['opacidad']->temp_ambiente . ";" .
                    $data['opacidad']->rpm_ralenti . ";" .
                    $data['opacidad']->rpm_ciclo1 . ";" .
                    $data['opacidad']->rpm_ciclo2 . ";" .
                    $data['opacidad']->rpm_ciclo3 . ";" .
                    $data['opacidad']->rpm_ciclo4 . ";" .
                    $data['opacidad']->op_ciclo1 . ";" .
                    $data['opacidad']->op_ciclo2 . ";" .
                    $data['opacidad']->op_ciclo3 . ";" .
                    $data['opacidad']->op_ciclo4 . ";" .
                    $data['opacidad']->opacidad_total . ";" .
                    $data['opacidad']->temp_inicial . ";" .
                    $data['opacidad']->temp_final . ";" .
                    $data['opacidad']->humedad . ";" .
                    $data['vehiculo']->diametro_escape . "|" . //cambiar
                    $data['opacidad']->fugasTuboEscape . ";" .
                    $data['opacidad']->fugasSilenciador . ";" .
                    $data['opacidad']->tapaCombustible . ";" .
                    $data['opacidad']->tapaAceite . ";" .
                    $data['opacidad']->sistemaMuestreo . ";" .
                    $data['opacidad']->salidasAdicionales . ";" .
                    $data['opacidad']->filtroAire . ";" .
                    $data['opacidad']->sistemaRefrigeracion . ";" .
                    $data['opacidad']->revolucionesFueraRango . "|";
            $diesel = str_replace(".", ",", str_replace("*", "", $diesel));
        } elseif ($combustible == 1 || $combustible == 2 || $combustible == 4) {
            if ($data['vehiculo']->tipo_vehiculo !== "3") {
                $gasesCarro = $data['gases']->operario . "';" .
                        $data['gases']->documento . ";" .
                        $data['gases']->rpm_ralenti . ";" .
                        $data['gases']->hc_ralenti . ";" .
                        $data['gases']->co_ralenti . ";" .
                        $data['gases']->co2_ralenti . ";" .
                        $data['gases']->o2_ralenti . ";" .
                        $data['gases']->rpm_crucero . ";" .
                        $data['gases']->hc_crucero . ";" .
                        $data['gases']->co_crucero . ";" .
                        $data['gases']->co2_crucero . ";" .
                        $data['gases']->o2_crucero . ";" .
                        $data['gases']->dilusion . ";" .
                        substr($data['vehiculo']->convertidorCat, 0, 1) . ";" .
                        $data['gases']->temperatura . ";" .
                        $data['gases']->temperatura_ambiente . ";" .
                        $data['gases']->humedad . "|" .
                        $data['gases']->fugasTuboEscape . ";" .
                        $data['gases']->fugasSilenciador . ";" .
                        $data['gases']->tapaCombustible . ";" .
                        $data['gases']->tapaAceite . ";" .
                        $data['gases']->salidasAdicionales . ";" .
                        $data['gases']->presenciaHumos . ";" .
                        $data['gases']->revolucionesFueraRango . ";" .
                        $data['gases']->fallaSistemaRefrigeracion . "|";
                $gasesCarro = str_replace(".", ",", str_replace("*", "", $gasesCarro));
            } else {
                $gasesMoto = $data['gases']->operario . "';" .
                        $data['gases']->documento . ";" .
                        $data['gases']->temperatura . ";" .
                        $data['gases']->rpm_ralenti . ";" .
                        $data['gases']->hc_ralenti . ";" .
                        $data['gases']->co_ralenti . ";" .
                        $data['gases']->co2_ralenti . ";" .
                        $data['gases']->o2_ralenti . ";" .
                        $data['gases']->temperatura_ambiente . ";" .
                        $data['gases']->humedad . "|" .
                        $data['gases']->revolucionesFueraRango . ";" .
                        $data['gases']->fugasTuboEscape . ";" .
                        $data['gases']->fugasSilenciador . ";" .
                        $data['gases']->tapaCombustible . ";" .
                        $data['gases']->tapaAceite . ";" .
                        $data['gases']->salidasAdicionales . ";" .
                        $data['gases']->presenciaHumos . "|";
                $gasesMoto = str_replace(".", ",", str_replace("*", "", $gasesMoto));
            }
        } else {
            $diesel = "||";
            $gasesCarro = "";
            $gasesMoto = "";
        }
//------------------------------------------------------------------------------Luces
        $luces = $data['luces']->operario . ";" .
                $data['luces']->documento . ";" .
                $data['luces']->valor_baja_derecha_1 . ";" .
                $data['luces']->valor_baja_derecha_2 . ";" .
                $data['luces']->valor_baja_derecha_3 . ";" .
                substr($data['luces']->simultaneaBaja, 0, 1) . ";" .
                $data['luces']->valor_baja_izquierda_1 . ";" .
                $data['luces']->valor_baja_izquierda_2 . ";" .
                $data['luces']->valor_baja_izquierda_3 . ";" .
                substr($data['luces']->simultaneaBaja, 0, 1) . ";" .
                $data['luces']->inclinacion_baja_derecha_1 . ";" .
                $data['luces']->inclinacion_baja_derecha_2 . ";" .
                $data['luces']->inclinacion_baja_derecha_3 . ";" .
                $data['luces']->inclinacion_baja_izquierda_1 . ";" .
                $data['luces']->inclinacion_baja_izquierda_2 . ";" .
                $data['luces']->inclinacion_baja_izquierda_3 . ";" .
                $data['luces']->intensidad_total . ";" .
                $data['luces']->valor_alta_derecha_1 . ";" .
                $data['luces']->valor_alta_derecha_2 . ";" .
                $data['luces']->valor_alta_derecha_3 . ";" .
                substr($data['luces']->simultaneaAlta, 0, 1) . ";" .
                $data['luces']->valor_alta_izquierda_1 . ";" .
                $data['luces']->valor_alta_izquierda_2 . ";" .
                $data['luces']->valor_alta_izquierda_3 . ";" .
                substr($data['luces']->simultaneaAlta, 0, 1) . ";" .
                $data['luces']->valor_antiniebla_derecha_1 . ";" .
                $data['luces']->valor_antiniebla_derecha_2 . ";" .
                $data['luces']->valor_antiniebla_derecha_3 . ";" .
                substr($data['luces']->simultaneaAntiniebla, 0, 1) . ";" .
                $data['luces']->valor_antiniebla_izquierda_1 . ";" .
                $data['luces']->valor_antiniebla_izquierda_2 . ";" .
                $data['luces']->valor_antiniebla_izquierda_3 . ";" .
                substr($data['luces']->simultaneaAntiniebla, 0, 1);
        $luces = str_replace(".", ",", str_replace("*", "", $luces));
//------------------------------------------------------------------------------FAS
        $fas = $data['frenos']->operario . ";" .
                $data['frenos']->documento . ";" .
                $data['vehiculo']->numejes . ";" .
                $data['frenos']->eficacia_total . ";" .
                $data['frenos']->eficacia_auxiliar . ";" .
                $data['frenos']->desequilibrio_1 . ";" .
                $data['frenos']->desequilibrio_2 . ";" .
                $data['frenos']->desequilibrio_3 . ";" .
                $data['frenos']->desequilibrio_4 . ";" .
                $data['frenos']->desequilibrio_5 . ";" .
                '' . ";" .
                $data['frenos']->freno_1_izquierdo . ";" .
                $data['frenos']->freno_2_izquierdo . ";" .
                $data['frenos']->freno_3_izquierdo . ";" .
                $data['frenos']->freno_4_izquierdo . ";" .
                $data['frenos']->freno_5_izquierdo . ";" .
                '' . ";" .
                $data['frenos']->freno_1_derecho . ";" .
                $data['frenos']->freno_2_derecho . ";" .
                $data['frenos']->freno_3_derecho . ";" .
                $data['frenos']->freno_4_derecho . ";" .
                $data['frenos']->freno_5_derecho . ";" .
                '' . ";" .
                $data['frenos']->peso_1_derecho . ";" .
                $data['frenos']->peso_2_derecho . ";" .
                $data['frenos']->peso_3_derecho . ";" .
                $data['frenos']->peso_4_derecho . ";" .
                $data['frenos']->peso_5_derecho . ";" .
                '' . ";" .
                $data['frenos']->peso_1_izquierdo . ";" .
                $data['frenos']->peso_2_izquierdo . ";" .
                $data['frenos']->peso_3_izquierdo . ";" .
                $data['frenos']->peso_4_izquierdo . ";" .
                $data['frenos']->peso_5_izquierdo . ";" .
                '' . ";" .
                $data['alineacion']->alineacion_1 . ";" .
                $data['alineacion']->alineacion_2 . ";" .
                $data['alineacion']->alineacion_3 . ";" .
                $data['alineacion']->alineacion_4 . ";" .
                $data['alineacion']->alineacion_5 . ";" .
                '' . ";" .
                $data['suspension']->delantera_izquierda . ";" .
                $data['suspension']->trasera_izquierda . ";" .
                $data['suspension']->delantera_derecha . ";" .
                $data['suspension']->trasera_derecha . ";" .
                $data['frenos']->sum_freno_aux_derecho . ";" .
                $data['frenos']->sum_peso_derecho . ";" .
                $data['frenos']->sum_freno_aux_izquierdo . ";" .
                $data['frenos']->sum_peso_izquierdo;
        $fas = str_replace(".", ",", str_replace("*", "", $fas));
//------------------------------------------------------------------------------Sensorial
        $defectos = "";
        if (count($data['defectosMecanizadosA']) > 0) {
            foreach ($data['defectosMecanizadosA']as $def) {
                $defectos = $defectos . $def->codigo . "_";
            }
        }
        if (count($data['defectosMecanizadosB']) > 0) {
            foreach ($data['defectosMecanizadosB']as $def) {
                $defectos = $defectos . $def->codigo . "_";
            }
        }
        if (count($data['defectosSensorialesA']) > 0) {
            foreach ($data['defectosSensorialesA']as $def) {
                $defectos = $defectos . $def->codigo . "_";
            }
        }
        if (count($data['defectosSensorialesB']) > 0) {
            foreach ($data['defectosSensorialesB']as $def) {
                $defectos = $defectos . $def->codigo . "_";
            }
        }
        if (count($data['defectosEnsenanzaA']) > 0) {
            foreach ($data['defectosEnsenanzaA']as $def) {
                $defectos = $defectos . $def->codigo . "_";
            }
        }
        if (count($data['defectosEnsenanzaB']) > 0) {
            foreach ($data['defectosEnsenanzaB']as $def) {
                $defectos = $defectos . $def->codigo . "_";
            }
        }

        if (strlen($defectos) > 0) {
            $defectos = substr($defectos, 0, strlen($defectos) - 1);
        }

        $sensorial = $data['sensorial']->operario . ";" .
                $data['sensorial']->documento . ";" .
                $defectos;
//------------------------------------------------------------------------------Taximetro
        $taximetro = $data['taximetro']->operario . ";" .
                $data['taximetro']->documento . ";" .
                $data['taximetro']->aplicaTaximetro . ";" .
                $data['taximetro']->tieneTaximetro . ";" .
                $data['taximetro']->taximetroVisible . ";" .
                $data['taximetro']->r_llanta . ";" .
                $data['taximetro']->distancia . ";" .
                $data['taximetro']->tiempo;
        $taximetro = str_replace(".", ",", str_replace("*", "", $taximetro));
//------------------------------------------------------------------------------Observaciones
        $observaciones = '';
        if (count($data['observaciones']) > 0) {
            foreach ($data['observaciones'] as $o) {
                $observaciones = $observaciones . "$o->codigo: $o->descripcion" . "_";
            }
        }
//------------------------------------------------------------------------------Certificado
        $certificado = $data['fur_aso'] . ";" .
                $this->idCdaRUNT;
//        $certificado = $data['numero_sustrato'] . ";" .
//                $data['numero_consecutivo'] . ";" .
//                $data['fur_aso'] . ";" .
//                $this->idCdaRUNT;
//------------------------------------------------------------------------------Estructura de llantas
        $llantas = $data['sensorial']->operario . ";" .
                $data['sensorial']->documento . ";" .
                $data['labrado']->eje1_derecho . ";" .
                $data['labrado']->eje2_derecho . ";" .
                $data['labrado']->eje3_derecho . ";" .
                $data['labrado']->eje4_derecho . ";" .
                $data['labrado']->eje5_derecho . ";" .
                $data['labrado']->eje1_izquierdo . ";" .
                $data['labrado']->eje2_izquierdo . ";" .
                $data['labrado']->eje3_izquierdo . ";" .
                $data['labrado']->eje4_izquierdo . ";" .
                $data['labrado']->eje5_izquierdo . ";" .
                $data['labrado']->eje2_derecho_interior . ";" .
                $data['labrado']->eje3_derecho_interior . ";" .
                $data['labrado']->eje4_derecho_interior . ";" .
                $data['labrado']->eje5_derecho_interior . ";" .
                $data['labrado']->eje2_izquierdo_interior . ";" .
                $data['labrado']->eje3_izquierdo_interior . ";" .
                $data['labrado']->eje4_izquierdo_interior . ";" .
                $data['labrado']->eje5_izquierdo_interior . ";" .
                $data['labrado']->repuesto . ";" .
                $data['labrado']->repuesto2 . ";" .
                $this->llanta_1_D . ";" .
                $this->llanta_2_DE . ";" .
                $this->llanta_3_DE . ";" .
                $this->llanta_4_DE . ";" .
                $this->llanta_5_DE . ";" .
                $this->llanta_1_I . ";" .
                $this->llanta_2_IE . ";" .
                $this->llanta_3_IE . ";" .
                $this->llanta_4_IE . ";" .
                $this->llanta_5_IE . ";" .
                $this->llanta_2_DI . ";" .
                $this->llanta_3_DI . ";" .
                $this->llanta_4_DI . ";" .
                $this->llanta_5_DI . ";" .
                $this->llanta_2_II . ";" .
                $this->llanta_3_II . ";" .
                $this->llanta_4_II . ";" .
                $this->llanta_5_II . ";" .
                $this->llanta_R . ";" .
                $this->llanta_R2;

        $llantas = str_replace(".", ",", str_replace("*", "", $llantas));
//------------------------------------------------------------------------------Máquinas
        $luxometro = "";
        $opacimetro = "";
        $analizador = "";
        $camara = "";
        $taximetroMaq = "";
        $frenometro = "";
        $bascula = "";
        $suspension = "";
        $alineador = "";
        $termohigrometro = "";
        $profundimetro = "";
        $captador = "";
        $pierey = "";
        $elevador = "";
        $detector = "";
        $sensorRPM = "";
        $sondaTMP = "";

        if ($data["maquinas"]->nombreLuxometro !== '') {
            $maq = explode("$", $data["maquinas"]->nombreLuxometro);
            $luxometro = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "2"},
EOF;
        }
        if ($data["maquinas"]->nombreOpacimetro !== '') {
            $maq = explode("$", $data["maquinas"]->nombreOpacimetro);
            $opacimetro = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "1"},
EOF;
        }
        if ($data["maquinas"]->nombreGases !== '') {
            $maq = explode("$", $data["maquinas"]->nombreGases);
            $analizador = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "1"},
EOF;
        }
        if ($data["maquinas"]->nombreFotos !== '') {
            $maq = explode("$", $data["maquinas"]->nombreFotos);
            $camara = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "4"},
EOF;
        }
        if ($data["maquinas"]->nombreTaximetro !== '') {
            $maq = explode("$", $data["maquinas"]->nombreTaximetro);
            $taximetroMaq = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "5"},
EOF;
        }
        if ($data["maquinas"]->nombreFrenos !== '') {
            $maq = explode("$", $data["maquinas"]->nombreFrenos);
            $frenometro = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "6"},
EOF;
        }
        if ($data["maquinas"]->nombreBascula !== '') {
            $maq = explode("$", $data["maquinas"]->nombreBascula);
            $bascula = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "6"},
EOF;
        }
        if ($data["maquinas"]->nombreSuspension !== '') {
            $maq = explode("$", $data["maquinas"]->nombreSuspension);
            $suspension = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "8"},
EOF;
        }
        if ($data["maquinas"]->nombreAlineador !== '') {
            $maq = explode("$", $data["maquinas"]->nombreAlineador);
            $alineador = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "7"},
EOF;
        }
        if ($data["maquinas"]->nombreTermohigrometro !== '') {
            $maq = explode("$", $data["maquinas"]->nombreTermohigrometro);
            $termohigrometro = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "1"},
EOF;
        }
        if ($data["maquinas"]->nombreProfundimetro !== '') {
            $maq = explode("$", $data["maquinas"]->nombreProfundimetro);
            $profundimetro = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "8"},
EOF;
        }
        if ($data["maquinas"]->nombreCaptador !== '') {
            $maq = explode("$", $data["maquinas"]->nombreCaptador);
            $captador = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "1"},
EOF;
        }
        if ($data["maquinas"]->nombreDetector !== '') {
            $maq = explode("$", $data["maquinas"]->nombreDetector);
            $detector = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "8"},
EOF;
        }
        if ($data["maquinas"]->nombreElevador !== '') {
            $maq = explode("$", $data["maquinas"]->nombreElevador);
            $elevador = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "8"},
EOF;
        }
        if ($data["maquinas"]->nombrePiederey !== '') {
            $maq = explode("$", $data["maquinas"]->nombrePiederey);
            $pierey = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "8"},
EOF;
        }
        if ($data["maquinas"]->nombreSensorRPM !== '') {
            $maq = explode("$", $data["maquinas"]->nombreSensorRPM);
            $sensorRPM = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "8"},
EOF;
        }
        if ($data["maquinas"]->nombreSondaTMP !== '') {
            $maq = explode("$", $data["maquinas"]->nombreSondaTMP);
            $sondaTMP = <<<EOF
                {"nombre": "$maq[0]","marca": "$maq[1]","noserie": "$maq[2]",
                 "pef": "$maq[4]","ltoe": "$maq[5]","NoSerieBench": "$maq[6]",
                "Esperiferico": "$maq[7]","Prueba": "8"},
EOF;
        }

        $maquinas = <<<EOF
{"Equipos": [$luxometro$opacimetro$analizador$taximetroMaq$frenometro$bascula$suspension$alineador$termohigrometro$profundimetro$captador$pierey$elevador$detector$sensorRPM$sondaTMP]}                
EOF;
//        $maquinas = <<<EOF
//{"Equipos": [$luxometro$opacimetro$analizador$camara$taximetroMaq$frenometro$suspension$alineador$termohigrometro$profundimetro$captador$pierey$elevador$detector$sensorRPM$sondaTMP]}                
//EOF;
        $maquinas = str_replace(",]}", "]}", $maquinas);

//------------------------------------------------------------------------------Software
        $software = $data["software"];
//------------------------------------------------------------------------------Jefe linea
        $jefeLinea = $data['hojatrabajo']->jefelinea . ";" . $this->Musuarios->getXnombreID($data['hojatrabajo']->jefelinea);
//------------------------------------------------------------------------------Numero fur
        $taxonomia = //
                $IdProveedor . "|" .
                $propietario . "|" .
                $vehiculo . "|" .
                $fotos . "|" .
                $diesel . $gasesCarro . $gasesMoto .
                $luces . "|" .
                $fas . "|" .
                $sensorial . "|" .
                $taximetro . "|" .
                $observaciones . "|" .
                $certificado . "|" .
                $llantas . "|" .
                $maquinas . "|" .
                $software . "|" .
                $jefeLinea . "|" .
                $data['fur_aso'] . ";" . trim(date_format(date_create($data['fechafur']), 'Y-m-d H:i:s'));

        if ($data['ocasion'] === 'true') {
            $reins = '1';
            $ocasion = '2';
        } else {
            $reins = '0';
            $ocasion = '1';
        }
        $url = 'http://' . $this->ipSicov . '/sicov.asmx?WSDL';
        $datos_conexion = explode(":", $this->ipSicov);
        if ($data['sicovModoAlternativo'] == '1') {
            $url = 'http://' . $this->ipSicovAlternativo . '/sicov.asmx?WSDL';
            $datos_conexion = explode(":", $this->ipSicovAlternativo);
        }
        $host = $datos_conexion[0];
        if (count($datos_conexion) > 1) {
            $port = $datos_conexion[1];
        } else {
            $port = 80;
        }
        $waitTimeoutInSeconds = 2;
//        if ($this->CARinformeActivo == "1") {
//            $rta = $this->Mambientales->getEnvioCar($this->idprueba_gases);
//            if (count($rta) == 0) {
//                $envioCarMsg = $this->getInformeCarNew($data['hojatrabajo']->idhojapruebas);
//                $msg = $msg . " - Respuesta CAR: " . $envioCarMsg . " - ";
//            }
//        }
        error_reporting(0);
        if ($fp = fsockopen($host, $port, $errCode, $errStr, $waitTimeoutInSeconds)) {
            $client = new SoapClient($url);
            $datos['idhojapruebas'] = $data['idhojapruebas'];
            $datos['reinspeccion'] = $reins;
            $msg = '';
            $encrptopenssl = New Opensslencryptdecrypt();
            if (!$this->segundo_envio) {
//                $taxo = $this->formato_texto($taxonomia);
//                $cad = str_replace(" ", "_", $taxo);
                file_put_contents('encdes/entradaFUR.txt', $this->formato_texto($taxonomia));
                $url = 'http://localhost:8093/enc/encFur.php' . '?fur=' . "1";
                $eve = file_get_contents($url);
                $fur = array(
                    'cadena' => $eve
                );
                $tipo = 'f';
//                file_put_contents('encdes/salidaFUR.txt', "");
//                file_put_contents('encdes/entradaFUR.txt', "");
//                file_put_contents('encdes/entradaFUR.txt', $this->formato_texto($taxonomia));
//                shell_exec('start C:/Apache24/htdocs/et/encdes/openFUR.bat');
//                usleep(500);
//                $fur_ = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', file_get_contents('encdes/salidaFUR.txt'));
//                $fur = array(
//                    'cadena' => $fur_
//                );
//                
//                

                $respuesta = $client->EnviarFurSicov($fur);
                $respuesta = $respuesta->EnviarFurSicovResult;
//                var_dump($fur);
                if ($respuesta->codRespuesta == '1') {
                    $datos['sicov'] = '1';
                    $estado = 'exito';
                    $msg = 'Operación Exitosa';
                    if ($aprobado !== '1') {
                        $datos['estadototal'] = '3';
                    } else {
                        $datos['estadototal'] = '2';
                    }
                    $this->Mhojatrabajo->update_x($datos);
                } else {
                    $msg = 'Operación Fallida';
                    $estado = 'error';
                }
            } else {
                $extranjero = "S";
                if ($data['pais']->nombre == 'COLOMBIA')
                    $extranjero = "N";
                $tipo = 'r';
                $runt = array(
                    'nombreEmpleado' => $data['vehiculo']->usuario_registro,
                    'numeroIdentificacion' => $data['vehiculo']->documento_usuario,
                    'placa' => $data['vehiculo']->numero_placa,
                    'extranjero' => $extranjero,
                    'consecutivoRUNT' => substr($data['numero_consecutivo'], 1),
                    'IdRunt' => $this->idCdaRUNT,
                    'direccionIpEquipo' => $_SERVER['REMOTE_ADDR']
                );
                $respuesta = $client->EnviarRuntSicov($runt);
                $respuesta = $respuesta->EnviarRuntSicovResult;
                if ($respuesta->codRespuesta == '1') {
                    $datos['sicov'] = '1';
                    $estado = 'exito';
                    $msg = 'Operación Exitosa';
                    if ($aprobado !== '1') {
                        $datos['estadototal'] = '7';
                    } else {
                        $datos['estadototal'] = '4';
                    }
                    if ($this->salaEspera2 == "1") {
                        $sala['idhojaprueba'] = $data['hojatrabajo']->idhojapruebas;
                        $sala['idtipo_prueba'] = "20";
                        $sala['estado'] = "1";
                        $sala['actualizado'] = "0";
                        $this->Mcontrol_salae->insertar($sala);
                    }
                    $this->Mhojatrabajo->update_x($datos);
                } else {
                    $msg = 'Operación Fallida';
                    $estado = 'error';
                }
//                if ($this->CARinformeActivo == "1") {
//                    $rta = $this->Mambientales->getEnvioCar($this->idprueba_gases);
//                    if (count($rta) == 0) {
//                        $envioCarMsg = $this->getInformeCarNew($data['hojatrabajo']->idhojapruebas);
//                        $msg = $msg . " - Respuesta CAR: " . $envioCarMsg . " - ";
//                    }
//                }
            }
            $mensaje = $msg . '|' . $respuesta->codRespuesta . '|' . $ocasion . '|' . $estado . '|' . $respuesta->msjRespuesta;
            $this->insertarEvento($data['vehiculo']->numero_placa, $this->formato_texto($taxonomia), $tipo, '1', $mensaje);
        } else {
            $mensaje = 'Operación fallida|0|' . $ocasion . '|error|Sin conexión a sicov';
            $this->insertarEvento($data['vehiculo']->numero_placa, '', 'f', '1', $mensaje);
        }
        if ($fp) {
            fclose($fp);
        }
        echo $mensaje;
    }

    private function formato_texto($cadena) {
        $no_permitidas = array("Ñ", "ñ", "á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹", "'", "");
        $permitidas = array("N", "n", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E", "", "");
        $texto = str_replace($no_permitidas, $permitidas, $cadena);
        return $texto;
    }

    private function rdnr($valor) {
        $dato = '';
        if ($valor !== '') {
            if (floatval($valor) === 0.00 || floatval($valor) === 0.0 || floatval($valor) === 0) {
                $dato = "0.00";
            } else {
                if (intval($valor) < 10) {
                    $valorNegativo = false;
                    $dato = abs(round($valor, 2));
                    if ($valor < 0) {
                        $valorNegativo = true;
                        if (intval($valor) > -10) {
                            if (substr($dato, 2) == "") {
                                $dato = $dato . ".00";
                            } elseif (substr($dato, 3) == "" || substr($dato, 3) == '0') {
                                $dato = $dato . "0";
                            }
                        } elseif (intval($valor) <= -10 && intval($valor) > -100) {
                            $dato = abs(round($valor, 1));
                            if (substr($dato, 2) == "") {
                                $dato = $dato . ".0";
                            }
                        } else {
                            $dato = abs(round($valor));
                        }
                    } else {
                        if (substr($dato, 1) == "") {
                            $dato = $dato . ".00";
                        } elseif (substr($dato, 3) == "" || substr($dato, 3) == '0') {
                            $dato = $dato . "0";
                        }
                    }
                    if ($valorNegativo) {
                        $dato = "-" . $dato;
                    }
                } elseif (intval($valor) >= 10 && intval($valor) < 100) {
                    $dato = round($valor, 1);
                    if (substr($dato, 2) == "") {
                        $dato = $dato . ".0";
                    }
                } else {
                    $dato = round($valor);
                }
            }
        }
        return $dato;
    }

//--------------------------------------------------------------------------PARAMETROS 
//--------------------------------------------------------------------------Opacidad
    function max_opacidad($modelo) {
        $flag = 0;
        $mod = intval($modelo);
        if ($mod >= 1998) {
            $flag = 35;
        } else if ($mod < 1998 && $mod >= 1985) {
            $flag = 40;
        } else if ($mod < 1985 && $mod >= 1971) {
            $flag = 45;
        } else if ($mod < 1971) {
            $flag = 50;
        }
        return $flag;
    }

//--------------------------------------------------------------------------Gases
    function getCoFlag($modelo, $tipoVeh, $tiempos) {
        $flag = 0;
        $mod = intval($modelo);
        if ($tipoVeh === "3") {
            if ($this->fechares762_Chispa !== "" && $this->fechares762_Chispa <= $this->fecha_inicialG) {
                if ($tiempos == "2" && $modelo >= 2010) {
                    $flag = 3.5;
                } else if ($tiempos == "2" && $modelo <= 2009) {
                    $flag = 3.5;
                } else if ($tiempos == "4") {
                    $flag = 3.5;
                }
            } elseif ($this->fechares762 !== "" && $this->fechares762 <= $this->fecha_inicialG) {
                if ($tiempos == "2" && $modelo >= 2010) {
                    $flag = 3.6;
                } else if ($tiempos == "2" && $modelo <= 2009) {
                    $flag = 4.5;
                } else if ($tiempos == "4") {
                    $flag = 3.6;
                }
            } else {
                $flag = 4.5;
            }
        } else {

            if ($this->fechares762_Chispa !== "" && $this->fechares762_Chispa <= $this->fecha_inicialG) {
                if ($mod <= 1984) {
                    $flag = 4.0;
                } else if ($mod > 1984 && $mod <= 1997) {
                    $flag = 3.0;
                } else if ($mod > 1997 && $mod <= 2009) {
                    $flag = 1.0;
                } else if ($mod > 2009) {
                    $flag = 0.8;
                }
            } else {
                if ($mod <= 1970) {
                    $flag = 5.0;
                } else if ($mod > 1970 && $mod <= 1984) {
                    $flag = 4.0;
                } else if ($mod > 1984 && $mod <= 1997) {
                    $flag = 3.0;
                } else if ($mod > 1997) {
                    $flag = 1.0;
                }
            }
        }
        return $flag;
    }

    function getCo2Flag($tipoVeh) {
        $flag = 0;
        if ($tipoVeh === "3") {
            $flag = 0;
        } else {
            $flag = 7.0;
        }
        return $flag;
    }

    function getO2Flag($tipoVeh) {
        $flag = 0;
        if ($tipoVeh === "3") {
            $flag = 20.9;
        } else {
            $flag = 5.0;
        }
        return $flag;
    }

    function getHcFlag($modelo, $tipoVeh, $tiempos) {
        $flag = 0;
        $mod = intval($modelo);
        if ($tipoVeh === "3") {
            if ($this->fechares762_Chispa !== "" && $this->fechares762_Chispa <= $this->fecha_inicialG) {
                if ($tiempos == "2") {
                    if ($mod > 2009) {
                        $flag = 1600;
                    } else {
                        $flag = 8000;
                    }
                } else {
                    $flag = 1300;
                }
            } elseif ($this->fechares762 !== "" && $this->fechares762 <= $this->fecha_inicialG) {
                if ($tiempos == "2") {
                    if ($mod > 2009) {
                        $flag = 1600;
                    } else {
                        $flag = 10000;
                    }
                } else {
                    $flag = 1600;
                }
            } else {
                if ($tiempos == "2") {
                    if ($mod > 2009) {
                        $flag = 2000;
                    } else {
                        $flag = 10000;
                    }
                } else {
                    $flag = 2000;
                }
            }
        } else {
            if ($this->fechares762_Chispa !== "" && $this->fechares762_Chispa <= $this->fecha_inicialG) {
                if ($mod <= 1984) {
                    $flag = 650;
                } else if ($mod > 1984 && $mod <= 1997) {
                    $flag = 400;
                } else if ($mod > 1997 && $mod <= 2009) {
                    $flag = 200;
                } else if ($mod > 2009) {
                    $flag = 160;
                }
            } else {
                if ($mod <= 1970) {
                    $flag = 800;
                } else if ($mod > 1970 && $mod <= 1984) {
                    $flag = 650;
                } else if ($mod > 1984 && $mod <= 1997) {
                    $flag = 400;
                } else if ($mod > 1997) {
                    $flag = 200;
                }
            }
        }
        return $flag;
    }

//--------------------------------------------------------------------------Frenos
    function min_des_B() {
        return 20;
    }

    function max_des_B() {
        return 30;
    }

    function min_des_A() {
        return 30;
    }

    function max_des_A() {
        return 40;
    }

    function efi_min($tipoVeh, $tipoEfi) {
        if ($tipoVeh === "3") {
            if ($tipoEfi === "total") {
                return 30;
            } else {
                return 18;
            }
        } else {
            if ($tipoEfi === "total") {
                return 50;
            } else {
                return 18;
            }
        }
    }

//--------------------------------------------------------------------------Luces
    function getRango_min_inc() {
        return 0.5;
    }

    function getRango_max_inc() {
        return 3.5;
    }

    function getMin_luz_baja() {
        return 2.5;
    }

    function getMax_luz_total() {
        return 225;
    }

//--------------------------------------------------------------------------Suspension
    function min() {
        return 40;
    }

//--------------------------------------------------------------------------Taxímetro
    function minmaxTax() {
        return 2;
    }

//--------------------------------------------------------------------------Alineación
    function minmaxAli() {
        return 10;
    }

//--------------------------------------------------------------------------Envio Informe Car
    function getInformeCarNew($idhojapruebas) {

        $datoInforme = $this->tipo_informe_fugas_cal_lin;
        $rta = $this->Mambientales->getPruebagases($idhojapruebas);
        $rtas = array();
        foreach ($rta as $value) {
            $data = $this->Mambientales->informe_car_adutioria_new($value->idmaquina, $value->idprueba, $datoInforme);
//            $basic = $this->BasicCar($data);
            $res = $this->envioCar($data, $value->idmaquina, $value->idprueba, $idhojapruebas);
            $r = array(
                'cadena' => json_encode($res),
                'idprueba' => $value->idprueba
//                'basic' => json_encode($basic),
            );
            array_push($rtas, $r);
        }
        echo json_encode($rtas);
    }

//    function BasicCar($data) {
//        $this->arrayCar["dtb_nombre"] = $data[0]->Nombre_razon_social_propietario;
//        $this->arrayCar["dtb_tipodoc"] = $data[0]->Tipo_documento;
//        $this->arrayCar["dtb_numdoc"] = $data[0]->No_documento;
//        $this->arrayCar["dtb_direccion"] = $data[0]->Direccion;
//        $this->arrayCar["dtb_telefono"] = $data[0]->Telefono_1;
//        $this->arrayCar["dtb_telefono2"] = $data[0]->Telefono_2;
//        $this->arrayCar["dtb_ciudad"] = $data[0]->Ciudad;
//        $this->arrayCar["dbt_marca"] = $data[0]->Marca;
//        $this->arrayCar["dbt_tipomotor"] = $data[0]->tipomotor;
//        $this->arrayCar["dbt_linea"] = $data[0]->Linea;
//        $this->arrayCar["dbt_diseno"] = $data[0]->Carroceria;
//        $this->arrayCar["dbt_modelo"] = $data[0]->Ano_modelo;
//        $this->arrayCar["dbt_placa"] = $data[0]->Placa;
//        $this->arrayCar["dbt_cilindraje"] = $data[0]->Cilindraje;
//        $this->arrayCar["dbt_clase"] = $data[0]->Clase;
//        $this->arrayCar["dbt_servicio"] = $data[0]->Servicio;
//        $this->arrayCar["dbt_combustible"] = $data[0]->Combustible;
//        $this->arrayCar["dbt_nomotor"] = $data[0]->Numero_motor;
//        $this->arrayCar["dbt_vinserie"] = $data[0]->Numero_VIN_serie;
//        $this->arrayCar["dbt_licencia"] = $data[0]->No_licencia_transito;
//        $this->arrayCar["dbt_kilometraje"] = $data[0]->Kilometraje;
//        $this->arrayCar["dtb_tipov"] = $data[0]->Kilometraje;
//        $basic = array(
//            'basic' => $this->arrayCar
//        );
//        return $basic;
//    }

    function envioCar($data, $idmaquina, $idprueba, $idhojapruebas) {
        $encrptopenssl = New Opensslencryptdecrypt();
        $json = $encrptopenssl->decrypt(file_get_contents('system/lineas.json', true));
        $datos = json_decode($json);
        foreach ($datos as $item) {
            if ($item->idconf_maquina == $idmaquina) {
                $marca = $item->marca;
                $serie_maquina = $item->serie_maquina;
            } else {
                $marca = $data[0]->Marca_analizador;
                $serie_maquina = $data[0]->No_serie_analizador;
            }
            if ($item->idconf_maquina == $data[0]->idsonometro) {
                $marca_sonometro = $item->marca;
                $serie_sonometro = $item->serie_maquina;
            } else {
                $marca_sonometro = $data[0]->Marca_sonometro;
                $serie_sonometro = $data[0]->Serie_sonometro;
            }
        }
        $toCar = array(
            'meastype' => $data[0]->norma,
            'placa' => $data[0]->Placa,
            'measdata' => array(
                'med_vrpef' => $data[0]->Vr_PEF,
                'med_seriebanco' => $data[0]->No_de_serie_banco,
                'med_serieanalizado' => $data[0]->No_serie_analizador,
                'med_analizador' => $data[0]->Marca_analizador,
                'med_vrspanbajohc' => $data[0]->Vr_Span_Bajo_HC,
                'med_resvrspanbajohc' => $data[0]->Resultado_Vr_Span_Bajo_HC,
                'med_vrspanbajoco' => $data[0]->Vr_Span_Bajo_CO,
                'med_resvrspanbajoco' => $data[0]->Resultado_Vr_Span_Bajo_CO,
                'med_vrspanbajoco2' => $data[0]->Vr_Span_Bajo_CO2,
                'med_resvrspanbajoco2' => $data[0]->Resultado_Vr_Span_Bajo_CO2,
                'med_vrspanaltohc' => $data[0]->Vr_Span_Alto_HC,
                'med_resvrspanaltohc' => $data[0]->Resultado_Vr_Span_Alto_HC,
                'med_vrspanaltoco' => $data[0]->Vr_Span_Alto_CO,
                'med_resvrspanaltoco' => $data[0]->Resultado_Vr_Span_Alto_CO,
                'med_vrspanaltoco2' => $data[0]->Vr_Span_Alto_CO2,
                'med_resvrspanaltoco2' => $data[0]->Resultado_Vr_Span_Alto_CO2,
                'med_fverificacion' => $data[0]->Fecha_y_hora_ultima_verificacion_y_ajuste,
                'med_nomproveedor' => $data[0]->Nombre_proveedor,
                'med_nomprograma' => $data[0]->Nombre_programa,
                'med_verprograma' => $data[0]->Version_programa,
                'med_seriemedido' => $serie_maquina,
                'med_marcamedidor' => $marca,
                'med_vrplinealidad' => $data[0]->Vr_primer_punto_de_linealidad,
                'med_resplinealidad' => $data[0]->Resultado_primer_punto_de_linealidad,
                'med_vrslinealidad' => $data[0]->Vr_segundo_punto_de_linealidad,
                'med_resslinealidad' => $data[0]->Resultado_segundo_punto_de_linealidad,
                'med_vrtlinealidad' => $data[0]->Vr_tercer_punto_de_linealidad,
                'med_restlinealidad' => $data[0]->Resultado_tercer_punto_de_linealidad,
                'med_vrclinealidad' => $data[0]->Vr_cuarto_punto_de_linealidad,
                'med_resclinealidad' => $data[0]->Resultado_cuarto_punto_de_linealidad,
                'med_placa' => $data[0]->Placa
            ),
            'proofdata' => array(
                'ana_consecutivo' => $data[0]->No_de_consecutivo_prueba,
                'ana_finicio' => $data[0]->Fecha_y_hora_inicio_de_la_prueba,
                'ana_ffin' => $data[0]->Fecha_y_hora_final_de_la_prueba,
                'ana_faborto' => $data[0]->Fecha_y_hora_aborto_de_la_prueba,
                'ana_operador' => $data[0]->Operador_realiza_prueba,
                'ana_metodotemperatura' => $data[0]->Metodo_de_medicion_de_temperatura_motor,
                'ana_tempambiente' => $data[0]->Temperatura_ambiente,
                'ana_humedad' => $data[0]->Humedad_relativa,
                'ana_causalaborto' => $data[0]->Causal_aborto_analisis
            ),
            'custdata' => array(
                'dus_nombre' => $data[0]->Nombre_razon_social_propietario,
                'dus_tdocumento' => $data[0]->Tipo_documento,
                'dus_documento' => $data[0]->No_documento,
                'dus_direccion' => $data[0]->Direccion,
                'dus_telefono' => $data[0]->Telefono_2,
                'dus_celular' => $data[0]->Telefono_1,
                'dus_ciudad' => $data[0]->Ciudad
            ),
            'vehidata' => array(
                'dve_marca' => $data[0]->Marca,
                'dve_tipomotor' => $data[0]->tipomotor,
                'dve_linea' => $data[0]->Linea,
                'dve_diseno' => $data[0]->Carroceria,
                'dve_ano' => $data[0]->Ano_modelo,
                'dve_placa' => $data[0]->Placa,
                'dve_cilindraje' => $data[0]->Cilindraje,
                'dve_clase' => $data[0]->Clase,
                'dve_servicio' => $data[0]->Servicio,
                'dve_combustible' => $data[0]->Combustible,
                'dve_nomotor' => $data[0]->Numero_motor,
                'dve_vinserie' => $data[0]->Numero_VIN_serie,
                'dve_licencia' => $data[0]->No_licencia_transito,
                'dve_kilometraje' => $data[0]->Kilometraje,
                'dve_modmotor' => $data[0]->modificadion_motor,
                'dve_potencia' => $data[0]->Potencia_motor
            ),
            'revidata' => array(
                'rev_tuboescape' => $data[0]->Fugas_tubo_escape,
                'rev_silenciador' => $data[0]->Fugas_silenciador,
                'rev_adefescape' => $data[0]->Accesorios_o_deformaciones_en_el_tubo_de_escape_que_no_permitan_la_instalacion_sistema_de_muestreo,
                'rev_tapacombustible' => $data[0]->Presencia_tapa_Combustible_o_fugas_en_el_mismo,
                'rev_tapaaceite' => $data[0]->Presencia_Tapa_Aceite,
                'rev_filtroaire' => $data[0]->Ausencia_o_mal_estado_filtro_de_Aire,
                'rev_salidaadic' => $data[0]->Salidas_adicionales_diseno,
                'rev_pcv' => $data[0]->PCV_Sistema_recirculacion_de_gases_del_carter,
                'rev_hunegroazul' => $data[0]->Presencia_humo_negro_azul,
                'rev_fuerarango' => $data[0]->RPM_fuera_rango,
                'rev_refrigeracion' => $data[0]->Falla_sistema_de_refrigeracion,
                'rev_tempmotor' => $data[0]->Temperatura_motor,
                'rev_rpm' => $data[0]->Rpm_ralenti,
                'rev_hc' => $data[0]->Hc_ralenti,
                'rev_co' => $data[0]->Co_ralenti,
                'rev_co2' => $data[0]->Co2_ralenti,
                'rev_o2' => $data[0]->O2_ralenti,
                'rev_rpmcrucero' => $data[0]->Rpm_crucero,
                'rev_hccrucero' => $data[0]->Hc_crucero,
                'rev_cocrucero' => $data[0]->Co_crucero,
                'rev_co2crucero' => $data[0]->Co2_crucero,
                'rev_o2crucero' => $data[0]->O2_crucero,
                'rev_dilucion' => $data[0]->Presencia_de_dilucion,
                'rev_incumemision' => $data[0]->Incumplimiento_de_niveles_de_emision_gases,
                'rev_revinestable' => $data[0]->Revoluciones_instables_o_fuera_rango_disel,
                'rev_indmalfunc' => $data[0]->Indicacion_mal_funcionamiento_del_motor_disel,
                'rev_controlvel' => $data[0]->Funcionamiento_del_sistema_de_control_velocidad_de_motor_disel,
                'rev_dispositivosrpm' => $data[0]->Instalacion_dispositivos_que_alteren_rpm_disel,
                'rev_tempinicial' => $data[0]->Temperatura_inicial_de_motor_disel,
                'rev_velalcanzada' => $data[0]->Velocidad_no_alcanzada_5_seg_disel,
                'rev_rpmvelocidad' => $data[0]->Rpm_velocidad_gobernada_disel,
                'rev_fallamotor' => $data[0]->Falla_subita_motor_disel,
                'rev_resopacidadpre' => $data[0]->Resultado_ciclo_preliminar_disel,
                'rev_rpmgobernadapre' => $data[0]->RPM_gobernada_ciclo_preliminar_disel,
                'rev_resopacidadpciclo' => $data[0]->Resultado_opacidad_primer_ciclo_disel,
                'rev_rpmpciclo' => $data[0]->RPM_gobernada_primer_ciclo_disel,
                'rev_resopacidadsciclo' => $data[0]->Resultado_opacidad_segundo_ciclo_disel,
                'rev_rpmsciclo' => $data[0]->RPM_gobernada_segundo_ciclo,
                'rev_resopacidadtciclo' => $data[0]->Resultado_opacidad_tercer_ciclo,
                'rev_rpmtciclo' => $data[0]->RPM_gobernada_tercer_ciclo,
                'rev_ltoe' => $data[0]->LTOE,
                'rev_tempfinal' => $data[0]->Temperatura_final_del_motor_disel,
                'rev_fallatemp' => $data[0]->Falla_por_temperatura_motor_disel,
                'rev_inestabilidad' => $data[0]->Inestabilidad_durante_ciclos_de_medicion_disel,
                'rev_difaritmetica' => $data[0]->Diferencias_aritmetica_disel,
                'rev_resfinal' => $data[0]->Resultado_final_opa_disel,
                'rev_causarechazo' => $data[0]->Causas_rechazo_opa,
                'rev_concepto' => $data[0]->Concepto_tecnico
            ),
            'sondata' => array(
                'son_marca' => $marca_sonometro,
                'son_serie' => $serie_sonometro,
                'son_valor' => $data[0]->Valor_de_ruido_reportado
            )
        );
//        echo $toCar;
        return $toCar;
//        $base_ = base_url();
//        echo '<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="crossorigin="anonymous"></script>
//            <script type="text/javascript">
//            console.log("entre");
//            console.log("entre");
//            $.ajax
//            ({
//                type: "POST",
//                url: "http://3.138.158.109:8480/cdapp/rest/final/medicionfinal",
//                headers: {
//                    "Authorization": "b56c19aa217e36a6c182be3ce6fab1851c32a6860f74a312f2cf6d230f6c1573",
//                    "Content-Type": "application/json"
//                },
//                data: JSON.stringify(' . $toCar . '),
//                success: function (rta) {
//                    if(rta.resp == "OK"){
//                        var estado = 1;
//                        guardarTabla(estado);
//                    }else{
//                        var estado = 0;
//                        guardarTabla(estado);
//                    }
//                },
//                errors: function (rta) {
//                    console.log(rta);
//                }
//            });
//            function guardarTabla(estado){
//                $.ajax
//                ({
//                    type: "POST",
//                    url: "' . $base_ . 'index.php/oficina/fur/CFUR/saveControl",
//                    data: {estado:estado,
//                    idprueba: ' . $idprueba . '},
//                        success: function (rta) {
//                            console.log(rta);
//                        },
//                        errors: function (rta) {
//                            console.log(rta);
//                        }
//                });
//            }
//            </script>';
//        $rta['dato'] = $idhojapruebas . '-' . 0 . '-' . '-4';
//        $this->load->view('oficina/gestion/CGVrechaSinConsecutivo',$rta);
//        $request_headers = array(
//            "Authorization:" . "b56c19aa217e36a6c182be3ce6fab1851c32a6860f74a312f2cf6d230f6c1573",
//            "Content-Type:" . "application/json"
//        );
//
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, 'http://3.138.158.109:8480/cdapp/rest/final/medicionfinal');
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $toCar);
//
//        $season_data = curl_exec($ch);
//
//        if (curl_errno($ch)) {
////            print "Error: " . curl_error($ch);
////            exit();
//        }
//        curl_close($ch);
//        $json = json_decode($season_data, true);
//        date_default_timezone_set('America/bogota');
//        if ($json['resp'] == "OK") {
//            $r['idprueba'] = $idprueba;
//            $r['tipo'] = 'Envio car exitoso';
//            $r['estado'] = 1;
//            $r['usuario'] = $this->session->userdata("IdUsuario");
//            $r['fecharegistro'] = date("Y-m-d H:i:s");
//            $this->Mambientales->insertControlCar($r);
//        } else {
//            $r['idprueba'] = $idprueba;
//            $r['tipo'] = 'Envio car error';
//            $r['estado'] = 0;
//            $r['usuario'] = $this->session->userdata("IdUsuario");
//            $r['fecharegistro'] = date("Y-m-d H:i:s");
//            $this->Mambientales->insertControlCar($r);
//        }
    }

    function saveControl() {
        $estado = $this->input->post('estado');
        $idprueba = $this->input->post('idprueba');
        $tipo = $this->input->post('tipo');
        $r['idprueba'] = $idprueba;
        $r['tipo'] = $tipo;
        $r['estado'] = $estado;
        $r['usuario'] = $this->session->userdata("IdUsuario");
        $r['fecharegistro'] = date("Y-m-d H:i:s");
        $this->Mambientales->insertControlCar($r);
    }

    function getBitacoraGases($idprueba) {
//        echo "id" . $idprueba;

        if ($this->generarLogGases == "1") {
            $r = fopen('C:\Apache24\htdocs\et\application\libraries\prueba.txt', 'w+b');
            fwrite($r, $idprueba);
            fclose($r);
            $cadena = "cd C:/Apache24/htdocs/et/application/libraries
                    start LogGasesData.jar
                    exit";
            $archivo = fopen('system/openLog.bat', "w+b");
            fwrite($archivo, $cadena);
            fclose($archivo);
            $task_list = array();
            shell_exec("tasklist 2>NUL", $task_list);
            $existe = false;
            foreach ($task_list AS $task_line) {

                $task_line = explode(" ", $task_line);
                if ($task_line[0] == "javaw.exe" || $task_line[0] == "java.exe") {
                    $existe = true;
                    break;
                }
            }

            if (!$existe) {
                shell_exec('start C:\Apache24\htdocs\et\system\openLog.bat');
            }

//            $hc = [1796, 1796, 1796, 3390, 3390, 4490, 4490, 4860, 5170, 5170, 5430, 6560, 6560, 6410, 6410, 6340, 6340, 6270, 6270, 6190, 6190, 6190, 6100, 6100, 6020, 6020, 6020, 5880, 5880, 5880, 5820, 5820, 5790, 5790, 5750, 5750, 5750, 5750, 5740, 5740, 5740, 5800, 5800, 5800, 5830, 5880, 5880, 5880, 5880, 5950, 5950, 5980, 5980, 6000, 6000, 6020, 6020, 6020, 6020, 6020];
//            $co = [2.31, 2.31, 2.31, 3.97, 3.97, 4.75, 4.75, 4.97, 5.12, 5.12, 5.21, 5.59, 5.59, 5.43, 5.43, 5.34, 5.34, 5.25, 5.25, 5.19, 5.19, 5.19, 5.14, 5.14, 5.09, 5.09, 5.09, 5, 5, 5, 4.98, 4.98, 4.97, 4.97, 4.96, 4.96, 4.96, 4.96, 4.96, 4.96, 4.96, 4.94, 4.94, 4.94, 4.93, 4.92, 4.92, 4.92, 4.92, 4.93, 4.93, 4.95, 4.95, 4.98, 4.98, 5, 5, 5.01, 5.01, 5.01];
//            $co2 = [1.4, 1.4, 1.4, 2.4, 2.4, 3, 3, 3.1, 3.1, 3.1, 3.2, 3.8, 3.8, 4.2, 4.2, 4.4, 4.4, 4.5, 4.5, 4.7, 4.7, 4.7, 4.8, 4.8, 4.9, 4.9, 4.9, 5.1, 5.1, 5.1, 5.2, 5.2, 5.2, 5.2, 5.2, 5.2, 5.2, 5.2, 5.2, 5.2, 5.2, 5.1, 5.1, 5.1, 5.1, 5, 5, 5, 5, 5, 5, 4.9, 4.9, 4.9, 4.9, 4.9, 4.9, 4.9, 4.9, 4.9];
//            $o2 = [16.5, 16.5, 16.5, 13.9, 13.9, 12.4, 12.4, 11.9, 11.6, 11.6, 11.3, 10.1, 10.1, 9.8, 9.8, 9.6, 9.6, 9.5, 9.5, 9.4, 9.4, 9.4, 9.3, 9.3, 9.2, 9.2, 9.2, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9, 9.2, 9.2, 9.2, 9.3, 9.4, 9.4, 9.4, 9.4, 9.4, 9.4, 9.4, 9.4, 9.4, 9.4, 9.4, 9.4, 9.4, 9.4, 9.3];
//            $rpm = [1380, 1460, 1340, 1310, 1320, 1320, 1340, 1350, 1340, 1340, 1320, 1370, 1380, 1400, 1400, 1420, 1400, 1380, 1380, 1380, 1400, 1410, 1400, 1390, 1390, 1400, 1400, 1400, 1400, 1410, 1380, 1400, 1410, 1400, 1400, 1400, 1400, 1400, 1380, 1370, 1390, 1400, 1420, 1400, 1380, 1390, 1410, 1400, 1410, 1390, 1410, 1420, 1380, 1370, 1390, 1390, 1420, 1420, 1420, 1440];
//            $ultimosSegundosHC_1 = [-3, -3, -3, -2, 0, 0, 2, 3, 3, 3];
//            $ultimosSegundosHC_2 = [-2, -2, -2, -1, 0, 0, 1, 2, 2, 2];
//            $ultimosSegundosHC_3 = [-1, -1, -1, 0, 0, 0, 0, 1, 1, 1];
//            $ultimosSegundosCO_1 = [-0.03, -0.03, -0.03, -0.02, 0, 0, 0.02, 0.03, 0.03, 0.03];
//            $ultimosSegundosCO_2 = [-0.02, -0.02, -0.02, -0.01, 0, 0, 0.01, 0.02, 0.02, 0.02];
//            $ultimosSegundosCO_3 = [-0.01, -0.01, -0.01, 0, 0, 0, 0, 0.01, 0.01, 0.01];
//            $rta = $this->Mambientales->getBitacoraGases($idprueba);
//            if ($rta[0]->control == 0) {
//// se valida si aplica o no la correcion de oxigeno
//                if (($rta[0]->o2_ralenti >= 11.0 && $rta[0]->tiempos == '2' && $rta[0]->tipo_vehiculo == 3 && $rta[0]->ano_modelo < 2010)) {
//                    $dfhc = 6001 - round($rta[0]->promhcra_ant);
//                    $dfco = 4.98 - $rta[0]->promcora_ant;
//                } else if (($rta[0]->o2_ralenti >= 6.0 && $rta[0]->tiempos == '4' && $rta[0]->tipo_vehiculo == 3) || ($rta[0]->o2_ralenti >= 6.0 && $rta[0]->tiempos == '2' && $rta[0]->tipo_vehiculo == 3 && $rta[0]->ano_modelo >= 2010)) {
//                    $dfhc = 6001 - round($rta[0]->promhcra_ant);
//                    $dfco = 4.98 - $rta[0]->promcora_ant;
//                } else {
//                    $dfhc = 6001 - round($rta[0]->hc_ralenti);
//                    $dfco = 4.98 - $rta[0]->co_ralenti;
//                }
//                $dfco2 = 4.91 - $rta[0]->co2_ralenti;
//                $dfo2 = 9.39 - $rta[0]->o2_ralenti;
//                $dfrpm = 1406 - $rta[0]->rpm_ralenti;
//// se calcula el modulo para poder sumarlo a las rpm y que de el promedio 
//                $rpmreal = $rpm[0] - $dfrpm;
//                $rpmmod = $rpmreal % 10;
//                $t = 0.0;
//                $hcfinal = [];
//                $cofinal = [];
//                $co2final = [];
//                $o2final = [];
//                $rpmfinal = [];
//                $fechahora = [];
//                $j_hc = 0;
//                $rhc = rand(1, 3);
//                $rco = rand(1, 3);
//                $encontradoo2 = false;
//                $o2v_ = 0;
//                $rpmdata = ceil($dfrpm / 10) * 10;
//                $ralentiData = [];
//                for ($i = 1; $i <= 60; $i++) {
//                    if ($i > 1) {
//                        $t = $t + 0.5;
//                    } else {
//                        $t = $t;
//                    }
//                    $rpm_ = $rpm[$i - 1] - $rpmdata;
//                    $hc_ = $hc[$i - 1] - $dfhc;
//                    $co_ = round($co[$i - 1] - $dfco, 3);
//                    $co2_ = round($co2[$i - 1] - $dfco2, 2);
//                    $o2_ = round($o2[$i - 1] - $dfo2, 2);
//                    if ($o2_ > 18 && !$encontradoo2) {
//                        $encontradoo2 = true;
//                        $o2v_ = $o2_;
//                    }
//// cuadre de hc y co mediante los vectores de los ultimo 10 datos
//                    if ($i > 50) {
//                        switch ($rhc) {
//                            case 1:
//                                $hc_ = $ultimosSegundosHC_1[$j_hc] + $hc_;
//                                break;
//                            case 2:
//                                $hc_ = $ultimosSegundosHC_2[$j_hc] + $hc_;
//                                break;
//                            case 3:
//                                $hc_ = $ultimosSegundosHC_3[$j_hc] + $hc_;
//                                break;
//                        }
//                        switch ($rco) {
//                            case 1:
//                                $co_ = $ultimosSegundosCO_1[$j_hc] + $co_;
//                                break;
//                            case 2:
//                                $co_ = $ultimosSegundosCO_2[$j_hc] + $co_;
//                                break;
//                            case 3:
//                                $co_ = $ultimosSegundosCO_3[$j_hc] + $co_;
//                                break;
//                        }
////cuadre de rpm
//                        if ($rpmmod > 0) {
//                            $rpm_ = $rpm_ + 10;
//                        }
//                        $rpmmod--;
//                        $j_hc++;
//                    }
//                    $arrayRal = [
//                        "tiempo" => $t,
//                        "hc" => $hc_,
//                        "co" => $co_,
//                        "co2" => $co2_,
//                        "o2" => $o2_,
//                        "rpm" => $rpm_
//                    ];
//
//                    array_push($ralentiData, $arrayRal);
//                }
//
//// se cambian los datos negativos por el primer valor positivo del vector
//// cuadre HC raelnti
//                $promediohc = 0;
//                for ($a = 0; $a < count($ralentiData); $a++) {
//                    if ($ralentiData[$a]["hc"] < 0) {
//                        $ralentiData[$a]['hc'] = $ralentiData[$a]['hc'] * - 1;
//                    }
//                    if ($a >= 50) {
//                        $promediohc = $promediohc + $ralentiData[$a]['hc'];
//                    }
//                }
//
////            echo floatval($rta[0]->hc_ralenti) ."<br>".floatval($promediohc / 10);
//                if (($rta[0]->o2_ralenti >= 11.0 && $rta[0]->tiempos == '2' && $rta[0]->tipo_vehiculo == 3 && $rta[0]->ano_modelo < 2010)) {
//                    if (floatval($rta[0]->promhcra_ant) !== floatval($promediohc / 10)) {
//                        $ralentiData = $this->promedioCalculo($rta[0]->promhcra_ant, $ralentiData, 0.1, 'hc');
//                    }
//                } else if (($rta[0]->o2_ralenti >= 6.0 && $rta[0]->tiempos == '4' && $rta[0]->tipo_vehiculo == 3) || ($rta[0]->o2_ralenti >= 6.0 && $rta[0]->tiempos == '2' && $rta[0]->tipo_vehiculo == 3 && $rta[0]->ano_modelo >= 2010)) {
//                    if (floatval($rta[0]->promhcra_ant) !== floatval($promediohc / 10)) {
//                        $ralentiData = $this->promedioCalculo($rta[0]->promhcra_ant, $ralentiData, 0.1, 'hc');
//                    }
//                } else {
//                    if (floatval($rta[0]->hc_ralenti) !== floatval($promediohc / 10)) {
////                    echo floatval($rta[0]->hc_ralenti) . "<br>" . floatval($promediohc / 10);
//                        $ralentiData = $this->promedioCalculo($rta[0]->hc_ralenti, $ralentiData, 0.1, 'hc');
//                    }
//                }
//
////
////
////            // cuadre CO raelnti
//                $PromedioCo = 0;
//                for ($b = 0; $b < count($ralentiData); $b++) {
//                    if ($ralentiData[$b]['co'] < 0) {
//                        $ralentiData[$b]['co'] = $ralentiData[$b]['co'] * - 1;
//                    }
//                    if ($b >= 50) {
//                        $PromedioCo = $PromedioCo + $ralentiData[$b]['co'];
//                    }
//                }
//                if (($rta[0]->o2_ralenti >= 11.0 && $rta[0]->tiempos == '2' && $rta[0]->tipo_vehiculo == 3 && $rta[0]->ano_modelo < 2010)) {
//                    if (floatval($rta[0]->promcora_ant) !== floatval($PromedioCo / 10)) {
//                        $ralentiData = $this->promedioCalculo($rta[0]->promcora_ant, $ralentiData, 0.0001, 'co');
//                    }
//                } else if (($rta[0]->o2_ralenti >= 6.0 && $rta[0]->tiempos == '4' && $rta[0]->tipo_vehiculo == 3) || ($rta[0]->o2_ralenti >= 6.0 && $rta[0]->tiempos == '2' && $rta[0]->tipo_vehiculo == 3 && $rta[0]->ano_modelo >= 2010)) {
//                    if (floatval($rta[0]->promcora_ant) !== floatval($PromedioCo / 10)) {
//                        $ralentiData = $this->promedioCalculo($rta[0]->promcora_ant, $ralentiData, 0.0001, 'co');
//                    }
//                } else {
//                    if (floatval($rta[0]->co_ralenti) !== floatval($PromedioCo / 10)) {
//                        $ralentiData = $this->promedioCalculo($rta[0]->co_ralenti, $ralentiData, 0.0001, 'co');
//                    }
//                }
////
////
////            // cuadre CO2 raelnti
//                $promedioCO2 = 0;
//                for ($c = 0; $c < count($ralentiData); $c++) {
//                    if ($ralentiData[$c]['co2'] < 0) {
//                        $ralentiData[$c]['co2'] = $ralentiData[$c]['co2'] * - 1;
//                    }
//                    if ($c >= 50) {
//                        $promedioCO2 = $promedioCO2 + $ralentiData[$c]['co2'];
//                    }
//                }
//                if (floatval($rta[0]->co2_ralenti) !== floatval($promedioCO2 / 10)) {
//                    $ralentiData = $this->promedioCalculo($rta[0]->co2_ralenti, $ralentiData, 0.0001, 'co2');
//                }
//
//                for ($d = 0; $d < count($ralentiData); $d++) {
//                    if ($ralentiData[$d]['o2'] > 18) {
//                        $ralentiData[$d]['o2'] = $o2v_;
//                    }
//                    if ($ralentiData[$d]['o2'] < 0) {
//                        $ralentiData[$d]['o2'] = 0.0;
//                    }
//                }
//
//                if ($rta[0]->rpm_crucero > 0) {
//                    $r = $this->logCrucero($rta);
//                    $res = json_encode($r);
//                } else {
//                    $res = "";
//                }
//                $datos["idprueba"] = $idprueba;
//                $datos["exosto"] = 1;
//                $datos["datos_ciclo_ralenti"] = json_encode($ralentiData);
//                $datos["datos_ciclo_crucero"] = $res;
//                $this->Mambientales->logGasesInsert($datos);
//            }
        }
    }

    public function logCrucero($rta) {
        $hc = [68, 68, 85, 85, 95, 95, 107, 107, 111, 121, 121, 121, 127, 127, 140, 140, 154, 154, 154, 172, 172, 172, 175, 175, 187, 187, 187, 187, 191, 193, 193, 193, 196, 196, 208, 208, 211, 211, 223, 224, 224, 224, 230, 230, 231, 231, 234, 234, 234, 234, 234, 234, 236, 236, 238, 238, 238, 245, 247, 253];
        $co = [0.15, 0.15, 0.19, 0.19, 0.22, 0.22, 0.24, 0.24, 0.26, 0.26, 0.26, 0.26, 0.29, 0.29, 0.3, 0.3, 0.33, 0.33, 0.33, 0.36, 0.36, 0.36, 0.37, 0.37, 0.38, 0.38, 0.39, 0.39, 0.4, 0.4, 0.4, 0.4, 0.4, 0.4, 0.41, 0.41, 0.41, 0.41, 0.42, 0.43, 0.43, 0.43, 0.44, 0.44, 0.44, 0.44, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45, 0.45];
        $co2 = [5.25, 5.25, 6.1, 6.1, 7.03, 7.03, 7.82, 7.82, 8.61, 9.31, 9.31, 9.31, 9.9, 9.9, 10.4, 10.4, 10.8, 10.8, 10.8, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12.6, 12.8, 12.8, 12.8, 13, 13, 13.2, 13.2, 13.3, 13.3, 13.5, 13.6, 13.6, 13.6, 13.7, 13.7, 13.8, 13.8, 13.8, 13.8, 13.9, 13.9, 13.9, 13.9, 14, 14, 14.1, 14.1, 14.1, 14.2, 14.2, 14.2];
        $o2 = [15.07, 15.07, 13.8, 13.8, 12.53, 12.53, 11.32, 11.32, 10.19, 9.13, 9.13, 9.13, 8.16, 8.16, 7.29, 7.29, 6.52, 6.52, 6.52, 5.27, 5.27, 5.27, 4.75, 4.75, 4.3, 4.3, 3.91, 3.91, 3.57, 3.27, 3.27, 3.27, 3.01, 3.01, 2.8, 2.8, 2.6, 2.6, 2.43, 2.29, 2.29, 2.29, 2.17, 2.17, 2.05, 2.05, 1.95, 1.95, 1.86, 1.79, 1.79, 1.79, 1.72, 1.72, 1.66, 1.66, 1.66, 1.6, 1.56, 1.51];
        $rpm = [2280, 2350, 2330, 2280, 2310, 2270, 2350, 2360, 2270, 2280, 2350, 2300, 2290, 2350, 2360, 2310, 2320, 2310, 2280, 2300, 2310, 2350, 2360, 2340, 2310, 2360, 2340, 2360, 2310, 2340, 2270, 2320, 2360, 2280, 2350, 2300, 2290, 2310, 2310, 2330, 2340, 2290, 2350, 2350, 2300, 2300, 2340, 2280, 2310, 2340, 2300, 2350, 2320, 2310, 2360, 2360, 2340, 2330, 2300, 2360];
        $ultimosSegundosHC_1 = [-3, -3, -3, -2, 0, 0, 2, 3, 3, 3];
        $ultimosSegundosHC_2 = [-2, -2, -2, -1, 0, 0, 1, 2, 2, 2];
        $ultimosSegundosHC_3 = [-1, -1, -1, 0, 0, 0, 0, 1, 1, 1];
        $ultimosSegundosCO_1 = [-0.03, -0.03, -0.03, -0.02, 0, 0, 0.02, 0.03, 0.03, 0.03];
        $ultimosSegundosCO_2 = [-0.02, -0.02, -0.02, -0.01, 0, 0, 0.01, 0.02, 0.02, 0.02];
        $ultimosSegundosCO_3 = [-0.01, -0.01, -0.01, 0, 0, 0, 0, 0.01, 0.01, 0.01];

// se valida si aplica o no la correcion de oxigeno

        $dfhc = 239 - round($rta[0]->hc_crucero);
        $dfco = 0.45 - $rta[0]->co_crucero;
        $dfco2 = 14.07 - $rta[0]->co2_crucero;
        $dfo2 = 1.667 - $rta[0]->o2_crucero;
        $dfrpm = 2333 - $rta[0]->rpm_crucero;
// se calcula el modulo para poder sumarlo a las rpm y que de el promedio 
        $rpmreal = $rpm[0] - $dfrpm;
        $rpmmod = $rpmreal % 10;
        $t = 0.0;
        $hcfinal = [];
        $cofinal = [];
        $co2final = [];
        $o2final = [];
        $rpmfinal = [];
        $fechahora = [];
        $j_hc = 0;
        $rhc = rand(1, 3);
        $rco = rand(1, 3);
        $encontradoo2 = false;
        $o2v_ = 0;
        $rpmdata = ceil($dfrpm / 10) * 10;
        $cruceroData = [];
        for ($i = 1; $i <= 60; $i++) {
            if ($i > 1) {
                $t = $t + 0.5;
            } else {
                $t = $t;
            }
            $rpm_ = $rpm[$i - 1] - $rpmdata;
            $hc_ = $hc[$i - 1] - $dfhc;
            $co_ = round($co[$i - 1] - $dfco, 3);
            $co2_ = round($co2[$i - 1] - $dfco2, 2);
            $o2_ = round($o2[$i - 1] - $dfo2, 2);
            if ($o2_ > 18 && !$encontradoo2) {
                $encontradoo2 = true;
                $o2v_ = $o2_;
            }
// cuadre de hc y co mediante los vectores de los ultimo 10 datos
            if ($i > 50) {
                switch ($rhc) {
                    case 1:
                        $hc_ = $ultimosSegundosHC_1[$j_hc] + $hc_;
                        break;
                    case 2:
                        $hc_ = $ultimosSegundosHC_2[$j_hc] + $hc_;
                        break;
                    case 3:
                        $hc_ = $ultimosSegundosHC_3[$j_hc] + $hc_;
                        break;
                }
                switch ($rco) {
                    case 1:
                        $co_ = $ultimosSegundosCO_1[$j_hc] + $co_;
                        break;
                    case 2:
                        $co_ = $ultimosSegundosCO_2[$j_hc] + $co_;
                        break;
                    case 3:
                        $co_ = $ultimosSegundosCO_3[$j_hc] + $co_;
                        break;
                }
//cuadre de rpm
                if ($rpmmod > 0) {
                    $rpm_ = $rpm_ + 10;
                }
                $rpmmod--;
                $j_hc++;
            }
            $arrayRal = [
                "tiempo" => $t,
                "hc" => $hc_,
                "co" => $co_,
                "co2" => $co2_,
                "o2" => $o2_,
                "rpm" => $rpm_
            ];

            array_push($cruceroData, $arrayRal);
        }
// se cambian los datos negativos por el primer valor positivo del vector
        $promedioHC = 0;
        for ($c = 0; $c < count($cruceroData); $c++) {
            if ($cruceroData[$c]['hc'] < 0) {
                $cruceroData[$c]['hc'] = $cruceroData[$c]['hc'] * - 1;
            }
            if ($c >= 50) {
                $promedioHC = $promedioHC + $cruceroData[$c]['hc'];
            }
        }
        if (floatval($rta[0]->hc_crucero) !== floatval($promedioHC / 10)) {
            $cruceroData = $this->promedioCalculo($rta[0]->hc_crucero, $cruceroData, 0.1, 'hc');
        }

        $promedioCO = 0;
        for ($c = 0; $c < count($cruceroData); $c++) {
            if ($cruceroData[$c]['co'] < 0) {
                $cruceroData[$c]['co'] = $cruceroData[$c]['co'] * - 1;
            }
            if ($c >= 50) {
                $promedioCO = $promedioCO + $cruceroData[$c]['co'];
            }
        }
        if (floatval($rta[0]->co_crucero) !== floatval($promedioCO / 10)) {
            $cruceroData = $this->promedioCalculo($rta[0]->co_crucero, $cruceroData, 0.0001, 'co');
        }

        $promedioCO2 = 0;
        for ($c = 0; $c < count($cruceroData); $c++) {
            if ($cruceroData[$c]['co2'] < 0) {
                $cruceroData[$c]['co2'] = $cruceroData[$c]['co2'] * - 1;
            }
            if ($c >= 50) {
                $promedioCO2 = $promedioCO2 + $cruceroData[$c]['co2'];
            }
        }
        if (floatval($rta[0]->co2_crucero) !== floatval($promedioCO2 / 10)) {
            $cruceroData = $this->promedioCalculo($rta[0]->co2_crucero, $cruceroData, 0.0001, 'co2');
        }

        for ($d = 0; $d < count($cruceroData); $d++) {
            if ($cruceroData[$d]['o2'] > 18) {
                $cruceroData[$d]['o2'] = $o2v_;
            }
            if ($cruceroData[$d]['o2'] < 0) {
                $cruceroData[$d]['o2'] = 0.0;
            }
        }
        return $cruceroData;
    }

    function promedioCalculo($rta, $ralentiData, $resolucion, $tipo) {
        do {
            $varPromedioCo_ = 0;
            for ($b = 0; $b < count($ralentiData); $b++) {
                if ($b >= 50) {
                    if ($rta == 0) {
                        $ralentiData[$b][$tipo] = 0;
                    } else {
                        if ($ralentiData[$b][$tipo] - $resolucion > 0) {
                            $ralentiData[$b][$tipo] = round($ralentiData[$b][$tipo] - $resolucion, 4);
                        }
                    }

                    $varPromedioCo_ = $varPromedioCo_ + $ralentiData[$b][$tipo];
                }
            }
            $var_ = $varPromedioCo_ / 10;
        } while ($rta < $var_);

//        echo "rta:" . $rta . "<br>";
//        echo "promedio: " . round($var_,3) . "<br>";

        if (floatval($rta) !== floatval($var_)) {
            for ($b = 0; $b < count($ralentiData); $b++) {
                if ($b >= 50) {
                    $ralentiData[$b][$tipo] = floatval($rta);
                }
            }
        }
        for ($b = 0; $b < count($ralentiData); $b++) {
            if ($b >= 50) {
                if ($tipo == "hc") {
                    $ralentiData[$b][$tipo] = round($ralentiData[$b][$tipo]);
                }
                if ($tipo == "co") {
                    $ralentiData[$b][$tipo] = round($ralentiData[$b][$tipo], 3);
                }
                if ($tipo == "co2") {
                    $ralentiData[$b][$tipo] = round($ralentiData[$b][$tipo], 2);
                }
            }
        }
        return $ralentiData;
    }

    function enviarEmail($email, $url, $placa) {
//        echo $this->emailCDA;
//        echo $this->pwEmailCDA;
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.googlemail.com',
            'smtp_port' => '465',
            'smtp_user' => $this->emailCDA,
            'smtp_pass' => $this->pwEmailCDA,
            'mailtype' => 'html',
            'smtp_crypto' => 'ssl',
            'newline' => "\r\n",
            'useragent' => $this->nombreCDA,
            'smtp_timeout' => '5',
            'wordwrap' => TRUE,
            'charset' => 'utf-8'
        );
//        $data = $this->load->view('Viewemail/emailregistro','', TRUE);
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from($this->emailCDA, $this->nombreCDA, $this->emailCDA);
        $this->email->subject($this->asuntoEmailCDA . "Formato de prerevisión " . $placa);
        $this->email->attach($url);
        $this->email->message($this->vistaEmailCDA);
        $this->email->to($email);
        if ($this->email->send(FALSE)) {
            return 1;
//            echo "enviado<br/>";
//            echo $this->email->print_debugger(array('headers'));
        } else {
            return 0;
//            echo "fallo <br/>";
//            echo "error: " . $this->email->print_debugger(array('headers'));
        }
    }

}
