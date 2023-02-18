<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mprueba extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', true);
        $this->myforge = $this->load->dbforge($this->db, TRUE);
    }

    function get($data) {
        $idhojapruebas = $data['idhojapruebas'];
        $idtipo_prueba = $data['idtipo_prueba'];
        $order = $data['order'];
        $query = $this->db->query("
            SELECT
                *
            FROM
                pruebas p
            WHERE
                p.idhojapruebas = $idhojapruebas and
                p.idtipo_prueba = $idtipo_prueba and
                (p.estado <> 5 and p.estado <> 9)
            ORDER BY 1 $order
            LIMIT 1");
        return $query;
    }

    function getMaq($data) {
        $idhojapruebas = $data['idhojapruebas'];
        $idtipo_prueba = $data['idtipo_prueba'];
//        $fechainicial = $data['fechainicial'];
        $order = $data['order'];
        if ($order == 'DESC') {
            $query = $this->db->query("
            SELECT 
            IF((SELECT COUNT(*) FROM pruebas p WHERE p.idhojapruebas=$idhojapruebas AND p.estado<>5 and p.estado <> 9 AND p.idtipo_prueba=$idtipo_prueba)>1,
            (SELECT p.idmaquina FROM pruebas p WHERE p.idhojapruebas=$idhojapruebas AND p.estado<>5 and p.estado <> 9 AND p.estado<>3 AND p.idtipo_prueba=$idtipo_prueba ORDER BY p.idprueba DESC LIMIT 1),'') idmaquina,
            IF((SELECT COUNT(*) FROM pruebas p WHERE p.idhojapruebas=$idhojapruebas AND p.estado<>5 and p.estado <> 9 AND p.idtipo_prueba=$idtipo_prueba)>1,
            (SELECT p.idusuario FROM pruebas p WHERE p.idhojapruebas=$idhojapruebas AND p.estado<>5 and p.estado <> 9 AND p.estado<>3 AND p.idtipo_prueba=$idtipo_prueba ORDER BY p.idprueba DESC LIMIT 1),'') idusuario");
        } else {
            $query = $this->db->query("
            SELECT 
            (SELECT p.idmaquina FROM pruebas p WHERE p.idhojapruebas=$idhojapruebas AND p.estado<>5 and p.estado <> 9 AND p.idtipo_prueba=$idtipo_prueba ORDER BY p.idprueba LIMIT 1) idmaquina,
            (SELECT p.idusuario FROM pruebas p WHERE p.idhojapruebas=$idhojapruebas AND p.estado<>5 and p.estado <> 9 AND p.idtipo_prueba=$idtipo_prueba ORDER BY p.idprueba LIMIT 1) idusuario");
        }
        return $query;
    }

//    function getMaq($data) {
//        $idhojapruebas = $data['idhojapruebas'];
//        $idtipo_prueba = $data['idtipo_prueba'];
//        $fechainicial = $data['fechainicial'];
//        $order = $data['order'];
//        $query = $this->db->query("
//            SELECT
//                *
//            FROM
//                pruebas p
//            WHERE
//                p.idhojapruebas = $idhojapruebas and
//                p.idtipo_prueba = $idtipo_prueba and
//                p.fechainicial = '$fechainicial' and
//                (p.estado <> 5)
//            ORDER BY 1 $order
//            LIMIT 1");
//        return $query;
//    }

    function get5($data) {
        $idhojapruebas = $data['idhojapruebas'];
        $idtipo_prueba = $data['idtipo_prueba'];
        $order = $data['order'];
        $query = $this->db->query("
            SELECT
                *
            FROM
                pruebas p
            WHERE
                p.idhojapruebas = $idhojapruebas and
                p.idtipo_prueba = $idtipo_prueba and
                (p.estado <> 0 and p.estado <> 5 and p.estado <> 9)
            ORDER BY 1 $order
            LIMIT 2");
        return $query;
    }

    function get55maquina($data) {
        $idhojapruebas = $data['idhojapruebas'];
        $idtipo_prueba = $data['idtipo_prueba'];
        $order = $data['order'];
        $query = $this->db->query("
            SELECT
                m.*
            FROM
                pruebas p, maquina m
            WHERE
                p.idhojapruebas = $idhojapruebas and
                m.idmaquina=p.idmaquina and
                m.idtipo_prueba=$idtipo_prueba and
                p.idtipo_prueba = '55' and
                (p.estado <> 0 and p.estado <> 5 and p.estado <> 9)
            ORDER BY 1 $order
            LIMIT 1");
        return $query;
    }

    function getInspectores($data) {
        $idhojapruebas = $data['idhojapruebas'];
        $query = $this->db->query("
            SELECT 
                CONCAT(tp.nombre,'-',u.nombres,' ',u.apellidos) operarios
            FROM 
                pruebas p,usuarios u,tipo_prueba tp,maquina m
            WHERE 
                p.idhojapruebas=$idhojapruebas AND
                m.idtipo_prueba=tp.idtipo_prueba AND
                p.idmaquina=m.idmaquina AND
                u.IdUsuario=p.idusuario 
            GROUP BY 1");
        return $query;
    }

    function getInspector($data) {
        $idhojapruebas = $data['idhojapruebas'];
        $query = $this->db->query("
            SELECT 
                CONCAT(tp.nombre,'-',u.nombres,' ',u.apellidos) operarios
            FROM 
                pruebas p,usuarios u,tipo_prueba tp,maquina m
            WHERE 
                p.idhojapruebas=$idhojapruebas AND
                m.idtipo_prueba=tp.idtipo_prueba AND
                p.idmaquina=m.idmaquina AND
                u.IdUsuario=p.idusuario 
            GROUP BY 1");
        return $query;
    }

    function getLast($data) {
        $idhojapruebas = $data['idhojapruebas'];
        $query = $this->db->query("
            SELECT
                *
            FROM
                pruebas p
            WHERE
                p.idhojapruebas = $idhojapruebas and
                (p.estado <> 0 and p.estado <> 5 and p.estado <> 9)
            ORDER BY p.fechainicial DESC
            LIMIT 1");
        return $query;
    }

    function getLastFecha($data, $order) {
        $idhojapruebas = $data['idhojapruebas'];
        $query = $this->db->query("
            SELECT 
                max(p.fechafinal) fechafinal
                FROM 
                pruebas p 
                WHERE 
                p.idhojapruebas=$idhojapruebas AND
                (p.estado <> 0 and p.estado <> 5 and p.estado <> 9)
                GROUP BY p.fechainicial ORDER BY p.fechainicial $order LIMIT 1");
        return $query;
    }

    function getFechaInicial($idhojapruebas, $reins) {
        $l = '';
        if ($reins == '1' || $reins == '44441') {
            $l = ',1';
        }
        $query = $this->db->query("
            SELECT  
                p.fechainicial,COUNT(*) c
            FROM 
            pruebas p 
            WHERE 
            p.idhojapruebas=$idhojapruebas
            GROUP BY 1 ORDER BY 2 DESC LIMIT 1$l");
        $r = $query->result();
        return $r[0]->fechainicial;
    }

    function insert($data, $juez) {
        $idhojapruebas = $data['idhojapruebas'];
        $fechainicial = $data['fechainicial'];
        $prueba = $data['prueba'];
        $estado = $data['estado'];
        $fechafinal = $data['fechafinal'];
        $idmaquina = null;
        $idusuario = $data['idusuario'];
        $idtipo_prueba = $data['idtipo_prueba'];
        if ($juez === "1") {
            $data['auditoria'] = $this->encrypt_MULTI($idhojapruebas . "|" . $fechainicial . "|" . $prueba . "|" . $estado . "|" . $fechafinal . "|" . $idmaquina . "|" . $idusuario . "|" . $idtipo_prueba);
        }
        $this->db->insert('pruebas', $data);
    }

    function update($data) {
        unset($data['fechainicial']);
        unset($data['fechafinal']);
        unset($data['idusuario']);
//        unset($data['idmaquina']);
        unset($data['estado']);
        $this->db->set('fechainicial', 'fechainicial', FALSE);
        $this->db->set('fechafinal', 'fechafinal', FALSE);
        $this->db->set('idusuario', 'idusuario', FALSE);
//        $this->db->set('idmaquina', 'idmaquina', FALSE);
        $this->db->set('estado', '3', FALSE);
        $this->db->where('idhojapruebas', $data['idhojapruebas']);
        $this->db->where('idtipo_prueba', $data['idtipo_prueba']);
        $this->db->where('prueba', $data['prueba']);
        $this->db->where('estado', '1');
        $this->db->where('estado <>', '5');
        $this->db->where('estado <>', '9');
        $this->db->update('pruebas', $data);
    }

    function update_($data) {
        unset($data['fechainicial']);
        unset($data['fechafinal']);
        unset($data['idusuario']);
        unset($data['estado']);
        $this->db->set('fechainicial', 'fechainicial', FALSE);
        $this->db->set('fechafinal', 'fechafinal', FALSE);
        $this->db->set('idusuario', 'idusuario', FALSE);
        $this->db->set('estado', '3', FALSE);
        $this->db->where('idhojapruebas', $data['idhojapruebas']);
        $this->db->where('idtipo_prueba', $data['idtipo_prueba']);
        $this->db->where('prueba', $data['prueba']);
        $this->db->where('estado <>', '5');
        $this->db->where('estado <>', '9');
        $this->db->update('pruebas', $data);
    }

    function update_B($idprueba) {
        $this->db->set('fechainicial', 'fechainicial', FALSE);
        $this->db->set('fechafinal', 'fechafinal', FALSE);
        $this->db->set('idusuario', 'idusuario', FALSE);
        $this->db->set('estado', '1', FALSE);
        $this->db->where('idprueba', $idprueba);
        $this->db->update('pruebas');
    }

    function eliminarPrueba($data) {
        $this->db->where('idhojapruebas', $data['idhojapruebas']);
        $this->db->where('fechainicial', $data['fechainicial']);
        $this->db->where('idtipo_prueba', $data['idtipo_prueba']);
        $this->db->delete('pruebas');
    }

    function eliminarPruebaID($data) {
        $this->db->where('idprueba', $data['idprueba']);
        $this->db->delete('pruebas');
    }

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

    function insertVisor($vehiculo) {
        $this->db->trans_start();
        $this->creteTableVisor();
        if ($vehiculo['luces'] == 0) {
            $luces = 'NULL';
        } else if ($vehiculo['luces'] == 1) {
            $luces = 0;
        }
        if ($vehiculo['gases'] == 0) {
            $gases = 'NULL';
        } else if ($vehiculo['gases'] == 1) {
            $gases = 0;
        }
        if ($vehiculo['opacidad'] == 0) {
            $opacidad = 'NULL';
        } else if ($vehiculo['opacidad'] == 1) {
            $opacidad = 0;
        }
        if ($vehiculo['sonometro'] == 0) {
            $sonometro = 'NULL';
        } else if ($vehiculo['sonometro'] == 1) {
            $sonometro = 0;
        }
        if ($vehiculo['visual'] == 0) {
            $visual = 'NULL';
        } else if ($vehiculo['visual'] == 1) {
            $visual = 0;
        }
        if ($vehiculo['camara'] == 0) {
            $camara = 'NULL';
        } else if ($vehiculo['camara'] == 1) {
            $camara = 0;
        }
        if ($vehiculo['alineacion'] == 0) {
            $alineacion = 'NULL';
        } else if ($vehiculo['alineacion'] == 1) {
            $alineacion = 0;
        }
        if ($vehiculo['frenos'] == 0) {
            $frenos = 'NULL';
        } else if ($vehiculo['frenos'] == 1) {
            $frenos = 0;
        }
        if ($vehiculo['suspension'] == 0) {
            $suspension = 'NULL';
        } else if ($vehiculo['suspension'] == 1) {
            $suspension = 0;
        }
        if ($vehiculo['taximetro'] == 0) {
            $taximetro = 'NULL';
        } else if ($vehiculo['taximetro'] == 1) {
            $taximetro = 0;
        }
//        $data = array(
//            'idhojapruebas' => $vehiculo['idhojapruebas'],
//            'reinspeccion' => $vehiculo['reinspeccion'],
//            'servicio' => $vehiculo['servicio'],
//            'placa' => $vehiculo['placa'],
//            'luces' => $luces,
//            'gases' => $gases,
//            'opacidad' => $opacidad,
//            'sonometro' => $sonometro,
//            'visual' => $visual,
//            'camara0' => $camara,
//            'camara1' => $camara,
//            'alineacion' => $alineacion,
//            'frenos' => $frenos,
//            'suspension' => $suspension,
//            'taximetro' => $taximetro,
//            'estadototal' => 1,
//            'sicov' => 0,
//            'certificado' => 0,
//        );
//        var_dump($data);
        //$cadena = "insert visor values (null, " . $vehiculo['idhojapruebas'] . ", " . $vehiculo['reinspeccion'] . "," . $vehiculo['servicio'] . ", '" . $vehiculo['placa'] . "','" . $luces . "', " . $gases . "," . $opacidad . ", " . $sonometro . ", " . $visual . ", " . $camara . ", " . $camara . ", " . $alineacion . ", " . $frenos . ", " . $suspension . ", " . $taximetro . ",1,0,0)";
        $cadena = $this->db->query("insert visor values (null, " . $vehiculo['idhojapruebas'] . ", " . $vehiculo['reinspeccion'] . "," . $vehiculo['servicio'] . ", '" . $vehiculo['placa'] . "'," . $luces . ", " . $gases . "," . $opacidad . ", " . $sonometro . ", " . $visual . ", " . $camara . ", " . $camara . ", " . $alineacion . ", " . $frenos . ", " . $suspension . ", " . $taximetro . ",0,0,1, NOW())");
        echo json_encode($cadena);
        $this->db->trans_complete();
    }

    public function creteTableVisor() {
        $fields = array(
            'idvisor' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'idhojapruebas' => array(
                'type' => 'INT',
                'constraint' => '10',
                'null' => FALSE,
            ),
            'reinspeccion' => array(
                'type' => 'INT',
                'constraint' => '10',
                'null' => FALSE,
            ),
            'servicio' => array(
                'type' => 'INT',
                'constraint' => '10',
                'null' => FALSE,
            ),
            'placa' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => FALSE,
            ),
            'luces' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'gases' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'opacidad' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'sonometro' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'visual' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'camara0' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'camara1' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'alineacion' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'frenos' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'suspension' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'taximetro' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'certificado' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'sicov' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'estadototal' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'fecha' => array(
                'type' => 'TIMESTAMP',
                'null' => FALSE,
            ),
        );
        $this->myforge->add_key('idvisor', TRUE);
        $this->myforge->add_field($fields);
        $attributes = array('ENGINE' => 'MyISAM');
        $this->myforge->create_table('visor', TRUE, $attributes);
    }

}
