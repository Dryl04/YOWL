<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Activation de votre compte</title>
</head>
<body>
<h2>Bonjour {{ $user->name }},</h2>
<p>Merci de vous être inscrit. Cliquez sur le lien ci-dessous pour activer votre compte :</p>
<p>
    <a href="{{ $activationLink }}" style="color:#1a73e8;">Activer mon compte</a>
</p>
<p>Si vous n'avez pas créé de compte, ignorez simplement cet e-mail.</p>
</body>
</html>
