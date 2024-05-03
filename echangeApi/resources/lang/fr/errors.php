<?php

declare(strict_types=1);

return [
    'delete_model_has_relations' => ':model a encore un(e) ou plusieurs :relation associé(e)s',
    'delivery_item_x_not_found_in_order' => 'Le produit :product n\'a pas été trouvée dans la commande',
    'delivery_item_x_quantity_x_is_greater_than_order_quantity_x' => 'Le produit :product a une quantité (:quantity) supérieure au restant de la commande à livrer (:remainingQuantity)',
    'this_order_has_validated_deliveries' => 'Cette commande a des livraisons validées',
    'this_invoice_has_completed_payments' => 'Cette facture a des paiements completés',
    'this_delivery_is_already_validated' => 'Cette livraison est déjà validée',
    'this_order_has_validated_invoice' => 'Cette commande a une facture validée',
    'invalid_credentials' => 'Identifiant ou mot de passe incorrect',
    'the_user_is_not_active' => 'L\'utilisateur est désactivé',
    'product_not_allowed_for_warehouse_x' => 'Ce produit n\'est pas autorisé dans l\'entrepôt :warehouse',
    'quantity_remainin_in_warehouse_x_is_less_than_x_x' => 'La quantité restante dans l\'entrepôt :warehouse est inferieure à :quantity :unit',
    'stock_movement_should_be_draft' => 'Le mouvement de stock devrait etre brouillon',
    'product_x_already_added' => 'Le produit :product existe déjà.',
    'invoicing_item_x_quantity_x_is_greater_than_order_quantity_x' => 'Le produit :product a une quantité (:quantity) supérieure au restant de la commande à facturer (:remainingQuantity)',
    'invoicing_item_x_not_found_in_order' => 'Le produit :product n\'a pas été trouvée dans la commande',
    'two_factor_authentication_required' => 'Authentification à deux facteurs requise.',
    'please_provide_a_token' => 'Veuillez fournir un token.',
    'invalid_token' => 'Token invalide.',
    'no_token_provided' => 'Aucun token fourni.',
    'mode_not_exists' => "Ce modèle d'import n'existe pas",
    'multiple_email_validation' => "Le format du champ :attribute n'est pas valide. Assurez-vous que les adresses e-mail sont séparées par des virgules.",
    'product_x_quantity_is_not_null' => 'Le produit :product a encore une quantité :quantity en stock.',
    'unit_of_measure_must_have_one_reference_unit' => 'L\'unite de mesure doit avoir au moins une unité de reference',
    'previous_max_amount_null' => 'LE MONTANT MAXIMUM DU PALIER PRÉCÉDENT EST VIDE. VEUILLEZ LUI ATTRIBUER UNE VALEUR POUR POUVOIR AJOUTER UN NOUVEAU PALIER.',
    'invalid_current_max_amount' => 'LE MONTANT MAXIMUM NE PEUT ÊTRE INFÉRIEUR AU MONTANT MINIMUM.',
    'interval_overlap_error' => 'CE PALIER CHEVAUCHE UN PALIER DÉJÀ EXISTANT.',
    'no_property_found' => 'Aucune propriété trouvée pour cet encan',
];
