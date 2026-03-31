<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ config('app.name', 'Sistem Cuti PA Sendawar') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            /* Palette Warna Modern */
            --primary-color: #107c41;       /* Hijau Mahkamah Agung Modern */
            --primary-gradient: linear-gradient(135deg, #107c41 0%, #0a4d29 100%);
            --bg-color: #f3f4f6;            /* Light Grey Background */
            --sidebar-bg: #ffffff;          /* White Sidebar */
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            
            /* Visual Effects */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius: 16px;
        }

        body { 
            background-color: var(--bg-color); 
            font-family: 'Poppins', sans-serif; 
            color: var(--text-dark);
            overflow-x: hidden; 
        }
        
        /* --- HEADER (Premium Gradient) --- */
        .header { 
            background: linear-gradient(135deg, #0d9e4f 0%, #107c41 30%, #0a5c30 70%, #064020 100%);
            color: white; 
            padding: 18px 30px; 
            box-shadow: 0 4px 20px rgba(10, 92, 48, 0.3); 
            position: sticky; 
            top: 0; 
            z-index: 100; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            margin-bottom: 25px;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 30% 50%, rgba(255,255,255,0.06) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .header-content { display: flex; align-items: center; gap: 15px; }
        
        .header-logo { 
            width: 42px; height: 42px; 
            border-radius: 50%; 
            background: white; 
            display: flex; align-items: center; justify-content: center; 
            overflow: hidden; padding: 2px;
            box-shadow: 0 0 10px rgba(255,255,255,0.3);
        }
        .header-logo img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .header h3 { font-size: 1.15rem; font-weight: 600; margin: 0; letter-spacing: 0.5px; }
        
        /* --- SIDEBAR MODERN --- */
        .sidebar { 
            background: var(--sidebar-bg); 
            height: 100vh; 
            width: 270px; 
            position: fixed; 
            top: 0; 
            left: 0; 
            z-index: 1000; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            padding-top: 30px; 
            box-shadow: 5px 0 25px rgba(0,0,0,0.03); 
            border-right: 1px solid rgba(0,0,0,0.03);
            overflow-y: auto; 
        }

        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }

        .sidebar-header { 
            padding: 0 25px 30px 25px; 
            border-bottom: 1px solid #f3f4f6; 
            margin-bottom: 15px;
        }
        
        .sidebar-title { 
            color: var(--primary-color); 
            font-weight: 700; 
            font-size: 1rem; 
            display: flex; align-items: center;
        }
        
        /* Menu Items */
        .sidebar .nav-link { 
            color: var(--text-muted); 
            padding: 12px 25px; 
            margin: 4px 15px; 
            border-radius: 12px; 
            transition: all 0.2s ease; 
            display: flex; 
            align-items: center; 
            font-weight: 500;
            font-size: 0.95rem;
        }

        .sidebar .nav-link i { 
            width: 24px; 
            margin-right: 12px; 
            font-size: 18px; 
            color: #9ca3af;
            transition: color 0.2s;
        }

        /* Hover Effect */
        .sidebar .nav-link:hover { 
            background-color: #f0fdf4; /* Hijau sangat muda */
            color: var(--primary-color); 
            transform: translateX(3px);
        }
        .sidebar .nav-link:hover i { color: var(--primary-color); }

        /* Active State (Modern Pill Style) */
        .sidebar .nav-link.active { 
            background: var(--primary-color); 
            color: white; 
            box-shadow: 0 4px 12px rgba(16, 124, 65, 0.25);
        }
        .sidebar .nav-link.active i { color: white; }
        
        /* --- MAIN CONTENT --- */
        .main-content { 
            margin-left: 270px; 
            padding: 0 30px 40px 30px; 
            transition: margin-left 0.3s ease; 
        }

        /* --- GLOBAL CARD STYLING (Agar Dashboard Langsung Cantik) --- */
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            background: white;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 24px;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        .card-header {
            background-color: white;
            border-bottom: 1px solid #f3f4f6;
            padding: 20px 25px;
            font-weight: 600;
            color: var(--text-dark);
        }
        .card-body { padding: 25px; }

        /* --- BUTTONS & BADGES --- */
        .btn { border-radius: 10px; padding: 10px 20px; font-weight: 500; font-size: 0.9rem; }
        .badge { padding: 6px 12px; border-radius: 8px; font-weight: 600; letter-spacing: 0.5px; }

        /* --- MOBILE RESPONSIVE --- */
        .menu-btn { 
            position: fixed; top: 15px; left: 15px; z-index: 1002; 
            background: white; color: var(--primary-color); 
            border: none; width: 45px; height: 45px; border-radius: 12px; 
            display: none; align-items: center; justify-content: center; 
            cursor: pointer; box-shadow: var(--shadow-md);
        }
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: 999; display: none; backdrop-filter: blur(2px); }

        @media (max-width: 991.98px) {
            .menu-btn { display: flex !important; }
            .main-content { margin-left: 0; padding-top: 80px; padding-left: 20px; padding-right: 20px; }
            .sidebar { left: -280px; }
            .sidebar.show { left: 0; }
            .overlay.show { display: block; }
            .header { border-radius: 0; margin-bottom: 20px; padding-left: 70px; }
        }
    </style>
</head>
<body>
    <button class="menu-btn" id="menuToggle"><i class="fas fa-bars" style="font-size: 20px;"></i></button>
    
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-title">
                <i class="fas fa-user-circle me-2" style="font-size: 1.2rem;"></i> 
                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</span>
            </div>
        </div>
        <ul class="nav flex-column">
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
            </li>

            {{-- MENU ADMIN --}}
            @if(Auth::user()->role == 'admin')
                <div class="text-muted small fw-bold px-4 mt-3 mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Administrator</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.pegawai.index') ? 'active' : '' }}" href="{{ route('admin.pegawai.index') }}">
                        <i class="fas fa-users"></i> Data Pegawai
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.pegawai.create') ? 'active' : '' }}" href="{{ route('admin.pegawai.create') }}">
                        <i class="fas fa-user-plus"></i> Tambah Pegawai
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.kalender') ? 'active' : '' }}" href="{{ route('admin.kalender') }}">
                        <i class="fas fa-calendar-alt"></i> Kalender Cuti
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.jenis-cuti.*') ? 'active' : '' }}" href="{{ route('admin.jenis-cuti.index') }}">
                        <i class="fas fa-layer-group"></i> Jenis Cuti
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.hari-libur.*') ? 'active' : '' }}" href="{{ route('admin.hari-libur.index') }}">
                        <i class="fas fa-calendar-day"></i> Hari Libur
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.audit') ? 'active' : '' }}" href="{{ route('admin.audit') }}">
                        <i class="fas fa-history"></i> Audit Trail
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}" href="{{ route('admin.laporan.index') }}">
                        <i class="fas fa-file-excel"></i> Laporan Cuti
                    </a>
                </li>
            @endif

            {{-- 
                ==========================================================
                LOGIKA SIDEBAR: SUPPORT KASUBAG, PLH & NOTIFIKASI
                ==========================================================
            --}}
            @php
                $user = Auth::user();
                $user_id = $user->id;
                
                // 1. Cek Atasan Langsung
                $is_atasan = \App\Models\User::where('atasan_id', $user_id)->exists();
                
                // 2. Cek Plh
                $is_plh = \App\Models\User::where('plh_id', $user_id)->exists();

                // 3. Hitung Notifikasi
                $notif_count = 0;
                $is_plh_pimpinan = \App\Models\User::where('plh_id', $user_id)->where('role', 'pimpinan')->exists();
                
                // [KHUSUS KASUBAG: Hitung yang Menunggu Verifikasi]
                if ($user->role == 'kasubag') {
                    $notif_count += \App\Models\Cuti::where('status', 'Menunggu Verifikasi')->count();
                }

                if($user->role == 'pimpinan' || $is_plh_pimpinan) {
                    $notif_count += \App\Models\Cuti::where('status', 'Menunggu Pejabat')->count();
                }

                if($is_atasan || $is_plh) {
                     $notif_atasan = \App\Models\Cuti::where('status', 'Menunggu Atasan')
                        ->whereHas('user', function($q) use ($user_id) {
                            $q->where('atasan_id', $user_id)
                              ->orWhereIn('atasan_id', function($sub) use ($user_id) {
                                  $sub->select('id')->from('users')->where('plh_id', $user_id);
                              });
                        })->count();
                     $notif_count += $notif_atasan;
                }
            @endphp

            {{-- TAMPILKAN MENU JIKA: Pimpinan OR Kasubag OR Atasan OR Plh --}}
            @if($user->role == 'pimpinan' || $user->role == 'kasubag' || $is_atasan || $is_plh)
                <div class="text-muted small fw-bold px-4 mt-3 mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Approval</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pimpinan.persetujuan.*') ? 'active' : '' }}" href="{{ route('pimpinan.persetujuan.index') }}">
                        <i class="fas fa-check-circle"></i> 
                        {{-- Label Dinamis --}}
                        @if($user->role == 'kasubag') Verifikasi Cuti @else Persetujuan @endif
                        
                        {{-- Badge Plh --}}
                        @if($is_plh)
                            <span class="badge bg-warning text-dark ms-2 shadow-sm" style="font-size: 0.6rem;">Plh</span>
                        @endif
                        
                        {{-- Badge Notifikasi --}}
                        @if($notif_count > 0)
                            <span class="badge bg-danger ms-auto shadow-sm" style="font-size: 0.75rem;">{{ $notif_count }}</span>
                        @endif
                    </a>
                </li>
                
                @if($user->role == 'pimpinan' || $is_plh_pimpinan)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pimpinan.kalender') ? 'active' : '' }}" href="{{ route('pimpinan.kalender') }}">
                            <i class="fas fa-calendar-alt"></i> Kalender Cuti
                        </a>
                    </li>
                @endif
            @endif

            {{-- MENU PEGAWAI --}}
            @if(Auth::user()->role == 'pegawai')
                <div class="text-muted small fw-bold px-4 mt-3 mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Menu Pegawai</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cuti.create') ? 'active' : '' }}" href="{{ route('cuti.create') }}">
                        <i class="fas fa-file-signature"></i> Ajukan Cuti
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cuti.index') ? 'active' : '' }}" href="{{ route('cuti.index') }}">
                        <i class="fas fa-history"></i> Riwayat Cuti
                    </a>
                </li>
            @endif

            {{-- MENU UMUM --}}
            <div class="text-muted small fw-bold px-4 mt-3 mb-1 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Pengaturan</div>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                    <i class="fas fa-user-cog"></i> Profil Saya
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('password.change') ? 'active' : '' }}" href="{{ route('password.change') }}">
                    <i class="fas fa-lock"></i> Ubah Password
                </a>
            </li>
            
            <li class="nav-item mt-4 px-3 mb-5">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="btn btn-danger w-100 text-white fw-bold d-flex align-items-center justify-content-center" href="#" onclick="event.preventDefault(); this.closest('form').submit();" style="border-radius: 12px; padding: 12px;">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </form>
            </li>

        </ul>
    </div>
    
    <div class="overlay" id="overlay"></div>
    
    <div class="main-content" id="mainContent">
        <div class="header">
            <div class="header-content">
                <div class="header-logo">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
                </div>
                <h3>E-CUTI PAS</h3>
            </div>
            
            {{-- BELL NOTIFIKASI DI HEADER --}}
            <div class="dropdown">
                <button class="btn btn-link text-white text-decoration-none position-relative p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fs-5"></i>
                    @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm border border-white" style="font-size: 0.55rem; padding: 0.25em 0.4em;">
                            {{ Auth::user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0" style="width: 320px; border-radius: 12px; overflow: hidden; z-index: 1050;">
                    <li class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">Notifikasi Baru</span>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <a href="{{ route('notifications.read_all') }}" class="text-primary text-decoration-none" style="font-size: 0.75rem;">Tandai Dibaca</a>
                        @endif
                    </li>
                    <div style="max-height: 300px; overflow-y: auto;">
                        @forelse(Auth::user()->unreadNotifications->take(6) as $notification)
                            <li>
                                <a class="dropdown-item py-2 px-3 border-bottom text-wrap" href="#">
                                    <div class="fw-bold mb-1" style="font-size: 0.8rem; color: #107c41;">{{ $notification->data['nama_pegawai'] ?? 'Sistem e-Cuti' }}</div>
                                    <div style="font-size: 0.8rem; color: #4b5563; line-height: 1.3;">{{ $notification->data['pesan'] ?? 'Ada rekam aktivitas baru di sistem.' }}</div>
                                    <div class="text-muted mt-1" style="font-size: 0.7rem;"><i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</div>
                                </a>
                            </li>
                        @empty
                            <li><div class="px-3 py-4 text-center text-muted" style="font-size: 0.85rem;"><i class="fas fa-check-circle me-1 text-success opacity-50"></i> Belum ada notifikasi baru.</div></li>
                        @endforelse
                    </div>
                </ul>
            </div>
        </div>

        <div class="container-fluid p-0">
            @yield('content')
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.getElementById('menuToggle');
            const overlay = document.getElementById('overlay');
            
            function openSidebar() { sidebar.classList.add('show'); overlay.classList.add('show'); }
            function closeSidebar() { sidebar.classList.remove('show'); overlay.classList.remove('show'); }
            
            if(menuToggle) menuToggle.addEventListener('click', (e) => { e.stopPropagation(); openSidebar(); });
            if(overlay) overlay.addEventListener('click', closeSidebar);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>