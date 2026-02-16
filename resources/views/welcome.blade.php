<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'E-CUTI PA Sendawar') }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #107c41;       /* Hijau Mahkamah Agung */
            --primary-dark: #0a502a;
            --accent: #facc15;        /* Kuning Emas */
            --text-dark: #1e293b;
            --text-grey: #64748b;
            --bg-light: #f8fafc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            background-color: white;
            overflow-x: hidden;
        }

        /* --- NAVBAR --- */
        .navbar {
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 15px 0;
            transition: all 0.3s;
        }
        .navbar-brand img { height: 45px; margin-right: 10px; }
        .navbar-brand span { font-weight: 700; color: var(--primary); font-size: 1.2rem; letter-spacing: 0.5px; }
        .nav-link { font-weight: 500; color: var(--text-dark); margin: 0 10px; }
        .nav-link:hover { color: var(--primary); }
        .btn-login {
            background-color: var(--primary); color: white; border-radius: 50px; padding: 10px 30px; font-weight: 600; transition: all 0.3s;
        }
        .btn-login:hover { background-color: var(--primary-dark); color: white; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(16, 124, 65, 0.3); }

        /* --- HERO SECTION --- */
        .hero-section {
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
            padding: 100px 0 80px 0;
            position: relative;
            overflow: hidden;
        }
        
        .blob { position: absolute; border-radius: 50%; filter: blur(60px); opacity: 0.4; z-index: 0; }
        .blob-1 { width: 300px; height: 300px; background: #86efac; top: -50px; right: -50px; }
        .blob-2 { width: 200px; height: 200px; background: #bbf7d0; bottom: 50px; left: -50px; }

        .hero-content { position: relative; z-index: 2; }
        .hero-badge { background: #dcfce7; color: var(--primary); padding: 8px 16px; border-radius: 30px; font-weight: 600; font-size: 0.85rem; display: inline-block; margin-bottom: 20px; }
        .hero-title { font-size: 3rem; font-weight: 800; line-height: 1.2; margin-bottom: 20px; background: linear-gradient(to right, var(--primary), var(--primary-dark)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-desc { font-size: 1.1rem; color: var(--text-grey); margin-bottom: 35px; line-height: 1.8; max-width: 600px; }
        
        /* --- FITUR CARDS --- */
        .features-section { padding: 80px 0; background-color: white; }
        .section-title { text-align: center; margin-bottom: 60px; }
        .section-title h2 { font-weight: 700; color: var(--text-dark); margin-bottom: 10px; }
        .section-title p { color: var(--text-grey); max-width: 600px; margin: 0 auto; }
        
        .feature-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 30px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .feature-card:hover {
            border-color: var(--primary);
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }
        .icon-wrapper {
            width: 60px; height: 60px;
            background: #f0fdf4;
            color: var(--primary);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .feature-card h5 { font-weight: 700; margin-bottom: 10px; }
        .feature-card p { color: var(--text-grey); font-size: 0.95rem; line-height: 1.6; margin-bottom: 0; }

        /* --- FOOTER --- */
        footer { background: var(--text-dark); color: white; padding: 50px 0 20px 0; }
        .footer-logo img { height: 50px; margin-bottom: 20px; border-radius: 50%; background: white; padding: 2px; }
        .footer-links a { color: #94a3b8; text-decoration: none; display: block; margin-bottom: 10px; transition: 0.2s; }
        .footer-links a:hover { color: white; padding-left: 5px; }
        
        /* Copyright Style */
        .copyright { 
            border-top: 1px solid #334155; 
            margin-top: 40px; 
            padding-top: 25px; 
            color: #94a3b8; 
            font-size: 0.9rem; 
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
                <span>E-CUTI PAS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#fitur">Fitur Unggulan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
                    <li class="nav-item ms-lg-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-login">
                                    <i class="fas fa-th-large me-2"></i>Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login Pegawai
                                </a>
                            @endauth
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="beranda" class="hero-section d-flex align-items-center">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <span class="hero-badge"><i class="fas fa-bolt me-2"></i>Versi 1.0 (Terbaru)</span>
                    <h1 class="hero-title">Pengajuan Cuti Digital & Terintegrasi.</h1>
                    <p class="hero-desc">Sistem Informasi Manajemen Cuti Pegawai Pengadilan Agama Sendawar. Solusi pengajuan cuti yang cepat, transparan, dan dapat diakses dari mana saja.</p>
                    <div class="d-flex gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-login btn-lg px-5">Buka Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-login btn-lg px-5">Ajukan Cuti Sekarang</a>
                        @endauth
                    </div>
                    
                    <div class="mt-5 d-flex gap-4">
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">100%</h4>
                            <small class="text-muted">Paperless</small>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">24/7</h4>
                            <small class="text-muted">Akses Online</small>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Fast</h4>
                            <small class="text-muted">Approval</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block text-center">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
     alt="Kantor Digital" 
     class="img-fluid" 
     style="width: 100%; max-width: 500px; border-radius: 20px; box-shadow: 0 20px 40px rgba(16, 124, 65, 0.2);">
                </div>
            </div>
        </div>
    </section>

    <section id="fitur" class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Fitur Unggulan</h2>
                <p>Kami menghadirkan teknologi terbaru untuk mempermudah birokrasi dan meningkatkan efisiensi kinerja pegawai.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon-wrapper">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h5>Plh Otomatis (Delegasi)</h5>
                        <p>Atasan sedang Dinas Luar? Sistem otomatis mengalihkan wewenang persetujuan ke Pelaksana Harian (Plh) yang ditunjuk secara digital.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon-wrapper">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <h5>Smart Calculation</h5>
                        <p>Tidak perlu hitung manual! Sistem otomatis mendeteksi hari libur nasional dan akhir pekan, memastikan kuota cuti terpotong secara adil.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon-wrapper">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h5>Kalender Terintegrasi</h5>
                        <p>Pimpinan dapat memantau jadwal cuti seluruh pegawai dalam satu tampilan kalender visual untuk memudahkan manajemen SDM.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon-wrapper">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <h5>Notifikasi Real-time</h5>
                        <p>Status pengajuan cuti dikirim langsung melalui Email. Pemohon dan Atasan selalu terupdate setiap ada perubahan status.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon-wrapper">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h5>Surat Cuti Digital (PDF)</h5>
                        <p>Selesai disetujui, surat cuti resmi dengan Tanda Tangan  langsung terbit dan dapat diunduh format PDF.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="icon-wrapper">
                            <i class="fas fa-history"></i>
                        </div>
                        <h5>Riwayat & Sisa Cuti</h5>
                        <p>Transparansi penuh. Pegawai dapat melihat sisa kuota cuti tahunan dan riwayat pengambilan cuti secara detail dan akurat.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer id="kontak">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="footer-logo">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
                    </div>
                    <h5 class="fw-bold mb-3">Pengadilan Agama Sendawar</h5>
                    <p class="text-white-50 small">Jl. Paulus Doy Lambeng, Barong Tongkok, Kec. Barong Tongkok, Kabupaten Kutai Barat, Kalimantan Timur 75777</p>
                </div>
                <div class="col-md-2 mb-4"></div>
                
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold mb-3 text-white">Tautan Cepat</h6>
                    <div class="footer-links">
                        <a href="https://pa-sendawar.go.id/" target="_blank">Website Resmi</a>
                        <a href="https://badilag.mahkamahagung.go.id/" target="_blank">Badilag</a>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold mb-3 text-white">Kontak Kami</h6>
                    <div class="footer-links">
                        <a href="#"><i class="fas fa-phone me-2"></i>+62-82253551790</a>
                        <a href="#"><i class="fas fa-envelope me-2"></i> pengadilanagamasendawar@gmail.com</a>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="mb-2 mb-md-0">
                        &copy; 2026 <strong>Pengadilan Agama Sendawar</strong>. Hak Cipta Dilindungi Undang-Undang.
                    </div>
                    <div>
                        <span class="text-white-50 small">Dikembangkan oleh</span> <span class="text-white fw-bold">Muhammad Fachrul Syahputra</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>