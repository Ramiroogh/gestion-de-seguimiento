<?php
class GS_Shortcode {
    public function __construct() {
        add_shortcode( 'rastreo_envios', array( $this, 'renderizar_shortcode' ) );
    }

    public function renderizar_shortcode() {
        ob_start(); ?>
        <div class="gs-contenedor-principal">
            <div class="gs-cabecera">
                <h2 class="gs-titulo">Ingresá el número del envío</h2>
                <p class="gs-subtitulo">Ingresá el número de seguimiento de tu envío, omitiendo puntos y espacios.</p>
            </div>

            <form id="gs-form-rastreo" class="gs-formulario">
                <div class="gs-grupo-input">
                    <span class="gs-prefijo">Nº</span>
                    <input type="text" id="gs-input-codigo" name="codigo" placeholder="00000000" required autocomplete="off" maxlength="30" pattern="[0-9]+" inputmode="numeric">
                    <button type="button" id="gs-btn-limpiar" class="gs-btn-limpiar" aria-label="Limpiar">&times;</button>
                </div>
                <button type="submit" id="gs-btn-consultar" class="gs-btn-consultar">Consultar</button>
            </form>

            <div id="gs-contenedor-resultado" class="gs-resultado-oculto"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}