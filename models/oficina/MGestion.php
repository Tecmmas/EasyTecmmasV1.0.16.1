<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MGestion extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', true);
        $this->myforge = $this->load->dbforge($this->db, TRUE);
    }

    function getVehiculosEnPista() {
        $this->tableSala2();
        $this->createTriguerSalae();
        $this->createTriguergetVisor();
        $this->creteTableVisor();
        $query = <<<EOF
select distinct 
                 CASE
                            WHEN v.servicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            END placa,
                  if(v.reinspeccion=0,'1ra','2da') ocacion,
                  v.idhojapruebas,v.placa  AS numero_placa,v.reinspeccion
            from 
                visor v
            where 
                v.estadototal = 1 and v.sicov = 0 and v.certificado = 0 AND 
                 (
                v.luces = 0 OR 
                v.gases = 0 OR 
                v.opacidad = 0 OR 
                v.sonometro = 0 OR 
                v.visual = 0 OR 
                v.camara0 = 0 OR 
                v.camara1 = 0 OR 
                v.alineacion = 0 OR 
                v.frenos = 0 OR 
                v.suspension = 0 OR 
                v.taximetro = 0
                ) order by v.fecha asc
EOF;
        $rta = $this->db->query($query);
        if ($rta->num_rows() > 0) {
            $rta = $rta->result();
        } else {
            $rta = '';
        }
        return $rta;
    }

    function tableSala2() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'idhojaprueba' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
            ),
            'idprueba' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
            ),
            'idtipo_prueba' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
            ),
            'estado' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
            ),
            'actualizado' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE,
            ),
            'fecha' => array(
                'type' => 'TIMESTAMP',
                'null' => FALSE,
            ),
        );
        $this->myforge->add_key('id', TRUE);
        $this->myforge->add_field($fields);
        $attributes = array('ENGINE' => 'InnoDB');
        $this->myforge->create_table('control_salae', TRUE, $attributes);
    }

    public function createTriguerSalae() {
        $data = $this->db->query("SHOW TRIGGERS");
        $rta = $data->result();
        $val = false;
        foreach ($rta as $value) {
            if ($value->Trigger == 'salae') {
                $val = true;
            }
        }
        if ($val == false) {
            $query = $this->db->query("
            CREATE DEFINER=`root`@`localhost` TRIGGER `salae` AFTER UPDATE ON `pruebas` FOR EACH ROW BEGIN
 	  if NEW.idtipo_prueba=5 then
 		 SET @numcamara=(SELECT count(*) from pruebas where idtipo_prueba=5 AND idhojapruebas=NEW.idhojapruebas AND estado=0 group by idtipo_prueba LIMIT 1); 
	 	  if @numcamara > 0 then
		  		   SET @estado=0;		  
		  else
		  		   SET @estado=2;
		  END if;
		    INSERT INTO control_salae VALUES (NULL,NEW.idhojapruebas,NEW.idprueba,NEW.idtipo_prueba,@estado,'0',NOW());		  
	  else
		    INSERT INTO control_salae VALUES (NULL,NEW.idhojapruebas,NEW.idprueba,NEW.idtipo_prueba,NEW.estado,'0',NOW());	  
	  END if;
END
            "
            );
        }
    }

    function pruebasPendientes() {
        $query = <<<EOF
            select v.numero_placa,tp.nombre from vehiculos v, hojatrabajo h, pruebas p, tipo_prueba tp where tp.idtipo_prueba=p.idtipo_prueba and h.idhojapruebas=p.idhojapruebas and v.idvehiculo=h.idvehiculo and p.estado=0
EOF;
        $rta = $this->db->query($query);
        return $rta;
    }

    function getVehiculosRechazados() {
        $query = <<<EOF
            SELECT distinct 
                 CASE
                            WHEN v.servicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            END placa,
                  if(v.reinspeccion=0,'1ra','2da') ocacion,
                  v.idhojapruebas,v.placa as numero_placa,v.reinspeccion
                from visor v
                WHERE 
                (v.sicov=0 OR v.estadototal=1) AND (v.estadototal<>4 and v.estadototal<>5) AND
                (
                ( 
                (v.luces IS  NULL OR v.luces <> 0) AND 
                (v.gases IS  NULL OR v.gases <> 0) AND
                (v.opacidad IS  NULL OR v.opacidad <> 0) AND
                (v.sonometro IS  NULL OR v.sonometro <> 0) AND
                (v.visual IS  NULL OR v.visual <> 0) AND
                (v.camara0 IS  NULL OR v.camara0 <> 0) AND
                (v.camara1 IS  NULL OR v.camara1 <> 0) AND  
                (v.alineacion IS  NULL OR v.alineacion <> 0) AND 
                (v.frenos IS  NULL OR v.frenos <> 0) AND 
                (v.suspension IS  NULL OR v.suspension <> 0) AND 
                (v.taximetro IS  NULL OR v.taximetro <> 0)                
                
               ) 
                  AND 
                  (
                  v.luces = 1 OR v.gases = 1 OR v.opacidad = 1 OR v.sonometro = 1 OR v.visual = 1 OR v.camara0 = 1 OR v.camara1 = 1 
                  OR v.alineacion = 1 OR  v.frenos = 1 OR v.suspension = 1 OR v.taximetro = 1
                  )
                ) order by v.fecha asc


EOF;
        $rta = $this->db->query($query);

        $rta = $this->db->query($query);
        if ($rta->num_rows() > 0) {
            $rta = $rta->result();
        } else {
            $rta = '';
        }
        return $rta;
    }

    function getVehiculosAprobados() {
        $query = <<<EOF
            SELECT distinct 
                 CASE
                            WHEN v.servicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            END placa,
                  if(v.reinspeccion=0,'1ra','2da') ocacion,
                  v.idhojapruebas,v.placa AS  numero_placa,v.reinspeccion
                FROM
                    visor v
                WHERE
                (v.sicov = 0 OR v.estadototal = 1) AND 
                (v.estadototal <> 4 AND v.estadototal<>5) AND
                IFNULL(v.luces,'2')=2 AND
                IFNULL(v.gases,'2')=2 AND
                IFNULL(v.opacidad,'2')=2 AND
                IFNULL(v.sonometro,'2')=2 AND
                IFNULL(v.visual,'2')=2 AND
                IFNULL(v.camara0,'2')=2 AND
                IFNULL(v.camara1,'2')=2 AND
                IFNULL(v.taximetro,'2')=2 AND
                IFNULL(v.alineacion,'2')=2 AND
                IFNULL(v.frenos,'2')=2 AND
                IFNULL(v.suspension,'2')=2 order by v.fecha asc
EOF;
        $rta = $this->db->query($query);
        if ($rta->num_rows() > 0) {
            $rta = $rta->result();
        } else {
            $rta = '';
        }
        return $rta;
    }

    function getRechazadoSinCosecutivo() {
        $query = <<<EOF
            select 
                distinct 
                 CASE
                            WHEN v.servicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            END placa,
                  if(v.reinspeccion=0,'1ra','2da') ocacion,
                  v.idhojapruebas,v.placa AS numero_placa,v.reinspeccion
            from 
                visor v 
            where 
            v.estadototal = 3 AND  v.sicov=1 order by v.fecha asc
EOF;
        $rta = $this->db->query($query);
        if ($rta->num_rows() > 0) {
            $rta = $rta->result();
        } else {
            $rta = '';
        }
        return $rta;
    }

    function getAprobadoSinCosecutivo() {
        $query = <<<EOF
            select 
                distinct 
                 CASE
                            WHEN v.servicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            WHEN v.servicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',v.placa,'</strong></label>')
                            END placa,
                  if(v.reinspeccion=0,'1ra','2da') ocacion,
                  v.idhojapruebas,v.placa as numero_placa,v.reinspeccion
            from 
            visor v 
            where 
            v.estadototal= 2 and v.sicov=1  order by v.fecha asc
EOF;
        $rta = $this->db->query($query);
        if ($rta->num_rows() > 0) {
            $rta = $rta->result();
        } else {
            $rta = '';
        }
        return $rta;
    }

    function getVehiculoTerminado() {
        $query = <<<EOF
            select 
                distinct 
                IFNULL((SELECT c.correo FROM clientes c WHERE v.idcliente = c.idcliente LIMIT 1),'') AS 'email',
                IFNULL((SELECT p.idpre_prerevision FROM pre_prerevision p WHERE p.numero_placa_ref = v.numero_placa LIMIT 1 ),'') AS 'idprerevision',
                 CASE
                            WHEN vr.servicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',vr.placa,'</strong></label>')
                            WHEN vr.servicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',vr.placa,'</strong></label>')
                            WHEN vr.servicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',vr.placa,'</strong></label>')
                            WHEN vr.servicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 2px"><strong>',vr.placa,'</strong></label>')
                            WHEN vr.servicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 2px"><strong>',vr.placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 14px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 2px"><strong>',vr.placa,'</strong></label>')
                            END placa,
                  if(vr.reinspeccion=0,'1ra','2da') ocacion,
                  vr.idhojapruebas,vr.placa as numero_placa,vr.reinspeccion,vr.estadototal
            from 
                vehiculos v, visor vr 
            where 
               vr.placa = v.numero_placa  AND (vr.estadototal=4 or vr.estadototal=7) AND vr.certificado = 1 and vr.sicov=1 order BY vr.fecha asc
EOF;
        //and 0<>(select count(*) from certificados c where c.idhojapruebas=h.idhojapruebas) 
        $rta = $this->db->query($query);
        if ($rta->num_rows() > 0) {
            $rta = $rta->result();
        } else {
            $rta = '';
        }
        return $rta;
    }

    function getAuditoria() {
        $this->borrarRegAudit();
        $query = <<<EOF
            select * from cron_audit where notificado=0;
EOF;
        $rta = $this->db->query($query);
        if ($rta->num_rows() > 0) {
            $rta = $rta->result();
            foreach ($rta as $r) {
                $this->db->where('id', $r->id);
                $this->db->set('notificado', 1, false);
                $this->db->update("cron_audit");
            }
            return $rta;
        } else {
            return '';
        }
    }

    function borrarRegAudit() {
        $query = <<<EOF
            delete from cron_audit where CURDATE()<>date(fecha)
EOF;
        $this->db->query($query);
    }

    function getPlacaSalaE() {
        $this->borrarPlacaSalaE();
        $query = <<<EOF
                    SELECT * FROM control_salae c WHERE c.actualizado=0 and c.estado<>5
EOF;
        $rta = $this->db->query($query);
        if ($rta->num_rows() > 0) {
            $rta = $rta->result();
            foreach ($rta as $r) {
                $this->db->where('id', $r->id);
                $this->db->set('actualizado', 1, false);
                $this->db->update("control_salae");
            }
            return $rta;
        } else {
            return '';
        }
    }

    function borrarPlacaSalaE() {
        $query = <<<EOF
            delete from control_salae where CURDATE()<> DATE_FORMAT(fecha,'%Y-%m-%d')
EOF;
        $this->db->query($query);
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

    public function createTriguergetVisor() {
        $data = $this->db->query("SHOW TRIGGERS");
        $rta = $data->result();
        $val = false;
        foreach ($rta as $value) {
            if ($value->Trigger == 'getVisor') {
                $val = true;
            }
        }
        if ($val == false) {
            $query = $this->db->query("
            CREATE DEFINER=`root`@`localhost` TRIGGER `getVisor` BEFORE UPDATE ON `pruebas` FOR EACH ROW BEGIN

                IF NEW.estado <> 5 AND NEW.estado <> 9 THEN 
                SET @reinspeccion= (SELECT h.reinspeccion FROM hojatrabajo h   WHERE h.idhojapruebas = NEW.idhojapruebas LIMIT 1);
                SET @idhojapruebas= (SELECT h.idhojapruebas FROM hojatrabajo h WHERE h.idhojapruebas = NEW.idhojapruebas LIMIT 1);
                   if OLD.idtipo_prueba = 1 THEN
                       UPDATE visor v SET v.luces = NEW.estado, v.fecha = v.fecha WHERE v.idhojapruebas = @idhojapruebas AND v.reinspeccion = @reinspeccion;
                   END IF; 
                   if OLD.idtipo_prueba = 2 THEN
                       UPDATE visor v SET v.opacidad = NEW.estado, v.fecha = v.fecha WHERE v.idhojapruebas = @idhojapruebas AND v.reinspeccion = @reinspeccion;
                   END IF;
                   if OLD.idtipo_prueba = 3 THEN
                       UPDATE visor v SET v.gases = NEW.estado, v.fecha = v.fecha WHERE v.idhojapruebas = @idhojapruebas AND v.reinspeccion = @reinspeccion;
                   END IF;
                   if OLD.idtipo_prueba = 4 THEN
                       UPDATE visor v SET v.sonometro = NEW.estado, v.fecha = v.fecha WHERE v.idhojapruebas = @idhojapruebas AND v.reinspeccion = @reinspeccion;
                   END IF;
                   if OLD.idtipo_prueba = 5 AND OLD.prueba = 0 THEN
                       UPDATE visor v SET v.camara0 = NEW.estado, v.fecha = v.fecha WHERE v.idhojapruebas = @idhojapruebas AND v.reinspeccion = @reinspeccion;
                   END IF;
                   if OLD.idtipo_prueba = 5 AND OLD.prueba = 1 THEN
                       UPDATE visor v SET v.camara1 = NEW.estado, v.fecha = v.fecha WHERE v.idhojapruebas = @idhojapruebas AND v.reinspeccion = @reinspeccion;
                   END IF;
                   if OLD.idtipo_prueba = 6 THEN
                       UPDATE visor v SET v.taximetro = NEW.estado, v.fecha = v.fecha WHERE v.idhojapruebas = @idhojapruebas AND v.reinspeccion = @reinspeccion;
                   END IF;
                   if OLD.idtipo_prueba = 7 THEN
                       UPDATE visor v SET v.frenos = NEW.estado, v.fecha = v.fecha WHERE v.idhojapruebas = @idhojapruebas AND v.reinspeccion = @reinspeccion;
                   END IF;
                   if OLD.idtipo_prueba = 8 THEN
                       UPDATE visor v SET v.visual = NEW.estado, v.fecha = v.fecha WHERE v.idhojapruebas = @idhojapruebas AND v.reinspeccion = @reinspeccion;
                   END IF;
                   if OLD.idtipo_prueba = 9 THEN
                       UPDATE visor v SET v.suspension = NEW.estado, v.fecha = v.fecha WHERE v.idhojapruebas = @idhojapruebas AND v.reinspeccion = @reinspeccion;
                   END IF;
                   if OLD.idtipo_prueba = 10 THEN
                       UPDATE visor v SET v.alineacion = NEW.estado, v.fecha = v.fecha WHERE v.idhojapruebas = @idhojapruebas AND v.reinspeccion = @reinspeccion;
                   END IF;
                END IF; 
             END
            "
            );
        }
        $this->borrarVisor();
    }

    function borrarVisor() {

        $data = $this->db->query("SHOW TABLES");
        $rta = $data->result();
        $val = false;
        foreach ($rta as $value) {
            if ($value->Tables_in_tecmmas_bd == 'visor') {
                $val = true;
            }
        }
        if ($val == true) {
            $query = <<<EOF
            delete from visor where CURDATE()<>date(fecha)
EOF;
            $this->db->query($query);
        }
    }

}
