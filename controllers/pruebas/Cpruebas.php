<?php

defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");

class Cpruebas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('security');
        $this->load->model("pruebas/Mpruebas");
        $this->load->model("Mutilitarios");
        $this->load->model("Mprerevision");
        $this->load->model("dominio/MEventosindra");
        $this->load->model("dominio/Mprueba");
        $this->load->library('Opensslencryptdecrypt');
    }

    public function index() {
        $idtipo_prueba = $this->input->post("idtipo_prueba");
        $reinspeccion = $this->input->post("reinspeccion");
        switch ($reinspeccion) {
            case 0:
                $reinspeccion = "";
                break;
            case 1:
                $reinspeccion = "(h.reinspeccion=1 or h.reinspeccion=0) and";
                break;
            case 2:
                $reinspeccion = "(h.reinspeccion=44441 or h.reinspeccion=4444) and";
                break;
            case 3:
                $reinspeccion = "h.reinspeccion=8888 and";
                break;
        }
        $tipo_vehiculo = $this->input->post("tipo_vehiculo");
        switch ($tipo_vehiculo) {
            case 0:
                $tipo_vehiculo = "";
                break;
            case 1:
                $tipo_vehiculo = "v.tipo_vehiculo=1 and";
                break;
            case 2:
                $tipo_vehiculo = "v.tipo_vehiculo=2 and";
                break;
            case 3:
                $tipo_vehiculo = "v.tipo_vehiculo=3 and (v.idclase<>14 and v.idclase<>19) and";
                break;
            case 4:
                $tipo_vehiculo = "(v.tipo_vehiculo=3 and (v.idclase=14 or v.idclase=19)) and";
                break;
            case 5:
                $tipo_vehiculo = "(v.tipo_vehiculo=1 or v.tipo_vehiculo=2) and";
                break;
            case 6:
                $tipo_vehiculo = "(v.tipo_vehiculo=1 or (v.idclase=14 or v.idclase=19)) and";
                break;
            case 7:
                $tipo_vehiculo = "(v.tipo_vehiculo=2 or (v.idclase=14 or v.idclase=19)) and";
                break;
            case 8:
                $tipo_vehiculo = "(v.tipo_vehiculo=3 and v.idclase=30) and";
                break;
        }
        $rta = $this->Mpruebas->getPruebas($idtipo_prueba, $reinspeccion, $tipo_vehiculo);
//$pruebas = $rta->result();
        $pruebas = array();
        foreach ($rta->result() as $prueba) {
            $src = $this->obtenerFoto($prueba->numero_placa, $prueba->idclase, $prueba->taximetro);
            switch ($prueba->idservicio) {
                case 1:
                    $prueba->color_placa = "gold";
                    $prueba->color_letra = "black";
                    break;
                case 2:
                    $prueba->color_placa = "white";
                    $prueba->color_letra = "black";
                    break;
                case 3:
                    $prueba->color_placa = "gold";
                    $prueba->color_letra = "black";
                    break;
                case 4:
                    $prueba->color_placa = "blue";
                    $prueba->color_letra = "whitesmoke";
                    break;
                case 7:
                    $prueba->color_placa = "blue";
                    $prueba->color_letra = "whitesmoke";
                    break;
            }

            $prueba->src = $src;
            array_push($pruebas, $prueba);
        }
        echo json_encode($pruebas);
    }

    public function getDia() {
        $dia = strval($this->Mutilitarios->getNow());
        $dia = str_replace("-", "", $dia);
        $dia = substr($dia, 0, 8);
        return $dia;
    }

    public function insertarResultado() {
        $resultados['idprueba'] = $this->input->post('idprueba');
        $resultados['tiporesultado'] = $this->input->post('tiporesultado');
        $resultados['valor'] = $this->input->post('valor');
        $resultados['observacion'] = $this->input->post('observacion');
        $resultados['idconfig_prueba'] = $this->input->post('idconfig_prueba');
        $this->Mpruebas->insertarResultado($resultados);
    }

    public function insertarResultados() {
        $resultados = $this->input->post('resultados');
        foreach ($resultados as $r) {
            $resultado['idprueba'] = $r['idprueba'];
            $resultado['tiporesultado'] = $r['tiporesultado'];
            $resultado['valor'] = $r['valor'];
            $resultado['observacion'] = $r['observacion'];
            $resultado['idconfig_prueba'] = $r['idconfig_prueba'];
            $this->Mpruebas->insertarResultado($resultado);
        }
    }

    public function insertarPeriferico() {
        $reinspeccion = $this->input->post('reinspeccion');
        $prueba['idhojapruebas'] = $this->input->post('idhojapruebas');
        $prueba['fechainicial'] = $this->Mprueba->getFechaInicial($prueba['idhojapruebas'], $reinspeccion);
        $prueba['fechafinal'] = $this->Mutilitarios->getNow();
        $prueba['prueba'] = 0;
        $prueba['estado'] = 2;
        $prueba['idusuario'] = $this->input->post('idusuario');
        $prueba['idmaquina'] = $this->input->post('idmaquina');
        $prueba['idtipo_prueba'] = $this->input->post('idtipo_prueba');
        $this->Mpruebas->insertarPrueba($prueba);
    }

    public function eliminarPeriferico() {
        $reinspeccion = $this->input->post('reinspeccion');
        $prueba['idhojapruebas'] = $this->input->post('idhojapruebas');
        $prueba['fechainicial'] = $this->Mprueba->getFechaInicial($prueba['idhojapruebas'], $reinspeccion);
        $prueba['idtipo_prueba'] = $this->input->post('idtipo_prueba');
        $this->Mprueba->eliminarPrueba($prueba);
    }

    function eliminarPruebaID() {
        $prueba['idprueba'] = $this->input->post('idprueba');
        $this->Mprueba->eliminarPruebaID($prueba);
    }

    public function insertarPrueba() {
        $prueba['idhojapruebas'] = $this->input->post('idhojapruebas');
        $prueba['fechainicial'] = $this->input->post('fechainicial');
        $prueba['prueba'] = 0;
        $prueba['estado'] = 0;
        $prueba['idusuario'] = $this->input->post('idusuario');
        $prueba['idtipo_prueba'] = $this->input->post('idtipo_prueba');
        $this->Mpruebas->insertarPrueba($prueba);
    }

    public function insertarPruebaExosto() {
        $prueba['idhojapruebas'] = $this->input->post('idhojapruebas');
        $prueba['fechainicial'] = $this->input->post('fechainicial');
        $prueba['estado'] = 0;
        $prueba['idusuario'] = $this->input->post('idusuario');
        $prueba['idtipo_prueba'] = $this->input->post('idtipo_prueba');
        echo $this->Mpruebas->insertarPruebaExosto($prueba);
    }

    public function actualizarPrueba() {
        $usuario = $this->Mpruebas->getUsuario($this->input->post('idusuario'));
//        $maquina = $this->Mpruebas->getMaquina($this->input->post('serie'), $this->input->post('idtipo_prueba'));
        $pruebas['idprueba'] = $this->input->post('idprueba');
        $pruebas['idusuario'] = $this->input->post('idusuario');
//        $pruebas['idmaquina'] = $maquina->idmaquina;
        $pruebas['idmaquina'] = $this->input->post('idmaquina');
        $pruebas['estado'] = $this->input->post('estado');
        $valid = true;
        $mesaje = "";
        $res = $this->Mpruebas->actualizarPruebas($pruebas);
        if ($res == 1) {
            $hojapruebas = $this->Mpruebas->getHojaPruebas($pruebas['idprueba']);
            if ($hojapruebas !== "" && $hojapruebas !== null) {
                if ($this->input->post('observacion') !== "" && $this->input->post('observacion') !== NULL) {
                    $resultado['idprueba'] = $this->input->post('idprueba');
                    $resultado['tiporesultado'] = 'Observacion Aborto';
                    $resultado['valor'] = $this->input->post('observacion');
                    $resultado['observacion'] = 'Observacion Aborto';
                    $resultado['idconfig_prueba'] = '700';
                    $res = $this->Mpruebas->insertarResultado($resultado);
                    if ($res !== 1) {
                        $valid = false;
                        $mesaje = $mesaje + "<br>Transaccion incompleta Resultados. ";
                    }
                }
                if ($hojapruebas->reinspeccion == '0' || $hojapruebas->reinspeccion == '1') {
                    $auditoria_sicov['id_revision'] = $hojapruebas->idhojapruebas;
                    $auditoria_sicov['serial_equipo_medicion'] = $this->input->post('serie');
                    $auditoria_sicov['ip_equipo_medicion'] = $this->input->post('ip');
                    $auditoria_sicov['fecha_registro_bd'] = $this->Mutilitarios->getNow();
                    $auditoria_sicov['fecha_evento'] = $this->Mutilitarios->getNow();
                    $auditoria_sicov['tipo_operacion'] = '1';
                    $auditoria_sicov['tipo_evento'] = '2';
                    $auditoria_sicov['codigo_proveedor'] = '862';
                    $auditoria_sicov['id_runt_cda'] = $this->getCodigoRuntCDA();
                    $auditoria_sicov['identificacion_usuario'] = $usuario->identificacion;
                    $auditoria_sicov['observacion'] = $this->input->post('observacion');
                    switch ($this->input->post('idtipo_prueba')) {
                        case 1:
//                    $this->auditLuxometro($auditoria_sicov, $pruebas['idprueba']);
                            break;
                        case 4:
//                    $this->auditSonometro($auditoria_sicov, $pruebas['idprueba']);
                            break;
                        case 3:
//                    $this->auditGases($auditoria_sicov, $pruebas['idprueba']);
                            break;
                    }
                }
            } else {
                $valid = false;
                $mesaje = $mesaje + "<br>Transaccion incompleta getHojatrabajo. ";
            }
        } else {
            $valid = false;
            $mesaje = "<br> Transaccion incompleta Actualizar Pruebas. ";
        }
        
        if(!$valid){
            echo $mesaje;
        }else{
            echo 1;
        }
    }
    
    public function eliminarResultados(){
        $idprueba = $this->input->post('idprueba');
        $rta = $this->Mpruebas->eliminarResultados($idprueba);
    }

    public function actualizarPruebaExosto() {
        $usuario = $this->Mpruebas->getUsuario($this->input->post('idusuario'));
//        $maquina = $this->Mpruebas->getMaquina($this->input->post('serie'), $this->input->post('idtipo_prueba'));
        $pruebas['idprueba'] = $this->input->post('idprueba');
        $pruebas['idusuario'] = $this->input->post('idusuario');
//        $pruebas['idmaquina'] = $maquina->idmaquina;
        $pruebas['idmaquina'] = $this->input->post('idmaquina');
        $pruebas['estado'] = $this->input->post('estado');
        $pruebas['prueba'] = $this->input->post('prueba');
        $this->Mpruebas->actualizarPruebasExosto($pruebas);
        $hojapruebas = $this->Mpruebas->getHojaPruebas($pruebas['idprueba']);
        if ($hojapruebas->reinspeccion == '0' || $hojapruebas->reinspeccion == '1') {
            $auditoria_sicov['id_revision'] = $hojapruebas->idhojapruebas;
            $auditoria_sicov['serial_equipo_medicion'] = $this->input->post('serie');
            $auditoria_sicov['ip_equipo_medicion'] = $this->input->post('ip');
            $auditoria_sicov['fecha_registro_bd'] = $this->Mutilitarios->getNow();
            $auditoria_sicov['fecha_evento'] = $this->Mutilitarios->getNow();
            $auditoria_sicov['tipo_operacion'] = '1';
            $auditoria_sicov['tipo_evento'] = '2';
            $auditoria_sicov['codigo_proveedor'] = '862';
            $auditoria_sicov['id_runt_cda'] = $this->getCodigoRuntCDA();
            $auditoria_sicov['identificacion_usuario'] = $usuario->identificacion;
            $auditoria_sicov['observacion'] = '';
            switch ($this->input->post('idtipo_prueba')) {
                case 1:
//                    $this->auditLuxometro($auditoria_sicov, $pruebas['idprueba']);
                    break;
                case 4:
//                    $this->auditSonometro($auditoria_sicov, $pruebas['idprueba']);
                    break;
                case 3:
//                    var_dump($auditoria_sicov);
//                    var_dump($pruebas);
//                    $this->auditGases($auditoria_sicov, $pruebas['idprueba']);
                    break;
            }
        }
    }

    public function actualizarPruebaVisual() {
        $usuario = $this->Mpruebas->getUsuario($this->input->post('idusuario'));
        $pruebas['idprueba'] = $this->input->post('idprueba');
        $pruebas['idusuario'] = $this->input->post('idusuario');
        $pruebas['idmaquina'] = $this->input->post('idmaquina');
        $pruebas['estado'] = $this->input->post('estado');
        $this->Mpruebas->actualizarPruebas($pruebas);
        $hojapruebas = $this->Mpruebas->getHojaPruebas($pruebas['idprueba']);
        if ($hojapruebas->reinspeccion == '0' || $hojapruebas->reinspeccion == '1') {
            $auditoria_sicov['id_revision'] = $hojapruebas->idhojapruebas;
            $auditoria_sicov['serial_equipo_medicion'] = $this->input->post('serial');
            $auditoria_sicov['ip_equipo_medicion'] = NULL;
            $auditoria_sicov['fecha_registro_bd'] = $this->Mutilitarios->getNow();
            $auditoria_sicov['fecha_evento'] = $this->Mutilitarios->getNow();
            $auditoria_sicov['tipo_operacion'] = '1';
            $auditoria_sicov['tipo_evento'] = '2';
            $auditoria_sicov['codigo_proveedor'] = '862';
            $auditoria_sicov['id_runt_cda'] = $this->getCodigoRuntCDA();
            $auditoria_sicov['identificacion_usuario'] = $usuario->identificacion;
            $auditoria_sicov['observacion'] = '';
            switch ($this->input->post('idtipo_prueba')) {
                case 8:
//                    $this->auditVisual($auditoria_sicov, $pruebas['idprueba'], $this->input->post('defectos'));
                    break;
            }
        }
    }

    public function cargarVehiculo() {
        $placa = $this->Mpruebas->obtenerPlacaIdprueba($this->input->post("idprueba"));
        $rtaVehiculo = $this->Mprerevision->cargarVehiculoLite($placa);
        if ($rtaVehiculo->num_rows() !== 0) {
            $vehiculo = $rtaVehiculo->result();
            $vehiculo[0]->src = $this->obtenerFoto($placa, $vehiculo[0]->idclase, $vehiculo[0]->taximetro);
            echo json_encode($vehiculo);
        } else {
            echo 'FALSE';
        }
    }

    private function obtenerFoto($numero_placa, $idclase, $taximetro) {
        $encrptopenssl = New Opensslencryptdecrypt();
        $file = "c:/tcm/prerevision/" . $this->getDia() . "/" . $numero_placa . "/mini_0.dat";
        if (file_exists($file)) {
            $src = file_get_contents($file, true);
            $src = $encrptopenssl->decrypt($src);
        } else {
            $file = "c:/tcm/prerevision/" . $this->getDia() . "/" . $numero_placa . "/mini_1.dat";
            if (file_exists($file)) {
                $src = file_get_contents($file, true);
                $src = $encrptopenssl->decrypt($src);
            } else {
                switch ($idclase) {
                    case 1:
                        $src = "../../img/automovil.jpg";
                        break;
                    case 'AUTOMOVIL':
                        $src = "../../img/automovil.jpg";
                        break;
                    case 2:
                        $src = "../../img/bus.jpg";
                        break;
                    case 'BUS':
                        $src = "../../img/bus.jpg";
                        break;
                    case 3:
                        $src = "../../img/bus.jpg";
                        break;
                    case 'BUSETA':
                        $src = "../../img/bus.jpg";
                        break;
                    case 4:
                        $src = "../../img/pesado.jpg";
                        break;
                    case 'CAMION':
                        $src = "../../img/pesado.jpg";
                        break;
                    case 5:
                        $src = "../../img/camioneta.jpg";
                        break;
                    case 'CAMIONETA':
                        $src = "../../img/camioneta.jpg";
                        break;
                    case 6:
                        $src = "../../img/camioneta.jpg";
                        break;
                    case 'CAMPERO':
                        $src = "../../img/camioneta.jpg";
                        break;
                    case 7:
                        $src = "../../img/microbus.jpg";
                        break;
                    case 'MICROBUS':
                        $src = "../../img/microbus.jpg";
                        break;
                    case 8:
                        $src = "../../img/pesado.jpg";
                        break;
                    case 'TRACTOCAMION':
                        $src = "../../img/pesado.jpg";
                        break;
                    case 9:
                        $src = "../../img/pesado.jpg";
                        break;
                    case 'VOLQUETA':
                        $src = "../../img/pesado.jpg";
                        break;
                    case 10:
                        $src = "../../img/moto.jpg";
                        break;
                    case 'MOTOCICLETA':
                        $src = "../../img/moto.jpg";
                        break;
                    case 14:
                        $src = "../../img/motocarro.jpg";
                        break;
                    case 'MOTOCARRO':
                        $src = "../../img/motocarro.jpg";
                        break;
                    case 19:
                        $src = "../../img/motocarro.jpg";
                        break;
                    case 30:
                        $src = "../../img/cuatrimoto.jpg";
                        break;
                    case 'CUATRIMOTO':
                        $src = "../../img/cuatrimoto.jpg";
                        break;
                    default:
                        $src = "../../img/sinclase.jpg";
                        break;
                }
                if ($taximetro == 1) {
                    $src = "../../img/taxi.jpg";
                }
            }
        }
        return $src;
    }

    private function auditLuxometro($auditoria_sicov, $idprueba) {
        // $auditoria_sicov['trama'] = '{'
        //         . '"intensidadDerecha":"' . $this->Mpruebas->buscarResultado($idprueba, 14) . '",'
        //         . '"inclinacionDerecha":"' . $this->Mpruebas->buscarResultado($idprueba, 20) . '",'
        //         . '"intensidadIzquierda":"' . $this->Mpruebas->buscarResultado($idprueba, 16) . '",'
        //         . '"inclinacionIzquierda":"' . $this->Mpruebas->buscarResultado($idprueba, 19) . '",'
        //         . '"SumatoriaIntensidad":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
        //         . '"tablaAfectada":"resultados",'
        //         . '"idRegistro":"' . $idprueba . '"}';
        $auditoria_sicov['trama'] = '{'
                . '"derBajaIntensidadValor1":"' . $this->Mpruebas->buscarResultado($idprueba, 14) . '",'
                . '"derBajaIntensidadValor2":"' . $this->Mpruebas->buscarResultado($idprueba, 20) . '",'
                . '"derBajaIntensidadValor3":"' . $this->Mpruebas->buscarResultado($idprueba, 16) . '",'
                . '"derBajaSimultaneas":"' . $this->Mpruebas->buscarResultado($idprueba, 19) . '",'
                . '"izqBajaIntensidadValor1":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqBajaIntensidadValor2":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqBajaIntensidaValor3":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"IzqBajaSimultaneas":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"derBajaInclinacionValor1":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"derBajaInclinacionValor2":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"derBajaInclinacionValor3":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqBajaInclinacionValor1":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqBajaInclinacionValor2":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqBajaInclinacionValor3":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"sumatoriaIntensidad":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"derAltaIntensidadValor1":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"derAltaIntensidadValor2":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"derAltaIntensidadValor3":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"derAltasSimultaneas":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqAltaIntesidadValor1":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqAltaIntesidadValor2":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqAltaIntesidadValor3":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqAltasSimultaneas":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"derExplorardorasValor1":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"derExplorardorasValor2":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"derExplorardorasValor3":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"derExploradorasSimultaneas":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqExplorardorasValor1":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqExplorardorasValor2":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqExplorardorasValor3":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"izqExploradorasSimultaneas":"' . $this->Mpruebas->buscarResultado($idprueba, 18) . '",'
                . '"tablaAfectada":"resultados",'
                . '"idRegistro":"' . $idprueba . '"}';
        $this->Mpruebas->insertarAuditoriaSicov($auditoria_sicov);
    }

    private function auditSonometro($auditoria_sicov, $idprueba) {
        $auditoria_sicov['trama'] = '{'
                . '"ruidoEscape":"' . $this->Mpruebas->buscarResultado($idprueba, 165) . '",'
                . '"tablaAfectada":"resultados",'
                . '"idRegistro":"' . $idprueba . '"}';
        $this->Mpruebas->insertarAuditoriaSicov($auditoria_sicov);
    }

    private function auditVisual($auditoria_sicov, $idprueba, $defectos) {
        $auditoria_sicov['trama'] = '{'
                . '"ObservacionesVisual":"' . $defectos . '",'
                . '"tablaAfectada":"resultados",'
                . '"idRegistro":"' . $idprueba . '"}';
        $this->Mpruebas->insertarAuditoriaSicov($auditoria_sicov);
    }

    private function auditGases($auditoria_sicov, $idprueba) {
        $dilu = "false";
        if ($this->Mpruebas->buscarResultado($idprueba, 99) !== "") {
            $dilu = "true";
        }
        // $auditoria_sicov['trama'] = '{'
        //         . '"tempRalenti":"' . $this->Mpruebas->buscarResultado($idprueba, 202) . '",'
        //         . '"tempCrucero":"' . $this->Mpruebas->buscarResultado($idprueba, 202) . '",'
        //         . '"rpmRalenti":"' . $this->Mpruebas->buscarResultado($idprueba, 86) . '",'
        //         . '"rpmCrucero":"' . $this->Mpruebas->buscarResultado($idprueba, 91) . '",'
        //         . '"CORalenti":"' . $this->Mpruebas->buscarResultado($idprueba, 88) . '",'
        //         . '"COCrucero":"' . $this->Mpruebas->buscarResultado($idprueba, 93) . '",'
        //         . '"CO2Ralenti":"' . $this->Mpruebas->buscarResultado($idprueba, 89) . '",'
        //         . '"CO2Crucero":"' . $this->Mpruebas->buscarResultado($idprueba, 94) . '",'
        //         . '"O2Ralenti":"' . $this->Mpruebas->buscarResultado($idprueba, 90) . '",'
        //         . '"O2Crucero":"' . $this->Mpruebas->buscarResultado($idprueba, 95) . '",'
        //         . '"HCRalenti":"' . $this->Mpruebas->buscarResultado($idprueba, 87) . '",'
        //         . '"HCCrucero":"' . $this->Mpruebas->buscarResultado($idprueba, 92) . '",'
        //         . '"NORalenti":"",'
        //         . '"NOCrucero":"",'
        //         . '"tempDiesel":"",'
        //         . '"rpmDiesel":"",'
        //         . '"ciclo1":"",'
        //         . '"ciclo2":"",'
        //         . '"ciclo3":"",'
        //         . '"ciclo4":"",'
        //         . '"resultadoValor":"",'
        //         . '"dilucion":"' . $dilu . '",'
        //         . '"revisionVisual":"' . $this->Mpruebas->buscarResultado($idprueba, 902) . '",'
        //         . '"tablaAfectada":"resultados",'
        //         . '"idRegistro":"' . $idprueba . '"}';
        $auditoria_sicov['trama'] = '{'
                . '"temperaturaAmbiente":"' . $this->Mpruebas->buscarResultado($idprueba, 202) . '",'
                . '"rpmRalenti":"' . $this->Mpruebas->buscarResultado($idprueba, 202) . '",'
                . '"tempRalenti":"' . $this->Mpruebas->buscarResultado($idprueba, 86) . '",'
                . '"humedadRelativa":"' . $this->Mpruebas->buscarResultado($idprueba, 91) . '",'
                . '"velocidadGobernada0":"' . $this->Mpruebas->buscarResultado($idprueba, 88) . '",'
                . '"velocidadGobernada1":"' . $this->Mpruebas->buscarResultado($idprueba, 93) . '",'
                . '"velocidadGobernada2":"' . $this->Mpruebas->buscarResultado($idprueba, 89) . '",'
                . '"velocidadGobernada3":"' . $this->Mpruebas->buscarResultado($idprueba, 94) . '",'
                . '"opacidad0":"' . $this->Mpruebas->buscarResultado($idprueba, 90) . '",'
                . '"opacidad1":"' . $this->Mpruebas->buscarResultado($idprueba, 95) . '",'
                . '"opacidad2":"' . $this->Mpruebas->buscarResultado($idprueba, 87) . '",'
                . '"opacidad3":"' . $this->Mpruebas->buscarResultado($idprueba, 92) . '",'
                . '"valorFinal":"",'
                . '"temperaturaInicial":"",'
                . '"temperaturaFinal":"",'
                . '"LTOEStandar":"",'
                . '"HCRalenti":"",'
                . '"CORalenti":"",'
                . '"CO2Ralenti":"",'
                . '"O2Ralenti":"",'
                . '"rpmCrucero":"",'
                . '"HCCrucero":"",'
                . '"COCrucero":"",'
                . '"CO2Crucero":"",'
                . '"O2Crucero":"",'
                . '"dilucion":"' . $dilu . '",'
                . '"catalizador":"",'
                . '"temperaturaPrueba":"",'
                . '"tablaAfectada":"resultados",'
                . '"idRegistro":"' . $idprueba . '"}';
        $this->Mpruebas->insertarAuditoriaSicov($auditoria_sicov);
    }

    private function getCodigoRuntCDA() {
        $sicov = $this->Mpruebas->getSicovRunt();
        $codigoRuntCDA = "";
        if ($sicov->valor == '1') {
            $array = explode('-', $sicov->adicional);
            if ($array[0] === 'INDRA') {
                $array1 = explode('|', $array[1]);
                if (count($array1) > 1) {
                    $codigoRuntCDA = $array1[1];
                }
            } else {
                $array1 = explode('@', $array[1]);
                $array2 = explode(':', $array1[0]);
                $array3 = explode('|', $array1[1]);
                $codigoRuntCDA = $array3[1];
            }
        }
        return $codigoRuntCDA;
    }

    function claveValida() {
        echo $this->Mpruebas->getUsuarioClave($this->input->post('passw'));
    }

    function insertarEventoIndra() {
        $idproveedor = '862';
        $fecha = $this->Mutilitarios->getNow();
        $nombrePrueba = $this->input->post('nombrePrueba');
        $placa = $this->input->post('placa');
        $serial = $this->input->post('serial');
        $tipoEvento = $this->input->post('tipoEvento');
        $cadena = $idproveedor . "|" . $fecha . "|" . $nombrePrueba . "|" . $placa . "|" . $serial . "|" . $tipoEvento . "|";
//        $encrptopenssl = New Opensslencryptdecrypt();
//        $cadena = $encrptopenssl->encrypt_RIJNDAEL($this->formato_texto($cadena));
        $data['idelemento'] = $placa . "-" . $nombrePrueba;
        $data['cadena'] = $cadena;
        $data['fecha'] = $fecha;
        $data['tipo'] = 'e';
        $data['enviado'] = '0';
        $data['respuesta'] = 'Operación pendiente';
        echo $this->MEventosindra->insert($data);
    }

    function validarAuditoria() {
//        $encrptopenssl = New Opensslencryptdecrypt();
        $cadena = 'CbOyODXYtUy9X87jjIiLKsozzcX7Tm4zI4V2wvY7FBFwV5aAo2JaKMVgu2PLa59S';
        $result = $this->desencrypt_MULTI($cadena);

        echo $result;
    }

//    function desencrypt_MULTI($string) {
//        $key = "Jik8ThGv5TrVkIolM45YtfvEdgYhjukL";
//        $iv = "hjsyduiohjsyduio";
//        $output = openssl_decrypt($string, 'AES-256-CBC', $key, 0, $iv);
////        return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $output);
//        return $output;
//    }
    function desencrypt_MULTI($string) {
        $key = "Jik8ThGv5TrVkIolM45YtfvEdgYhjukL";
        $iv = "hjsyduiohjsyduio";
        $output = openssl_decrypt($string, 'AES-256-CBC', $key, 0, $iv);
        return $output;
    }

    function encrypt_MULTI($string) {
        $key = "Jik8ThGv5TrVkIolM45YtfvEdgYhjukL";
        $iv = "hjsyduiohjsyduio";
        $output = openssl_encrypt($string, 'AES-256-CBC', $key, 0, $iv);
        return $output;
    }

    private function formato_texto($cadena) {
        $no_permitidas = array("Ñ", "ñ", "á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹", "'", "");
        $permitidas = array("N", "n", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E", "", "");
        $texto = str_replace($no_permitidas, $permitidas, $cadena);
        return $texto;
    }

}
