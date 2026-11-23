<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Obede</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 48 48%22><circle cx=%2224%22 cy=%2224%22 r=%2220%22 stroke=%22%234F46E5%22 stroke-width=%224%22 fill=%22none%22/><path d=%22M24 12V24L32 32%22 stroke=%22%234F46E5%22 stroke-width=%224%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22/></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Reset básico para garantir visual em qualquer navegador */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #F3F4F6; /* Cinza claro universal */
            color: #1F2937;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        /* Container Principal */
        .login-container {
            width: 100%;
            max-width: 420px;
            background-color: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            padding: 2.5rem 2rem;
            position: relative;
        }

        /* Cabeçalho / Logo */
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .logo-icon {
            width: 2.5rem;
            height: 2.5rem;
            color: #4F46E5; /* Índigo */
        }

        .logo-text {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.025em;
        }

        .subtitle {
            color: #6B7280;
            font-size: 0.875rem;
        }

        /* Formulários */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.25rem;
            height: 1.25rem;
            color: #9CA3AF;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            background-color: #F9FAFB;
            border: 1px solid #D1D5DB;
            border-radius: 10px;
            color: #111827;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input:focus {
            background-color: #FFFFFF;
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-input::placeholder {
            color: #9CA3AF;
        }

        /* Botão do Olho (Senha) */
        .toggle-password {
            position: absolute;
            right: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #9CA3AF;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover {
            color: #4F46E5;
        }

        /* Opções (Lembrar de mim) */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            color: #4B5563;
        }

        .checkbox-wrapper input {
            width: 1rem;
            height: 1rem;
            accent-color: #4F46E5;
            cursor: pointer;
        }

        .forgot-link {
            color: #4F46E5;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* Botão Submit */
        .btn-submit {
            width: 100%;
            padding: 0.875rem;
            background-color: #4F46E5;
            color: #FFFFFF;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-submit:hover {
            background-color: #4338CA;
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        /* Alerta de Erro */
        .alert-error {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Rodapé */
        .footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.75rem;
            color: #9CA3AF;
        }

        /* Responsividade para Mobile */
        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
                border-radius: 0;
                box-shadow: none;
                background-color: transparent;
                max-width: 100%;
            }
            body {
                background-color: #FFFFFF;
                justify-content: flex-start;
                padding-top: 3rem;
            }
        }
    </style>
</head>
<body>

    <!-- Container de Login -->
    <div class="login-container">
        
        <!-- Cabeçalho -->
        <div class="header">
            <div class="logo-wrapper">
                <!-- Ícone do Obede -->
                <svg class="logo-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4" />
                    <path d="M24 12V24L32 32" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="logo-text">Obede</span>
            </div>
            <p class="subtitle">Acesse sua conta para continuar</p>
        </div>

        <!-- Alerta de Erro -->
        @if ($errors->any())
            <div class="alert-error">
                <svg style="width: 1.25rem; height: 1.25rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Formulário -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- E-mail -->
            <div class="form-group">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-wrapper">
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-input" placeholder="nome@empresa.com">
                </div>
            </div>

            <!-- Senha -->
            <div class="form-group">
                <label for="password" class="form-label">Senha</label>
                <div class="input-wrapper">
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <input type="password" name="password" id="password" required autocomplete="current-password" class="form-input" placeholder="Sua senha" style="padding-right: 2.75rem;">
                    
                    <!-- Botão Olho -->
                    <button type="button" onclick="togglePassword()" class="toggle-password" aria-label="Mostrar senha">
                        <svg id="eye-open" style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg id="eye-closed" style="width: 1.25rem; height: 1.25rem; display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            <!-- Opções: Lembrar de mim e Esqueceu a senha -->
            <div class="form-options">
                <label class="checkbox-wrapper">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    Lembrar de mim
                </label>
                
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Esqueceu a senha?</a>
                @endif
            </div>

            <!-- Botão Entrar -->
            <button type="submit" class="btn-submit">
                Entrar
            </button>
        </form>
    </div>

    <!-- Rodapé -->
    <div class="footer">
        &copy; {{ date('Y') }} Obede. Todos os direitos reservados.
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                input.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        }
    </script>
</body>
</html>