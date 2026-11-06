<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Digital Kampus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            margin: 0;
            background: #eef2f7;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #334155;
            padding: 20px;
        }
        .login-box {
            background: #eef2f7;
            padding: 42px 38px;
            border-radius: 28px;
            box-shadow: 12px 12px 28px rgba(166, 180, 200, 0.45), -12px -12px 28px #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.85);
            width: 100%;
            max-width: 400px;
            position: relative;
        }
        .brand-badge {
            background: linear-gradient(135deg, #7ca1e6, #9bb8f0);
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 12px;
            box-shadow: 3px 3px 8px rgba(124, 161, 230, 0.35), -2px -2px 6px #ffffff;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }
        .login-box h1 {
            font-size: 24px;
            font-weight: 800;
            margin-top: 4px;
            margin-bottom: 24px;
            color: #1e293b;
            letter-spacing: -0.3px;
        }
        label {
            display: block;
            margin-top: 14px;
            font-weight: 700;
            font-size: 12.5px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            margin-top: 6px;
            box-sizing: border-box;
            background: #eef2f7;
            border: 1px solid rgba(166, 180, 200, 0.35);
            border-radius: 14px;
            font-size: 14px;
            font-family: inherit;
            color: #1e293b;
            box-shadow: inset 3px 3px 6px rgba(166, 180, 200, 0.35), inset -3px -3px 6px #ffffff;
            outline: none;
            transition: all 0.2s ease;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #7ca1e6;
            box-shadow: inset 2px 2px 4px rgba(124, 161, 230, 0.3), inset -2px -2px 4px #ffffff, 0 0 0 3px rgba(124, 161, 230, 0.2);
        }
        .error {
            color: #b91c1c;
            font-size: 13px;
            font-weight: 600;
            margin-top: 5px;
        }
        .alert-success {
            background: #dcfce7;
            color: #15803d;
            padding: 12px 16px;
            border-radius: 14px;
            margin-bottom: 18px;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid rgba(134, 239, 172, 0.6);
            box-shadow: 4px 4px 10px rgba(166, 180, 200, 0.25), -4px -4px 10px #ffffff;
        }
        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
        }
        .checkbox-row label {
            display: inline;
            margin-top: 0;
            font-weight: 600;
            font-size: 13px;
            color: #64748b;
            text-transform: none;
            letter-spacing: normal;
            cursor: pointer;
        }
        .checkbox-row input[type="checkbox"] {
            width: auto;
            margin: 0;
            cursor: pointer;
        }
        .btn {
            margin-top: 24px;
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #7ca1e6, #678ed4);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            box-shadow: 5px 5px 12px rgba(124, 161, 230, 0.4), -5px -5px 12px #ffffff;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn:hover {
            background: linear-gradient(135deg, #8cb0f2, #759be0);
            box-shadow: 6px 6px 16px rgba(124, 161, 230, 0.45), -6px -6px 16px #ffffff;
            transform: translateY(-1px);
        }
        .btn:active {
            box-shadow: inset 3px 3px 6px rgba(80, 110, 170, 0.45), inset -3px -3px 6px rgba(255, 255, 255, 0.6);
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="brand-badge">📚 Perpustakaan Digital</div>
        <h1>Masuk ke Akun</h1>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="nama@pens.ac.id" autofocus>
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="••••••••">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <div class="checkbox-row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Ingat sesi saya</label>
            </div>

            <button type="submit" class="btn">Masuk Sekarang &rarr;</button>
        </form>
    </div>
</body>
</html>
