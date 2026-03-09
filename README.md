# Gestor de Seguimientos

**Gestor de Seguimientos** es un plugin ligero para WordPress diseñado para mejorar la experiencia de post-venta en tiendas que operan con repartos propios o por lotes. Permite ofrecer un sistema de rastreo global/ficticio que centraliza el estado del envío por producto, reduciendo la ansiedad del cliente y proyectando profesionalismo y compromiso.

## 🚀 Tecnologías Utilizadas

* **WordPress Core**: Arquitectura de plugins.
* **ACF (Advanced Custom Fields)**: Gestión de Custom Post Types y Metadatos.
* **WooCommerce**: Integración de datos de productos (título y descripción).
* **PHP / AJAX**: Procesamiento lógico y consultas asíncronas.
* **Vanilla JS / CSS3**: Interfaz de usuario dinámica y responsiva.

---

## ⚙️ Configuración del Entorno (ACF)

Para que el plugin funcione correctamente, se deben configurar los siguientes elementos en el panel de ACF:

### 1. Custom Post Type (CPT)

* **Nombre**: Seguimientos
* **Slug**: `seguimiento`
* **Público**: Se recomienda desactivar "Publicly Queryable" para que los códigos no sean indexables por buscadores.

### 2. Grupo de Campos (Field Group)

Asignar este grupo para que se muestre cuando el tipo de contenido sea igual a `seguimiento`.

| Etiqueta del Campo | Nombre del Campo (Slug) | Tipo de Campo | Configuración |
| --- | --- | --- | --- |
| **Código de seguimiento** | `codigo_de_seguimiento` | Texto | Obligatorio. Usado para la consulta. |
| **Estado del envío** | `estado_del_envio` | Select | Opciones: `desactivado`, `en_proceso`, `finalizado`. |
| **Artículo** | `gs_producto_id` | Post Object | Filtrar por `product`. Retornar Post ID. |

---

## 🛠️ Estructura del Plugin

```text
gestor-seguimientos/
├── gestor-seguimientos.php     # Inicialización y encolado de assets.
├── uninstall.php               # Limpieza de datos al eliminar el plugin.
├── includes/
│   ├── class-gs-shortcode.php  # Renderizado del formulario [rastreo_envios].
│   └── class-gs-ajax.php       # Lógica de consulta segura y validación.
└── assets/
    ├── css/gs-frontend.css     # Estilos visuales de la interfaz.
    └── js/gs-frontend.js       # Manejo de peticiones Fetch y UI dinámica.

```

---

## 🛡️ Seguridad y Auditoría

El sistema implementa múltiples capas de protección para garantizar la integridad del sitio:

* **Validación de Nonces**: Protección contra ataques CSRF en peticiones AJAX.
* **Rate Limiting**: Restricción de 20 consultas cada 5 minutos por IP mediante *Transients* de WordPress para evitar ataques de fuerza bruta.
* **Validación de Input**: Uso de Expresiones Regulares (Regex) para aceptar únicamente caracteres numéricos (0-9).
* **Escapado de Salida**: Uso de `esc_html`, `esc_attr` y `wp_kses_post` para prevenir vulnerabilidades XSS (Cross-Site Scripting).

---

## 📖 Uso del Shortcode

Para mostrar el buscador de seguimiento en cualquier página, entrada o maquetador visual (Elementor, Divi, etc.), utiliza:

`[rastreo_envios]`

### Lógica de Estados

1. **Desactivado**: El código no arrojará resultados en el frontend.
2. **En Proceso**: Muestra una tarjeta con indicador visual verde y los detalles del producto vinculado.
3. **Finalizado**: Muestra un mensaje de éxito/entrega junto con los detalles del producto.
