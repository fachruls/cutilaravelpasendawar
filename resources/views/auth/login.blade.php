<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pegawai - E-CUTI PA Sendawar</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #0f6b3d;       /* Hijau PA Sendawar */
            --primary-dark: #084a28;
            --accent: #fcd34d;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            overflow: hidden; 
            background-color: white;
        }

        .login-container {
            height: 100vh;
            display: flex;
        }

        /* --- SISI KIRI (BRANDING - DESKTOP) --- */
        .login-brand {
            flex: 1.2; 
            background: linear-gradient(145deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
            text-align: center;
            padding: 40px;
        }

        .pattern-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.6;
        }

        .brand-content { z-index: 2; position: relative; }

        .brand-logo {
            width: 110px; height: 110px;
            background: white; border-radius: 50%; padding: 5px; margin-bottom: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            animation: float 6s ease-in-out infinite;
            display: flex; align-items: center; justify-content: center;
        }
        .brand-logo img { width: 100%; height: 100%; object-fit: contain; }
        .brand-title { font-size: 2.8rem; font-weight: 700; margin-bottom: 5px; letter-spacing: 1px; }
        .brand-subtitle { font-size: 1.1rem; opacity: 0.9; font-weight: 300; margin-top: 10px; line-height: 1.6; }

        /* --- SISI KANAN (FORM AREA) --- */
        .login-form-section {
            flex: 1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .login-card { width: 100%; max-width: 420px; padding: 10px; }

        .form-header { margin-bottom: 30px; }
        .form-title { font-size: 1.8rem; font-weight: 700; color: #1f2937; margin-bottom: 5px; }
        .form-subtitle { color: #6b7280; font-size: 0.95rem; }

        /* Modern Input */
        .form-floating { position: relative; margin-bottom: 20px; }
        .form-floating > .form-control { 
            border-radius: 12px; border: 1px solid #e5e7eb; padding-left: 20px; height: 55px; background-color: #f9fafb;
        }
        .form-floating > .form-control:focus { 
            border-color: var(--primary); box-shadow: 0 0 0 4px rgba(15, 107, 61, 0.1); background-color: white;
        }
        .form-floating > label { padding-left: 20px; color: #9ca3af; }

        .password-toggle {
            position: absolute; top: 50%; right: 20px; transform: translateY(-50%);
            cursor: pointer; color: #9ca3af; z-index: 10; background: none; border: none; padding: 0;
        }
        .password-toggle:hover { color: var(--primary); }

        .btn-login {
            background-color: var(--primary); color: white; border-radius: 12px; padding: 14px;
            font-weight: 600; font-size: 1rem; width: 100%; border: none; transition: all 0.3s;
            margin-top: 10px; box-shadow: 0 4px 6px -1px rgba(15, 107, 61, 0.2);
        }
        .btn-login:hover {
            background-color: var(--primary-dark); transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(15, 107, 61, 0.3);
        }

        .back-link {
            position: absolute; top: 30px; right: 40px; text-decoration: none;
            color: #6b7280; font-weight: 500; font-size: 0.9rem;
            display: flex; align-items: center; transition: 0.2s;
        }
        .back-link:hover { color: var(--primary); }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0px); }
        }

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 991.98px) {
            body, html { overflow-y: auto; } /* Izinkan scroll di HP */
            .login-brand { display: none; } /* Sembunyikan sisi kiri di HP */
            .login-form-section { 
                flex: 100%; 
                background-color: #f3f4f6; /* Background abu muda biar kontras sama kartu */
                min-height: 100vh;
                padding: 20px;
                align-items: center; /* Center vertikal */
            }
            .login-card { 
                background: white; 
                padding: 30px; 
                border-radius: 20px; 
                box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            }
            .back-link { top: 20px; right: 20px; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-brand">
            <div class="pattern-overlay"></div>
            <div class="brand-content">
                <div class="brand-logo">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo PA">
                </div>
                <h1 class="brand-title">E-CUTI PAS</h1>
                <p class="brand-subtitle">Sistem Informasi Manajemen Cuti Pegawai<br>Pengadilan Agama Sendawar</p>
            </div>
        </div>

        <div class="login-form-section">
            <a href="{{ url('/') }}" class="back-link">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>

            <div class="login-card">
                
                <div class="text-center d-lg-none mb-4">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo Mobile" style="width: 70px; height: 70px;">
                    <h4 class="fw-bold mt-2 text-dark" style="color: var(--primary) !important;">E-CUTI PAS</h4>
                </div>

                <div class="form-header">
                    <h2 class="form-title">Selamat Datang! 👋</h2>
                    <p class="form-subtitle">Silakan login menggunakan Username atau NIP Anda.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-start fade show" role="alert">
                        <i class="fas fa-exclamation-circle mt-1 me-3 fs-5"></i>
                        <div>
                            <strong>Gagal Login!</strong><br>
                            <small class="text-danger-emphasis">
                                @foreach ($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                            </small>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-floating">
                        <input type="text" class="form-control" id="nip" name="nip" placeholder="NIP Pegawai" value="{{ old('nip') }}" required autofocus>
                        <label for="nip">NIP / Username</label>
                    </div>

                    <div class="form-floating position-relative">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-muted small" for="remember">
                                Ingat Saya
                            </label>
                        </div>
                        <a href="#" class="text-decoration-none small text-muted fw-medium">
                            Hubungi admin jika lupa passowrd
                        </a>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> MASUK APLIKASI
                    </button>
                    
                    <div class="text-center mt-4 text-muted small opacity-75">
                        &copy; {{ date('Y') }} Pengadilan Agama Sendawar
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function (e) {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>