<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 580px; margin: 40px auto; background: #ffffff;
                     border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #1a1a2e; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0; }
        .body { padding: 30px 40px; }
        .body p { color: #444; line-height: 1.7; font-size: 15px; }
        .alert-box { background: #fff3e0; border-left: 4px solid #ff9800;
                     padding: 15px 20px; border-radius: 5px; margin: 20px 0; }
        .alert-box p { margin: 0; color: #e65100; font-size: 14px; }
        .footer { background: #f9f9f9; text-align: center; padding: 20px;
                  font-size: 12px; color: #999; }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1>🔐 Modification de mot de passe</h1>
    </div>

    <div class="body">
        <p>Bonjour <strong>{{ $user->name }}</strong>,</p>

        <p>
            Nous vous confirmons que le mot de passe de votre compte associé à
            l'adresse <strong>{{ $user->email }}</strong> a été modifié avec succès.
        </p>

        <div class="alert-box">
            <p>
                ⚠️ Si vous n'êtes <strong>pas à l'origine</strong> de cette modification,
                contactez immédiatement notre support ou réinitialisez votre mot de passe.
            </p>
        </div>

        <p>
            Cette modification a été effectuée le
            <strong>{{ now()->format('d/m/Y à H:i') }}</strong>.
        </p>

        <p>Cordialement,<br>L'équipe {{ config('app.name') }}</p>
    </div>

    <div class="footer">
        © {{ date('Y') }} {{ config('app.name') }} — Cet email est envoyé automatiquement, merci de ne pas y répondre.
    </div>

</div>
</body>
</html>