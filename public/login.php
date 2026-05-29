// Proses Autentikasi User 
<?php

require_once '../src/Auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (Auth::login($username, $password)) {

        header("Location: index.php");
        exit;

    } else {

        $error = "Username atau password salah";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIAKAD</title>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>

        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body{
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #4f46e5, #7c3aed, #9333ea);
            overflow: hidden;
            position: relative;
        }

        /* background blur circle */
        body::before{
            content: '';
            position: absolute;
            width: 350px;
            height: 350px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            top: -100px;
            left: -100px;
            filter: blur(10px);
            animation: float 6s ease-in-out infinite;
        }

        body::after{
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            bottom: -100px;
            right: -100px;
            filter: blur(10px);
            animation: float 8s ease-in-out infinite;
        }

        @keyframes float{
            0%{
                transform: translateY(0px);
            }
            50%{
                transform: translateY(20px);
            }
            100%{
                transform: translateY(0px);
            }
        }

        .login-container{
            width: 380px;
            padding: 40px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 25px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            color: white;
            position: relative;
            z-index: 1;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn{
            from{
                opacity: 0;
                transform: translateY(30px);
            }
            to{
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container h2{
            text-align: center;
            margin-bottom: 10px;
            font-size: 32px;
            font-weight: 700;
        }

        .subtitle{
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
            opacity: 0.8;
        }

        .error{
            background: rgba(255, 0, 0, 0.2);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            animation: shake 0.3s ease-in-out;
        }

        @keyframes shake{
            0%,100%{
                transform: translateX(0);
            }
            25%{
                transform: translateX(-5px);
            }
            75%{
                transform: translateX(5px);
            }
        }

        .input-group{
            margin-bottom: 20px;
        }

        .input-group label{
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .input-group input{
            width: 100%;
            padding: 14px 16px;
            border: none;
            outline: none;
            border-radius: 14px;
            background: rgba(255,255,255,0.15);
            color: white;
            font-size: 14px;
            transition: 0.3s;
        }

        .input-group input::placeholder{
            color: rgba(255,255,255,0.7);
        }

        .input-group input:focus{
            background: rgba(255,255,255,0.22);
            transform: scale(1.02);
            box-shadow: 0 0 10px rgba(255,255,255,0.2);
        }

        .login-btn{
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 14px;
            background: white;
            color: #6d28d9;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-btn:hover{
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .footer-text{
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
            opacity: 0.8;
        }

        @media(max-width: 450px){

            .login-container{
                width: 90%;
                padding: 30px;
            }

            .login-container h2{
                font-size: 26px;
            }

        }

    </style>

</head>
<body>

    <div class="login-container">

        <h2>Login SIAKAD</h2>
        <p class="subtitle">silakan masuk ke sistem akademik</p>

        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">

            <div class="input-group">
                <label>Username</label>

                <input 
                    type="text" 
                    name="username"
                    placeholder="Masukkan username"
                    autocomplete="off"
                    required>
            </div>

            <div class="input-group">
                <label>Password</label>

                <input 
                    type="password" 
                    name="password"
                    placeholder="Masukkan password"
                    autocomplete="off"
                    required>
            </div>

            <button type="submit" class="login-btn">
                Login
            </button>

        </form>

        <div class="footer-text">
            Powered by @sayy.mi_
        </div>

    </div>

</body>
</html>