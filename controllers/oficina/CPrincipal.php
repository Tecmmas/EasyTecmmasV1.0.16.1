<?php

defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
ini_set('memory_limit', '-1');
set_time_limit(0);

class CPrincipal extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('security');
        $this->load->model("dominio/Mpre_atributo");
        $this->load->model("oficina/MPrincipal");
        $this->load->library('Opensslencryptdecrypt');
    }

    private $usuario = "";
    private $contrasena = "";

    public function index() {
        if ($this->session->userdata('IdUsuario') == '' || $this->session->userdata('IdUsuario') == '1024') {
            redirect('Cindex');
        }
//        $this->session->sess_destroy();
//        $data['usuario'] = $this->usuario;
//        $data['contrasena'] = $this->contrasena
        date_default_timezone_set('America/bogota');
        $rta ['fecha'] = date("Y-m-d");
//        $dominio = file_get_contents('system/dominio.dat', true);
//        $url = 'http://' . $dominio . '/cda/index.php/Cadicionales/selMetrologia';
//        $d = @get_headers($url);
//        if ($d[0] == "HTTP/1.1 404 Not Found") {
//            $rta ['metrologia'] = [];
//        } else {
//            $rta ['metrologia'] = json_decode(file_get_contents($url));
//        }
        $encrptopenssl = New Opensslencryptdecrypt();
        
        $json = $encrptopenssl->decrypt(file_get_contents('system/oficina.json', true), true);
        $ofc = json_decode($json, true);
        $this->session->set_userdata('actualizaciones', '0');
        $this->session->set_userdata('backup', '0');
        $this->session->set_userdata('agop', '0');
        foreach ($ofc as $d) {
            if ($d['nombre'] == 'actualizaciones') {
                $this->session->set_userdata('actualizaciones', $d['valor']);
            }
            if ($d['nombre'] == 'backup') {
                $this->session->set_userdata('backup', $d['valor']);
            }
            if ($d['nombre'] == 'agop') {
                $this->session->set_userdata('agop', $d['valor']);
            }
        }
        $this->setAtr("jefe_pista", "jefe_pista", "0");
        $this->load->view('oficina/VPrincipal', $rta);
    }

    private function setAtr($id, $label, $orden) {
        $data['id'] = $id;
        $data['label'] = $label;
        $data['orden'] = $orden;
        $r = $this->Mpre_atributo->getXid($data);
        if ($r->num_rows() == 0) {
            $this->Mpre_atributo->insert($data);
        }
    }

    public function validar() {
        $this->form_validation->set_rules('usuario', 'Usuario', 'required');
        $this->form_validation->set_rules('contrasena', 'Contrasena', 'required');
        if ($this->form_validation->run()) {
            $this->usuario = $this->input->post('usuario');
            $this->contrasena = $this->input->post('contrasena');
            $rta = $this->Mindex->puede_entrar($this->usuario, $this->contrasena);
            if ($rta->num_rows() > 0) {
                $user = $rta->result();
                if (strcmp($user[0]->estado, '0') == 0) {
                    $this->error('Usuario inactivo');
                }
                $session_data = array(
                    'usuario' => $this->usuario,
                    'nombre' => $user[0]->nombres,
                    'idUsuario' => $user[0]->idUsuario
                );
                $this->session->set_userdata($session_data);
                //Determinar rol de usuario ADMINISTRADOR-ADMINISTRATIVO-OPERARIO-AUDITOR
                redirect(base_url() . 'index.php/CPistaPrincipal');
            } else {
                $this->error('Nombre de usuario o contraseña inválidos');
            }
        } else {
            $this->index();
        }
    }

    private function error($mensaje) {
        $this->session->set_flashdata('error', $mensaje);
        redirect(base_url() . 'index.php/Cindex');
    }

//    function getInformacion() {
//        $url = 'http://updateapp.tecmmas.com/Informacion/Info.dat';
//        $encrptopenssl = New Opensslencryptdecrypt();
//        $archivo = fopen('system/informacion.dat', "w+b");
//        fwrite($archivo, $encrptopenssl->encrypt(file_get_contents($url, true), true));
//        fclose($archivo);
//    }

}
