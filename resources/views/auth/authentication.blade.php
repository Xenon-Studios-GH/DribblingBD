<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login & Register — DribblingBD</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            color: #fff;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #25252b;
            overflow: hidden;
        }

        .container {
            position: relative;
            width: 820px;
            height: 500px;
            border: 2px solid #e46033;
            box-shadow: 0 0 25px #e46033;
            overflow: hidden;
        }

        .container .form-box {
            position: absolute;
            top: 0;
            width: 50%;
            height: 100%;
            display: flex;
            justify-content: center;
            flex-direction: column;
        }

        .form-box.Login {
            left: 0;
            padding: 0 50px;
        }

        .form-box.Login .animation {
            transform: translateX(0%);
            transition: .7s;
            opacity: 1;
            transition-delay: calc(.1s * var(--S));
        }

        .container.active .form-box.Login .animation {
            transform: translateX(-120%);
            opacity: 0;
            transition-delay: calc(.1s * var(--D));
        }

        .form-box.Register {
            right: 0;
            padding: 0 40px;
        }

        .form-box.Register .animation {
            transform: translateX(120%);
            transition: .7s ease;
            opacity: 0;
            filter: blur(10px);
            transition-delay: calc(.1s * var(--S));
        }

        .container.active .form-box.Register .animation {
            transform: translateX(0%);
            opacity: 1;
            filter: blur(0px);
            transition-delay: calc(.1s * var(--li));
        }

        .form-box h2 {
            font-size: 36px;
            text-align: center;
        }

        .form-box.Register h2 {
            font-size: 26px;
        }

        .form-box.Register .input-box {
            height: 44px;
            margin-top: 16px;
        }

        .form-box.Register .input-box input {
            font-size: 14px;
        }

        .form-box.Register .input-box label {
            font-size: 14px;
        }

        .form-box.Register .btn {
            height: 40px;
            font-size: 14px;
        }

        .form-box.Register .regi-link {
            font-size: 13px;
            margin: 12px 0 8px;
        }

        .form-box .input-box {
            position: relative;
            width: 100%;
            height: 52px;
            margin-top: 28px;
        }

        .input-box input {
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            outline: none;
            font-size: 17px;
            color: #fff;
            font-weight: 600;
            border-bottom: 2px solid #fff;
            padding-right: 23px;
            transition: .5s;
        }

        .input-box input:focus,
        .input-box input:valid {
            border-bottom: 2px solid #e46033;
        }

        .input-box label {
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            font-size: 17px;
            color: #fff;
            transition: .5s;
        }

        .input-box input:focus~label,
        .input-box input:valid~label {
            top: -5px;
            color: #e46033;
        }

        .input-box .icon {
            position: absolute;
            top: 50%;
            right: 0;
            font-size: 18px;
            transform: translateY(-50%);
            color: #fff;
            width: 20px;
            height: 20px;
        }

        .input-box input:focus~.icon,
        .input-box input:valid~.icon {
            color: #e46033;
        }

        .input-box .icon svg {
            width: 20px;
            height: 20px;
        }

        .btn {
            position: relative;
            width: 100%;
            height: 50px;
            background: transparent;
            border-radius: 40px;
            cursor: pointer;
            font-size: 17px;
            font-weight: 600;
            border: 2px solid #e46033;
            overflow: hidden;
            z-index: 1;
        }

        .btn::before {
            content: "";
            position: absolute;
            height: 300%;
            width: 100%;
            background: linear-gradient(#25252b, #e46033, #25252b, #e46033);
            top: -100%;
            left: 0;
            z-index: -1;
            transition: .5s;
        }

        .btn:hover:before {
            top: 0;
        }

        .regi-link {
            font-size: 15px;
            text-align: center;
            margin: 24px 0 12px;
        }

        .regi-link a {
            text-decoration: none;
            color: #e46033;
            font-weight: 600;
            cursor: pointer;
        }

        .regi-link a:hover {
            text-decoration: underline;
        }

        .info-content {
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            display: flex;
            justify-content: center;
            flex-direction: column;
        }

        .info-content.Login {
            right: 0;
            text-align: right;
            padding: 0 50px 60px 150px;
        }

        .info-content.Login .animation {
            transform: translateX(0);
            transition: .7s ease;
            transition-delay: calc(.1s * var(--S));
            opacity: 1;
            filter: blur(0px);
        }

        .container.active .info-content.Login .animation {
            transform: translateX(120%);
            opacity: 0;
            filter: blur(10px);
            transition-delay: calc(.1s * var(--D));
        }

        .info-content.Register {
            left: 0;
            text-align: left;
            padding: 0 130px 50px 40px;
            pointer-events: none;
        }

        .info-content.Register h2 {
            font-size: 28px;
        }

        .info-content.Register p {
            font-size: 15px;
        }

        .info-content.Register .animation {
            transform: translateX(-120%);
            transition: .7s ease;
            opacity: 0;
            filter: blur(10PX);
            transition-delay: calc(.1s * var(--S));
        }

        .container.active .info-content.Register .animation {
            transform: translateX(0%);
            opacity: 1;
            filter: blur(0);
            transition-delay: calc(.1s * var(--li));
        }

        .info-content h2 {
            text-transform: uppercase;
            font-size: 40px;
            line-height: 1.3;
        }

        .info-content p {
            font-size: 18px;
        }

        .container .curved-shape {
            position: absolute;
            right: 0;
            top: -5px;
            height: 650px;
            width: 920px;
            background: linear-gradient(45deg, #25252b, #e46033);
            transform: rotate(10deg) skewY(40deg);
            transform-origin: bottom right;
            transition: 1.5s ease;
            transition-delay: 1.6s;
        }

        .container.active .curved-shape {
            transform: rotate(0deg) skewY(0deg);
            transition-delay: .5s;
        }

        .container .curved-shape2 {
            position: absolute;
            left: 280px;
            top: 100%;
            height: 750px;
            width: 920px;
            background: #25252b;
            border-top: 3px solid #e46033;
            transform: rotate(0deg) skewY(0deg);
            transform-origin: bottom left;
            transition: 1.5s ease;
            transition-delay: .5s;
        }

        .container.active .curved-shape2 {
            transform: rotate(-11deg) skewY(-41deg);
            transition-delay: 1.2s;
        }

        .remember-row {
            margin-top: 16px;
            display: flex;
            align-items: center;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            cursor: pointer;
            user-select: none;
        }

        .remember-checkbox {
            width: 16px;
            height: 16px;
            accent-color: #e46033;
            cursor: pointer;
        }

        .error-msg {
            font-size: 12px;
            color: #ff6b6b;
            margin-top: 4px;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="container @if($errors->any()) active @endif">
        <div class="curved-shape"></div>
        <div class="curved-shape2"></div>

        {{-- Login Form --}}
        <div class="form-box Login">
            <h2 class="animation" style="--D:0; --S:21">Login</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="form" value="login">

                <div class="input-box animation" style="--D:1; --S:22">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    <label for="email">Email</label>
                    <span class="icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-box animation" style="--D:2; --S:23">
                    <input id="password" type="password" name="password" required autocomplete="current-password">
                    <label for="password">Password</label>
                    <span class="icon">
                        <i class="fas fa-lock"></i>
                    </span>
                </div>

                <div class="remember-row animation" style="--D:3; --S:24">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" value="1" class="remember-checkbox">
                        <span>Remember me</span>
                    </label>
                </div>

                <div class="input-box animation" style="--D:3; --S:24">
                    <button class="btn" type="submit">Login</button>
                </div>

                <div class="regi-link animation" style="--D:4; --S:25">
                    <p>Don't have an account? <br> <a class="SignUpLink">Sign Up</a></p>
                </div>
            </form>
        </div>

        {{-- Login Info --}}
        <div class="info-content Login">
            <h2 class="animation" style="--D:0; --S:20">WELCOME BACK!</h2>
            <p class="animation" style="--D:1; --S:21">We are happy to have you with us again. If you need anything, we are here to help.</p>
        </div>

        {{-- Register Form --}}
        <div class="form-box Register">
            <h2 class="animation" style="--li:17; --S:0">Register</h2>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="form" value="register">

                <div class="input-box animation" style="--li:18; --S:1">
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                    <label for="name">Full Name</label>
                    <span class="icon">
                        <i class="fas fa-user"></i>
                    </span>
                    @error('name')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-box animation" style="--li:19; --S:2">
                    <input id="reg_email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    <label for="reg_email">Email</label>
                    <span class="icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-box animation" style="--li:19; --S:3">
                    <input id="reg_password" type="password" name="password" required autocomplete="new-password">
                    <label for="reg_password">Password</label>
                    <span class="icon">
                        <i class="fas fa-lock"></i>
                    </span>
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="input-box animation" style="--li:20; --S:4">
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                    <label for="password_confirmation">Confirm Password</label>
                    <span class="icon">
                        <i class="fas fa-lock"></i>
                    </span>
                </div>

                <div class="input-box animation" style="--li:20; --S:4">
                    <button class="btn" type="submit">Register</button>
                </div>

                <div class="regi-link animation" style="--li:21; --S:5">
                    <p>Already have an account? <br> <a class="SignInLink">Sign In</a></p>
                </div>
            </form>
        </div>

        {{-- Register Info --}}
        <div class="info-content Register">
            <h2 class="animation" style="--li:17; --S:0">WELCOME!</h2>
            <p class="animation" style="--li:18; --S:1">We're delighted to have you here. If you need any assistance, feel free to reach out.</p>
        </div>
    </div>

    <script>
        const container = document.querySelector('.container');
        const LoginLink = document.querySelector('.SignInLink');
        const RegisterLink = document.querySelector('.SignUpLink');

        if (RegisterLink) {
            RegisterLink.addEventListener('click', (e) => {
                e.preventDefault();
                container.classList.add('active');
            });
        }

        if (LoginLink) {
            LoginLink.addEventListener('click', (e) => {
                e.preventDefault();
                container.classList.remove('active');
            });
        }
    </script>
</body>

</html>