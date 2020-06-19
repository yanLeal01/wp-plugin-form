<?php
/**
 * Plugin Name: WP Plugin Form
 * Autor: Yan Leal
 * Description: Plugin para crear formulario persoanlizado en wordpress utilizando el shortcode [wp-plugin-form]
 */

register_activation_hook(__FILE__, 'WP_registros_init');

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
        mesCumple INT(10) NOT NULL,
        edoVotas INT(10) NOT NULL,
        seccionVotas INT(10) NOT NULL,
        edoVives INT(10) NOT NULL,
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
                'header' => array("Content-type: application/x-www-form-urlencoded", "Authorization: Bearer " . $token),
                'method' => $method,
                'content' => http_build_query($data)
            )
        );
    }
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) { /* Handle error */
    }
    var_dump($result);
    if ($auth)
        return json_decode($result)->token;
}

function WP_Plugin_form()
{
    global $wpdb;

    if (!empty($_POST)
        and $_POST['txtNombre'] != ''
        and is_email($_POST['txtEmail'])
        and $_POST['txtAp_pat'] != ''
        and $_POST['txtAp_mat'] != ''
        and $_POST['txtSeccion'] != ''
        and $_POST['txtCel'] != ''
    ) {
        $tabla_registros = $wpdb->prefix . 'registros';
        /*print_r($_POST);*/
        $simpatizante = $_POST['chkSimpatizante'];
        $candidato = $_POST['chkCandidato'];
        $patrocinador = (int)$_POST['chkPatrocinador'];
        $activista = (int)$_POST['chkActivista'];
        $equipo = sanitize_text_field($_POST['txtEquipo']);
        $nombre = sanitize_text_field($_POST['txtNombre']);
        $ap_pat = sanitize_text_field($_POST['txtAp_pat']);
        $ap_mat = sanitize_text_field($_POST['txtAp_mat']);
        $dia_cumple = (int)$_POST['cmbDia_cumple'];
        $mes_cumple = (int)$_POST['cmbMes_cumple'];
        $edo_votas = (int)$_POST['cmbEdo_votas'];
        $seccion = (int)$_POST['txtSeccion'];
        $edo_vives = (int)$_POST['cmbEdo_vives'];
        $correo = sanitize_text_field($_POST['txtEmail']);
        $celular = sanitize_text_field($_POST['txtCel']);
        $autorizacion = (int)$_POST['cmbAutorizacion'];
        $eventos = (int)$_POST['cmbEventos'];
        $comentario = sanitize_text_field($_POST['txaComentario']);
        $created_at = date('Y-m-d H:i:s');

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
                'mesCumple' => $mes_cumple,
                'edoVotas' => $edo_votas,
                'seccionVotas' => $seccion,
                'edoVives' => $edo_vives,
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
                    'mes' => ""
                ),
            ),
            'estadoVota' => array(
                'idEstado' => "",
                'estado' => $edo_votas
            ),
            'estadoVive' => array(
                'idEstado' => "",
                'estado' => $edo_vives
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
        sendRequest("propuesta", $data, "POST", false, $_SESSION["token"]);

    } else {
        $data = array('site' => 'ciudadanosxmex', 'log' => 'c35d1d1n45x');
        $_SESSION["token"] = sendRequest("login", $data, "POST", true, "");
    }

    // Carga esta hoja de estilo para dar formato al formulatio
    wp_enqueue_style('css_registros', plugins_url('style.css', __FILE__));
    ob_start();
    ?>

    <form action="<?php get_the_permalink(); ?>" method="post" id="form_registros" class="cuestionario">
        <?php wp_nonce_field('graba_registros', 'registros_nonce'); ?>
        <div class="form-input">
            <h4>Contigo somos más</h4>
            <h3>Únete y participa</h3>
            <h5>Puedes participar en una o más categorías</h5>
            <h5>¿Cómo te gustaría ayudar?</h5>
            <input class="form-check-input" type="checkbox" id="chkSimpatizante" name="chkSimpatizante" value="1"
                   checked>
            <label class="form-check-label" for="chkSimpatizante">Simpatizante</label>
            <input class="form-check-input" type="checkbox" id="chkCandidato" name="chkCandidato" value="1">
            <label class="form-check-label" for="chkCandidato">Aspirante a candidato</label>
            <input class="form-check-input" type="checkbox" id="chkPatrocinador" name="chkPatrocinador" value="1">
            <label class="form-check-label" for="chkPatrocinador">Patrocinador</label>
            <input class="form-check-input" type="checkbox" id="chkActivista" name="chkActivista" value="1"
                   onchange="javascript:showContent()">
            <label class="form-check-label" for="chkActivista">Activista</label>
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
                <input type="text" name="txtEquipo" id="txtEquipo" placeholder="Nombre de tu equipo">
                <!-- LOGO -->
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 me-trading-form-box">
                        <label><strong>Carga el logo de tu equipo</strong></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 me-trading-form-box">
                        <input type="file" id="iLoad" accept="image/*"/>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 me-trading-form-box">
                        <img style="border-radius: 50%;" id="img" height="150">
                    </div>
                </div>
                <br>
            </div>
        </div>
        <div class="form-input">
            <label for="txtNombre">Nombre</label>
            <input type="text" name="txtNombre" id="txtNombre" placeholder="Nombre *" required>
        </div>
        <div class="form-input">
            <label for="txtAp_pat">Apellido Paterno</label>
            <input type="text" name="txtAp_pat" id="txtAp_pat" placeholder="Apellido Paterno *" required>
        </div>
        <div class="form-input">
            <label for="txtAp_mat">Apellido Materno</label>
            <input type="text" name="txtAp_mat" id="txtAp_mat" placeholder="Apellido Materno *" required>
        </div>
        <div class="form-input">
            <input type="text" placeholder="Día de cumpleaños *" disabled>
            <select id="cmbDia_cumple" name="cmbDia_cumple" required>
                <option selected hidden>Seleccionar</option>
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
        </div>
        <div class="form-input">
            <input type="text" placeholder="Mes de Cumpleaños *" disabled>
            <select id="cmbMes_cumple" name="cmbMes_cumple" required>
                <option selected hidden>Seleccionar</option>
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
        </div>
        <div class="form-input">
            <input type="text" placeholder="Estado donde votas *" disabled>
            <select id="cmbEdo_votas" name="cmbEdo_votas" required>
                <option selected hidden>Selecionar</option>
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
        </div>
        <div class="form-input">
            <label for="txtSeccion">Sección donde votas</label>
            <input type="text" name="txtSeccion" id="txtSeccion" placeholder="Sección donde votas*" required>
        </div>
        <div class="form-input">
            <input type="text" placeholder="Estado donde vives *" disabled>
            <select id="cmbEdo_vives" name="cmbEdo_vives" required>
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
        </div>
        <div class="form-input">
            <label for="txtEmail">Email</label>
            <input type="email" name="txtEmail" id="txtEmail" placeholder="Email">
        </div>
        <div class="form-input">
            <label for="txtCel">Celular</label>
            <input type="tel" name="txtCel" id="txtCel" placeholder="Celular (10 dígitos) *" maxlength="10"
                   pattern="[0-9]{10}" required>
        </div>
        <div class="form-input">
            <label><strong>¿Te podemos contactar? *</strong></label>
            <select id="cmbAutorizacion" name="cmbAutorizacion" required>
                <option selected hidden>Seleccionar</option>
                <option value="1">Si</option>
                <option value="2">No</option>
            </select>
        </div>
        <div class="form-input">
            <label><strong>¿Te gustaría asistir a eventos de Recuperemos México en tu distrito?*</strong></label>
            <select id="cmbEventos" name="cmbEventos" required>
                <option selected hidden>Seleccionar</option>
                <option value="1">Si</option>
                <option value="2">No</option>
            </select>
        </div>
        <div class="form-input">
            <label><strong>¡Nos interesa muchísimo saber lo que piensas! </strong>¿Quisieras
                compartirnos algún comentario o idea?</label>
            <textarea class="form-control" id="txaComentario" name="txaComentario" rows="3"
                      placeholder="Comentario, idea, propuesta..."></textarea>
        </div>
        <input id="bEnviar" type="submit" onclick="sendPropuesta()" value="ENVIAR">
    </form>

    <?php
    return ob_get_clean();
}
