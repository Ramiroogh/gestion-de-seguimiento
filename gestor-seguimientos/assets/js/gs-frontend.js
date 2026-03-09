document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('gs-form-rastreo');
    const inputCodigo = document.getElementById('gs-input-codigo');
    const btnLimpiar = document.getElementById('gs-btn-limpiar');
    const btnConsultar = document.getElementById('gs-btn-consultar');
    const contenedorResultado = document.getElementById('gs-contenedor-resultado');

    if (!form) return;

    // Botón para limpiar el input (la crucecita roja)
    btnLimpiar.addEventListener('click', () => {
        inputCodigo.value = '';
        inputCodigo.focus();
        contenedorResultado.innerHTML = '';
        contenedorResultado.classList.add('gs-resultado-oculto');
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const codigo = inputCodigo.value.trim();
        if (!codigo) return;

        // Estado de carga
        btnConsultar.textContent = 'Consultando...';
        btnConsultar.disabled = true;
        contenedorResultado.innerHTML = '';
        contenedorResultado.classList.remove('gs-resultado-oculto');

        // Preparar datos para AJAX en WordPress
        const formData = new FormData();
        formData.append('action', 'gs_consultar_codigo');
        formData.append('nonce', gs_ajax_obj.nonce);
        formData.append('codigo', codigo);

        try {
            const response = await fetch(gs_ajax_obj.ajax_url, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                renderizarExito(result.data);
            } else {
                renderizarError(result.data.mensaje);
            }
        } catch (error) {
            renderizarError('Ocurrió un error de conexión. Intenta nuevamente.');
        } finally {
            btnConsultar.textContent = 'Consultar';
            btnConsultar.disabled = false;
        }
    });

    function renderizarError(mensaje) {
        contenedorResultado.innerHTML = `
            <div class="gs-tarjeta-error">
                <div>
                    <span class="gs-error-etiqueta">Error</span>
                    <p class="gs-error-mensaje">${mensaje}</p>
                </div>
                <div class="gs-icono-error">?</div>
            </div>
        `;
    }

    function renderizarExito(data) {
        let estadoHtml = '';
        
        if (data.estado === 'en_proceso') {
            estadoHtml = `
                <div class="gs-tarjeta-estado">
                    <div class="gs-punto-verde"></div>
                    <div>
                        <span class="gs-estado-etiqueta">Actual</span>
                        <p class="gs-estado-mensaje">En proceso de despacho</p>
                    </div>
                </div>
            `;
        } else if (data.estado === 'finalizado') {
            estadoHtml = `
                <div class="gs-tarjeta-estado gs-estado-finalizado">
                    <div>
                        <span class="gs-estado-etiqueta">Pedido entregado</span>
                        <p class="gs-estado-mensaje">Ya entregamos tu pedido, gracias.</p>
                    </div>
                    <div class="gs-icono-check">✔</div>
                </div>
            `;
        }

        let productoHtml = '';
        if (data.producto && data.producto.titulo) {
            productoHtml = `
                <div class="gs-detalle-articulo">
                    <span class="gs-articulo-etiqueta">Detalle del artículo</span>
                    <h3 class="gs-articulo-titulo">${data.producto.titulo}</h3>
                    <div class="gs-articulo-descripcion">${data.producto.descripcion}</div>
                </div>
            `;
        }

        contenedorResultado.innerHTML = `
            <div class="gs-tarjeta-codigo">
                <div>
                    <span class="gs-codigo-etiqueta">NÚMERO DE ENVÍO</span>
                    <p class="gs-codigo-valor">${data.codigo}</p>
                </div>
                <div class="gs-icono-caja">📦</div>
            </div>
            ${estadoHtml}
            ${productoHtml}
        `;
    }
});