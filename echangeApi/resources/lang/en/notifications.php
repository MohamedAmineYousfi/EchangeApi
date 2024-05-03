<?php

declare(strict_types=1);

return [
    'object_created_subject' => '[:APPNAME][:OBJECTTYPE] :causer created :objectName',
    'object_updated_subject' => '[:APPNAME][:OBJECTTYPE] :causer modified :objectName',
    'object_deleted_subject' => '[:APPNAME][:OBJECTTYPE] :causer deleted :objectName',
    'object_created_description' => ':causer created :objectName (:objectType)',
    'object_updated_description' => ':causer modified :objectName (:objectType)',
    'object_deleted_description' => ':causer deleted :objectName (:objectType)',
    'show' => 'Show',
    'notification_title' => '',
    'notification_before_action' => '',
    'notification_action' => ':text',
    'notification_after_action' => '',
    'notification_footer' => '',
    'notifications_url' => env('FRONT_APP_URL').'/notifications/view/:id',
    'message_enable' => 'Two-factor authentication enabled successfully.',
    'message_disable' => 'Two-factor authentication disabled successfully.',
    'verification_code_label' => 'Your verification code:',
    'verification_code_sent_success' => 'The verification code has been successfully sent to your email address.',
    'verification_code_incorrect' => 'Invalid verification code.',
    'connected' => 'Connected successfully.',
    'invalid_or_expired_code' => 'Invalid or expired verification code.',

    'verification_code_instructions' => "Please use the verification code sent to your registered email address to enable two-factor authentication in your account. Do not share this code with anyone. If you didn't request this, please ignore the email. Thank you for trusting us",
    'code_expires' => 'This code expires in 15 minutes.',
    'verification_code' => 'Your verification code to enable two-factor authentication is:',
    'greeting' => 'Hello,',
    'unauthorized_sync_product' => 'You are not authorized to synchronize products',
];
