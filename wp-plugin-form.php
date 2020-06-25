<?php
/**
 * Plugin Name: WP Plugin Form
 * Autor: Yan Leal & JC Salazar
 * Description: Plugin para crear formulario persoanlizado en wordpress utilizando el shortcode [wp-plugin-form]
 */

register_activation_hook(__FILE__, 'WP_registros_init');
$token;

function WP_registros_init()
{
    global $wpdb;
    $tabla_registros = $wpdb->prefix . 'registros';
    $charset_collate = $wpdb->get_charset_collate();
    //Preparar la consulta que vamos a lanzar para crear la tabla
    $query = "CREATE TABLE IF NOT EXISTS $tabla_registros (
        id INT(9) NOT NULL AUTO_INCREMENT,
        simpatizante TINYINT(1),
        activista TINYINT(1),
        candidato TINYINT(1),
        patrocinador TINYINT(1),
        equipo VARCHAR(50) NOT NULL,
        nombre VARCHAR(50) NOT NULL,
        apePat VARCHAR(50) NOT NULL,
        apeMat VARCHAR(50) NOT NULL,
        diaCumple INT(10) NOT NULL,
        mesCumple VARCHAR(50) NOT NULL,
        edoVotas VARCHAR(50) NOT NULL,
        seccionVotas INT(10) NOT NULL,
        edoVives VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        celular VARCHAR(50) NOT NULL,
        contactar TINYINT(1) NOT NULL,
        asistirEventos TINYINT(1) NOT NULL,
        comentario VARCHAR(250) NOT NULL,
        created_at DATETIME NOT NULL,
        UNIQUE (id)
        ) $charset_collate";
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($query);
}


// Definir el shortcode que pinta el formulario
add_shortcode('wp-plugin-form', 'WP_Plugin_form');

/** Crea el shortcode
 *
 * @return void
 */
function sendRequest($ruta, $data, $method, $auth, $token)
{
    //session_start();
    //print_r($_SESSION);
    //die();

    $url = 'https://recuperemosmexico.org/contmx/' . $ruta;

    // use key 'http' even if you send the request to https://...
    if ($auth) {
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => $method,
                'content' => http_build_query($data)
            )
        );
    } else {
        $options = array(
            'http' => array(
                'header' => array("Content-type: application/json", "Authorization: Bearer " . $token),
                'method' => $method,
                'content' => json_encode($data)
            )
        );
    }

    $context = stream_context_create($options);
    
    $result = file_get_contents($url, false, $context);

    
    if ($result === FALSE) { /* Handle error */
    }
    
    if ($auth) {
        return json_decode($result)->token;
    } 
    // else {
    //     var_dump($result);
    // }
}

function WP_Plugin_form()
{
    global $wpdb;
    global $token;

    if (!empty($_POST)
        and $_POST['txtNombre'] != ''
        and is_email($_POST['txtEmail'])
        and $_POST['txtAp_pat'] != ''
        and $_POST['txtAp_mat'] != ''
        and $_POST['txtSeccion'] != ''
        and $_POST['txtCel'] != ''
    ) {
        $tabla_registros = $wpdb->prefix . 'registros';

        //var_dump($_POST);

        $simpatizante = (int)$_POST['chkSimpatizante'];
        $candidato = (int)$_POST['chkCandidato'];
        $patrocinador = (int)$_POST['chkPatrocinador'];
        $activista = (int)$_POST['chkActivista'];
        $equipo = 'Todos con México';
        $nombre = sanitize_text_field($_POST['txtNombre']);
        $ap_pat = sanitize_text_field($_POST['txtAp_pat']);
        $ap_mat = sanitize_text_field($_POST['txtAp_mat']);
        $dia_cumple = (int)$_POST['cmbDia_cumple'];
        $mes_cumple = (int)$_POST['cmbMes_cumple'];
        $mesCumpleStr = $_POST["mesCumpleStr"];
        $edo_votas = (int)$_POST['cmbEdo_votas'];
        $edoVotaStr = $_POST["edoVotaStr"];
        $seccion = (int)$_POST['txtSeccion'];
        $edo_vives = (int)$_POST['cmbEdo_vives'];
        $edoViveStr = $_POST["edoViveStr"];
        $correo = sanitize_text_field($_POST['txtEmail']);
        $celular = sanitize_text_field($_POST['txtCel']);
        $autorizacion = (int)$_POST['cmbAutorizacion'];
        $eventos = (int)$_POST['cmbEventos'];
        $comentario = sanitize_text_field($_POST['txaComentario']);
        $created_at = date('Y-m-d H:i:s');

        /*
        $asunto = "Bienvenido a Recuperemos México";
        $msj = "Estimad@ " . $nombre ;
        $header = "From: contacto@recuperemosmexico.org" . "\r\n";
        $header.= "Reply-To: noreply@recuperemosmexico.org" . "\r\n";
        $header.= "X-Mail: PHP/". phpversion();
        $mail = mail($correo, $asunto, $msj, $header);
        if($mail) {
            echo "<h4>¡Mail enviado exitosamente!</h4>";
        }
        */

        $wpdb->insert(
            $tabla_registros,
            array(
                'simpatizante' => $simpatizante,
                'candidato' => $candidato,
                'patrocinador' => $patrocinador,
                'activista' => $activista,
                'equipo' => $equipo,
                'nombre' => $nombre,
                'apePat' => $ap_pat,
                'apeMat' => $ap_mat,
                'diaCumple' => $dia_cumple,
                'mesCumple' => $mesCumpleStr,
                'edoVotas' => $edoVotaStr,
                'seccionVotas' => $seccion,
                'edoVives' => $edoViveStr,
                'email' => $correo,
                'celular' => $celular,
                'contactar' => $autorizacion,
                'asistirEventos' => $eventos,
                'comentario' => $comentario,
                'created_at' => $created_at
            )
        );
        $data = array(
            'nombre' => $nombre,
            'aPaterno' => $ap_pat,
            'aMaterno' => $ap_mat,
            'celular' => $celular,
            'seccion' => $seccion,
            'email' => $correo,
            'padre' => "5eed3acfbb881e3c1c7eb171",
            'equipo' => $equipo,
            'propuesta' => $comentario,
            'imagen' => "",
            'fNacimiento' => array(
                'dia' => $dia_cumple,
                'mes' => array(
                    'idMes' => $mes_cumple,
                    'mes' => $mesCumpleStr
                ),
            ),
            'estadoVota' => array(
                'idEstado' => $edo_votas,
                'estado' => $edoVotaStr
            ),
            'estadoVive' => array(
                'idEstado' => $edo_vives,
                'estado' => $edoViveStr
            ),
            'tipoAyuda' => array(
                'miDerecho' => $simpatizante,
                'candidato' => $candidato,
                'patrocinador' => $patrocinador,
                'activista' => $activista,
            ),
            'contactar' => $autorizacion,
            'asistir' => $eventos,
        );
        
        sendRequest("propuesta", $data, "POST", false, $_POST["tkn"]);
    } else {
        $data = array('site' => 'ciudadanosxmex', 'log' => 'c35d1d1n45x');
        $token = sendRequest("login", $data, "POST", true, "");
    }

    // Carga esta hoja de estilo para dar formato al formulatio
    wp_enqueue_style('css_registros', plugins_url('style.css',__FILE__));
    ob_start();
?>
    
    <script>
        function getSelectDescription(sel, hid) {
            valor = sel.options[sel.selectedIndex].text;
            hid.value = valor;
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    <form action="<?php get_the_permalink(); ?>" method="post" id="form_registros" class="cuestionario" style="<?php if (empty($_POST)) { echo "display: block;";} else { echo "display: none;";} ?>">
    <?php wp_nonce_field('graba_registros', 'registros_nonce'); ?>
        <center>
            <img class="logo" src="https://todosconmexico.mx/wp-content/plugins/wp-plugin-form/images/logo-recuperemosMéxico.png" alt="Logo Recuperemos México" width="260px">            
            <h5>Únete, contigo somos uno más</h5>
        </center>

        <table class="tabla-form">
            <tr>
                <td colspan="2">
                    <h6 style="margin:10px !important;">Puedes participar en una o más categorías</h6>
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <div class="form-check-inline">
                        <input class="form-check-input" type="checkbox" id="chkSimpatizante" name="chkSimpatizante" value="1" checked>&nbsp;&nbsp;Simpatizante
                    </div>                
                </td>
                <td width="50%">
                    <div class="form-check-inline">
                        <input class="form-check-input" type="checkbox" id="chkCandidato" name="chkCandidato" value="1">&nbsp;&nbsp;Aspirante a candidato
                    </div>
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <div class="form-check-inline">
                        <input class="form-check-input" type="checkbox" id="chkPatrocinador" name="chkPatrocinador" value="1">&nbsp;&nbsp;Patrocinador
                    </div>                
                </td>
                <td width="50%">
                    <div class="form-check-inline">
                        <input class="form-check-input" type="checkbox" id="chkActivista" name="chkActivista" value="1">&nbsp;&nbsp;Activista
                    </div> 
                </td>
            </tr>            
        </table>
         
        <div class="form-group">
            <!-- 
            <script type="text/javascript">
                function showContent() {
                    element = document.getElementById("container-activista");
                    check = document.getElementById("chkActivista");
                    if (check.checked) {
                        element.style.display = 'block';
                    } else {
                        element.style.display = 'none';
                    }
                }
            </script>
            <div id="container-activista" style="display: none;">
                <h5>Si eres activista podrás invitar amigos ¿Cómo te gustaría llamar a tu equipo?</h5>
                <input class="form-control" type="text" name="txtEquipo" id="txtEquipo" placeholder="Nombre de tu equipo">
                <label>Carga el logo de tu equipo</label>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 me-trading-form-box">
                        <input type="file" id="iLoad" accept="image/*"/>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 me-trading-form-box"  >
                        <img style="border-radius: 50%;" id="img" height="150">
                    </div>
                </div>
            </div>
            -->
        </div>
        
        <table class="tabla-form">
            <tbody>
                <tr>
                    <th width="35%">Nombre*</th>
                    <td width="65%" colspan="2">
                        <input class="form-control" type="text" name="txtNombre" id="txtNombre" required>
                    </td>
                </tr>
                <tr>
                    <th>Apellido Paterno*</th>
                    <td colspan="2">
                        <input class="form-control" type="text" name="txtAp_pat" id="txtAp_pat" required>
                    </td>
                </tr>
                <tr>
                    <th>Apellido Materno*</th>
                    <td colspan="2">
                        <input class="form-control" type="text" name="txtAp_mat" id="txtAp_mat" required>
                    </td>
                </tr>
                <tr>
                    <th>Fecha de cumpleaños*</th>
                    <td width="30%">
                        <select class="form-control" id="cmbDia_cumple" name="cmbDia_cumple" required>
                            <option selected hidden>Día</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                            <option value="24">24</option>
                            <option value="25">25</option>
                            <option value="26">26</option>
                            <option value="27">27</option>
                            <option value="28">28</option>
                            <option value="29">29</option>
                            <option value="30">30</option>
                            <option value="31">31</option>
                        </select>
                    </td>
                    <td width="35%">
                        <select class="form-control" id="cmbMes_cumple" name="cmbMes_cumple" required onchange="getSelectDescription(this, document.getElementById('mesCumpleStr'));">
                            <option selected hidden>Mes</option>
                            <option value="01">Enero</option>
                            <option value="02">Febrero</option>
                            <option value="03">Marzo</option>
                            <option value="04">Abril</option>
                            <option value="05">Mayo</option>
                            <option value="06">Junio</option>
                            <option value="07">Julio</option>
                            <option value="08">Agosto</option>
                            <option value="09">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                        <input type="hidden" id="mesCumpleStr" name="mesCumpleStr">
                    </td>                    
                </tr>
                <tr>
                    <th>Estado donde votas*</th>
                    <td colspan="2">
                        <select class="form-control" id="cmbEdo_votas" name="cmbEdo_votas" required onchange="getSelectDescription(this, document.getElementById('edoVotaStr'));">
                            <option selected hidden>Seleccionar</option>
                            <option value="1">Aguascalientes</option>
                            <option value="2">Baja California</option>
                            <option value="3">Baja California Sur</option>
                            <option value="4">Campeche</option>
                            <option value="5">CDMX</option>
                            <option value="6">Chiapas</option>
                            <option value="7">Chihuahua</option>
                            <option value="8">Coahuila</option>
                            <option value="9">Colima</option>
                            <option value="10">Durango</option>
                            <option value="11">Estado de México</option>
                            <option value="12">Guanajuato</option>
                            <option value="13">Guerrero</option>
                            <option value="14">Hidalgo</option>
                            <option value="15">Jalisco</option>
                            <option value="16">Michoacán</option>
                            <option value="17">Morelos</option>
                            <option value="18">Nayarit</option>
                            <option value="19">Nuevo León</option>
                            <option value="20">Oaxaca</option>
                            <option value="21">Puebla</option>
                            <option value="22">Querétaro</option>
                            <option value="23">Quintana Roo</option>
                            <option value="24">San Luis Potosí</option>
                            <option value="25">Sinaloa</option>
                            <option value="26">Sonora</option>
                            <option value="27">Tabasco</option>
                            <option value="28">Tamaulipas</option>
                            <option value="29">Tlaxcala</option>
                            <option value="30">Veracruz</option>
                            <option value="31">Yucatán</option>
                            <option value="32">Zacatecas</option>
                        </select>
                        <input type="hidden" id="edoVotaStr" name="edoVotaStr">
                    </td>
                </tr>
                <tr>
                    <th>Sección donde votas*</th>
                    <td colspan="2">
                        <input class="form-control" type="text" name="txtSeccion" id="txtSeccion" 
                        onfocus="mostrarCredencial()" onblur="ocultarCredencial()" required>
                        <script type="text/javascript">
                            function mostrarCredencial() {
                                element = document.getElementById("credencial");
                                element.style.display = 'block';
                            }
                            function ocultarCredencial() {
                                element = document.getElementById("credencial");
                                element.style.display = 'none';
                            }                            
                        </script>
                        <div id="credencial" style="display:none;">
                            <img src="https://todosconmexico.mx/wp-content/plugins/wp-plugin-form/images/ine.png" alt="Credencial INE localizar sección">
                            <h6 style="margin: 1rem !important; text-align:center; ">Localiza tu sección en tu credencial de elector</h6>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>Estado donde vives*</th>
                    <td colspan="2">
                        <select class="form-control" id="cmbEdo_vives" name="cmbEdo_vives" required onchange="getSelectDescription(this, document.getElementById('edoViveStr'));">
                            <option selected hidden>Seleccionar</option>
                            <option value="1">Aguascalientes</option>
                            <option value="2">Baja California</option>
                            <option value="3">Baja California Sur</option>
                            <option value="4">Campeche</option>
                            <option value="5">CDMX</option>
                            <option value="6">Chiapas</option>
                            <option value="7">Chihuahua</option>
                            <option value="8">Coahuila</option>
                            <option value="9">Colima</option>
                            <option value="10">Durango</option>
                            <option value="11">Estado de México</option>
                            <option value="12">Guanajuato</option>
                            <option value="13">Guerrero</option>
                            <option value="14">Hidalgo</option>
                            <option value="15">Jalisco</option>
                            <option value="16">Michoacán</option>
                            <option value="17">Morelos</option>
                            <option value="18">Nayarit</option>
                            <option value="19">Nuevo León</option>
                            <option value="20">Oaxaca</option>
                            <option value="21">Puebla</option>
                            <option value="22">Querétaro</option>
                            <option value="23">Quintana Roo</option>
                            <option value="24">San Luis Potosí</option>
                            <option value="25">Sinaloa</option>
                            <option value="26">Sonora</option>
                            <option value="27">Tabasco</option>
                            <option value="28">Tamaulipas</option>
                            <option value="29">Tlaxcala</option>
                            <option value="30">Veracruz</option>
                            <option value="31">Yucatán</option>
                            <option value="32">Zacatecas</option>
                        </select>
                        <input type="hidden" id="edoViveStr" name="edoViveStr">
                    </td>
                </tr>  
                <tr>
                    <th>Email</th>
                    <td colspan="2">
                        <input class="form-control" type="email" name="txtEmail" id="txtEmail" placeholder="example@domain.com">
                    </td>
                </tr> 
                <tr>
                    <th>Celular (10 dígitos)*</th>
                    <td colspan="2">
                        <input class="form-control" type="tel" name="txtCel" id="txtCel" maxlength="10" placeholder="999-999-0000" pattern="[0-9]{10}" required>
                    </td>
                </tr>  
                <tr>
                    <th>¿Te podemos contactar?*</th>
                    <td colspan="2">
                        <select  class="form-control" id="cmbAutorizacion" name="cmbAutorizacion" required>
                            <option selected hidden>Seleccionar</option>
                            <option value="1">Si</option>
                            <option value="2">No</option>
                        </select>                        
                    </td>
                </tr> 
                <tr>
                    <th>¿Te gustaría asistir a eventos de Recuperemos México en tu distrito?*</th>
                    <td colspan="2">
                        <select class="form-control" id="cmbEventos" name="cmbEventos" required>
                            <option selected hidden>Seleccionar</option>
                            <option value="1">Si</option>
                            <option value="2">No</option>
                        </select>
                    </td>
                </tr> 
                <tr>
                    <th>¡Nos interesa muchísimo saber lo que piensas!</th>
                    <td colspan="2">
                        <span> ¿Quisieras compartirnos algún comentario o idea?</span>
                        <textarea class="form-control" id="txaComentario" name="txaComentario" rows="3" placeholder="Comentario, idea, propuesta..."></textarea>
                    </td>
                </tr>  
                <tr>
                    <th></th>
                    <td colspan="2">
                        <input type="hidden" id="tkn" name="tkn" value="<?php echo $token; ?>">
                        <input type="submit" value="Enviar">                        
                    </td>
                </tr> 
            </tbody>
        </table>
    </form>

    <div style="<?php if (empty($_POST)) { echo "display: none;";} else { echo "display: block;";} ?>">
        <p>Muchas gracias por tu registro. Seguiremos en contacto</p>
        <button onclick="window.location.href = window.location.href;">Continuar</button>
    </div>
    <?php

    return ob_get_clean();
}

add_action("admin_menu", "WP_registros_menu");

/** Agrega el menu del plugin al formulario de wordpress
 * 
 * @return void
 */

 function WP_registros_menu()
 {
    add_menu_page("Ciudadanos registrados", "Ciudadanos", "manage_options", 
    "WP_registros_menu", "WP_registros_admin", "dashicons-feedback", 75);
}

function WP_registros_admin()
{
    global $wpdb;

    $tabla_registros = $wpdb->prefix . 'registros';
    $registros = $wpdb->get_results("Select * from $tabla_registros");
    echo '<div class="warp"><h1>Listado de ciudadanos registrados</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead> <tr> <th width="10%">Nombre</th> <th width="10%">Apellido Paterno</th>';
    echo '<th width="10%">Apellido Materno</th> <th width="10%">Celular</th> <th width="15%">Correo</th>';
    echo '<th width="10%">Estado en donde votas</th> <th width="10%">No. Sección</th>';
    echo '<th width="20%">Comentario</th> </tr> </thead> ';
    echo '<tbody id="the-list">';
    foreach ($registros as $registro){
        $nombre= esc_textarea( $registro->nombre );
        $ape_pat= esc_textarea( $registro->apePat );
        $ape_mat= esc_textarea( $registro->apeMat );
        $celular= esc_textarea( $registro->celular );
        $correo= esc_textarea( $registro->email );
        $edoVotas= esc_textarea( $registro->edoVotas );
        $seccion= (int)$registro->seccionVotas;
        $comentario= esc_textarea( $registro->comentario );
        echo "<tr><td>$nombre</td><td>$ape_pat</td><td>$ape_mat</td><td>$celular</td><td>$correo</td>";
        echo "<td>$edoVotas</td><td>$seccion</td><td>$comentario</td></tr>";
    }
    echo '</tbody></table></div>';
}


