$(document).ready(function () {

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Sidebar

    $('.nav-item').click(function () {
        $('.nav-item').removeClass('active');
        $(this).addClass('active');

        let target = $(this).data('target');

        // GUARDAMOS EN MEMORIA LA PESTAÑA ACTUAL
        localStorage.setItem('activeTab', target);

        if (target === 'dashboard') {
            $('#view-dashboard').removeClass('hidden');
            $('#view-productos').addClass('hidden');
            cargarStats();
        } else {
            $('#view-dashboard').addClass('hidden');
            $('#view-productos').removeClass('hidden');
            cargarProductos();
        }

        // Cerrar menú móvil si está abierto
        if ($(window).width() <= 992) {
            $('.sidebar').removeClass('active');
            $('#mobileOverlay').removeClass('active');
        }
    });

    // Resumen de bignumbers para totales en dashboard

    function cargarStats() {
        $.get('/products?stats=true', function (data) {
            $('#stat-total').text(data.total);
            $('#stat-stock').text(data.stock);
            $('#stat-vencidos').text(data.vencidos);
        });
    }

    // Consulta de productos

    function cargarProductos(page = 1) {
        $('#loadingSpinner').show();
        $('#listaProductosContainer').empty();

        $.get('/products?page=' + page, function (data) {
            $('#loadingSpinner').hide();
            renderTabla(data.data);
            renderPaginacion(data);
        });
    }

    function renderTabla(productos) {
        let html = '';
        if (productos.length === 0) {
            html = '<div style="padding: 2rem; text-align:center; color: #666;">No hay productos registrados.</div>';
        } else {
            productos.forEach(p => {
                let img = p.fotografia ?
                    `<img src="/storage/${p.fotografia}" class="badge-img">` :
                    `<div class="badge-img">📷</div>`;

                let fIngreso = p.fecha_ingreso.split('T')[0];
                let fVenc = p.fecha_vencimiento.split('T')[0];

                html += `
                <div class="data-row">
                    <div>${img}</div>
                    <div class="text-muted font-bold" style="color:#007bff">${p.codigo_producto}</div>
                    <div>${p.cantidad}</div>
                    <div>${p.nombre_producto}</div>
                    <div class="price-tag">Q${parseFloat(p.precio).toFixed(2)}</div>
                    <div>${fIngreso}</div>
                    <div>${fVenc}</div>
                    <div>
                        <button class="action-btn btn-ver" data-id="${p.id}" title="Ver Detalle"><span class="mdi--eye"></span></button>
                        <button class="action-btn btn-editar" data-id="${p.id}" title="Editar"><span class="tdesign--edit-filled"></span></button>
                        <button class="action-btn btn-eliminar" data-id="${p.id}" title="Eliminar"><span class="material-symbols--delete"></span></button>
                    </div>
                </div>`;
            });
        }
        $('#listaProductosContainer').html(html);
    }

    window.renderPaginacion = function (data) {
        let html = '';
        if (data.prev_page_url) {
            html += `<button class="btn btn-primary btn-sm" onclick="cambiarPagina(${data.current_page - 1})">Anterior</button> `;
        }
        if (data.last_page > 1) {
            html += `<span style="margin: 0 10px;">Página ${data.current_page} de ${data.last_page}</span>`;
        }
        if (data.next_page_url) {
            html += ` <button class="btn btn-primary btn-sm" onclick="cambiarPagina(${data.current_page + 1})">Siguiente</button>`;
        }
        $('#paginationContainer').html(html);
    };

    window.cambiarPagina = function (page) {
        cargarProductos(page);
    };

    // Crud de editar y crear producto

    $('#btnGlobalAgregar').click(() => {
        $('#formProducto')[0].reset();
        $('#producto_id').val('');
        $('#modalTitle').text('Crear Producto');


        $('.text-danger').text('');


        $.get('/products/next-code', function (data) {
            $('#codigo_producto').val(data.code);
        });

        $('#loader-editar').addClass('d-none');
        $('#formProducto').removeClass('d-none');
        $('.modal-footer').removeClass('d-none');

        $('#modalProducto').addClass('active');
    });

    $(document).on('click', '.btn-editar', function () {
        let id = $(this).data('id');

        $('#modalTitle').text('Cargando...');
        $('#modalProducto').addClass('active');

        $('#loader-editar').removeClass('d-none');
        $('#formProducto').addClass('d-none');
        $('.modal-footer').addClass('d-none');

        $.get(`/products/${id}`, function (data) {
            $('#producto_id').val(data.id);
            $('#codigo_producto').val(data.codigo_producto);
            $('#nombre_producto').val(data.nombre_producto);
            $('#cantidad').val(data.cantidad);
            $('#precio').val(data.precio);
            $('#fecha_ingreso').val(data.fecha_ingreso.split('T')[0]);
            $('#fecha_vencimiento').val(data.fecha_vencimiento.split('T')[0]);

            $('#modalTitle').text('Editar Producto');
            $('#loader-editar').addClass('d-none');
            $('#formProducto').removeClass('d-none');
            $('.modal-footer').removeClass('d-none');
        });
    });

  // Código para actualziar datos (CON NUEVA ANIMACIÓN)
    $('#formProducto').submit(function (e) {
        e.preventDefault();

        if (!validarFormulario()) return;

        // 1. Ocultar form y botones
        $('#formProducto').addClass('d-none');
        
        // 2. Mostrar Loader de "Guardando..."
        $('#loader-guardando').removeClass('d-none');

        let formData = new FormData(this);
        let id = $('#producto_id').val();
        let url = id ? `/products/${id}` : '/products';

        if (id) formData.append('_method', 'PUT');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
               
                $('#loader-guardando').addClass('d-none');
                $('#mensaje-exito').removeClass('d-none');
                
               
                cargarProductos();
                cargarStats();
 
                setTimeout(() => {
                    cerrarModal();
                    
                    
                    $('#mensaje-exito').addClass('d-none');
                   
                }, 1500);
            },
            error: function (xhr) {
   
                $('#loader-guardando').addClass('d-none');
                $('#formProducto').removeClass('d-none');
                
                if (xhr.status === 422) {
                    alert('Error de validación: Revise los campos.');
                } else {
                    alert('Error al guardar el producto.');
                }
            }
        });
    });

    window.cerrarModal = function () {
        $('#modalProducto').removeClass('active');
    };

    // Código para el modal que muestra los detalles

    $(document).on('click', '.btn-ver', function () {
        let id = $(this).data('id');

        $('#modalVerProducto').addClass('active');
        $('#loader-ver').removeClass('d-none');
        $('#content-ver').addClass('d-none');

        $.get(`/products/${id}`, function (data) {

            $('#view_nombre').text(data.nombre_producto);
            $('#view_precio').text('Q' + parseFloat(data.precio).toFixed(2));
            $('#view_cantidad').text(data.cantidad);

            let fIngreso = new Date(data.fecha_ingreso).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: '2-digit' });
            let fVenc = new Date(data.fecha_vencimiento).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: '2-digit' });

            $('#view_ingreso').text(fIngreso);
            $('#view_vencimiento').text(fVenc);

            let imgContainer = $('#detail-img-container');
            imgContainer.empty();

            if (data.fotografia) {
                imgContainer.html(`<img src="/storage/${data.fotografia}" class="detail-img-full">`);
            } else {
                imgContainer.html(`
                    <div style="text-align:center;">
                        <div style="font-size:4rem; color: white; line-height:1; margin-bottom: 10px;">●</div>
                        <svg width="80" height="50" viewBox="0 0 100 60" fill="white" style="opacity:0.7;">
                            <path d="M10 50 L30 20 L50 50 Z" />
                            <path d="M40 50 L60 10 L90 50 Z" />
                        </svg>
                    </div>
                `);
            }

            $('#loader-ver').addClass('d-none');
            $('#content-ver').removeClass('d-none');
        });
    });

    window.cerrarModalDetalle = function () {
        $('#modalVerProducto').removeClass('active');
    };

    $(document).on('click', '.btn-eliminar', function () {
        if (confirm('¿Estás seguro de eliminar este producto?')) {
            $.ajax({
                url: `/products/${$(this).data('id')}`,
                type: 'DELETE',
                success: function () {
                    cargarProductos();
                    cargarStats();
                }
            });
        }
    });

    function validarFormulario() {
        let codigo = $('#codigo_producto').val();
        if (!codigo) { alert('El código es obligatorio'); return false; }
        return true;
    }

    // BTN para abrir el menu en móvil

    $('#btnMobileMenu').click(function (e) {
        e.stopPropagation();
        $('.sidebar').addClass('active');
        $('#mobileOverlay').addClass('active');
    });

    $('#mobileOverlay').click(function () {
        if ($(window).width() <= 992) {
            $('.sidebar').removeClass('active');
            $('#mobileOverlay').removeClass('active');
        }
    });

    // RECUPERAR ULTIMA PESTAÑA AL INICIAR

    let tabGuardada = localStorage.getItem('activeTab');
    if (tabGuardada && tabGuardada !== 'dashboard') {
        // Si había una pestaña guardada (ej: productos), simulamos click
        $(`.nav-item[data-target="${tabGuardada}"]`).click();
    } else {
        // Si no, cargamos dashboard por defecto
        cargarStats();
    }
});