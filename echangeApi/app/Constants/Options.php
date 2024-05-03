<?php

namespace App\Constants;

use App\Models\Option as OptionModel;

class Options
{
    public const MODULE_GENERAL = 'GENERAL';

    public const OPTION_DOCUMENT_FOOTER = 'DOCUMENT_FOOTER';

    public const OPTION_DEFAULT_TAXES_GROUP = 'DEFAULT_TAXES_GROUP';

    public const OPTIONS_MAP = [
        Options::MODULE_GENERAL => [
            Options::OPTION_DOCUMENT_FOOTER => [
                'type' => OptionModel::OPTION_TYPE_WYSIWYG,
                'default' => '',
            ],
            Options::OPTION_DEFAULT_TAXES_GROUP => [
                'type' => OptionModel::OPTION_TYPE_OBJECT,
                'default' => '',
                'data' => [
                    'ressource' => 'tax-groups',
                    'searchField' => 'name',
                ],
            ],
        ],
    ];
}
