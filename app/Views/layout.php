<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Web Console - Cyberpunk</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --cyberpunk-neon: #00ffff;
            --cyberpunk-pink: #ff0080;
            --cyberpunk-yellow: #ffff00;
            --cyberpunk-purple: #8000ff;
            --cyberpunk-dark: #0a0a0a;
            --cyberpunk-darker: #050505;
            --cyberpunk-gray: #1a1a1a;
            --cyberpunk-light-gray: #2a2a2a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Rajdhani', sans-serif;
            background: linear-gradient(135deg, var(--cyberpunk-dark) 0%, var(--cyberpunk-darker) 100%);
            color: #ffffff;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .cyberpunk-bg {
            background: linear-gradient(45deg, var(--cyberpunk-dark), var(--cyberpunk-gray));
            border: 1px solid var(--cyberpunk-neon);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
        }

        .cyberpunk-card {
            background: rgba(26, 26, 26, 0.9);
            border: 2px solid var(--cyberpunk-neon);
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .cyberpunk-btn {
            background: linear-gradient(45deg, var(--cyberpunk-neon), var(--cyberpunk-purple));
            border: none;
            color: var(--cyberpunk-dark);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .cyberpunk-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.4);
            color: var(--cyberpunk-dark);
        }

        .cyberpunk-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .cyberpunk-btn:hover::before {
            left: 100%;
        }

        .cyberpunk-input {
            background: rgba(10, 10, 10, 0.8);
            border: 2px solid var(--cyberpunk-neon);
            color: #ffffff;
            border-radius: 5px;
            padding: 10px 15px;
            font-family: 'Orbitron', monospace;
        }

        .cyberpunk-input:focus {
            outline: none;
            border-color: var(--cyberpunk-pink);
            box-shadow: 0 0 15px rgba(255, 0, 128, 0.3);
        }

        .cyberpunk-textarea {
            background: rgba(10, 10, 10, 0.9);
            border: 2px solid var(--cyberpunk-neon);
            color: #ffffff;
            border-radius: 5px;
            padding: 15px;
            font-family: 'Orbitron', monospace;
            resize: vertical;
            min-height: 150px;
        }

        .cyberpunk-textarea:focus {
            outline: none;
            border-color: var(--cyberpunk-pink);
            box-shadow: 0 0 20px rgba(255, 0, 128, 0.3);
        }

        .cyberpunk-table {
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid var(--cyberpunk-neon);
            border-radius: 5px;
        }

        .cyberpunk-table th {
            background: var(--cyberpunk-purple);
            color: #ffffff;
            border-color: var(--cyberpunk-neon);
            font-weight: 600;
        }

        .cyberpunk-table td {
            border-color: var(--cyberpunk-neon);
            color: #ffffff;
        }

        .cyberpunk-table tbody tr:hover {
            background: rgba(0, 255, 255, 0.1);
        }

        .neon-text {
            color: var(--cyberpunk-neon);
            text-shadow: 0 0 10px var(--cyberpunk-neon);
        }

        .pink-text {
            color: var(--cyberpunk-pink);
            text-shadow: 0 0 10px var(--cyberpunk-pink);
        }

        .yellow-text {
            color: var(--cyberpunk-yellow);
            text-shadow: 0 0 10px var(--cyberpunk-yellow);
        }

        .cyberpunk-header {
            background: linear-gradient(45deg, var(--cyberpunk-dark), var(--cyberpunk-purple));
            border-bottom: 3px solid var(--cyberpunk-neon);
            padding: 20px 0;
        }

        .cyberpunk-title {
            font-family: 'Orbitron', monospace;
            font-weight: 900;
            font-size: 2.5rem;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .cyberpunk-sidebar {
            background: rgba(10, 10, 10, 0.95);
            border-right: 2px solid var(--cyberpunk-neon);
            min-height: 100vh;
        }

        .cyberpunk-nav {
            list-style: none;
            padding: 0;
        }

        .cyberpunk-nav li {
            margin: 10px 0;
        }

        .cyberpunk-nav a {
            display: block;
            padding: 15px 20px;
            color: #ffffff;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .cyberpunk-nav a:hover {
            background: rgba(0, 255, 255, 0.1);
            border-left-color: var(--cyberpunk-neon);
            color: var(--cyberpunk-neon);
        }

        .cyberpunk-nav a.active {
            background: rgba(0, 255, 255, 0.2);
            border-left-color: var(--cyberpunk-neon);
            color: var(--cyberpunk-neon);
        }

        .cyberpunk-alert {
            border: 2px solid;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }

        .cyberpunk-alert.success {
            border-color: var(--cyberpunk-neon);
            background: rgba(0, 255, 255, 0.1);
            color: var(--cyberpunk-neon);
        }

        .cyberpunk-alert.danger {
            border-color: var(--cyberpunk-pink);
            background: rgba(255, 0, 128, 0.1);
            color: var(--cyberpunk-pink);
        }

        .cyberpunk-alert.warning {
            border-color: var(--cyberpunk-yellow);
            background: rgba(255, 255, 0, 0.1);
            color: var(--cyberpunk-yellow);
        }

        .loading-spinner {
            border: 3px solid var(--cyberpunk-gray);
            border-top: 3px solid var(--cyberpunk-neon);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .cyberpunk-modal .modal-content {
            background: var(--cyberpunk-dark);
            border: 2px solid var(--cyberpunk-neon);
            border-radius: 10px;
        }

        .cyberpunk-modal .modal-header {
            border-bottom: 1px solid var(--cyberpunk-neon);
            background: var(--cyberpunk-purple);
        }

        .cyberpunk-modal .modal-footer {
            border-top: 1px solid var(--cyberpunk-neon);
        }

        .cyberpunk-badge {
            background: var(--cyberpunk-purple);
            color: #ffffff;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .cyberpunk-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .cyberpunk-scrollbar::-webkit-scrollbar-track {
            background: var(--cyberpunk-dark);
        }

        .cyberpunk-scrollbar::-webkit-scrollbar-thumb {
            background: var(--cyberpunk-neon);
            border-radius: 4px;
        }

        .cyberpunk-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--cyberpunk-pink);
        }

        .glitch {
            animation: glitch 1s infinite;
        }

        @keyframes glitch {
            0% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
            100% { transform: translate(0); }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 cyberpunk-sidebar">
                <div class="p-3">
                    <h4 class="neon-text text-center mb-4">
                        <i class="fas fa-database"></i> SQL Console
                    </h4>
                    
                    <ul class="cyberpunk-nav">
                        <li><a href="#" class="nav-link" data-section="console">
                            <i class="fas fa-terminal"></i> Consola SQL
                        </a></li>
                        <li><a href="#" class="nav-link" data-section="tables">
                            <i class="fas fa-table"></i> Tablas
                        </a></li>
                        <li><a href="#" class="nav-link" data-section="tokens">
                            <i class="fas fa-key"></i> Gestión Tokens
                        </a></li>
                        <li><a href="#" class="nav-link" data-section="profile">
                            <i class="fas fa-user"></i> Perfil
                        </a></li>
                    </ul>
                    
                    <div class="mt-4">
                        <button class="btn cyberpunk-btn w-100" id="logoutBtn">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <?php echo $content ?? ''; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Global variables
        let currentUser = null;
        let isAuthenticated = false;
        
        // Initialize app
        $(document).ready(function() {
            checkAuthStatus();
            setupEventListeners();
        });
        
        function checkAuthStatus() {
            $.post('api.php', {action: 'check_auth'}, function(response) {
                if (response.success) {
                    isAuthenticated = true;
                    currentUser = response.username;
                    showSection('console');
                } else {
                    showLoginForm();
                }
            });
        }
        
        function setupEventListeners() {
            // Navigation
            $('.nav-link').click(function(e) {
                e.preventDefault();
                const section = $(this).data('section');
                showSection(section);
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
            });
            
            // Logout
            $('#logoutBtn').click(function() {
                logout();
            });
        }
        
        function showSection(section) {
            switch(section) {
                case 'console':
                    loadConsole();
                    break;
                case 'tables':
                    loadTables();
                    break;
                case 'tokens':
                    loadTokens();
                    break;
                case 'profile':
                    loadProfile();
                    break;
            }
        }
        
        function showLoginForm() {
            $('.col-md-9').html(`
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
            `);
            
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                login();
            });
        }
        
        function login() {
            const username = $('#username').val();
            const token = $('#token').val();
            
            $.post('api.php', {
                action: 'login',
                username: username,
                token: token
            }, function(response) {
                if (response.success) {
                    isAuthenticated = true;
                    currentUser = username;
                    showSection('console');
                } else {
                    showAlert('danger', response.message);
                }
            });
        }
        
        function logout() {
            $.post('api.php', {action: 'logout'}, function(response) {
                isAuthenticated = false;
                currentUser = null;
                showLoginForm();
            });
        }
        
        function showAlert(type, message) {
            const alertHtml = `
                <div class="cyberpunk-alert ${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('.col-md-9 .p-4').prepend(alertHtml);
        }
    </script>
</body>
</html> 