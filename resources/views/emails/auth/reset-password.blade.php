@component('mail::message')
# Bonjour {{ $userName }},

Vous avez demandé à réinitialiser votre mot de passe pour votre compte **CRM RH**.

Cliquez sur le bouton ci-dessous pour définir un nouveau mot de passe :

@component('mail::button', ['url' => $url])
Réinitialiser mon mot de passe
@endcomponent

Si vous n’êtes pas à l’origine de cette demande, ignorez simplement ce message.

Cordialement,
**L’équipe CRM RH**
@endcomponent
