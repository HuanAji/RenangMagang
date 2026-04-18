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
        
        .btn-custom-outline {
            background-color: white;
            color: #003399;
            padding: 10px;
            font-weight: 500;
            border: 1px solid #003399;
            width: 100%;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .btn-custom-outline:hover {
            background-color: #f0f4fa;
            color: #003399;
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
            
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                
                @if ($errors->any())
                    <div class="alert alert-danger" style="font-size: 0.85rem; padding: 10px;">
                        {{ $errors->first() }}
                    </div>
                @endif
                
                <div class="mb-3 position-relative">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan Email" value="{{ old('email') }}" required>
                </div>
                
                <div class="mb-3 position-relative">
                    <label class="form-label d-inline-block" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan Password" required>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
                    <label class="form-check-label text-muted" style="font-size: 0.85rem;" for="rememberMe">Ingat Saya</label>
                </div>
                
                <button type="submit" class="btn btn-custom-primary">Login</button>
                
            </form>
        </div>
    </div>

</body>
</html>
