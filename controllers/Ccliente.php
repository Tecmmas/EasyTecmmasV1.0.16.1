<?php

defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");

class Ccliente extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('security');
        $this->load->model("Mutilitarios");
        $this->load->model("Mcliente");
        $this->load->model("Mprerevision");
        $this->load->library('Opensslencryptdecrypt');
    }

    public function index() {
           if ($this->session->userdata('IdUsuario') == '') {
            redirect('Cindex');
        }
        $rta = $this->Musuario->getUsuarioId($this->input->post('IdUsuario'));
        $usuario = $rta->result();
        echo json_encode($usuario);
    }

    public function guardarFirma() {
        $archivo = fopen(
                "c:/tcm/prerevision/" . $this->getDia() . "/" . $this->input->post('placa') . "/" .
                "sig_" . $this->input->post('ocasion') . ".dat", "w+b");
        $encrptopenssl = New Opensslencryptdecrypt();
        $firma = $encrptopenssl->encrypt($this->input->post('firma'));
        fwrite($archivo, $firma);
        fclose($archivo);
    }

    public function leerFirma() {
        $encrptopenssl = New Opensslencryptdecrypt();
        $file = "c:/tcm/prerevision/" . $this->getDia() . "/" . $this->input->post('placa') . "/" .
                "sig_" . $this->input->post('ocasion') . ".dat";
        if (file_exists($file)) {
            $firma = file_get_contents($file, true);
            echo $encrptopenssl->decrypt($firma);
        } else {
            echo "NA";
        }
    }

    public function guardarFoto() {
        $archivo = fopen(
                "c:/tcm/prerevision/" . $this->getDia() . "/" . $this->input->post('placa') . "/" .
                $this->input->post('cons') . "_" .
                $this->input->post('ocasion') . ".dat", "w+b");
        $encrptopenssl = New Opensslencryptdecrypt();
//$plain_txt = "this is awsome";
        $foto = $encrptopenssl->encrypt($this->input->post('foto'));  //encypting plain text
//$testdec = $encrptopenssl->decrypt($testenc);    //decrypting plain texx
//$firma = $this->encrypt->enconde();
        fwrite($archivo, $foto);
        fclose($archivo);
        $rutaFoto = $this->getDia() . "|" . $this->input->post('placa') . "|" .
                $this->input->post('cons') . "_" .
                $this->input->post('ocasion');
        echo $rutaFoto;
    }

    public function borrarFoto() {
        $foto = "c:\\tcm\\prerevision\\" . $this->getDia() . "\\" . $this->input->post('placa') . "\\" .
                $this->input->post('cons') . "_" .
                $this->input->post('ocasion') . ".dat";
        unlink($foto);
//        $rutaFoto = $this->getDia() . "|" . $this->input->post('placa') . "|" .
//                $this->input->post('cons') . "_" .
//                $this->input->post('ocasion');
//        $this->Mprerevision->borrarFoto($rutaFoto);
        //echo $rutaFoto;
    }

    public function getDia() {
        $dia = strval($this->Mutilitarios->getNow());
        $dia = str_replace("-", "", $dia);
        $dia = substr($dia, 0, 8);
        return $dia;
    }

}
