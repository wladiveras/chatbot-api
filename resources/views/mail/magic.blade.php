<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        p {
            font-size: 12px;
        }

        .signature {
            font-style: italic;
        }
    </style>
</head>

<body>
    <div>
        <p>Hey {{ $name }},</p>
        <p>Bem vindo(a) ao Marinabot, chatbot de automação de marketing e auto atendimento. 😉 </p>
        <p class="signature">clique aqui para entrar no sistema: {{ $link }}</p>
    </div>
</body>

</html>
