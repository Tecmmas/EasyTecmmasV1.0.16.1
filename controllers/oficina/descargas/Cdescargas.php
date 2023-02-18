<?php

defined("BASEPATH") OR exit("No direct script access allowed");
header("Access-Control-Allow-Origin: *");
ini_set("memory_limit", "-1");
set_time_limit(300);

class Cdescargas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper("form");
        $this->load->helper("url");
        $this->load->helper("security");
        $this->load->helper('download');
    }

    public function index() {
        if ($this->session->userdata("IdUsuario") == "" || $this->session->userdata("IdUsuario") == "1024") {
            redirect("Cindex");
        }
        $url = 'http://updateapp.tecmmas.com/actualizaciones.json';
        $archivo = fopen('system/actualizaciones.json', "w+b");
        fwrite($archivo, file_get_contents($url));
        fclose($archivo);
        $json = file_get_contents('system/actualizaciones.json', true);
        $rta ['actu'] = json_decode($json);
        $from = "C:/Apache24/htdocs/et/application/EasyTecmmasV1.0.12";
        $this->load->view('oficina/descargas/Vdescargas', $rta);
    }

    function getDescripcion() {
        $idactualizacion = $this->input->post('idactualizacion');
        $json = file_get_contents('system/actualizaciones.json', true);
        $rta = json_decode($json);
        foreach ($rta as $value) {
            if ($idactualizacion == $value->id) {
                $descripcion = $value->descripcion;
            }
        }
        echo json_encode($descripcion);
    }

    public function Getactualizacion() {
        $idactualizacion = $this->input->post('idactualizacion');
        $json = file_get_contents('system/actualizaciones.json', true);
        $rta = json_decode($json);
        foreach ($rta as $value) {
            if ($idactualizacion == $value->id) {
                $url = $value->url;
                $file = $value->file;
                $version = $value->version;
            }
        }
        $this->createbatgit($url, $file, $version);
    }

    private function createbatgit($url, $file, $version) {
        $data = 'C:\Apache24\htdocs\et\application';
        $cadena = "cd $data
                    git init
                    git clone $url
                    exit";
        $archivo = fopen('system/dwngit.bat', "w+b");
        fwrite($archivo, $cadena);
        fclose($archivo);
        $out = shell_exec('start C:/Apache24/htdocs/et/system/dwngit.bat');
        $to = '"C:/Apache24/htdocs/et/application"';
        $from = "C:/Apache24/htdocs/et/application/$file";
        if (file_exists($from)) {
            $this->getfoldercop($to, $file, $version);
        } else {
            $this->error("Lo sentimos el archivo no se pudo descargar, por favor comunicate con el area de soporte para validar." . $out);
        }
    }

    public function getfoldercop($to, $file, $version) {
        $from = '"' . "C:/Apache24/htdocs/et/application/$file" . '"';
        $cadena = "Xcopy  /e /y $from $to";
        shell_exec($cadena);
        $d = "cd C:/Apache24/htdocs/et/application
                 RD /S /Q $file
                exit";
        $archivo = fopen('system/deletFolder.bat', "w+b");
        fwrite($archivo, $d);
        fclose($archivo);
        shell_exec('start C:/Apache24/htdocs/et/system/deletFolder.bat');
        $dominio = file_get_contents('system/dominio.dat', true);
        $url = 'http://updateapp.tecmmas.com/Actualizaciones/index.php/Cactualizaciones/updateVersion' . '?dominio=' . $dominio. '&version=' . $version ;
        file_get_contents($url);
        redirect("Cindex");
    }

    private function error($mensaje) {
        $this->session->set_flashdata('error', $mensaje);
        redirect("oficina/descargas/Cdescargas");
    }

}
