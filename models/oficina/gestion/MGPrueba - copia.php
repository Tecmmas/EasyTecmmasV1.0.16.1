<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MGPrueba extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getVehiculoEnPista($idhojapruebas) {
        $query = <<<EOF
            select distinct 
                 CASE
                            WHEN v.idservicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 16px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            END placa,v.numero_placa,v.diseno,v.imagen,h.reinspeccion
            from 
                vehiculos v, 
                hojatrabajo h, 
                pruebas p 
            where 
                h.idhojapruebas=p.idhojapruebas and 
                v.idvehiculo=h.idvehiculo and 
                h.idhojapruebas=$idhojapruebas
EOF;
        $rta = $this->db->query($query);
        if ($rta->num_rows() > 0) {
            $r = $rta->result();
        } else {
            $r = (object)
                    array(
                        'placa' => '');
        }
        return $r[0];
    }

    function pruebasPendientes($idhojapruebas) {
        $query = <<<EOF
            select v.numero_placa,tp.nombre from vehiculos v, hojatrabajo h, pruebas p, tipo_prueba tp where tp.idtipo_prueba=p.idtipo_prueba and h.idhojapruebas=p.idhojapruebas and v.idvehiculo=h.idvehiculo and p.estado=0 and h.idhojapruebas=$idhojapruebas and p.idtipo_prueba<11
EOF;
        $rta = $this->db->query($query);
        if ($rta->num_rows() > 0) {
            $r = $rta->result();
        } else {
            $r = (object)
                    array(
                        '0' => (object) array(
                            'numero_placa' => '',
                            'nombre' => 'NO REGISTRA',
                        )
            );
        }
        return $r;
    }

    function pruebasRechazadas($numero_placa) {
        $query = <<<EOF
            select v.numero_placa,tp.nombre from vehiculos v, hojatrabajo h, pruebas p, tipo_prueba tp where tp.idtipo_prueba=p.idtipo_prueba and h.idhojapruebas=p.idhojapruebas and CURDATE()=date(p.fechafinal) and v.idvehiculo=h.idvehiculo and p.estado=1 and v.numero_placa='$numero_placa'
EOF;
        $rta = $this->db->query($query);
        if ($rta->num_rows() > 0) {
            $r = $rta->result();
        } else {
            $r = (object)
                    array(
                        '0' => (object) array(
                            'numero_placa' => '',
                            'nombre' => 'NO REGISTRA',
                        )
            );
        }
        return $r;
    }

    function getVehiculosRechazados() {
        $query = <<<EOF
            SELECT distinct 
                 CASE
                            WHEN v.idservicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            END placa,
                  if(h.reinspeccion=0,'1ra','2da') ocacion,
                  h.idhojapruebas,v.numero_placa,h.reinspeccion
from hojatrabajo h, vehiculos v 
WHERE 
if(0 IN (select estado FROM pruebas where idhojapruebas=h.idhojapruebas AND estado=0 LIMIT 1),'SI','NO') = 'NO' AND
(CURDATE()=date(h.fechainicial) or CURDATE()=date(h.fechafinal)) and v.idvehiculo=h.idvehiculo and (h.estadototal<>4 and h.estadototal<>5) AND
(if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=1 and estado<>5  order by idprueba desc limit 1 ),6))=1 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=1 order by idprueba desc limit 1 ),6))=3,'R','A')='R' OR
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=2 and estado<>5  order by idprueba desc limit 1 ),6))=1 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=2 and estado<>5  order by idprueba desc limit 1 ),6))=3,'R','A')='R' OR
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=3 and estado<>5  order by idprueba desc limit 1 ),6))=1 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=3 and estado<>5  order by idprueba desc limit 1 ),6))=3,'R','A')='R' OR
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=4 and estado<>5  order by idprueba desc limit 1 ),6))=1 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=4 and estado<>5  order by idprueba desc limit 1 ),6))=3,'R','A')='R' OR
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=5 and estado<>5  and prueba=0 order by idprueba desc limit 1 ),6))=1 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=5 and prueba=0 order by idprueba desc limit 1 ),6))=3,'R','A')='R' OR
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=5 and estado<>5  and prueba=1 order by idprueba desc limit 1 ),6))=1 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=5 and estado<>5  and prueba=1 order by idprueba desc limit 1 ),6))=3,'R','A')='R' OR
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=6 and estado<>5  order by idprueba desc limit 1 ),6))=1 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=6 and estado<>5  order by idprueba desc limit 1 ),6))=3,'R','A')='R' OR
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=7 and estado<>5  order by idprueba desc limit 1 ),6))=1 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=7 and estado<>5  order by idprueba desc limit 1 ),6))=3,'R','A')='R' OR
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=8 and estado<>5  order by idprueba desc limit 1 ),6))=1 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=8 order by idprueba desc limit 1 ),6))=3,'R','A')='R' OR
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=9 and estado<>5  order by idprueba desc limit 1 ),6))=1 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=9 and estado<>5  order by idprueba desc limit 1 ),6))=3,'R','A')='R' OR
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=10 and estado<>5  order by idprueba desc limit 1 ),6))=1 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=10 and estado<>5  order by idprueba desc limit 1 ),6))=3,'R','A')='R')
EOF;
        $rta = $this->db->query($query);
        return $rta;
    }

    function getVehiculosAprobados() {
        $query = <<<EOF
            SELECT distinct 
                 CASE
                            WHEN v.idservicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            END placa,
                  if(h.reinspeccion=0,'1ra','2da') ocacion,
                  h.idhojapruebas,v.numero_placa,h.reinspeccion
from hojatrabajo h, vehiculos v where (CURDATE()=date(h.fechainicial) or CURDATE()=date(h.fechafinal) or CURDATE() in (select date(fechafinal) from pruebas where idhojapruebas=h.idhojapruebas and (estado<>5 and estado<>0) and CURDATE()=date(NOW()))) and v.idvehiculo=h.idvehiculo and (h.estadototal<>4 and h.estadototal<>5) and (h.reinspeccion=0 or h.reinspeccion=1) AND
(if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=1 and estado<>5  order by idprueba desc limit 1 ),6))=2 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=1 and estado<>5  order by idprueba desc limit 1 ),6))=6,'A','R')='A' AND
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=2 and estado<>5  order by idprueba desc limit 1 ),6))=2 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=2 and estado<>5  order by idprueba desc limit 1 ),6))=6,'A','R')='A' AND
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=3 and estado<>5  order by idprueba desc limit 1 ),6))=2 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=3 and estado<>5  order by idprueba desc limit 1 ),6))=6,'A','R')='A' AND
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=4 and estado<>5  order by idprueba desc limit 1 ),6))=2 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=4 and estado<>5  order by idprueba desc limit 1 ),6))=6,'A','R')='A' AND
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=5 and estado<>5  and prueba=0 order by idprueba desc limit 1),6))=2 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=5 and estado<>5  and prueba=0 order by idprueba desc limit 1),6))=6,'A','R')='A' AND
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=5 and estado<>5  and prueba=1 order by idprueba desc limit 1 ),6))=2 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=5 and estado<>5  and prueba=1 order by idprueba desc limit 1 ),6))=6,'A','R')='A' AND
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=6 and estado<>5  order by idprueba desc limit 1 ),6))=2 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=6 and estado<>5  order by idprueba desc limit 1 ),6))=6,'A','R')='A' AND
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=7 and estado<>5  order by idprueba desc limit 1 ),6))=2 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=7 and estado<>5  order by idprueba desc limit 1 ),6))=6,'A','R')='A' AND
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=8 and estado<>5  order by idprueba desc limit 1 ),6))=2 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=8 and estado<>5  order by idprueba desc limit 1 ),6))=6,'A','R')='A' AND
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=9 and estado<>5  order by idprueba desc limit 1 ),6))=2 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=9 and estado<>5  order by idprueba desc limit 1 ),6))=6,'A','R')='A' AND
if(
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=10 and estado<>5  order by idprueba desc limit 1 ),6))=2 OR
(select ifnull((select estado FROM pruebas where idhojapruebas = h.idhojapruebas and idtipo_prueba=10 and estado<>5  order by idprueba desc limit 1 ),6))=6,'A','R')='A')
EOF;
        $rta = $this->db->query($query);

        return $rta;
    }

    function getRechazadoSinCosecutivo() {
        $query = <<<EOF
            select 
                distinct 
                 CASE
                            WHEN v.idservicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            END placa,
                  if(h.reinspeccion=0,'1ra','2da') ocacion,
                  h.idhojapruebas,v.numero_placa,h.reinspeccion
            from 
                vehiculos v, hojatrabajo h 
            where 
            v.idvehiculo=h.idvehiculo AND CURDATE()=date(h.fechafinal) and h.estadototal=3 and 0=(select count(*) from certificados c where c.idhojapruebas=h.idhojapruebas) and (h.reinspeccion=0 or h.reinspeccion=1)  and h.sicov=1 order by h.fechafinal desc
EOF;
        $rta = $this->db->query($query);
        return $rta;
    }

    function getAprobadoSinCosecutivo() {
        $query = <<<EOF
            select 
                distinct 
                 CASE
                            WHEN v.idservicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            END placa,
                  if(h.reinspeccion=0,'1ra','2da') ocacion,
                  h.idhojapruebas,v.numero_placa,h.reinspeccion
            from 
            vehiculos v, hojatrabajo h 
            where v.idvehiculo=h.idvehiculo AND CURDATE()=date(h.fechafinal) and h.estadototal=2 and (h.reinspeccion=0 or h.reinspeccion=1)  and h.sicov=1 order by h.fechafinal desc
EOF;
        $rta = $this->db->query($query);
        return $rta;
    }

    function getVehiculoTerminado() {
        $query = <<<EOF
            select 
                distinct 
                 CASE
                            WHEN v.idservicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 20px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            END placa,
                  if(h.reinspeccion=0,'1ra','2da') ocacion,
                  h.idhojapruebas,v.numero_placa,h.reinspeccion
            from 
                vehiculos v, hojatrabajo h 
            where 
                v.idvehiculo=h.idvehiculo AND CURDATE()=date(h.fechafinal) and 0<>(select count(*) from certificados c where c.idhojapruebas=h.idhojapruebas) and h.estadototal<>5 and (h.reinspeccion=0 or h.reinspeccion=1)  and h.sicov=1 order by h.fechafinal desc
EOF;
        $rta = $this->db->query($query);
        return $rta;
    }

    //---------------------------------------INTEGRACION 20210320 BRAYAN LEON

    public function getPlaca($idhojapruebas) {
        $query = $this->db->query("SELECT v.numero_placa AS placa,v.idtipocombustible AS combustible, v.tipo_vehiculo AS tipovehiculo, 
                                    v.idservicio AS servicio,h.idhojapruebas, 
                                    CASE
                                        WHEN h.estadototal= 1 THEN 'Asignado'
                                        WHEN h.estadototal= 2 THEN 'Aprobado'
                                        WHEN h.estadototal= 3 THEN 'Rechazado'
                                        ELSE 'Abortada'
                                    END AS 'estado',
                                    h.fechainicial AS 'fechainicial', h.fechafinal AS 'fechafinal',
                                    CASE
                                        WHEN h.reinspeccion = 0 THEN 'Tec 1ra'
                                        WHEN h.reinspeccion = 1 THEN 'Tec Rei'
                                        WHEN h.reinspeccion = 4444 THEN 'Pre 1ra'
                                        WHEN h.reinspeccion = 44441 THEN 'Pre Rei'
                                        WHEN h.reinspeccion = 8888 THEN 'Lib'
                                        ELSE 'Error'
                                    END AS 'tipoins',
                                    (SELECT p.fechainicial FROM pruebas p WHERE h.idhojapruebas=p.idhojapruebas ORDER BY 1 DESC LIMIT 1) AS 'pfechainicial',
                                    h.reinspeccion,
                                    IF(h.pin0,h.pin0,'---') AS pin
                                    FROM vehiculos v, hojatrabajo h
                                    WHERE
                                    v.idvehiculo=h.idvehiculo AND h.idhojapruebas=$idhojapruebas ORDER BY h.fechainicial DESC LIMIT 1");
        return $query;
    }

    public function getPruebasprimera($idhojapruebas) {
        $query = $this->db->query("SELECT  p.idprueba, p.fechainicial,
                                    CASE
                                        WHEN p.estado = 0 THEN 'Asignado'
                                        WHEN p.estado = 1 THEN 'Rechazado'
                                        WHEN p.estado = 2 THEN 'Aprobado'
                                        ELSE 'Reasignado'
                                    END AS 'estado',
                                     IFNULL(p.fechafinal,'---') AS fechafinal,
                                     CASE
                                        WHEN p.idtipo_prueba = 1 THEN 'Luces'
                                        WHEN p.idtipo_prueba = 2 THEN 'Opacidad'
                                        WHEN p.idtipo_prueba = 3 THEN 'Gases'
                                        WHEN p.idtipo_prueba = 4 THEN 'Sonometro'
                                        WHEN (p.idtipo_prueba = 5 AND p.prueba = 1) THEN 'Camara-1'
                                        WHEN (p.idtipo_prueba = 5 AND p.prueba = 0) THEN 'Camara-0'
                                        WHEN p.idtipo_prueba = 6 THEN 'Taximetro'
                                        WHEN p.idtipo_prueba = 7 THEN 'Frenos'
                                        WHEN p.idtipo_prueba = 8 THEN 'Visual'
                                        WHEN p.idtipo_prueba = 9 THEN 'Suspension'
                                        ELSE 'Alineador'
                                    END AS 'pruebas',
                                    p.idtipo_prueba
                                    FROM hojatrabajo h, pruebas p
                                    WHERE 
                                    h.idhojapruebas = p.idhojapruebas AND 
                                    (p.idtipo_prueba <> 12 AND p.idtipo_prueba <> 13 AND p.idtipo_prueba <> 14 AND p.idtipo_prueba <> 15 AND p.idtipo_prueba <> 16
                                    AND p.idtipo_prueba <> 17 AND p.idtipo_prueba <> 18 AND p.idtipo_prueba <> 19 AND p.idtipo_prueba <> 21 AND p.idtipo_prueba <> 22 
                                    AND p.idtipo_prueba <> 23) AND (p.estado <> 3 AND p.estado <> 5 and p.estado <> 9)
                                    AND h.idhojapruebas=$idhojapruebas");
        return $query->result();
    }

    public function getPruebassegunda($idhojapruebas) {
        $query = $this->db->query("SELECT  p.idprueba, p.fechainicial,
                                    CASE
                                        WHEN p.estado = 0 THEN 'Asignado'
                                        WHEN p.estado = 1 THEN 'Rechazado'
                                        WHEN p.estado = 2 THEN 'Aprobado'
                                        ELSE 'Reasignado'
                                    END AS 'estado',
                                     IFNULL(p.fechafinal,'---') AS fechafinal,
                                     CASE
                                        WHEN p.idtipo_prueba = 1 THEN 'Luces'
                                        WHEN p.idtipo_prueba = 2 THEN 'Opacidad'
                                        WHEN p.idtipo_prueba = 3 THEN 'Gases'
                                        WHEN p.idtipo_prueba = 4 THEN 'Sonometro'
                                        WHEN (p.idtipo_prueba = 5 AND p.prueba = 1) THEN 'Camara-1'
                                        WHEN (p.idtipo_prueba = 5 AND p.prueba = 0) THEN 'Camara-0'
                                        WHEN p.idtipo_prueba = 6 THEN 'Taximetro'
                                        WHEN p.idtipo_prueba = 7 THEN 'Frenos'
                                        WHEN p.idtipo_prueba = 8 THEN 'Visual'
                                        WHEN p.idtipo_prueba = 9 THEN 'Suspension'
                                        ELSE 'Alineador'
                                    END AS 'pruebas',
                                    p.idtipo_prueba
                                    FROM hojatrabajo h, pruebas p
                                    WHERE 
                                    h.idhojapruebas = p.idhojapruebas AND 
                                    (p.idtipo_prueba <> 12 AND p.idtipo_prueba <> 13 AND p.idtipo_prueba <> 14 AND p.idtipo_prueba <> 15 AND p.idtipo_prueba <> 16
                                    AND p.idtipo_prueba <> 17 AND p.idtipo_prueba <> 18 AND p.idtipo_prueba <> 19 AND p.idtipo_prueba <> 21 AND p.idtipo_prueba <> 22 
                                    AND p.idtipo_prueba <> 23) 
                                    AND h.idhojapruebas=$idhojapruebas AND (p.estado <> 3 AND p.estado <> 5 AND p.estado <> 9)
                                    AND p.fechainicial BETWEEN 
				    DATE_SUB((SELECT p.fechainicial  FROM pruebas p WHERE h.idhojapruebas=p.idhojapruebas AND 
                                    (p.idtipo_prueba <> 12 AND p.idtipo_prueba <> 13 AND p.idtipo_prueba <> 14 AND p.idtipo_prueba <> 15 AND p.idtipo_prueba <> 16
                                    AND p.idtipo_prueba <> 17 AND p.idtipo_prueba <> 18 AND p.idtipo_prueba <> 19 AND p.idtipo_prueba <> 21 AND p.idtipo_prueba <> 22 
                                    AND p.idtipo_prueba <> 23)  ORDER BY 1 DESC LIMIT 1),INTERVAL 45 MINUTE)								
                                    AND 
				    (SELECT p.fechainicial  FROM pruebas p WHERE h.idhojapruebas=p.idhojapruebas AND 
                                    (p.idtipo_prueba <> 12 AND p.idtipo_prueba <> 13 AND p.idtipo_prueba <> 14 AND p.idtipo_prueba <> 15 AND p.idtipo_prueba <> 16
                                    AND p.idtipo_prueba <> 17 AND p.idtipo_prueba <> 18 AND p.idtipo_prueba <> 19 AND p.idtipo_prueba <> 21 AND p.idtipo_prueba <> 22 
                                    AND p.idtipo_prueba <> 23) and p.estado <> 9  ORDER BY 1 DESC LIMIT 1) ");
        return $query->result();
    }

    public function getPruebasVisualprimera($idhojapruebas) {
        $query = $this->db->query("SELECT  p.idprueba,
                                    CASE
                                       WHEN p.idtipo_prueba = 12 THEN 'Th'
                                       WHEN p.idtipo_prueba = 13 THEN 'Profundimetro'
                                       WHEN p.idtipo_prueba = 14 THEN 'Captador'
                                       WHEN p.idtipo_prueba = 15 THEN 'Pie de rey'
                                       WHEN p.idtipo_prueba = 16 THEN 'Detector H'
                                       WHEN p.idtipo_prueba = 17 THEN 'Elevador'
                                       WHEN p.idtipo_prueba = 18 THEN 'Calibrador'
                                       WHEN p.idtipo_prueba = 19 THEN 'Cronometro'
                                       WHEN p.idtipo_prueba = 21 THEN 'Perifecrico Rmp'
                                       WHEN p.idtipo_prueba = 22 THEN 'Periferico Tem'
                                       ELSE 'Bascula'
                                   END AS 'pruebas',
                                   p.idtipo_prueba
                                   FROM hojatrabajo h, pruebas p
                                   WHERE 
                                   h.idhojapruebas = p.idhojapruebas AND 
                                   (p.idtipo_prueba <> 1 AND p.idtipo_prueba <> 2 AND p.idtipo_prueba <> 3 AND p.idtipo_prueba <> 4 AND p.idtipo_prueba <> 5
                                   AND p.idtipo_prueba <> 6 AND p.idtipo_prueba <> 7 AND p.idtipo_prueba <> 8 AND p.idtipo_prueba <> 9 AND p.idtipo_prueba <> 10 ) 
                                   AND h.idhojapruebas=$idhojapruebas and p.estado <> 9");
        return $query->result();
    }

    public function getPruebasVisualsegunda($idhojapruebas) {
        $query = $this->db->query("SELECT  p.idprueba,
                                    CASE
                                       WHEN p.idtipo_prueba = 12 THEN 'Th'
                                       WHEN p.idtipo_prueba = 13 THEN 'Profundimetro'
                                       WHEN p.idtipo_prueba = 14 THEN 'Captador'
                                       WHEN p.idtipo_prueba = 15 THEN 'Pie de rey'
                                       WHEN p.idtipo_prueba = 16 THEN 'Detector H'
                                       WHEN p.idtipo_prueba = 17 THEN 'Elevador'
                                       WHEN p.idtipo_prueba = 18 THEN 'Calibrador'
                                       WHEN p.idtipo_prueba = 19 THEN 'Cronometro'
                                       WHEN p.idtipo_prueba = 21 THEN 'Perifecrico Rmp'
                                       WHEN p.idtipo_prueba = 22 THEN 'Periferico Tem'
                                       ELSE 'Bascula'
                                   END AS 'pruebas',
                                   p.idtipo_prueba
                                   FROM hojatrabajo h, pruebas p
                                   WHERE 
                                   h.idhojapruebas = p.idhojapruebas AND 
                                   (p.idtipo_prueba <> 1 AND p.idtipo_prueba <> 2 AND p.idtipo_prueba <> 3 AND p.idtipo_prueba <> 4 AND p.idtipo_prueba <> 5
                                   AND p.idtipo_prueba <> 6 AND p.idtipo_prueba <> 7 AND p.idtipo_prueba <> 8 AND p.idtipo_prueba <> 9 AND p.idtipo_prueba <> 10 ) 
                                   AND h.idhojapruebas=$idhojapruebas AND p.estado <> 3 
                                   AND p.fechainicial BETWEEN 
                                   DATE_SUB((SELECT p.fechainicial  FROM pruebas p WHERE h.idhojapruebas=p.idhojapruebas AND 
                                   (p.idtipo_prueba <> 1 AND p.idtipo_prueba <> 2 AND p.idtipo_prueba <> 3 AND p.idtipo_prueba <> 4 AND p.idtipo_prueba <> 5
                                   AND p.idtipo_prueba <> 6 AND p.idtipo_prueba <> 7 AND p.idtipo_prueba <> 8 AND p.idtipo_prueba <> 9 AND p.idtipo_prueba <> 10 ) and p.estado <> 9   ORDER BY 1 DESC LIMIT 1)  ,INTERVAL 45 MINUTE)
				   AND 
				   (SELECT p.fechainicial  FROM pruebas p WHERE h.idhojapruebas=p.idhojapruebas AND 
                                   (p.idtipo_prueba <> 1 AND p.idtipo_prueba <> 2 AND p.idtipo_prueba <> 3 AND p.idtipo_prueba <> 4 AND p.idtipo_prueba <> 5
                                   AND p.idtipo_prueba <> 6 AND p.idtipo_prueba <> 7 AND p.idtipo_prueba <> 8 AND p.idtipo_prueba <> 9 AND p.idtipo_prueba <> 10 ) and p.estado <> 9   ORDER BY 1 DESC LIMIT 1)");
        return $query->result();
    }

    function getCreateCaptador($idhojapruebas, $fechainicial) {
        $query = $this->db->query("INSERT INTO pruebas VALUES (NULL,$idhojapruebas,'$fechainicial',0,0,NULL,NULL,1,14)");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function updateHojatrabajo($idhojapruebas) {
        $query = $this->db->query("UPDATE hojatrabajo  SET fechainicial=fechainicial, fechafinal=fechafinal, estadototal = 1 WHERE idhojapruebas=$idhojapruebas ");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function updateVisual($idhojapruebas, $idvisual) {
//         $query = $this->db->query("DELETE FROM resultados  WHERE idprueba=$idvisual;");
        $query = $this->db->query("UPDATE pruebas SET fechainicial=fechainicial, estado=9 WHERE idprueba=$idvisual AND idhojapruebas=$idhojapruebas");
        $query = $this->db->query("INSERT INTO pruebas  VALUES (NULL,$idhojapruebas,(SELECT p.fechainicial FROM pruebas p WHERE p.idprueba=$idvisual LIMIT 1),
                                (SELECT p.prueba FROM pruebas p WHERE p.idprueba=$idvisual LIMIT 1),0,NULL,NULL,1, (SELECT p.idtipo_prueba FROM pruebas p WHERE p.idprueba=$idvisual LIMIT 1))");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
//        $query = $this->db->query("DELETE FROM resultados WHERE idprueba=$idvisual;");
//        $query = $this->db->query("UPDATE pruebas SET fechainicial=fechainicial, fechafinal= null, idmaquina = null, estado=0 WHERE idprueba=$idvisual AND idhojapruebas=$idhojapruebas");
//        if ($query) {
//            return 1;
//        } else {
//            return 0;
//        }
    }

    function updatePruebasVisual($idhojapruebas, $idtipoprueba) {
        $query = $this->db->query("UPDATE pruebas SET fechainicial=fechainicial,fechafinal= null, estado=0, idmaquina = null WHERE idprueba=$idtipoprueba AND idhojapruebas=$idhojapruebas");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function updatePruebas($idhojapruebas, $idtipoprueba) {
        $query = $this->db->query("UPDATE pruebas SET fechainicial=fechainicial, estado=9 WHERE idprueba=$idtipoprueba AND idhojapruebas=$idhojapruebas");
        $query = $this->db->query("INSERT INTO pruebas  VALUES (NULL,$idhojapruebas,(SELECT p.fechainicial FROM pruebas p WHERE p.idprueba=$idtipoprueba LIMIT 1),
                                (SELECT p.prueba FROM pruebas p WHERE p.idprueba=$idtipoprueba LIMIT 1),0,NULL,NULL,1, (SELECT p.idtipo_prueba FROM pruebas p WHERE p.idprueba=$idtipoprueba LIMIT 1))");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
//        $query = $this->db->query("DELETE FROM resultados WHERE idprueba=$idtipoprueba;");
//        $query = $this->db->query("DELETE FROM imagenes WHERE idprueba=$idtipoprueba;");
//        $query = $this->db->query("UPDATE pruebas SET fechainicial=fechainicial,estado=0,fechafinal= null, idmaquina = null WHERE idprueba=$idtipoprueba AND idhojapruebas=$idhojapruebas");
//        if ($query) {
//            return 1;
//        } else {
//            return 0;
//        }
    }

    function deletePerifericos($idhojapruebas, $idtipoprueba) {
        $query = $this->db->query("DELETE FROM pruebas  WHERE idprueba=$idtipoprueba AND idhojapruebas=$idhojapruebas");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    //-------------------------------------------------------- fin de reasignacion individual------------------------------------------//
    function getHojatrabajoPin($estado, $pin, $idhojapruebas) {
        $query = $this->db->query("UPDATE hojatrabajo  SET fechainicial=fechainicial, fechafinal=fechafinal, estadototal = $estado, pin0= $pin WHERE idhojapruebas=$idhojapruebas ");
        return $query;
    }

    function getHojatrabajoPinEstado($pin, $idhojapruebas) {
        $query = $this->db->query("UPDATE hojatrabajo  SET fechainicial=fechainicial, fechafinal=fechafinal, pin0= $pin WHERE idhojapruebas=$idhojapruebas ");
        return $query;
    }

    //-------------------------------------------------------- fin de actualizacion estado y pin ------------------------------------------//
    function Createtaximetro($idhojapruebas, $pfechainicial) {
        $query = $this->db->query("INSERT INTO pruebas VALUES (NULL,$idhojapruebas,'$pfechainicial',0,0,NULL,NULL,1,6)");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function Actualizarvehiculo($placa, $servicio, $combustible, $tipovehiculo) {
        $query = $this->db->query("UPDATE vehiculos v SET v.idservicio=$servicio, v.idtipocombustible=$combustible , v.tipo_vehiculo=$tipovehiculo  WHERE v.numero_placa='$placa'");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function AsignarDisel($idhojapruebas) {
        $query = $this->db->query("UPDATE pruebas p SET p.fechainicial=p.fechainicial, p.idtipo_prueba=2 WHERE p.idtipo_prueba=3 AND p.idhojapruebas=$idhojapruebas");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function AsignarGasolina($idhojapruebas) {
        $query = $this->db->query("UPDATE pruebas p SET p.fechainicial=p.fechainicial, p.idtipo_prueba=3 WHERE p.idtipo_prueba=2 AND p.idhojapruebas=$idhojapruebas");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function deleteTaximetro($idhojapruebas) {
        $query = $this->db->query("DELETE FROM pruebas  WHERE  idhojapruebas=$idhojapruebas AND idtipo_prueba=6;");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function livianoapesado($idhojapruebas, $pfechainicial) {
        $query = $this->db->query("UPDATE pruebas SET idtipo_prueba=2, fechainicial=fechainicial WHERE idtipo_prueba=3 AND idhojapruebas=$idhojapruebas ");
        $query = $this->db->query("DELETE FROM pruebas  WHERE  idhojapruebas=$idhojapruebas AND idtipo_prueba=9 ");
        $query = $this->db->query("INSERT INTO pruebas VALUES (NULL,$idhojapruebas,'$pfechainicial',0,0,NULL,NULL,1,15)");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function pesadoliviano($idhojapruebas, $pfechainicial) {
        $query = $this->db->query("UPDATE pruebas SET idtipo_prueba=3, fechainicial=fechainicial WHERE idtipo_prueba=2 AND idhojapruebas=$idhojapruebas ");
        $query = $this->db->query("DELETE FROM pruebas  WHERE  idhojapruebas=$idhojapruebas AND idtipo_prueba=15 AND fechainicial='$pfechainicial'");
        $query = $this->db->query("INSERT INTO pruebas VALUES (NULL,$idhojapruebas,'$pfechainicial',0,0,NULL,NULL,1,9)");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function motoLiviano($idhojapruebas, $pfechainicial) {
        $query = $this->db->query("DELETE FROM pruebas  WHERE  idhojapruebas=$idhojapruebas AND idtipo_prueba=17 AND fechainicial='$pfechainicial'");
        $query = $this->db->query("INSERT INTO pruebas VALUES (NULL,$idhojapruebas,'$pfechainicial',0,0,NULL,NULL,1,9)");
        $query = $this->db->query("INSERT INTO pruebas VALUES (NULL,$idhojapruebas,'$pfechainicial',0,0,NULL,NULL,1,10)");
        $query = $this->db->query("INSERT INTO pruebas VALUES (NULL,$idhojapruebas,'$pfechainicial',0,0,NULL,NULL,1,16)");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function livianoMoto($idhojapruebas, $pfechainicial) {
        $query = $this->db->query("UPDATE pruebas SET idtipo_prueba=17, fechainicial=fechainicial WHERE idtipo_prueba=16 AND idhojapruebas=$idhojapruebas ");
        $query = $this->db->query("DELETE FROM pruebas  WHERE  idhojapruebas=$idhojapruebas AND idtipo_prueba=9 AND fechainicial='$pfechainicial'");
        $query = $this->db->query("DELETE FROM pruebas  WHERE  idhojapruebas=$idhojapruebas AND idtipo_prueba=10 AND fechainicial='$pfechainicial'");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function pesadoMoto($idhojapruebas, $pfechainicial) {
        $query = $this->db->query("UPDATE pruebas SET idtipo_prueba=17, fechainicial=fechainicial WHERE idtipo_prueba=16 AND idhojapruebas=$idhojapruebas ");
        $query = $this->db->query("UPDATE pruebas SET idtipo_prueba=3, fechainicial=fechainicial WHERE idtipo_prueba=2 AND idhojapruebas=$idhojapruebas ");
        $query = $this->db->query("DELETE FROM pruebas  WHERE  idhojapruebas=$idhojapruebas AND idtipo_prueba=10 AND fechainicial='$pfechainicial'");
        $query = $this->db->query("DELETE FROM pruebas  WHERE  idhojapruebas=$idhojapruebas AND idtipo_prueba=15 AND fechainicial='$pfechainicial'");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function motoPesado($idhojapruebas, $pfechainicial) {
        $query = $this->db->query("UPDATE pruebas SET idtipo_prueba=2, fechainicial=fechainicial WHERE idtipo_prueba=3 AND idhojapruebas=$idhojapruebas ");
        $query = $this->db->query("DELETE FROM pruebas  WHERE  idhojapruebas=$idhojapruebas AND idtipo_prueba=17 AND fechainicial='$pfechainicial'");
        $query = $this->db->query("INSERT INTO pruebas VALUES (NULL,$idhojapruebas,'$pfechainicial',0,0,NULL,NULL,1,10)");
        $query = $this->db->query("INSERT INTO pruebas VALUES (NULL,$idhojapruebas,'$pfechainicial',0,0,NULL,NULL,1,15)");
        $query = $this->db->query("INSERT INTO pruebas VALUES (NULL,$idhojapruebas,'$pfechainicial',0,0,NULL,NULL,1,16)");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    function asignarSonometro($idhojapruebas, $pfechainicial) {
        $query = $this->db->query("INSERT INTO pruebas VALUES (NULL,$idhojapruebas,'$pfechainicial',0,0,NULL,NULL,1,4)");
        if ($query) {
            return 1;
        } else {
            return 0;
        }
    }

    //-------------------------------------------------------- fin de reconfiguracion de pruebas ------------------------------------------//
    function cancelarPruebas($idhojapruebas, $reinspeccion) {
        $data = $this->db->query("SELECT 
                                    DATE_FORMAT(h.fechainicial,'%Y-%m-%d %H:%i:%s') AS fechai, 
                                    p.*
                                    FROM hojatrabajo h, pruebas p 
                                    WHERE 
                                    h.idhojapruebas= p.idhojapruebas AND 
                                    p.idhojapruebas= $idhojapruebas ORDER BY 1 DESC ");
        $rta = $data->result();
        if ($reinspeccion == 1) {
            $query = $this->db->query("UPDATE hojatrabajo h SET h.estadototal=7, h.reinspeccion=0, h.sicov=1, h.fechainicial=h.fechainicial, h.fechafinal=h.fechafinal WHERE h.idhojapruebas=$idhojapruebas");
            foreach ($rta as $value) {
                if ($value->estado == 3) {
                    $this->db->query("UPDATE pruebas p SET p.estado=1, p.fechainicial=p.fechainicial, p.fechafinal=p.fechafinal WHERE p.idhojapruebas=$idhojapruebas and p.estado=3");
                } elseif (($value->fechainicial !== $value->fechai) || $value->estado == 0) {
                    $this->db->trans_start();
                    $this->db->query("UPDATE pruebas p SET  p.estado=5, p.fechainicial=p.fechainicial, p.fechafinal=NOW(), p.idmaquina=$idhojapruebas,p.idusuario=1 WHERE p.idhojapruebas=$idhojapruebas and p.idprueba = $value->idprueba");
                    $this->db->trans_complete();
                }
            }
            return 1;
        } else {
            $query = $this->db->query("UPDATE hojatrabajo h SET h.estadototal=5, h.fechainicial=h.fechainicial, h.fechafinal=h.fechafinal WHERE h.idhojapruebas=$idhojapruebas");
            $query = $this->db->query("UPDATE pruebas p SET p.estado=5, p.fechainicial=p.fechainicial, p.fechafinal=p.fechafinal WHERE p.idhojapruebas=$idhojapruebas");
            return 1;
        }
    }

    //-------------------------------------------------------- fin cancelacion de pruebas ------------------------------------------//
    function registroentrada($where) {
        $query = $this->db->query("SELECT 
                                pp.fecha_prerevision 'Fecharegistro',
                                p.numero_identificacion 'Documentopropietario',
                                CONCAT(IFNULL(CONCAT(p.apellido1,' '),''),IFNULL(CONCAT(p.apellido2,' '),''),IFNULL(p.nombre1,''),IFNULL(CONCAT(' ',p.nombre2),'')) 'Nombrepropietario',
                                IFNULL(p.direccion,'NO REGISTRA') 'Direccionpropietario',
                                IFNULL(p.telefono1,'NO REGISTRA') 'Telefonopropietario',
                                IFNULL(p.correo,'NO REGISTRA') 'Correopropietario',
                                c.numero_identificacion 'Documentocliente',
                                CONCAT(IFNULL(CONCAT(c.apellido1,' '),''),IFNULL(CONCAT(c.apellido2,' '),''),IFNULL(c.nombre1,''),IFNULL(CONCAT(' ',c.nombre2),'')) 'Nombrecliente',
                                IFNULL(c.direccion,'NO REGISTRA') 'Direccioncliente',
                                IFNULL(c.telefono1,'NO REGISTRA') 'Telefonocliente',
                                IFNULL(c.correo,'NO REGISTRA') 'Correocliente',
                                v.numero_placa 'Placa',
                                CASE
                                    WHEN v.tipo_vehiculo = 1 THEN 'Liviano'
                                    WHEN v.tipo_vehiculo = 2 THEN 'Pesado'
                                    ELSE 'Moto'
                                END 'Tipovehiculo',
                                CASE
                                    WHEN v.idservicio = 1 THEN 'Oficial'
                                    WHEN v.idservicio = 2 THEN 'Público'
                                    WHEN v.idservicio = 3 THEN 'Particular'
                                    WHEN v.idservicio = 4 THEN 'Diplomático'	 	     
                                    ELSE 'EspecialRNMA'
                                END 'Servicio',
                                CASE
                                    WHEN v.taximetro = 1 THEN 'Si'
                                    ELSE 'No'
                                END 'Taxi',
                                CASE
                                    WHEN pp.tipo_inspeccion = 1 THEN 'Tecnico-mecánica'
                                    WHEN pp.tipo_inspeccion = 2 THEN 'Preventiva'
                                    ELSE 'Prueba libre'
                                END 'Tipoinspeccion',
                                CASE
                                    WHEN pp.reinspeccion = 0 THEN 'Primera vez'
                                    ELSE 'Segunda vez'
                                END 'Ocacion',
                                CASE
                                    WHEN v.ensenanza = 1 THEN 'SI'
                                    ELSE 'NO'
                                END 'Ensenanza'
                                FROM 
                                pre_prerevision pp,vehiculos v,clientes c, clientes p
                                WHERE
                                pp.numero_placa_ref=v.numero_placa AND
                                c.idcliente=v.idcliente AND
                                p.idcliente=v.idpropietarios AND
                                $where
                                GROUP BY v.numero_placa, pp.reinspeccion
                                ORDER BY pp.fecha_prerevision desc
                                ");
        return $query->result();
    }

}
