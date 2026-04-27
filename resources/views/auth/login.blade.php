<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            margin: 0;

            /* Background Image */
            background: url("{{ asset('images/swimmingonpool.png') }}") no-repeat center center/cover;

            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Overlay biar lebih readable */
        body::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            top: 0;
            left: 0;
            z-index: 0;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            z-index: 1;
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo h2 {
            color: white;
            font-weight: 800;
            margin-top: 10px;
        }

        /* Glassmorphism Card */
        .card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border-radius: 15px;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
            padding: 30px;
            color: white;
        }

        .card-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-label {
            font-size: 0.85rem;
            color: #eee;
        }

        .form-control {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 10px 15px;
        }

        .form-control::placeholder {
            color: #ddd;
        }

        .form-control:focus {
            background: rgba(255,255,255,0.3);
            box-shadow: none;
            color: white;
        }

        .form-check-label {
            color: #ddd;
            font-size: 0.85rem;
        }

        .btn-custom-primary {
            background: rgba(0, 51, 153, 0.85);
            color: white;
            border: none;
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-custom-primary:hover {
            background: #003399;
        }

        .alert {
            font-size: 0.85rem;
            padding: 10px;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <div class="card">
            <div class="text-left mb-4">
                <h2 style="font-weight: 700;">Login</h2>
                <p style="font-size: 0.9rem; color: #ddd; margin-top: 5px;">
                    Welcome back, please login to your account
                </p>
            </div>
            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Masukkan Email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Ingat Saya</label>
                </div>

                <button type="submit" class="btn btn-custom-primary">Login</button>
            </form>
        </div>
    </div>

</body>
</html>