@extends('layouts.app') @section('content')

<!-- sección de tablero dashboard -->
<div id="view-dashboard">
    <div class="welcome-banner">
        <div>
            <h1 style="font-size: 2rem; margin-bottom: 0.5rem">Bienvenido!</h1>
            <p style="color: #666">
                Proyecto de Ejemplo para catálogo de productos
            </p>
        </div>
        <div style="font-size: 4rem; color: #ffccaa"><span class="material-icon-theme--folder-store"></span></div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Total de productos</div>
            <div class="stat-number" id="stat-total">-</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">En Stock</div>
            <div class="stat-number" id="stat-stock">-</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Vencidos</div>
            <div class="stat-number" id="stat-vencidos">-</div>
        </div>
    </div>
</div>

<!-- SECCIÓN DE PRODUCTOS-->
<div id="view-productos" class="hidden">
    <h2 class="page-title">Listado de Productos</h2>

    <div class="table-container">
        <div class="header-row">
            <div>Img</div>
            <div>Código</div>
            <div>Cant.</div>
            <div>Nombre</div>
            <div>Precio</div>
            <div>Fecha Ingreso</div>
            <div>Vencimiento</div>
            <div>Acciones</div>
        </div>

        <div id="listaProductosContainer"></div>
    </div>

    <div id="loadingSpinner" class="loader-container" style="display: none">
        <div class="loader"></div>
    </div>

    <div
        id="paginationContainer"
        style="padding: 1rem; text-align: center"
    ></div>
</div>

<!-- Mpdl para crear y editar productos -->

<div id="modalProducto" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Crear Producto</h3>
            <button class="close-modal" onclick="cerrarModal()">x</button>
        </div>

        <div style="padding: 220px 0px" id="loader-editar" class="loader-container d-none">
            <div class="loader"></div>
        </div>

        <div style="padding: 220px 0px" id="loader-guardando" class="saving-container d-none">
            <div class="loader-saving">
                <div></div>
            </div>
            <div class="saving-text">Guardando cambios...</div>
        </div>


        <div id="mensaje-exito" class="saving-container d-none">
            <div class="success-message-box">
                <span class="success-icon"><span class="ooui--success"></span></span>
                <div class="success-text">¡Guardado Exitosamente!</div>
            </div>
        </div>


        <form id="formProducto">
            <div class="modal-body">
    
                <input type="hidden" id="producto_id" name="producto_id" />
 
                 <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Código *</label>
                        <input type="text" name="codigo_producto" id="codigo_producto" class="form-control" readonly style="background-color: #e9ecef; cursor: not-allowed;" />
                        <small class="text-danger" id="err_codigo"></small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre_producto" id="nombre_producto" class="form-control" />
                        <small class="text-danger" id="err_nombre"></small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Precio (Q) *</label>
                        <input type="number" step="0.01" name="precio" id="precio" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cantidad *</label>
                        <input type="number" name="cantidad" id="cantidad" class="form-control" />
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Fecha Ingreso *</label>
                        <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha Vencimiento *</label>
                        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Fotografía</label>
                    <div style="display: flex; gap: 10px; align-items: center">
                        <input type="file" name="fotografia" id="fotografia" class="form-control" accept="image/*" />
                    </div>
                    <small class="text-muted">Máx: 1.5MB (JPG, PNG, GIF)</small>
                    <small class="text-danger" id="err_foto"></small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-modal-save">Guardar</button>
            </div>
        </form>
    </div>
</div>
<!--Modal para ver detalles del producto -->
<div id="modalVerProducto" class="modal">
    <div class="modal-detail-content">
        <button class="btn-close-detail" onclick="cerrarModalDetalle()">
            X
        </button>

 
        <div
            style="padding: 120px 0px"
            id="loader-ver"
            class="loader-container d-none"
        >
            <div class="loader"></div>
        </div>

        <div id="content-ver" class="detail-grid">
            <div class="detail-img-box" id="detail-img-container"></div>

            <div class="detail-info-box">
                <h2 class="detail-title" id="view_nombre">NOMBRE PRODUCTO</h2>
                <div class="detail-price" id="view_precio">Q0.00</div>

                <div class="detail-row">
                    <span class="detail-label">Cant.</span>
                    <span class="detail-value" id="view_cantidad">0</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha Ingreso</span>
                    <span class="detail-value" id="view_ingreso">--/--/--</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Fecha Vencimiento *</span>
                    <span class="detail-value" id="view_vencimiento"
                        >--/--/--</span
                    >
                </div>
            </div>
        </div>
    </div>
</div>
@endsection @section('scripts')
<script src="{{ asset('js/products.js') }}"></script>
@endsection
