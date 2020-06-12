<?php
/**
 * Plugin Name: WP Plugin Form
 * Autor: Yan Leal
 * Description: Plugin para crear formulario persoanlizado en wordpress utilizando el shortcode [wp-plugin-form]
 */

 // Definir el shortcode que pinta el formulario
 add_shortcode( 'wp-plugin-form', 'WP_Plugin_form');

 function WP_Plugin_form()
 {
    ob_start();
    ?>
    
    <form action="<?php get_the_permalink(); ?>" method="post" 
        class="cuestionario">
        <div class="form-input">
            <div class="me-heading2">
                <h4>Contigo somos más</h4>
                <h3>Únete y participa</h3>
                <h5 style="color: #f93e7e;">Puedes participar en una o más categorías</h5>
            </div>
            <div class="row">
                <div class="col-lg-9 col-md-8 col-sm-12">
                    <div class="me-contact-form">
                        <div class="row" style="padding-left: 20px;">
                            <h5>¿Cómo te gustaría ayudar?</h5>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                                <div class="form-check form-check-inline" style="align-items:flex-start;">
                                    <input class="form-check-input" type="checkbox" id="chkDerecho" value="option6"
                                               style="width: 16px; height: 16px;">
                                    <label class="form-check-label" for="chkDerecho">Como simpatizante</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                                <div class="form-check form-check-inline" style="align-items:flex-start;">
                                    <input class="form-check-input" type="checkbox" id="chkCandidato"
                                               value="option8" style="width: 16px; height: 16px;">
                                    <label class="form-check-label" for="chkCandidato">Aspirante a candidato</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6col-xs-6">
                                <div class="form-check form-check-inline" style="align-items:flex-start;">
                                    <input class="form-check-input" type="checkbox" id="chkPatrocinador"
                                                value="option9" style="width: 16px; height: 16px;">
                                    <label class="form-check-label" for="chkPatrocinador">Como patrocinador</label>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                                <div class="form-check form-check-inline" style="align-items:flex-start;">
                                    <input class="form-check-input" type="checkbox" id="chkActivista"
                                               value="option7" onchange="javascript:showContent()"
                                               style="width: 16px; height: 16px;">
                                    <label class="form-check-label" for="chkActivista" style="color: #f93e7e;">Como
                                            activista</label>
                                </div>
                            </div>
                        </div>
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
                                <div class="col-lg-6 col-md-6 col-sm-12 me-trading-form-box"  >
                                    <img style="border-radius: 50%;" id="img" height="150">
                                </div>
                            </div>
                            <br>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-6">
                                <input type="text" name="txtNombre" id="txtNombre" placeholder="Nombre *" required>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6">
                                <input type="text" name="txtAp_pat" id="txtAp_pat" placeholder="Apellido Paterno *"
                                            required>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6">
                                <input type="text" name="txtAp_mat" id="txtAp_mat" placeholder="Apellido Materno *"
                                            required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 me-trading-form-box">
                                <input type="text" placeholder="Día de cumpleaños *" disabled
                                            style="background: #fff; color:aqua !important;">
                                <select id="cmbDia_cumple" name="cmbDia_cumple" required style="height: 50px;">
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
                            <div class="col-lg-6 col-md-6 col-sm-6 me-trading-form-box">
                                        <input type="text" placeholder="Mes de Cumpleaños *" disabled
                                            style="background: #fff;color:aqua">
                                        <select id="cmbMes_cumple" name="cmbMes_cumple" required style="height: 50px;">
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
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 me-trading-form-box">
                                        <input type="text" placeholder="Estado donde votas *" disabled
                                            style="background: #fff;color:aqua">
                                        <select id="cmbEdo_votas" name="cmbEdo_votas" required style="height: 50px;">
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
                            <div class="col-lg-6 col-md-6 col-sm-6 me-trading-form-box">
                                        <input type="text" placeholder="Estado donde vives *" disabled
                                            style="background: #fff;color:aqua">
                                        <select id="cmbEdo_vives" name="cmbEdo_vives" required style="height: 50px;">
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
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <input type="email" name="txtEmail" id="txtEmail" placeholder="Email">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <input type="tel" name="txtCel" id="txtCel" placeholder="Celular (10 dígitos) *"
                                           maxlength="10"
                                           pattern="[0-9]{10}" required>
                            </div>
                        </div>
                            <!-- Select Autorización Comunicación -->
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 me-trading-form-box">
                                        <label><strong>¿Te podemos contactar? *</strong></label>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12" style="padding: 5px 15px;">
                                        <select id="cmbAutorizacion" name="cmbAutorizacion" required class="combo">
                                            <option selected hidden>Seleccionar</option>
                                            <option value="1">Si</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Select Eventos Comentarios -->
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 me-trading-form-box">
                                    <label><strong>¿Te gustaría asistir a eventos de Recuperemos México en tu distrito?
                                        *</strong></label>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12" style="padding: 5px 15px;">
                                    <select id="cmbEventos" name="cmbEventos" required class="combo">
                                        <option selected hidden>Seleccionar</option>
                                        <option value="1">Si</option>
                                        <option value="2">No</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Textarea Comentarios -->
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 me-trading-form-box">
                                    <label><strong>¡Nos interesa muchísimo saber lo que piensas! </strong>¿Quisieras
                                        compartirnos algún comentario o idea?</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 me-trading-form-box">
                                    <textarea class="form-control" id="ControlTextareaComentario" rows="3"
                                              placeholder="Comentario, idea, propuesta..."></textarea>
                                </div>
                            </div>
                            <input id="bEnviar" type="submit" onclick="sendPropuesta()" value="ENVIAR"
                                   class="me-btn me-btn-pink"/>
                        </div>
                    </div>
                </div>        
        </div>

    </form>

    <?php
    return ob_get_clean();
}