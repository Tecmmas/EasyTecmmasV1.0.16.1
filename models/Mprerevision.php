<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mprerevision extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getConsecutivo($tipo) {
        $result = $this->db->query("select count(*) + 1 cons from pre_prerevision where tipo_inspeccion=$tipo");
        $rta = $result->result();
        return $rta[0]->cons;
    }

    function guardarPrerevision($pre_prerevision) {
        $this->updatePre($pre_prerevision);
        echo $this->db->insert("pre_prerevision", $pre_prerevision);
        return $this->db->insert_id();
    }

    function updatePre($pre_prerevision) {
        $numero_placa_ref = $pre_prerevision['numero_placa_ref'];
        $tipo_inspeccion = $pre_prerevision['tipo_inspeccion'];
        $reinspeccion = $pre_prerevision['reinspeccion'];
//        $fecha_prerevision = $pre_prerevision['fecha_prerevision'];
        $this->db->query("
                        UPDATE
                        pre_prerevision p
                        SET
                        p.numero_placa_ref=CONCAT('$numero_placa_ref','-C'),
                        p.fecha_prerevision=p.fecha_prerevision 
                        WHERE 
                        p.numero_placa_ref='$numero_placa_ref' AND 
                        DATE_FORMAT(p.fecha_prerevision, '%Y-%m-%d')  = CURDATE() AND 
                        p.tipo_inspeccion = $tipo_inspeccion AND p.reinspeccion = $reinspeccion");
    }

    function guardarPreDato($preDato, $preAtributo, $preZona) {
        $rtaAtributo = $this->buscarPreAtributo($preAtributo['id']);
        if ($rtaAtributo->num_rows() !== 0) {
            $this->actualizarPreAtributo($preAtributo);
            $rta = $rtaAtributo->result();
            $preDato['idpre_atributo'] = $rta[0]->idpre_atributo;
        } else {
            $preDato['idpre_atributo'] = $this->crearPreAtributo($preAtributo);
        }
        $rtaZona = $this->buscarPreZona($preZona['nombre']);
        if ($rtaZona->num_rows() !== 0) {
            $rta = $rtaZona->result();
            $preDato['idpre_zona'] = $rta[0]->idpre_zona;
        } else {
            $preDato['idpre_zona'] = $this->crearPreZona($preZona);
        }
        $this->db->insert("pre_dato", $preDato);
    }

    function buscarPreAtributo($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('pre_atributo');
        return $query;
    }

    function crearPreAtributo($preAtributo) {
        echo $this->db->insert("pre_atributo", $preAtributo);
        return $this->db->insert_id();
    }

    function actualizarPreAtributo($preAtributo) {
        $this->db->where('id', $preAtributo['id']);
        echo $this->db->update("pre_atributo", $preAtributo);
    }

    function buscarPreZona($nombre) {
        $this->db->where('nombre', $nombre);
        $query = $this->db->get('pre_zona');
        return $query;
    }

    function crearPreZona($preZona) {
        echo $this->db->insert("pre_zona", $preZona);
        return $this->db->insert_id();
    }

    function guardarVehiculo($vehiculo) {
        $rtaVehiculo = $this->buscarVehiculo($vehiculo['numero_placa']);
        if ($rtaVehiculo->num_rows() !== 0) {
            $this->db->where('numero_placa', $vehiculo['numero_placa']);
            echo $this->db->update('vehiculos', $vehiculo);
        } else {
            $vehiculo["diametro_escape"] = "0";
            echo $this->db->insert("vehiculos", $vehiculo);
        }
    }

    function buscarVehiculo($numero_placa) {
        $this->db->where('numero_placa', $numero_placa);
        $query = $this->db->get('vehiculos');
        return $query;
    }

    function cargarVehiculo($numero_placa) {
        $result = $this->db->query("select 
                                    v.numero_placa,
                                    cli.numero_identificacion,
                                    cli.tipo_identificacion,
                                    cli.nombre1,
                                    cli.apellido1,
                                    cli.telefono1,
                                    ifnull(cli.telefono2,'') telefono2,
                                    cli.direccion,
                                    cli.numero_licencia,
                                    cli.categoria_licencia,
                                    cli.correo,
                                    replace(cli.cumpleanos,'-','') cumpleanos,
                                    ciu_cli.nombre cod_ciudad,
                                    ifnull(pro.numero_identificacion,'') numero_identificacion_p,
                                    ifnull(pro.tipo_identificacion,'') tipo_identificacion_p,
                                    pro.nombre1 nombre1_p,
                                    pro.apellido1 apellido1_p,
                                    ifnull(pro.telefono1,'') telefono1_p,
                                    ifnull(pro.telefono2,'') telefono2_p,
                                    ifnull(pro.direccion,'') direccion_p,
                                    ifnull(pro.numero_licencia,'') numero_licencia_p,
                                    ifnull(pro.categoria_licencia,'') categoria_licencia_p,
                                    ifnull(pro.correo,'') correo_p,
                                    ifnull(replace(pro.cumpleanos,'-',''),'') cumpleanos_p,
                                    ifnull(ciu_pro.nombre,'') cod_ciudad_p,
                                    upper(s.nombre) idservicio,
                                    if(v.registrorunt='0',(select l.nombre from linea l where l.idlinea=v.idlinea limit 1),(select l.nombre from linearunt l where l.idlinearunt=v.idlinea limit 1)) idlinea,
                                    if(v.registrorunt='0',(select m.nombre from linea l,marca m where l.idlinea=v.idlinea and l.idmarca=m.idmarca limit 1),(select m.nombre from linearunt l,marcarunt m where l.idlinearunt=v.idlinea and m.idmarcarunt=l.idmarcarunt limit 1)) idmarca,
                                    c.nombre idclase,
                                    if(v.registrorunt='0',(select co.nombre from color co where co.idcolor=v.idcolor limit 1),(select co.nombre from colorrunt co where co.idcolorrunt=v.idcolor limit 1)) idcolor,
                                    upper(tc.nombre) idtipocombustible,
                                    v.ano_modelo,
                                    v.numero_motor,
                                    v.numero_serie,
                                    v.numero_tarjeta_propiedad,
                                    v.cilindraje,
                                    v.num_pasajeros,
                                    v.potencia_motor,
                                    V.tipo_vehiculo,
                                    v.taximetro,
                                    v.tiempos,
                                    v.ensenanza,
                                    p.nombre idpais,
                                    replace(v.fecha_matricula,'-','') fecha_matricula,
                                    v.blindaje,
                                    v.polarizado,
                                    v.numsillas,
                                    v.numero_vin,
                                    v.numero_vin numero_chasis,
                                    v.numero_llantas,
                                    v.numero_exostos,
                                    v.scooter,
                                    v.numejes,
                                    v.kilometraje,
                                    car.nombre diseno,
                                    ifnull((select 
                                        p_da.valor
                                        from 
                                        pre_prerevision p_pr,
                                        pre_atributo p_at,
                                        pre_dato p_da
                                        where 
                                        numero_placa_ref='$numero_placa'  and
                                        p_at.id='llanta_ejes' and
                                        p_da.idpre_atributo=p_at.idpre_atributo and
                                        p_da.idpre_prerevision=p_pr.idpre_prerevision
                                        order by 
                                        p_pr.idpre_prerevision 
                                        desc limit 1),
                                        CASE
                                            WHEN c.nombre = 'MOTOCICLETA' THEN '1-1'
                                            WHEN c.nombre = 'CUATRIMOTO' THEN '2-2'
                                            WHEN c.nombre = 'MOTOTRICICLO' THEN '1-2'
                                            WHEN c.nombre = 'MOTOCARRO' THEN '1-2'
                                            WHEN c.nombre = 'CUADRICICLO' THEN '2-2'
                                            WHEN c.nombre = 'TRICIMOTO' THEN '2-1'
                                            WHEN c.nombre = 'CICLOMOTOR' THEN '2-2'
                                            WHEN c.nombre = 'AUTOMOVIL' THEN '2-2'
                                            WHEN c.nombre = 'CAMIONETA' THEN '2-2'
                                            WHEN c.nombre = 'CAMPERO' THEN '2-2'
                                            WHEN c.nombre = 'MICROBUS' THEN '2-2'
                                            WHEN c.nombre = 'BUS' THEN '2-4'
                                            WHEN c.nombre = 'BUSETA' THEN '2-4'
                                            WHEN c.nombre = 'CAMION' THEN '2-4'
                                            WHEN c.nombre = 'TRACTOCAMION' THEN '2-4-4'
                                            WHEN c.nombre = 'VOLQUETA' THEN '2-4'
                                            ELSE '2-2'
					END
                                        ) conf_inf,
                                    ifnull((select 
                                        p_da.valor
                                        from 
                                        pre_prerevision p_pr,
                                        pre_atributo p_at,
                                        pre_dato p_da
                                        where 
                                        numero_placa_ref='$numero_placa'  and
                                        p_at.id='numero_certificado_g' and
                                        p_da.idpre_atributo=p_at.idpre_atributo and
                                        p_da.idpre_prerevision=p_pr.idpre_prerevision
                                        order by 
                                        p_pr.idpre_prerevision 
                                        desc limit 1),'') numero_certificado_gas,
                                    ifnull((select 
                                        p_da.valor
                                        from 
                                        pre_prerevision p_pr,
                                        pre_atributo p_at,
                                        pre_dato p_da
                                        where 
                                        numero_placa_ref='$numero_placa'  and
                                        p_at.id='fecha_inicio_certgas' and
                                        p_da.idpre_atributo=p_at.idpre_atributo and
                                        p_da.idpre_prerevision=p_pr.idpre_prerevision
                                        order by 
                                        p_pr.idpre_prerevision 
                                        desc limit 1),'') fecha_inicio_certgas,
                                    ifnull((select 
                                        p_da.valor
                                        from 
                                        pre_prerevision p_pr,
                                        pre_atributo p_at,
                                        pre_dato p_da
                                        where 
                                        numero_placa_ref='$numero_placa'  and
                                        p_at.id='fecha_final_certgas' and
                                        p_da.idpre_atributo=p_at.idpre_atributo and
                                        p_da.idpre_prerevision=p_pr.idpre_prerevision
                                        order by 
                                        p_pr.idpre_prerevision 
                                        desc limit 1),'') fecha_final_certgas
                                    from 
                                    vehiculos v,
                                    servicio s,
                                    clase c,
                                    clientes cli,
                                    clientes pro,
                                    tipo_combustible tc,
                                    tipo_vehiculo tv,
                                    paises p,
                                    ciudades ciu_cli,
                                    ciudades ciu_pro,
                                    carroceria car
                                    where
                                    v.idcliente=cli.idcliente and
                                    v.idpropietarios=pro.idcliente and
                                    v.idclase=c.idclase and
                                    v.idpais=p.idpais and
                                    v.idservicio=s.idservicio and
                                    v.idtipocombustible=tc.idtipocombustible and
                                    v.tipo_vehiculo=tv.idtipo_vehiculo and
                                    cli.cod_ciudad=ciu_cli.cod_ciudad and
                                    pro.cod_ciudad=ciu_pro.cod_ciudad and
                                    v.diseno=car.idcarroceria and
                                    v.numero_placa = '$numero_placa' limit 1");
        return $result;
    }

    function cargarVehiculoLite($numero_placa) {
        $result = $this->db->query("SELECT 
v.numero_placa,
upper(s.nombre) AS  idservicio,
if(v.registrorunt='0',(select l.nombre from linea l where l.idlinea=v.idlinea limit 1),(select l.nombre from linearunt l where l.idlinearunt=v.idlinea limit 1)) idlinea,
if(v.registrorunt='0',(select m.nombre from linea l,marca m where l.idlinea=v.idlinea and l.idmarca=m.idmarca limit 1),(select m.nombre from linearunt l,marcarunt m where l.idlinearunt=v.idlinea and m.idmarcarunt=l.idmarcarunt limit 1)) idmarca,
c.nombre AS  idclase,
if(v.registrorunt='0',(select co.nombre from color co where co.idcolor=v.idcolor limit 1),(select co.nombre from colorrunt co where co.idcolorrunt=v.idcolor limit 1)) idcolor,
v.ano_modelo,
v.numero_motor,
v.numero_serie,
v.numero_tarjeta_propiedad,
v.cilindraje,
v.potencia_motor,
v.tipo_vehiculo,
v.taximetro,
v.tiempos,
v.ensenanza,
IFNULL((SELECT p.nombre FROM paises p WHERE v.idpais = p.idpais LIMIT 1),'') AS   idpais,
replace(v.fecha_matricula,'-','') fecha_matricula,
v.blindaje,
v.polarizado,
v.numsillas,
v.numero_vin,
v.numero_vin numero_chasis,
v.numero_llantas,
v.numero_exostos,
v.scooter,
v.numejes,
v.kilometraje,
IFNULL(c.tipolux, '') AS tipolux,
v.convertidor,
(v.diametro_escape * 10) diametro_escape,
v.idtipocombustible,
if(v.idtipocombustible=1,'DIESEL',if(v.idtipocombustible=2,'GASOLINA',if(v.idtipocombustible=3,'GNV',if(v.idtipocombustible=4,'GAS-GASOL',if(v.idtipocombustible=5,'ELECTRICO',if(v.idtipocombustible=6,'HIDROGENO',if(v.idtipocombustible=7,'ETANOL',if(v.idtipocombustible=8,'BIODIESEL',if(v.idtipocombustible=9,'GLP',if(v.idtipocombustible=10,'GAS-ELECTRICO','GAS-DIESEL')))))))))) combustible,
v.numero_tarjeta_propiedad,
IFNULL((SELECT CONCAT(c.nombre1,' ',c.nombre2,' ',c.apellido1,' ',c.apellido2) FROM clientes c WHERE v.idcliente = c.idcliente LIMIT 1),'') AS nombre_propietario,
IFNULL((SELECT if(c.tipo_identificacion=1,'CC',if(c.tipo_identificacion=3,'CE',if(c.tipo_identificacion=4,'TI',if(c.tipo_identificacion=6,'PA','NIT')))) FROM clientes c WHERE v.idcliente = c.idcliente LIMIT 1),'') AS tipo_identificacion,
IFNULL((SELECT c.numero_identificacion FROM clientes c WHERE v.idcliente = c.idcliente LIMIT 1),'') AS numero_identificacion,
IFNULL((SELECT c.direccion FROM clientes c WHERE v.idcliente = c.idcliente LIMIT 1),'') AS direccion,
IFNULL((SELECT c.telefono1 FROM clientes c WHERE v.idcliente = c.idcliente LIMIT 1),'') AS telefono1,
IFNULL((SELECT c.telefono2 FROM clientes c WHERE v.idcliente = c.idcliente LIMIT 1),'') AS telefono2,
IFNULL((SELECT ci.nombre FROM clientes c, ciudades ci WHERE v.idcliente = c.idcliente AND c.cod_ciudad = ci.cod_ciudad LIMIT 1),'') AS nombre_ciudad,
IFNULL((SELECT ca.nombre FROM carroceria ca WHERE v.diseno = ca.idcarroceria LIMIT 1),'SIN CARROCERIA') AS carroceria
FROM vehiculos v,servicio s, clase c
WHERE 
v.idservicio = s.idservicio AND v.idclase = c.idclase AND v.numero_placa = '$numero_placa' limit 1");
        return $result;
    }

}
