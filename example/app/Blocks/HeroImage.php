<?php

namespace App\Blocks;

class HeroImage
{
    public static function render()
    {
        return [
            'name' => 'HeroImage',
            'slug' => 'cb-hero-image',
            'icon' => 'universal-access-alt',
            'fields' => [
                // supported types are:
                // text, rich_text, color, upload_image
                // repeatable ( 'fields' => [  ] )
                // blocks ( 'allowed' => ['core/paragraph', 'core/list'] )
                // select (
                //     'placeholder' => 'string',
                //     'multiple' => true/false,
                //     'disabled' => true/false,
                //     'default' => string or array,
                //     'options' => array with key => value,
                // )

                [
                    'name' => 'title',
                    'label' => 'Titlu',
                    'type' => 'text',
                ],
            ],
        ];
    }
}
