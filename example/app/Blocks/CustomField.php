<?php

namespace App\Blocks;

class CustomField
{
    public static function render()
    {
        return [
            'name' => 'CustomField',
            'slug' => 'cb-custom-field',
            'icon' => 'block-default', // https://developer.wordpress.org/resource/dashicons/
            'fields' => [
                [
                    'name' => 'content',
                    'label' => 'Content',
                    'type' => 'custom',
                    'multilanguage' => true,
                    'js' => <<<'JS'
                        const element = React.useRef(null);
                        const instance = React.useRef(null);
                        const old = React.useRef(props.activeLang);
                        const doubleChange = React.useRef();
                        
                        function loadLibrary() {
                            return new Promise((resolve, reject) => {
                                if ($.fn.summernote) {
                                    resolve();
                                } else {
                                    $('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', '/packages/summernote/dist/summernote-bs4.css'));
                                    $('head').append($('<style type="text/css"> .note-editor.note-frame .note-status-output, .note-editor.note-airframe .note-status-output { height: auto; } </style>'));
                                    $.getScript('/packages/summernote/dist/summernote-bs4.min.js', resolve);
                                }
                            });
                        }
                        
                        React.useEffect(() => {
                            var summernoteOptions = {
                                dialogsInBody: true,
                                tooltip: false,
                                callbacks: {
                                    onChange: function(contents, $editable) {
                                        if (doubleChange.current) {
                                            doubleChange.current = null;
                                            return;
                                        }
                                        props.onChange(contents);
                                    },
                                },
                            };
                            loadLibrary().then(() => {
                                $(element.current).summernote(summernoteOptions);
                                instance.current = $(element.current);
                            });
                            return () => {
                                if (instance.current) instance.current.summernote('destroy');
                            }
                        }, [element]);
                        
                        React.useEffect(() => {
                            console.log('useeffect old', old.current, 'new', props.activeLang);
                            if (instance.current && old.current !== props.activeLang) {
                                let newVal = props.field.multilanguage ? props.value[props.activeLang] : props.value;
                                doubleChange.current = true; // the next line trigger onchange. this disables it;
                                instance.current.summernote('code', newVal);
                            }
                            old.current = props.activeLang;
                        }, [props.value, props.activeLang]);
                        
                        return (
                            React.createElement('div', {},
                                React.createElement('textarea', {
                                    ref: element,
                                    value: props.field.multilanguage ? props.value[props.activeLang] : props.value,
                                })
                            )
                        );
                    JS,
                ],
            ],
        ];
    }
}
