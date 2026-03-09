<?php
class GS_Ajax {
    public function __construct() {
        add_action( 'wp_ajax_nopriv_gs_consultar_codigo', array( $this, 'procesar_consulta' ) );
        add_action( 'wp_ajax_gs_consultar_codigo', array( $this, 'procesar_consulta' ) );
    }

    public function procesar_consulta() {
        // SEGURIDAD 1: Verificación de Nonce (Previene CSRF)
        check_ajax_referer( 'gs_seguridad_nonce', 'nonce' );

        // SEGURIDAD 2: Prevención de Fuerza Bruta (Rate Limiting)
        $ip_usuario = $_SERVER['REMOTE_ADDR'];
        $transient_name = 'gs_rastreo_limit_' . md5( $ip_usuario );
        $intentos = get_transient( $transient_name ) ? (int) get_transient( $transient_name ) : 0;

        // Limitar a 20 consultas por cada 5 minutos por IP
        if ( $intentos >= 20 ) {
            wp_send_json_error( array( 'mensaje' => 'Demasiadas consultas. Por seguridad, intenta nuevamente en unos minutos.' ) );
        }
        set_transient( $transient_name, $intentos + 1, 5 * MINUTE_IN_SECONDS );

        // Limpieza básica
        $codigo_ingresado = sanitize_text_field( wp_unslash( $_POST['codigo'] ) );

        if ( empty( $codigo_ingresado ) ) {
            wp_send_json_error( array( 'mensaje' => 'Por favor, ingresa un código.' ) );
        }

        // SEGURIDAD 3: Validación estricta con Regex (Lista Blanca)
        // Solo Números, con longitu de 30 caracteres.
        if ( ! preg_match( '/^[0-9]{5,30}$/', $codigo_ingresado ) ) {
            wp_send_json_error( array( 'mensaje' => 'El formato del código no es válido.' ) );
        }

        // Búsqueda en la base de datos (WP_Query ya protege contra SQLi)
        $args = array(
            'post_type'  => 'seguimiento', 
            'meta_query' => array(
                array(
                    'key'     => 'codigo_de_seguimiento', 
                    'value'   => $codigo_ingresado,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        );

        $query = new WP_Query( $args );

        if ( ! $query->have_posts() ) {
            wp_send_json_error( array( 'mensaje' => 'No se encontraron resultados' ) );
        }

        $query->the_post();
        $post_id = get_the_ID();
        
        $estado = get_field( 'estado_del_envio', $post_id );
        
        if ( $estado === 'desactivado' || empty( $estado ) ) {
            wp_send_json_error( array( 'mensaje' => 'No se encontraron resultados' ) );
        }

        $producto_id = get_field( 'gs_producto_id', $post_id );
        $producto_data = array();

        if ( $producto_id && class_exists( 'WooCommerce' ) ) {
            $producto = wc_get_product( $producto_id );
            if ( $producto ) {
                // SEGURIDAD 4: Escapado de salida (Previene XSS reflejado/almacenado)
                $producto_data = array(
                    'titulo'      => esc_html( $producto->get_name() ), 
                    'descripcion' => wp_kses_post( $producto->get_short_description() ) 
                );
            }
        }

        wp_reset_postdata();

        wp_send_json_success( array(
            'codigo'   => esc_html( $codigo_ingresado ), // Escapamos el código que devolvemos
            'estado'   => esc_attr( $estado ), 
            'producto' => $producto_data
        ));
    }
}