<div class="cyberpunk-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="neon-text">
            <i class="fas fa-table"></i> Estructura de la Base de Datos
        </h3>
        <button class="btn cyberpunk-btn" id="refreshTablesBtn">
            <i class="fas fa-sync-alt"></i> Actualizar
        </button>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="cyberpunk-card p-3">
                <h6 class="yellow-text">
                    <i class="fas fa-list"></i> Tablas Disponibles
                </h6>
                <div id="tablesList" class="cyberpunk-scrollbar" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center">
                        <div class="loading-spinner"></div>
                        <p class="mt-2">Cargando tablas...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="cyberpunk-card p-3">
                <h6 class="yellow-text">
                    <i class="fas fa-info-circle"></i> Estructura de la Tabla
                </h6>
                <div id="tableStructure">
                    <p class="text-muted">Selecciona una tabla para ver su estructura</p>
                </div>
            </div>
            
            <div class="cyberpunk-card p-3 mt-3" id="tableData" style="display: none;">
                <h6 class="yellow-text">
                    <i class="fas fa-database"></i> Datos de la Tabla
                </h6>
                <div class="mb-3">
                    <div class="input-group">
                        <input type="number" class="form-control cyberpunk-input" id="limitRows" 
                               placeholder="Límite de filas" value="10" min="1" max="1000">
                        <button class="btn cyberpunk-btn" id="loadDataBtn">
                            <i class="fas fa-eye"></i> Ver Datos
                        </button>
                    </div>
                </div>
                <div id="tableDataContent">
                    <!-- Los datos se cargarán aquí -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentTable = null;

$(document).ready(function() {
    loadTables();
    setupTableEvents();
});

function setupTableEvents() {
    // Actualizar tablas
    $('#refreshTablesBtn').click(function() {
        loadTables();
    });
    
    // Cargar datos de tabla
    $('#loadDataBtn').click(function() {
        if (currentTable) {
            loadTableData(currentTable);
        }
    });
    
    // Enter en el límite de filas
    $('#limitRows').keypress(function(e) {
        if (e.which === 13 && currentTable) {
            loadTableData(currentTable);
        }
    });
}

function loadTables() {
    $('#tablesList').html(`
        <div class="text-center">
            <div class="loading-spinner"></div>
            <p class="mt-2">Cargando tablas...</p>
        </div>
    `);
    
    $.post('api.php', {action: 'get_tables'}, function(response) {
        if (response.success) {
            displayTables(response.tables);
        } else {
            $('#tablesList').html(`
                <div class="cyberpunk-alert danger">
                    <i class="fas fa-exclamation-triangle"></i> ${response.message}
                </div>
            `);
        }
    });
}

function displayTables(tables) {
    if (tables.length === 0) {
        $('#tablesList').html('<p class="text-muted">No se encontraron tablas</p>');
        return;
    }
    
    let tablesHtml = '';
    tables.forEach(table => {
        tablesHtml += `
            <div class="cyberpunk-card p-2 mb-2 table-item" data-table="${table}" style="cursor: pointer;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-table me-2 neon-text"></i>
                    <span class="text-light">${table}</span>
                </div>
            </div>
        `;
    });
    
    $('#tablesList').html(tablesHtml);
    
    // Event listeners para las tablas
    $('.table-item').click(function() {
        const tableName = $(this).data('table');
        selectTable(tableName);
    });
}

function selectTable(tableName) {
    currentTable = tableName;
    
    // Actualizar UI
    $('.table-item').removeClass('active');
    $(`.table-item[data-table="${tableName}"]`).addClass('active');
    
    // Cargar estructura
    loadTableStructure(tableName);
    
    // Mostrar sección de datos
    $('#tableData').show();
}

function loadTableStructure(tableName) {
    $('#tableStructure').html(`
        <div class="text-center">
            <div class="loading-spinner"></div>
            <p class="mt-2">Cargando estructura...</p>
        </div>
    `);
    
    $.post('api.php', {
        action: 'get_table_structure',
        table: tableName
    }, function(response) {
        if (response.success) {
            displayTableStructure(response.structure);
        } else {
            $('#tableStructure').html(`
                <div class="cyberpunk-alert danger">
                    <i class="fas fa-exclamation-triangle"></i> ${response.message}
                </div>
            `);
        }
    });
}

function displayTableStructure(structure) {
    if (structure.length === 0) {
        $('#tableStructure').html('<p class="text-muted">No se encontró estructura</p>');
        return;
    }
    
    let tableHtml = `
        <div class="cyberpunk-table-responsive">
            <table class="table table-dark cyberpunk-table">
                <thead>
                    <tr>
                        <th>Campo</th>
                        <th>Tipo</th>
                        <th>Nulo</th>
                        <th>Clave</th>
                        <th>Predeterminado</th>
                        <th>Extra</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    structure.forEach(field => {
        tableHtml += `
            <tr>
                <td><strong>${field.Field}</strong></td>
                <td><code>${field.Type}</code></td>
                <td>${field.Null === 'YES' ? '<span class="text-warning">Sí</span>' : '<span class="text-success">No</span>'}</td>
                <td>${field.Key ? `<span class="cyberpunk-badge">${field.Key}</span>` : '-'}</td>
                <td>${field.Default || '-'}</td>
                <td>${field.Extra || '-'}</td>
            </tr>
        `;
    });
    
    tableHtml += '</tbody></table></div>';
    
    $('#tableStructure').html(tableHtml);
}

function loadTableData(tableName) {
    const limit = $('#limitRows').val() || 10;
    
    $('#tableDataContent').html(`
        <div class="text-center">
            <div class="loading-spinner"></div>
            <p class="mt-2">Cargando datos...</p>
        </div>
    `);
    
    const sql = `SELECT * FROM \`${tableName}\` LIMIT ${parseInt(limit)}`;
    
    $.post('api.php', {
        action: 'execute_query',
        sql: sql
    }, function(response) {
        if (response.success) {
            displayTableData(response.data);
        } else {
            $('#tableDataContent').html(`
                <div class="cyberpunk-alert danger">
                    <i class="fas fa-exclamation-triangle"></i> ${response.message}
                </div>
            `);
        }
    });
}

function displayTableData(data) {
    if (data.type !== 'SELECT' || data.data.length === 0) {
        $('#tableDataContent').html('<p class="text-muted">No se encontraron datos</p>');
        return;
    }
    
    const columns = Object.keys(data.data[0]);
    let tableHtml = `
        <div class="cyberpunk-table-responsive">
            <table class="table table-dark cyberpunk-table">
                <thead>
                    <tr>
    `;
    
    columns.forEach(column => {
        tableHtml += `<th>${column}</th>`;
    });
    
    tableHtml += '</tr></thead><tbody>';
    
    data.data.forEach(row => {
        tableHtml += '<tr>';
        columns.forEach(column => {
            const value = row[column];
            if (value === null) {
                tableHtml += '<td><span class="text-muted">NULL</span></td>';
            } else {
                tableHtml += `<td>${escapeHtml(String(value))}</td>`;
            }
        });
        tableHtml += '</tr>';
    });
    
    tableHtml += '</tbody></table></div>';
    
    $('#tableDataContent').html(tableHtml);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<style>
.table-item.active {
    background: rgba(0, 255, 255, 0.2) !important;
    border-color: var(--cyberpunk-neon) !important;
}

.table-item:hover {
    background: rgba(0, 255, 255, 0.1) !important;
    border-color: var(--cyberpunk-neon) !important;
}
</style> 