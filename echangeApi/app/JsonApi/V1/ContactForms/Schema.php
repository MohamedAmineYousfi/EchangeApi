<?php

namespace App\JsonApi\V1\ContactForms;

use App\Models\ContactForm;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{
    /**
     * @var string
     */
    protected $resourceType = 'contact-forms';

    /**
     * @param  ContactForm  $resource
     *                         the domain record being serialized.
     */
    public function getId($resource): string
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param  ContactForm  $resource
     *                         the domain record being serialized.
     */
    public function getAttributes($resource): array
    {
        return [
            'firstname' => $resource->firstname,
            'lastname' => $resource->lastname,
            'email' => $resource->email,
            'phone' => $resource->phone,
            'message' => $resource->message,
        ];
    }

    public function getRelationships($item, $isPrimary, array $includeRelationships): array
    {
        return [];
    }
}
