<div class="cyberpunk-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="neon-text">
            <i class="fas fa-terminal"></i> Consola SQL
        </h3>
        <div class="cyberpunk-badge">
            <i class="fas fa-user"></i> <?php echo htmlspecialchars($currentUser ?? 'Usuario'); ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label class="form-label neon-text">
                    <i class="fas fa-code"></i> Consulta SQL
                </label>
                <textarea class="form-control cyberpunk-textarea" id="sqlQuery" 
                          placeholder="Escribe tu consulta SQL aquí..."></textarea>
            </div>
            
            <div class="d-flex gap-2 mb-3">
                <button class="btn cyberpunk-btn" id="executeBtn">
                    <i class="fas fa-play"></i> Ejecutar
                </button>
                <button class="btn cyberpunk-btn" id="clearBtn">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
                <button class="btn cyberpunk-btn" id="formatBtn">
                    <i class="fas fa-magic"></i> Formatear
                </button>
            </div>
            
            <div id="queryHistory" class="mb-3">
                <h5 class="pink-text">Historial de Consultas</h5>
                <div id="historyList" class="cyberpunk-scrollbar" style="max-height: 200px; overflow-y: auto;">
                    <!-- Historial se cargará aquí -->
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="cyberpunk-card p-3 mb-3">
                <h6 class="yellow-text">
                    <i class="fas fa-info-circle"></i> Información
                </h6>
                <div id="queryInfo">
                    <p><strong>Estado:</strong> <span id="connectionStatus">Desconectado</span></p>
                    <p><strong>Base de datos:</strong> <span id="dbName">-</span></p>
                    <p><strong>Tablas:</strong> <span id="tableCount">-</span></p>
                </div>
            </div>
            
            <div class="cyberpunk-card p-3">
                <h6 class="yellow-text">
                    <i class="fas fa-lightbulb"></i> Sugerencias
                </h6>
                <div class="small">
                    <p><i class="fas fa-check"></i> Usa <code>SELECT</code> para consultas</p>
                    <p><i class="fas fa-check"></i> <code>INSERT</code> para agregar datos</p>
                    <p><i class="fas fa-check"></i> <code>UPDATE</code> para modificar</p>
                    <p><i class="fas fa-check"></i> <code>DELETE</code> para eliminar</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Resultados -->
    <div id="queryResults" class="mt-4" style="display: none;">
        <h5 class="neon-text">
            <i class="fas fa-table"></i> Resultados
        </h5>
        <div id="resultsContent" class="cyberpunk-scrollbar" style="max-height: 400px; overflow-y: auto;">
            <!-- Los resultados se mostrarán aquí -->
        </div>
    </div>
</div>

<!-- Modal de confirmación para consultas destructivas -->
<div class="modal fade cyberpunk-modal" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pink-text">
                    <i class="fas fa-exclamation-triangle"></i> Confirmación Requerida
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="yellow-text">Esta consulta puede modificar o eliminar datos. ¿Estás seguro de que quieres continuar?</p>
                <div class="cyberpunk-card p-3">
                    <code id="confirmQuery" class="text-light"></code>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn cyberpunk-btn" id="confirmExecute">
                    <i class="fas fa-check"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let queryHistory = [];
let pendingQuery = null;

$(document).ready(function() {
    loadDatabaseInfo();
    setupConsoleEvents();
});

function setupConsoleEvents() {
    // Ejecutar consulta
    $('#executeBtn').click(function() {
        executeQuery();
    });
    
    // Limpiar consola
    $('#clearBtn').click(function() {
        $('#sqlQuery').val('');
        $('#queryResults').hide();
    });
    
    // Formatear SQL
    $('#formatBtn').click(function() {
        formatSQL();
    });
    
    // Ejecutar con Enter (Ctrl+Enter)
    $('#sqlQuery').keydown(function(e) {
        if (e.ctrlKey && e.keyCode === 13) {
            executeQuery();
        }
    });
    
    // Confirmar ejecución destructiva
    $('#confirmExecute').click(function() {
        if (pendingQuery) {
            executeQueryDirect(pendingQuery);
            $('#confirmModal').modal('hide');
        }
    });
}

function executeQuery() {
    const sql = $('#sqlQuery').val().trim();
    if (!sql) {
        showAlert('warning', 'Por favor, escribe una consulta SQL');
        return;
    }
    
    // Verificar si es una consulta destructiva
    if (isDestructiveQuery(sql)) {
        pendingQuery = sql;
        $('#confirmQuery').text(sql);
        $('#confirmModal').modal('show');
        return;
    }
    
    executeQueryDirect(sql);
}

function executeQueryDirect(sql) {
    $('#executeBtn').html('<div class="loading-spinner d-inline-block"></div> Ejecutando...');
    $('#executeBtn').prop('disabled', true);
    
    $.post('api.php', {
        action: 'execute_query',
        sql: sql
    }, function(response) {
        $('#executeBtn').html('<i class="fas fa-play"></i> Ejecutar');
        $('#executeBtn').prop('disabled', false);
        
        if (response.success) {
            addToHistory(sql);
            displayResults(response.data);
            showAlert('success', response.message);
        } else {
            showAlert('danger', response.message);
        }
    }).fail(function() {
        $('#executeBtn').html('<i class="fas fa-play"></i> Ejecutar');
        $('#executeBtn').prop('disabled', false);
        showAlert('danger', 'Error de conexión');
    });
}

function isDestructiveQuery(sql) {
    const destructiveKeywords = ['DROP', 'DELETE', 'TRUNCATE', 'ALTER', 'UPDATE'];
    const upperSql = sql.toUpperCase();
    
    return destructiveKeywords.some(keyword => upperSql.includes(keyword));
}

function displayResults(data) {
    $('#queryResults').show();
    
    if (data.type === 'SELECT') {
        if (data.data.length === 0) {
            $('#resultsContent').html('<p class="text-muted">No se encontraron resultados</p>');
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
                tableHtml += `<td>${escapeHtml(row[column] ?? 'NULL')}</td>`;
            });
            tableHtml += '</tr>';
        });
        
        tableHtml += '</tbody></table></div>';
        
        $('#resultsContent').html(tableHtml);
    } else {
        $('#resultsContent').html(`
            <div class="cyberpunk-alert success">
                <i class="fas fa-check-circle"></i> ${data.message}
                <br><strong>Filas afectadas:</strong> ${data.rowCount}
            </div>
        `);
    }
}

function addToHistory(sql) {
    queryHistory.unshift({
        sql: sql,
        timestamp: new Date().toLocaleString()
    });
    
    // Mantener solo los últimos 10
    if (queryHistory.length > 10) {
        queryHistory = queryHistory.slice(0, 10);
    }
    
    updateHistoryDisplay();
}

function updateHistoryDisplay() {
    let historyHtml = '';
    queryHistory.forEach((item, index) => {
        historyHtml += `
            <div class="cyberpunk-card p-2 mb-2" style="cursor: pointer;" onclick="loadQueryFromHistory('${index}')">
                <div class="small text-muted">${item.timestamp}</div>
                <div class="text-truncate">${escapeHtml(item.sql)}</div>
            </div>
        `;
    });
    
    $('#historyList').html(historyHtml || '<p class="text-muted">No hay consultas en el historial</p>');
}

function loadQueryFromHistory(index) {
    $('#sqlQuery').val(queryHistory[index].sql);
}

function formatSQL() {
    const sql = $('#sqlQuery').val();
    if (!sql) return;
    
    // Formateo básico de SQL
    let formatted = sql
        .replace(/\b(SELECT|FROM|WHERE|AND|OR|ORDER BY|GROUP BY|HAVING|INSERT INTO|UPDATE|DELETE FROM|CREATE TABLE|ALTER TABLE|DROP TABLE)\b/gi, '\n$1')
        .replace(/\b(JOIN|LEFT JOIN|RIGHT JOIN|INNER JOIN|OUTER JOIN)\b/gi, '\n$1')
        .replace(/,\s*/g, ',\n  ')
        .replace(/\s+/g, ' ')
        .trim();
    
    $('#sqlQuery').val(formatted);
}

function loadDatabaseInfo() {
    $.post('api.php', {action: 'get_tables'}, function(response) {
        if (response.success) {
            $('#tableCount').text(response.tables.length);
            $('#connectionStatus').html('<span class="text-success">Conectado</span>');
        } else {
            $('#connectionStatus').html('<span class="text-danger">Error</span>');
        }
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script> 