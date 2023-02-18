<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MfUR extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function consultar($numero_placa, $fecha) {
        $query = <<< EOF
SELECT h.idhojapruebas idcontrol,
                            CASE
                            WHEN v.idservicio = '1' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 22px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '2' THEN
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: white;
                            color:black;
                            font-size: 22px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '3' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 22px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '4' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 22px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            WHEN v.idservicio = '7' THEN 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: blue;
                            color:black;
                            font-size: 22px;
                            font-weight: bold;
                            border: solid;
                            border-color: whitesmoke;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            ELSE 
                            concat('
                            <label style="
                            border-radius: 6px 6px 6px 6px;background: gold;
                            color:black;
                            font-size: 22px;
                            font-weight: bold;
                            border: solid;
                            border-color: black;
                            padding: 7px"><strong>',v.numero_placa,'</strong></label>')
                            END placa,
            if(h.reinspeccion=0 OR h.reinspeccion=1,
									 '<strong>RTMEC<strong>',
			    if(h.reinspeccion=4444 OR h.reinspeccion=44441,
									 '<strong>PREVENTIVA<strong>',
									 '<strong>PRUEBA LIBRE<strong>')) tipo,
                            h.fechainicial,
                            h.fechafinal,
            if(h.reinspeccion=0 OR h.reinspeccion=4444 OR  h.reinspeccion=8888,
									 CONCAT('<button name="dato" class="btn btn-accent btn-block" value ="',h.idhojapruebas,'-',h.reinspeccion,'" type="submit" formtarget="_blank" style="border-radius: 40px 40px 40px 40px;font-size: 20px;background-color: #393185">📄 Ver 1ra Vez</button>'),
			   if(h.reinspeccion=1,
									 CONCAT('<button name="dato" class="btn btn-accent btn-block" value ="',h.idhojapruebas,'-0','" type="submit" formtarget="_blank" style="border-radius: 40px 40px 40px 40px;font-size: 20px;background-color: #393185">📄 Ver 1ra Vez</button>',
									        '<button name="dato" class="btn btn-accent btn-block" value ="',h.idhojapruebas,'-1','" type="submit" formtarget="_blank" style="border-radius: 40px 40px 40px 40px;font-size: 20px;background-color: #393185">📄 Ver 2da Vez</button>'),
				if(h.reinspeccion=44441,
									 CONCAT('<button name="dato" class="btn btn-accent btn-block" value ="',h.idhojapruebas,'-4444','" type="submit" formtarget="_blank" style="border-radius: 40px 40px 40px 40px;font-size: 20px;background-color: #393185">📄 Ver 1ra Vez</button>',
									        '<button name="dato" class="btn btn-accent btn-block" value ="',h.idhojapruebas,'-44441','" type="submit" formtarget="_blank" style="border-radius: 40px 40px 40px 40px;font-size: 20px;background-color: #393185">📄 Ver 2da Vez</button>'),
									 '<strong>NO APLICA<strong>'))) btnFur,
                if(h.reinspeccion=0 and (h.estadototal=4 or h.estadototal=7),
									 CONCAT('<button onclick="emailFur(event, this.value, this.title)" name="dato" id="btn_email_fur" data-toggle="modal" data-target="#envioEmail"  class="btn btn-accent btn-block" value ="',h.idhojapruebas,'-',h.reinspeccion,'" type="submit" formtarget="_blank" style="border-radius: 40px 40px 40px 40px;font-size: 20px;background-color: #393185" title="',CAST(IFNULL(c.correo,'') AS CHAR CHARACTER SET utf8),'">✉ Enviar 1ra Vez</button>'),
			    if(h.reinspeccion=1 and (h.estadototal=4 or h.estadototal=7),
				CONCAT('<button onclick="emailFur(event, this.value, this.title)"  name="dato" id="btn_email_fur" data-toggle="modal" data-target="#envioEmail"  class="btn btn-accent btn-block" value ="',h.idhojapruebas,'-0','" type="submit" formtarget="_blank" style="border-radius: 40px 40px 40px 40px;font-size: 20px;background-color: #393185" title="',CAST(IFNULL(c.correo,'') AS CHAR CHARACTER SET utf8),'">✉ Enviar 1ra Vez</button>',
				'<button onclick="emailFur(event, this.value, this.title)"  name="dato" id="btn_email_fur" data-toggle="modal" data-target="#envioEmail"  class="btn btn-accent btn-block" value ="',h.idhojapruebas,'-1','" type="submit" formtarget="_blank" style="border-radius: 40px 40px 40px 40px;font-size: 20px;background-color: #393185" title="',CAST(IFNULL(c.correo,'') AS CHAR CHARACTER SET utf8),'">✉ Enviar 2da Vez</button>'),
                                '<strong>NO APLICA<strong>')) btnEmail
                            FROM 
                            hojatrabajo h,vehiculos v, clientes c  
                            where
                            v.idcliente = c.idcliente AND
                            h.idvehiculo=v.idvehiculo AND (h.reinspeccion=0 OR h.reinspeccion=1 OR h.reinspeccion=4444 OR h.reinspeccion=44441 OR h.reinspeccion=8888) and
                            v.numero_placa LIKE '%$numero_placa%' $fecha ORDER BY h.idhojapruebas desc;
EOF;
        return $this->db->query($query);
    }

}
