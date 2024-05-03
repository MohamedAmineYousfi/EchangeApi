<?php

declare(strict_types=1);

return [
    'object_created_subject' => '[:APPNAME][:OBJECTTYPE] :causer a créé :objectName',
    'object_updated_subject' => '[:APPNAME][:OBJECTTYPE] :causer a modifié :objectName',
    'object_deleted_subject' => '[:APPNAME][:OBJECTTYPE] :causer a supprimé :objectName',
    'object_created_description' => ':causer a créé :objectName (:objectType)',
    'object_updated_description' => ':causer a modifié :objectName (:objectType)',
    'object_deleted_description' => ':causer a supprimé :objectName (:objectType)',
    'show' => 'Voir',
    'notification_title' => '',
    'notification_before_action' => '',
    'notification_action' => ':text',
    'notification_after_action' => '',
    'notification_footer' => '',
    'notifications_url' => env('FRONT_APP_URL').'/notifications/view/:id',
    'message_enable' => 'Double authentification activée avec succès.',
    'message_disable' => 'Double authentification désactivée avec succès.',
    'verification_code_label' => 'Votre code de vérification :',
    'verification_code_sent_success' => 'Le code de vérification a été envoyé avec succès à votre adresse e-mail.',
    'verification_code_incorrect' => 'Code de vérification incorrect.',
    'connected' => 'Connecté avec succès.',
    'invalid_or_expired_code' => 'Code invalide ou expiré.',

    'verification_code_instructions' => "Veuillez utiliser le code de vérification envoyé à votre adresse e-mail enregistrée pour activer l'authentification à deux facteurs sur votre compte. Ne partagez pas ce code avec quiconque. Si vous n'avez pas fait cette demande, veuillez ignorer cet e-mail. Merci de votre confiance.",
    'code_expires' => 'Ce code expire dans 15 minutes.',
    'verification_code' => 'Votre code de vérification pour activer l\'authentification à deux facteurs est :',
    'greeting' => 'Salut ',
    'immutable_status_property' => 'Le statut de cette propriété ne plus être changé, veuillez garder le statut en approuvé pour continuer',
    'unauthorized_sync_product' => "Vous n'êtes pas autorisés à synchroniser des produits",
    'unauthorized_to_delete_transaction' => "Vous n'êtes pas autorisés à supprimer cette transaction",
    'unauthorized_activate_deactivate_properties' => "Vous n'êtes pas autorisés à activer ou desactiver des propriétés",
    'unauthorized_to_export_properties' => "Vous n'êtes pas autorisés à exporter les propriétés",
    'unauthorized_to_change_approved_status_for_properties' => "Vous n'êtes pas autorisés à changer le statut des propriétés approuvées",
    'property_is_not_confirmed' => "La proppriété doit être confirmée d'abord avant de passer à activé.",
    'auction_closed' => "La période d'inscription à l'encan associé à cette propriété est clôturée. Les modifications ne sont plus possibles.",
    'auction_not_found' => 'Aucun encan n’est présentement en cours pour la création de la propriété.',
    'property_cancel_default_reason' => 'Le compte de taxes impayés à été réglé par le (s) propriétaire(s).',
    'unauthorized_to_create_transaction_property' => "Vous n'êtes autorisé(e)s à créer une transaction dans une propriété"
];
