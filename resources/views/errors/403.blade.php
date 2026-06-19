<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }

        .container {
            max-width: 600px;
            padding: 50px 40px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin: 20px;
        }

        .error-code {
            font-size: 140px;
            font-weight: 800;
            color: #ef4444;
            line-height: 1;
            margin-bottom: 10px;
            text-shadow: 6px 6px 0px #fee2e2;
            letter-spacing: -5px;
        }

        .error-title {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .error-desc {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 36px;
            line-height: 1.6;
        }

        .btn-back {
            display: inline-block;
            padding: 14px 28px;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        .btn-back:hover {
            background-color: #1d4ed8;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 100px;
            }
            .error-title {
                font-size: 24px;
            }
            .container {
                padding: 40px 20px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="error-code">403</div>
        
        <h1 class="error-title">Akses Ditolak!</h1>
        
        <p class="error-desc">
            {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki izin yang cukup untuk membuka halaman ini.' }}
        </p>
        
        <a href="{{ url('/') }}" class="btn-back">Kembali ke Beranda</a>
    </div>

</body>
</html>