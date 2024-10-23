<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velocity | Exception </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 90%;
            height: -webkit-fill-available;
            margin: 50px;
        }
        h1 {
            font-size: 48px;
            margin: 0 0 20px;
        }
        p {
            font-size: 18px;
            margin: 10px 0;
        }
        .error-details {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            border-radius: 4px;
            text-align: left;
            margin: 20px 0;
        }
        .stack-trace {
            background-color: #f7f7f9;
            color: #333;
            padding: 10px;
            border-radius: 4px;
            text-align: left;
            font-family: monospace;
            overflow-x: auto;
        }
        a {
            text-decoration: none;
            color: #3498db;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>VelocityWeb - Erro da Aplicação</h1>
        <p>Ocorreu um erro inesperado. Pedimos desculpas pelo inconveniente.</p>
        <div class="error-details">
            <p><strong>Código do Erro:</strong> <span id="error-code"><?=$errorCode ?></span></p>
            <p><strong>Mensagem:</strong> <span id="error-message"><?=$e->getMessage();?></span></p>
        </div>
        <div class="stack-trace">
            <pre id="stack-trace"><?=$e;?></pre>
        </div>
    </div>
</body>
</html>
