<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            margin: 0;
            background-color: #f8f8f8;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .email-body {
            padding: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            font-size: 12px;
            color: #777;
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h2>Сброс пароля</h2>
        </div>
        <div class="email-body">
            <p>Кто-то (надеемся, что это были вы) создал запрос на изменение забытого пароля вашего аккаунта.</p>
            
            <p>Чтобы сменить пароль от вашего аккаунта, нажмите на кнопку ниже:</p>
            
            <a href="{{ $url }}" class="button">Сбросить пароль</a>
            
            <p>Вы получили это письмо, так как этот адрес электронной почты привязан к аккаунту сервиса опросов.</p>
            
            <p>Если вы не запрашивали сброс пароля, проигнорируйте это письмо.</p>
            
            <p>Ссылка действительна в течение {{ $count }} минут.</p>
            
            <div class="footer">
                <p>© {{ date('Y') }} {{ config('app.name') }}. Все права защищены.</p>
            </div>
        </div>
    </div>
</body>
</html> 