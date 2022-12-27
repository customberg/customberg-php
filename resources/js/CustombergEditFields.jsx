import React from 'react';
const { Component, useState } = window.Laraberg.wordpress.element;
const { InnerBlocks, RichText } = window.Laraberg.wordpress.blockEditor;
const { ColorPicker, Popover } = window.Laraberg.wordpress.components;
const { withSelect } = window.Laraberg.wordpress.data;
import ButtonUpload from './components/ButtonUpload';
import CustombergPreviewBlock from './CustombergPreviewBlock';
import Select from 'react-select';

const languages = [
    { name: 'ro', value: 'Romana' },
    { name: 'en', value: 'English' },
];

window.CustombergEditFields = (block) => {
    return withSelect((select, props) => {
        let isSelected = select('core/block-editor').isBlockSelected(props.clientId);
        let isInnerSelected = select('core/block-editor').hasSelectedInnerBlock(props.clientId, true);
        return { isSelected, isInnerSelected };
    })(
        class extends Component {
            constructor() {
                super(...arguments);
                this.props.attributes.activeLang = languages[0].name;
            }

            componentDidMount() {
                console.log('PROPS: ', this.props);
                console.log('BLOCK: ', block);

                Object.keys(this.props.attributes).map((fieldName) => {
                    let field = block.fields.find((e) => e.name == fieldName);
                    if (field && field.multilanguage) {
                        if (typeof this.props.attributes[field.name] == 'string') {
                            this.props.attributes[fieldName] = { ro: this.props.attributes[fieldName] };
                        }
                    }
                    if (field && field.type == 'repeatable') {
                        if (this.props.attributes[field.name]) {
                            Object.keys(this.props.attributes[field.name]).map((subFieldName) => {
                                let subField = field.fields.find((e) => e.name == subFieldName);
                                if (subField && subField.multilanguage) {
                                    if (typeof this.props.attributes[field.name][subFieldName] == 'string') {
                                        this.props.attributes[fieldName][subFieldName] = {
                                            ro: this.props.attributes[fieldName][subFieldName],
                                        };
                                    }
                                }
                            });
                        }
                    }
                });
            }

            componentDidUpdate = (prevProps) => {
                const watchProps = ['isSelected', 'isInnerSelected'];
                let shouldUpdate = false;
                watchProps.forEach((k) => {
                    if (this.props[k] != prevProps[k]) shouldUpdate = true;
                });
                if (shouldUpdate) this.forceUpdate();
            };

            changeLang(lang) {
                this.props.setAttributes({ activeLang: lang.name });
                // this.activeLang = lang.name;
                // this.forceUpdate();
                // this.props.setAttributes(this.props.attributes);
            }

            render() {
                const { isSelected, isInnerSelected } = this.props;
                return (
                    <div className={this.props.className}>
                        <h3>Block: {block.name}</h3>

                        {!isSelected && !isInnerSelected ? (
                            <CustombergPreviewBlock block={block} attributes={this.props.attributes} />
                        ) : (
                            <>
                                {block.multilanguage && (
                                    <div style={styles.langButtonsContainer}>
                                        <button
                                            style={{
                                                backgroundColor:
                                                    this.props.attributes.activeLang == 'ro'
                                                        ? '#1E90FF'
                                                        : 'rgba(0,0,0,0.1)',
                                                marginRight: 10,
                                                borderRadius: 5,
                                                borderWidth: 0,
                                            }}
                                            type="button"
                                            onClick={() => this.changeLang({ name: 'ro' })}
                                        >
                                            <span
                                                style={{
                                                    color: this.props.attributes.activeLang == 'ro' ? '#fff' : '#000',
                                                    padding: '5px 10px',
                                                }}
                                            >
                                                ro
                                            </span>
                                        </button>
                                        <button
                                            style={{
                                                color: this.props.attributes.activeLang == 'en' ? '#fff' : '#000',
                                                backgroundColor:
                                                    this.props.attributes.activeLang == 'en'
                                                        ? '#1E90FF'
                                                        : 'rgba(0,0,0,0.1)',
                                                marginRight: 10,
                                                borderRadius: 5,
                                                borderWidth: 0,
                                            }}
                                            type="button"
                                            onClick={() => this.changeLang({ name: 'en' })}
                                        >
                                            <span
                                                style={{
                                                    color: this.props.attributes.activeLang == 'en' ? '#fff' : '#000',
                                                    padding: '5px 10px',
                                                }}
                                            >
                                                en
                                            </span>
                                        </button>
                                    </div>
                                )}
                                <div class="customberg-fields">
                                    {block.fields.map((field, index) => (
                                        <div key={field.name + index} style={{ padding: 4 }}>
                                            {this.renderField(field, this.props.attributes[field.name], (value) => {
                                                if (field.multilanguage) {
                                                    this.props.setAttributes({
                                                        [field.name]: {
                                                            ...this.props.attributes[field.name],
                                                            [this.props.attributes.activeLang]: value,
                                                        },
                                                    });
                                                } else this.props.setAttributes({ [field.name]: value });
                                            })}
                                        </div>
                                    ))}
                                </div>
                            </>
                        )}
                    </div>
                );
            }

            renderField(field, value, onChange) {
                if (field.multilanguage) {
                    if (typeof value == 'string' || !value) {
                        value = { ro: value, en: value };
                    }
                }

                if (field.type == 'text') {
                    return (
                        <label style={{ width: '100%' }}>
                            <div>{field.label}</div>
                            {field.multilanguage ? (
                                <input
                                    type="text"
                                    value={value[this.props.attributes.activeLang]}
                                    onChange={(e) => onChange(e.target.value)}
                                    style={{ width: '100%' }}
                                />
                            ) : (
                                <input
                                    type="text"
                                    value={value}
                                    onChange={(e) => onChange(e.target.value)}
                                    style={{ width: '100%' }}
                                />
                            )}
                        </label>
                    );
                }

                if (field.type == 'upload_image') {
                    return (
                        <label style={{ width: '100%' }}>
                            <div>{field.label}</div>
                            <ButtonUpload value={value} onChange={(items) => onChange(items)} />
                        </label>
                    );
                }

                // isInnerSelected does not work when you focus an inner block.
                // 'blocks' field type is disabled now
                // if (field.type == 'blocks') {
                //     if (
                //         value &&
                //         window.Laraberg.wordpress.data.select('core/block-editor').getBlock(this.props.clientId)
                //             ?.innerBlocks?.length == 0
                //     ) {
                //         window.Laraberg.wordpress.data
                //             .dispatch('core/block-editor')
                //             .insertBlocks(window.Laraberg.wordpress.blocks.parse(value), 0, this.props.clientId);
                //     }
                //     return (
                //         <label style={{ width: '100%' }}>
                //             <div>{field.label}</div>
                //             <div style={{ padding: 10, border: '1px solid #ccc' }}>
                //                 <InnerBlocks
                //                     value={window.Laraberg.wordpress.blocks.parse(value || '')}
                //                     onChange={(content) =>
                //                         onChange(window.Laraberg.wordpress.blocks.serialize(content))
                //                     }
                //                     allowedBlocks={field.allowed || null}
                //                 />
                //             </div>
                //         </label>
                //     );
                // }

                if (field.type == 'select') {
                    let select_options = Object.keys(field?.options || []).map((itemKey) => ({
                        value: itemKey,
                        label: field.options[itemKey],
                    }));
                    if (typeof value == 'undefined' && field.default) value = field.default;
                    let select_value = value
                        ? field.multiple
                            ? value.map((itemValue) => select_options.find((item) => item.value == itemValue))
                            : select_options.find((item) => item.value == value)
                        : null;
                    const select_change = (newValue, event) => {
                        onChange(
                            newValue ? (field.multiple ? newValue.map((item) => item.value) : newValue.value) : null,
                        );
                    };
                    return (
                        <label style={{ width: '100%' }} class="customberg-field-select">
                            <div>{field.label}</div>
                            <Select
                                value={select_value}
                                options={select_options}
                                onChange={select_change}
                                placeholder={field.placeholder || null}
                                isClearable={true}
                                isMulti={field.multiple ? true : false}
                                isDisabled={field.disabled ? true : false}
                            />
                        </label>
                    );
                }

                if (field.type == 'rich_text') {
                    return (
                        <label style={{ width: '100%' }} className="customberg-field-rich-text">
                            <div>{field.label}</div>
                            <div className="rich-text-component">
                                <RichText
                                    tagName="div"
                                    value={value}
                                    onChange={(content) => onChange(content)}
                                    inlineToolbar
                                    multiline
                                />
                            </div>
                        </label>
                    );
                }

                if (field.type == 'color') {
                    return (
                        <div style={{ width: '100%' }} className="customberg-field-color">
                            <div>{field.label}</div>
                            <ColorPickerField value={value} onChange={onChange} />
                        </div>
                    );
                }

                if (field.type == 'repeatable') {
                    if (!value) value = [];
                    const addItem = () => {
                        let items = value ? [...value] : [];
                        items.push({});
                        onChange(items);
                    };
                    const updateItem = (subField, index, subValue) => {
                        let items = [...value];
                        if (subField.multilanguage) {
                            if (typeof items[index][subField.name] == 'string' || !items[index][subField.name]) {
                                items[index][subField.name] = { ro: items[index][subField.name] };
                            }
                            items[index][subField.name][this.props.attributes.activeLang] = subValue;
                        } else {
                            items[index][subField.name] = subValue;
                        }
                        console.log('updateitem', items);
                        onChange(items);
                    };
                    const deleteItem = (index) => {
                        let items = [...value];
                        items.splice(index, 1);
                        onChange(items);
                    };
                    let reachedMaxItems = false;
                    if (field.maxItems && value.length >= field.maxItems) {
                        reachedMaxItems = true;
                    }
                    return (
                        <div>
                            <div>{field.label}</div>
                            <div>
                                {value.map((item, index) => (
                                    <div key={field.name + index} style={styles.repeatableItem}>
                                        <button
                                            type="button"
                                            onClick={() => deleteItem(index)}
                                            style={styles.repeatableDeleteBtn}
                                        >
                                            X
                                        </button>
                                        {field.fields.map((subField, subIndex) => (
                                            <div key={subField.name + subIndex} style={{ padding: 4 }}>
                                                {this.renderField(subField, item[subField.name], (value) => {
                                                    updateItem(subField, index, value);
                                                })}
                                            </div>
                                        ))}
                                    </div>
                                ))}
                            </div>
                            <div>
                                <button
                                    type="button"
                                    onClick={() => !reachedMaxItems && addItem()}
                                    disabled={reachedMaxItems}
                                >
                                    Add item
                                </button>
                            </div>
                        </div>
                    );
                }
            }
        },
    );
};

function ColorPickerField({ value, onChange }) {
    const [visible, setVisible] = useState(false);
    const toRgbaString = ({ rgb }) => `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${rgb.a})`;
    return (
        <>
            <div
                onClick={(event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    setVisible(true);
                }}
                style={{
                    display: 'inline-flex',
                    flexDirection: 'row',
                    alignContent: 'center',
                    position: 'relative',
                    marginTop: 4,
                }}
            >
                <div className="components-color-picker__swatch">
                    <div className="components-color-picker__active" style={{ backgroundColor: value }} />
                    <div style={{ position: 'absolute', left: '50%', top: '100%' }}>
                        {visible && (
                            <Popover noArrow={false} onClose={() => setVisible(false)}>
                                <ColorPicker
                                    color={value}
                                    onChangeComplete={(value) => onChange(value ? toRgbaString(value) : null)}
                                />
                            </Popover>
                        )}
                    </div>
                </div>
                <input
                    type="text"
                    value={value}
                    style={{
                        width: 180,
                        ...(visible ? { borderColor: 'rgb(0, 124, 186)', boxShadow: '0 0 0 0.5px #007cba' } : {}),
                    }}
                />
            </div>
        </>
    );
}

const styles = {
    repeatableItem: {
        border: '1px solid #ccc',
        padding: 10,
        paddingLeft: 40,
        marginBottom: 10,
        position: 'relative',
    },
    repeatableDeleteBtn: {
        webkitAppearance: 'none',
        background: 'rgba(0, 0, 0, 0.05)',
        border: 'none',
        height: 26,
        width: 26,
        borderRadius: '100%',
        fontFamily: 'monospace',
        fontSize: 16,
        verticalAlign: 'middle',
        position: 'absolute',
        left: 5,
        top: '50%',
        transform: 'translateY(-50%)',
    },
    langButtonsContainer: {
        position: 'absolute',
        top: '5px',
        right: '5px',
    },
};
