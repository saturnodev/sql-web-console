<div class="cyberpunk-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="neon-text">
            <i class="fas fa-user"></i> Perfil de Usuario
        </h3>
        <div class="cyberpunk-badge">
            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($currentUser ?? 'Usuario'); ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="cyberpunk-card p-3 mb-3">
                <h6 class="yellow-text">
                    <i class="fas fa-info-circle"></i> Información de Sesión
                </h6>
                <div id="sessionInfo">
                    <p><strong>Usuario:</strong> <span id="currentUsername">-</span></p>
                    <p><strong>Estado:</strong> <span id="sessionStatus">-</span></p>
                    <p><strong>Último acceso:</strong> <span id="lastAccess">-</span></p>
                </div>
            </div>
            
            <div class="cyberpunk-card p-3">
                <h6 class="yellow-text">
                    <i class="fas fa-cog"></i> Configuración
                </h6>
                <div class="mb-3">
                    <label class="form-label">Tema de interfaz</label>
                    <select class="form-control cyberpunk-input" id="themeSelect">
                        <option value="cyberpunk">Cyberpunk (Predeterminado)</option>
                        <option value="dark">Oscuro</option>
                        <option value="light">Claro</option>
                    </select>
                </div>
                <button class="btn cyberpunk-btn w-100" id="saveSettingsBtn">
                    <i class="fas fa-save"></i> Guardar Configuración
                </button>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="cyberpunk-card p-3 mb-3">
                <h6 class="yellow-text">
                    <i class="fas fa-chart-bar"></i> Estadísticas
                </h6>
                <div id="userStats">
                    <p><strong>Consultas ejecutadas:</strong> <span id="queryCount">-</span></p>
                    <p><strong>Tablas accedidas:</strong> <span id="tableCount">-</span></p>
                    <p><strong>Tiempo de sesión:</strong> <span id="sessionTime">-</span></p>
                </div>
            </div>
            
            <div class="cyberpunk-card p-3">
                <h6 class="yellow-text">
                    <i class="fas fa-shield-alt"></i> Seguridad
                </h6>
                <div class="mb-3">
                    <button class="btn cyberpunk-btn w-100 mb-2" id="changePasswordBtn">
                        <i class="fas fa-key"></i> Cambiar Token
                    </button>
                    <button class="btn btn-outline-danger w-100" id="logoutAllBtn">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Todas las Sesiones
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar token -->
<div class="modal fade cyberpunk-modal" id="changeTokenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title neon-text">
                    <i class="fas fa-key"></i> Generar Nuevo Token
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="cyberpunk-alert warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>¡Atención!</strong> Al generar un nuevo token, el token actual será invalidado.
                </div>
                <div class="mb-3">
                    <label class="form-label">Duración del nuevo token (horas)</label>
                    <input type="number" class="form-control cyberpunk-input" id="newTokenDuration" 
                           value="24" min="1" max="168">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn cyberpunk-btn" id="generateNewTokenBtn">
                    <i class="fas fa-plus"></i> Generar Token
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadProfileInfo();
    setupProfileEvents();
});

function setupProfileEvents() {
    // Guardar configuración
    $('#saveSettingsBtn').click(function() {
        saveSettings();
    });
    
    // Cambiar token
    $('#changePasswordBtn').click(function() {
        $('#changeTokenModal').modal('show');
    });
    
    // Generar nuevo token
    $('#generateNewTokenBtn').click(function() {
        generateNewToken();
    });
    
    // Cerrar todas las sesiones
    $('#logoutAllBtn').click(function() {
        if (confirm('¿Estás seguro de que quieres cerrar todas las sesiones?')) {
            logoutAll();
        }
    });
}

function loadProfileInfo() {
    // Cargar información de sesión
    $.post('api.php', {action: 'get_profile'}, function(response) {
        if (response.success) {
            $('#currentUsername').text(response.username);
            $('#sessionStatus').html('<span class="text-success">Activa</span>');
            $('#lastAccess').text(response.lastAccess);
            $('#queryCount').text(response.stats.queries || 0);
            $('#tableCount').text(response.stats.tables || 0);
            $('#sessionTime').text(response.stats.sessionTime || '0 min');
        }
    });
    
    // Cargar configuración guardada
    const savedTheme = localStorage.getItem('theme') || 'cyberpunk';
    $('#themeSelect').val(savedTheme);
}

function saveSettings() {
    const theme = $('#themeSelect').val();
    localStorage.setItem('theme', theme);
    
    // Aplicar tema
    applyTheme(theme);
    
    showAlert('success', 'Configuración guardada correctamente');
}

function applyTheme(theme) {
    // Aquí se puede implementar el cambio de tema
    console.log('Aplicando tema:', theme);
}

function generateNewToken() {
    const duration = parseInt($('#newTokenDuration').val());
    
    if (duration < 1 || duration > 168) {
        showAlert('warning', 'La duración debe estar entre 1 y 168 horas');
        return;
    }
    
    $('#generateNewTokenBtn').html('<div class="loading-spinner d-inline-block"></div> Generando...');
    $('#generateNewTokenBtn').prop('disabled', true);
    
    $.post('api.php', {
        action: 'generate_new_token',
        duration: duration
    }, function(response) {
        $('#generateNewTokenBtn').html('<i class="fas fa-plus"></i> Generar Token');
        $('#generateNewTokenBtn').prop('disabled', false);
        
        if (response.success) {
            $('#changeTokenModal').modal('hide');
            showNewToken(response.token, response.expiry);
        } else {
            showAlert('danger', response.message);
        }
    });
}

function showNewToken(token, expiry) {
    const modalHtml = `
        <div class="modal fade cyberpunk-modal" id="newTokenModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title pink-text">
                            <i class="fas fa-key"></i> Nuevo Token Generado
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="cyberpunk-alert warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>¡Importante!</strong> Guarda este token de forma segura. El token anterior ya no funcionará.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nuevo Token</label>
                            <div class="input-group">
                                <input type="text" class="form-control cyberpunk-input" id="newTokenValue" value="${token}" readonly>
                                <button class="btn cyberpunk-btn" onclick="copyNewToken()">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Expira</label>
                            <input type="text" class="form-control cyberpunk-input" value="${expiry}" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn cyberpunk-btn" data-bs-dismiss="modal">
                            <i class="fas fa-check"></i> Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    $('#newTokenModal').remove();
    
    // Agregar nuevo modal
    $('body').append(modalHtml);
    $('#newTokenModal').modal('show');
}

function copyNewToken() {
    const tokenInput = document.getElementById('newTokenValue');
    tokenInput.select();
    tokenInput.setSelectionRange(0, 99999);
    
    try {
        document.execCommand('copy');
        showAlert('success', 'Token copiado al portapapeles');
    } catch (err) {
        showAlert('danger', 'Error al copiar el token');
    }
}

function logoutAll() {
    $.post('api.php', {action: 'logout_all'}, function(response) {
        if (response.success) {
            showAlert('success', 'Todas las sesiones han sido cerradas');
            setTimeout(() => {
                window.location.href = 'index.php?action=login';
            }, 2000);
        } else {
            showAlert('danger', response.message);
        }
    });
}
</script> 