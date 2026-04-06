<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Daftar</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-box {
            width: 100%;
            max-width: 500px;
            padding: 15px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .card {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 8px;
            background: white;
            padding: 30px;
        }
        
        .card-title {
            text-align: center;
            font-weight: bold;
            color: #003399;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #003399;
        }
        
        .form-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.3rem;
        }
        
        .form-control {
            border-color: #e0e6ed;
            padding: 10px 15px;
            font-size: 0.95rem;
            margin-bottom: 15px;
        }
        
        .form-control:focus {
            border-color: #003399;
            box-shadow: 0 0 0 0.2rem rgba(0, 51, 153, 0.25);
        }
        
        .btn-custom-primary {
            background-color: #003399;
            color: white;
            padding: 10px;
            font-weight: 500;
            border: none;
            width: 100%;
            margin-bottom: 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-custom-primary:hover {
            background-color: #002266;
            color: white;
        }
        
        .btn-google {
            background-color: #ff4d4f;
            color: white;
            padding: 10px;
            font-weight: 500;
            border: none;
            width: 100%;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-google:hover {
            background-color: #ff1a1c;
            color: white;
        }
        
        .forgot-pass {
            font-size: 0.85rem;
            float: right;
            text-decoration: none;
            color: #003399;
        }
        
        .google-icon {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            background-color: white;
            mask: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 488 512"><path d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"/></svg>') no-repeat center / contain;
            -webkit-mask: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 488 512"><path d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"/></svg>') no-repeat center / contain;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="SwimPool Logo" style="max-height: 140px; width: auto; max-width: 100%;">
            <h2 style="color: #003399; font-weight: 800; margin-top: 15px;">SwimPool</h2>
        </div>
        
        <div class="card">
            <h4 class="card-title">Masuk</h4>
            
            <form action="{{ route('participant.dashboard') }}" method="GET">
                <div class="mb-3 position-relative">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" placeholder="Masukkan Email">
                </div>
                
                <div class="mb-3 position-relative">
                    <label class="form-label d-inline-block">Password</label>
                    <a href="#" class="forgot-pass">Lupa Password?</a>
                    <input type="password" class="form-control" placeholder="Masukkan Password">
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe">
                    <label class="form-check-label text-muted" style="font-size: 0.85rem;" for="rememberMe">Ingat Saya</label>
                </div>
                
                <button type="submit" class="btn btn-custom-primary">Login</button>
                <button type="button" class="btn btn-google mb-4">
                    <span class="google-icon"></span> Login dengan Google
                </button>
                
                <div class="text-start mt-4">
                    <p class="text-muted mb-2" style="font-size: 0.85rem;">Belum Punya Akun?</p>
                    <button type="button" class="btn btn-google">
                        <span class="google-icon"></span> Daftar dengan Google
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
