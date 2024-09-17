<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        p {
            font-size: 14px;
        }

        .logo {
            width: 300px;
            margin: 0 auto;
        }

        .link {
            font-style: bold;
        }

        .details {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div>
        <img src="https://marinabot.com.br/Logo.png" alt="Logo" class="logo">
        <p>Olá, tudo bem?</p>
        <p>Bem-vindo(a) ao Marinabot, o chatbot de automação de marketing e autoatendimento. 😉</p>
        <p class="link">
            Clique <a href="{{ $link }}">aqui</a> para acessar o sistema.
        </p>
    </div>
    <div class="details">
        <p>
            {{ $appName }} é um chatbot com fluxos automatizados e inteligência artificial avançada. foi
            projetado para facilitar a automação de marketing e o autoatendimento.
        </p>
        <p>
            Com o {{ $appName }}, você pode criar fluxos de conversa personalizados para interagir com seus
            clientes de forma eficiente e escalável.
        </p>
        <p>
            Além disso, {{ $appName }} possui integração direta com o WhatsApp, permitindo que você se conecte
            diretamente com seus clientes através desse popular aplicativo de mensagens.
        </p>
        <p>
            Experimente agora mesmo o poder do {{ $appName }} e leve sua estratégia de marketing para o próximo
            nível!
        </p>
    </div>

    <div>
        <p>Atenciosamente,</p>
        <p>
            <{{ $appName }}< /p>
                <small>Se você não se cadastrou no {{ $appName }}, por favor, ignore este e-mail.</small>
    </div>
</body>

</html>
