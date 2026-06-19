<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .error-wrapper {
            text-align: center;
            max-width: 480px;
            width: 100%;
        }
        .error-code {
            font-size: 7.5rem;
            font-weight: 800;
            color: #3b82f6;
            line-height: 1;
            margin-bottom: 1rem;
            letter-spacing: -0.05em;
            position: relative;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.75rem;
        }
        .error-message {
            font-size: 1rem;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 2rem;
            padding: 0 10px;
        }
        .btn-back {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
        }
        .btn-back:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 6px 12px -2px rgba(37, 99, 235, 0.3);
        }
        .btn-back:active {
            transform: translateY(0);
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-12px);
            }
        }
    </style>
</head>
<body>

    <div class="error-wrapper">
        <div class="error-code">404</div>
        
        <h1 class="error-title">Waduh, Nyasar ke Mana Nih?</h1>
        
        <p class="error-message">
            {{ $exception->getMessage() ?: 'Halaman yang Anda cari tidak ditemukan, telah dihapus, atau jalurnya diubah.' }}
        </p>
        
        <a href="{{ url('/') }}" class="btn-back">Kembali ke Beranda</a>
    </div>

</body>
</html>