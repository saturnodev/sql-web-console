<div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-6 col-lg-4">
        <div class="cyberpunk-card p-4">
            <h2 class="text-center neon-text mb-4">
                <i class="fas fa-shield-alt"></i> Acceso Seguro
            </h2>
            <form id="loginForm">
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" class="form-control cyberpunk-input" id="username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Token</label>
                    <input type="password" class="form-control cyberpunk-input" id="token" required>
                </div>
                <button type="submit" class="btn cyberpunk-btn w-100">
                    <i class="fas fa-sign-in-alt"></i> Conectar
                </button>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        login();
    });
});

function login() {
    const username = $('#username').val();
    const token = $('#token').val();
    
    $.post('api.php', {
        action: 'login',
        username: username,
        token: token
    }, function(response) {
        if (response.success) {
            window.location.href = 'index.php?action=console';
        } else {
            showAlert('danger', response.message);
        }
    });
}
</script> 