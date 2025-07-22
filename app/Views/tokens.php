<div class="cyberpunk-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="neon-text">
            <i class="fas fa-key"></i> Gestión de Tokens
        </h3>
        <button class="btn cyberpunk-btn" data-bs-toggle="modal" data-bs-target="#createTokenModal">
            <i class="fas fa-plus"></i> Crear Token
        </button>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="cyberpunk-card p-3">
                <h6 class="yellow-text">
                    <i class="fas fa-list"></i> Tokens Activos
                </h6>
                <div id="tokensList" class="cyberpunk-scrollbar" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center">
                        <div class="loading-spinner"></div>
                        <p class="mt-2">Cargando tokens...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="cyberpunk-card p-3">
                <h6 class="yellow-text">
                    <i class="fas fa-info-circle"></i> Información de Seguridad
                </h6>
                <div class="small">
                    <p><i class="fas fa-shield-alt"></i> Los tokens son únicos por usuario</p>
                    <p><i class="fas fa-clock"></i> Expiran automáticamente</p>
                    <p><i class="fas fa-lock"></i> Almacenados de forma segura</p>
                    <p><i class="fas fa-user-secret"></i> No se pueden recuperar</p>
                </div>
            </div>
            
            <div class="cyberpunk-card p-3 mt-3">
                <h6 class="yellow-text">
                    <i class="fas fa-cog"></i> Configuración
                </h6>
                <div class="mb-3">
                    <label class="form-label">Expiración (horas)</label>
                    <input type="number" class="form-control cyberpunk-input" id="tokenExpiry" 
                           value="24" min="1" max="168">
                </div>
                <button class="btn cyberpunk-btn w-100" id="updateConfigBtn">
                    <i class="fas fa-save"></i> Actualizar Configuración
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear token -->
<div class="modal fade cyberpunk-modal" id="createTokenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title neon-text">
                    <i class="fas fa-plus"></i> Crear Nuevo Token
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre de Usuario</label>
                    <input type="text" class="form-control cyberpunk-input" id="newTokenUsername" 
                           placeholder="Ingresa el nombre de usuario">
                </div>
                <div class="mb-3">
                    <label class="form-label">Duración (horas)</label>
                    <input type="number" class="form-control cyberpunk-input" id="newTokenDuration" 
                           value="24" min="1" max="168">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn cyberpunk-btn" id="createTokenBtn">
                    <i class="fas fa-plus"></i> Crear Token
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar token creado -->
<div class="modal fade cyberpunk-modal" id="showTokenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pink-text">
                    <i class="fas fa-key"></i> Token Creado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="cyberpunk-alert warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>¡Importante!</strong> Guarda este token de forma segura. No se podrá recuperar.
                </div>
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" class="form-control cyberpunk-input" id="showTokenUsername" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Token</label>
                    <div class="input-group">
                        <input type="text" class="form-control cyberpunk-input" id="showTokenValue" readonly>
                        <button class="btn cyberpunk-btn" id="copyTokenBtn">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Expira</label>
                    <input type="text" class="form-control cyberpunk-input" id="showTokenExpiry" readonly>
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

<!-- Modal de confirmación para revocar token -->
<div class="modal fade cyberpunk-modal" id="revokeTokenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title pink-text">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Revocación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="yellow-text">¿Estás seguro de que quieres revocar el token para el usuario <strong id="revokeUsername"></strong>?</p>
                <p class="text-muted">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn cyberpunk-btn" id="confirmRevokeBtn">
                    <i class="fas fa-trash"></i> Revocar Token
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let pendingRevokeUser = null;

$(document).ready(function() {
    loadTokens();
    setupTokenEvents();
});

function setupTokenEvents() {
    // Crear token
    $('#createTokenBtn').click(function() {
        createToken();
    });
    
    // Copiar token
    $('#copyTokenBtn').click(function() {
        copyTokenToClipboard();
    });
    
    // Confirmar revocación
    $('#confirmRevokeBtn').click(function() {
        if (pendingRevokeUser) {
            revokeToken(pendingRevokeUser);
            $('#revokeTokenModal').modal('hide');
        }
    });
    
    // Actualizar configuración
    $('#updateConfigBtn').click(function() {
        updateConfig();
    });
}

function loadTokens() {
    $('#tokensList').html(`
        <div class="text-center">
            <div class="loading-spinner"></div>
            <p class="mt-2">Cargando tokens...</p>
        </div>
    `);
    
    $.post('api.php', {action: 'get_tokens'}, function(response) {
        if (response.success) {
            displayTokens(response.tokens);
        } else {
            $('#tokensList').html(`
                <div class="cyberpunk-alert danger">
                    <i class="fas fa-exclamation-triangle"></i> ${response.message}
                </div>
            `);
        }
    });
}

function displayTokens(tokens) {
    if (tokens.length === 0) {
        $('#tokensList').html('<p class="text-muted">No hay tokens activos</p>');
        return;
    }
    
    let tokensHtml = '';
    tokens.forEach(token => {
        const created = new Date(token.created * 1000).toLocaleString();
        const expires = new Date(token.expires * 1000).toLocaleString();
        const isExpired = Date.now() > token.expires * 1000;
        
        tokensHtml += `
            <div class="cyberpunk-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="neon-text">
                            <i class="fas fa-user"></i> ${token.username}
                        </h6>
                        <div class="small text-muted">
                            <p><strong>Creado:</strong> ${created}</p>
                            <p><strong>Expira:</strong> ${expires}</p>
                            <p><strong>Estado:</strong> 
                                ${isExpired ? 
                                    '<span class="text-danger">Expirado</span>' : 
                                    '<span class="text-success">Activo</span>'
                                }
                            </p>
                        </div>
                    </div>
                    <div class="ms-3">
                        ${!isExpired ? `
                            <button class="btn btn-sm cyberpunk-btn" onclick="revokeTokenConfirm('${token.username}')">
                                <i class="fas fa-trash"></i> Revocar
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#tokensList').html(tokensHtml);
}

function createToken() {
    const username = $('#newTokenUsername').val().trim();
    const duration = parseInt($('#newTokenDuration').val());
    
    if (!username) {
        showAlert('warning', 'Por favor, ingresa un nombre de usuario');
        return;
    }
    
    if (duration < 1 || duration > 168) {
        showAlert('warning', 'La duración debe estar entre 1 y 168 horas');
        return;
    }
    
    $('#createTokenBtn').html('<div class="loading-spinner d-inline-block"></div> Creando...');
    $('#createTokenBtn').prop('disabled', true);
    
    $.post('api.php', {
        action: 'create_token',
        username: username,
        duration: duration
    }, function(response) {
        $('#createTokenBtn').html('<i class="fas fa-plus"></i> Crear Token');
        $('#createTokenBtn').prop('disabled', false);
        
        if (response.success) {
            $('#createTokenModal').modal('hide');
            showCreatedToken(username, response.token, duration);
            loadTokens(); // Recargar lista
        } else {
            showAlert('danger', response.message);
        }
    });
}

function showCreatedToken(username, token, duration) {
    const expiry = new Date(Date.now() + duration * 3600 * 1000).toLocaleString();
    
    $('#showTokenUsername').val(username);
    $('#showTokenValue').val(token);
    $('#showTokenExpiry').val(expiry);
    
    $('#showTokenModal').modal('show');
}

function copyTokenToClipboard() {
    const tokenInput = document.getElementById('showTokenValue');
    tokenInput.select();
    tokenInput.setSelectionRange(0, 99999);
    
    try {
        document.execCommand('copy');
        showAlert('success', 'Token copiado al portapapeles');
    } catch (err) {
        showAlert('danger', 'Error al copiar el token');
    }
}

function revokeTokenConfirm(username) {
    pendingRevokeUser = username;
    $('#revokeUsername').text(username);
    $('#revokeTokenModal').modal('show');
}

function revokeToken(username) {
    $.post('api.php', {
        action: 'revoke_token',
        username: username
    }, function(response) {
        if (response.success) {
            showAlert('success', 'Token revocado correctamente');
            loadTokens(); // Recargar lista
        } else {
            showAlert('danger', response.message);
        }
    });
}

function updateConfig() {
    const expiry = parseInt($('#tokenExpiry').val());
    
    if (expiry < 1 || expiry > 168) {
        showAlert('warning', 'La expiración debe estar entre 1 y 168 horas');
        return;
    }
    
    $.post('api.php', {
        action: 'update_config',
        token_expiry: expiry
    }, function(response) {
        if (response.success) {
            showAlert('success', 'Configuración actualizada');
        } else {
            showAlert('danger', response.message);
        }
    });
}
</script> 