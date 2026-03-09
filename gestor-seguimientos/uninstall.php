<?php
/**
 * Archivo de desinstalación del plugin.
 * Se ejecuta automáticamente cuando el usuario hace clic en "Borrar" en la lista de plugins.
 */

// Medida de seguridad vital: Si el archivo no es llamado por WordPress, salimos inmediatamente.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// 1. Definimos los argumentos para buscar todos los envíos registrados
$args = array(
    'post_type'      => 'gs_envio',
    'posts_per_page' => -1,    // Traer todos sin límite
    'post_status'    => 'any', // Traer publicados, borradores, papelera, etc.
    'fields'         => 'ids'  // Solo pedimos los IDs para que la consulta sea más ligera
);

$envios = get_posts( $args );

// 2. Recorremos cada envío y lo borramos de la base de datos de forma permanente
if ( $envios ) {
    foreach ( $envios as $envio_id ) {
        // El segundo parámetro en 'true' fuerza el borrado permanente, saltándose la papelera
        wp_delete_post( $envio_id, true );
    }
}

// Nota sobre ACF:
// No incluimos código para borrar el Grupo de Campos de ACF aquí.
// Es más seguro dejar que administres los campos desde el panel de ACF, 
// así evitas perder la configuración accidentalmente si solo estabas reinstalando el plugin.