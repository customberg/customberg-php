import React from 'react';
const { Component, useState } = window.Laraberg.wordpress.element;
const { InnerBlocks, RichText } = window.Laraberg.wordpress.blockEditor;
const { ColorPicker, Popover, Button, Dashicon, DropdownMenu } = window.Laraberg.wordpress.components;
const { withSelect } = window.Laraberg.wordpress.data;
import ButtonUpload from './components/ButtonUpload';
import CustomLoader from './components/CustomLoader';
import CustombergPreviewBlock from './CustombergPreviewBlock';
import Select from 'react-select';
import { cloneDeep } from 'lodash';

window.CustombergEditFields = (block) => {
    return withSelect((select, props) => {
        let isSelected = select('core/block-editor').isBlockSelected(props.clientId);
        let isInnerSelected = select('core/block-editor').hasSelectedInnerBlock(props.clientId, true);
        return { isSelected, isInnerSelected };
    })(
        class extends Component {
            constructor() {
                super(...arguments);
                this.default_language = window.CustombergConfig?.default_language || 'en';
                this.props.attributes.activeLang = this.default_language;
                this.languages = {};
                Object.entries(window.CustombergConfig?.languages || {}).forEach(([code, name]) => {
                    this.languages[code] = { code, name };
                });
            }

            componentDidMount() {
                // console.log('PROPS: ', this.props);
                // console.log('BLOCK: ', block);
                const { activeLang } = this.props.attributes;
                let attributes = cloneDeep(this.props.attributes);

                Object.keys(attributes).map((fieldName) => {
                    let field = block.fields.find((e) => e.name == fieldName);
                    if (field && field.multilanguage) {
                        if (typeof attributes[field.name] == 'string') {
                            attributes[fieldName] = {
                                [activeLang]: attributes[fieldName],
                            };
                        }
                    }
                    if (field && field.type == 'repeatable') {
                        if (attributes[field.name]) {
                            Object.keys(attributes[field.name]).map((subFieldName) => {
                                let subField = field.fields.find((e) => e.name == subFieldName);
                                if (subField && subField.multilanguage) {
                                    if (typeof attributes[field.name][subFieldName] == 'string') {
                                        attributes[fieldName][subFieldName] = {
                                            [activeLang]: attributes[fieldName][subFieldName],
                                        };
                                    }
                                }
                            });
                        }
                    }
                });
                this.props.setAttributes(attributes);
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
                this.props.setAttributes({ activeLang: lang.code });
            }

            render() {
                const { isSelected, isInnerSelected } = this.props;
                const { activeLang } = this.props.attributes;
                return (
                    <div className={this.props.className}>
                        <h3 style={{ paddingTop: 10 }}>Block: {block.name}</h3>

                        {!isSelected && !isInnerSelected ? (
                            <CustombergPreviewBlock block={block} attributes={this.props.attributes} />
                        ) : (
                            <>
                                {block.multilanguage && (
                                    <div style={styles.langButtonsContainer}>
                                        <DropdownMenu
                                            icon={null}
                                            label=""
                                            toggleProps={{
                                                variant: 'primary',
                                                style: { borderRadius: 6 },
                                                children: <b>{this.languages[activeLang]?.name}</b>,
                                            }}
                                            controls={Object.values(this.languages).map((item) => ({
                                                title: item.name,
                                                onClick: () => this.changeLang(item),
                                            }))}
                                        />
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
                                                            [activeLang]: value,
                                                        },
                                                    });
                                                } else this.props.setAttributes({ [field.name]: value });
                                            })}
                                            {field.type != 'repeatable' && field.hint ? (
                                                <div style={{ marginBottom: '.5rem', marginTop: '-0.3rem', fontSize: '15px' }}>{field.hint}</div>
                                            ) : null}
                                            {field.type != 'repeatable' && field.hint_html ? (
                                                <div style={{ marginBottom: '.5rem', marginTop: '-0.3rem', fontSize: '15px' }} dangerouslySetInnerHTML={{ __html: field.hint_html }}></div>
                                            ) : null}
                                        </div>
                                    ))}
                                </div>
                                {/* <pre>{JSON.stringify(this.props.attributes, null, 2)}</pre> */}
                            </>
                        )}
                    </div>
                );
            }

            renderField(field, value, onChange) {
                let { activeLang } = this.props.attributes;
                if (field.multilanguage) {
                    if (typeof value == 'string' || !value) {
                        value = { [activeLang]: value };
                    }
                }

                if (field.type == 'text' || field.type == 'number' || field.type == 'email' || field.type == 'url') {
                    return (
                        <label style={{ width: '100%' }}>
                            <div>{field.label}</div>
                            <input
                                type={field.type}
                                value={field.multilanguage ? value[activeLang] : value}
                                placeholder={field.placeholder || undefined}
                                onChange={(e) => onChange(e.target.value)}
                                style={{
                                    width: '100%',
                                    padding: '5px 10px',
                                    borderRadius: 6,
                                    border: '1px solid #ccc',
                                }}
                            />
                        </label>
                    );
                }
                
                if (field.type == 'checkbox') {
                    return (
                        <label style={{ width: '100%' }}>
                            <input
                                type="checkbox"
                                checked={field.multilanguage ? value[activeLang] : value}
                                onChange={(e) => onChange(e.target.checked)}
                                style={{
                                    marginRight: '10px',
                                }}
                            />
                            <span>{field.label}</span>
                        </label>
                    );
                }

                if (field.type == 'textarea') {
                    return (
                        <label style={{ width: '100%' }}>
                            <div>{field.label}</div>
                            <textarea
                                value={field.multilanguage ? value[activeLang] : value}
                                onChange={(e) => onChange(e.target.value)}
                                placeholder={field.placeholder || undefined}
                                style={{
                                    width: '100%',
                                    padding: '5px 10px',
                                    resize: 'none',
                                    height: '6rem',
                                    borderRadius: 6,
                                    border: '1px solid #ccc',
                                }}
                            />
                        </label>
                    );
                }

                if (field.type == 'upload_image' || field.type == 'upload_file') {
                    return (
                        <div style={{ width: '100%', marginBottom: '0.5rem' }}>
                            <label style={{ marginBottom: 0 }}>{field.label}</label>
                            <ButtonUpload
                                type={field.type == 'upload_image' ? 'image' : 'file'}
                                value={value}
                                onChange={(items) => onChange(items)}
                                self_path={field.self_path}
                                block_slug={block.slug}
                                allowed_types={field.allowed_types}
                                multiple={field.type == 'upload_file' && field.multiple ? true : false}
                            />
                        </div>
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
                                    value={field.multilanguage ? value[activeLang] : value}
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
                        <div style={{ width: '100%', marginBottom: '0.5rem' }} className="customberg-field-color">
                            <label style={{ marginBottom: 0 }}>{field.label}</label>
                            <ColorPickerField value={value} onChange={onChange} />
                        </div>
                    );
                }

                if (field.type == 'custom') {
                    return (
                        <div style={{ width: '100%', marginBottom: '0.5rem' }} className="customberg-field-custom">
                            <label style={{ marginBottom: 0 }}>{field.label}</label>
                            <CustomLoader
                                js={field.js}
                                props={{ field: field, value: value, onChange: onChange, activeLang: activeLang }}
                            />
                        </div>
                    );
                }

                if (field.type == 'repeatable') {
                    if (!value) value = [];
                    const addItem = () => {
                        let items = cloneDeep(value || []);
                        items.push({});
                        onChange(items);
                    };
                    const updateItem = (subField, index, subValue) => {
                        let items = cloneDeep(value || []);
                        if (subField.multilanguage) {
                            if (typeof items[index][subField.name] == 'string' || !items[index][subField.name]) {
                                items[index][subField.name] = { ro: items[index][subField.name] };
                            }
                            items[index][subField.name][this.props.attributes.activeLang] = subValue;
                        } else {
                            items[index][subField.name] = subValue;
                        }
                        onChange(items);
                    };
                    const deleteItem = (index) => {
                        let items = cloneDeep(value || []);
                        items.splice(index, 1);
                        onChange(items);
                    };
                    const moveItem = (index, toIndex) => {
                        let items = cloneDeep(value || []);
                        items.splice(toIndex, 0, items.splice(index, 1)[0]);
                        onChange(items);
                    };
                    const duplicateItem = (index) => {
                        let cloned = cloneDeep(value[index]);
                        let items = cloneDeep(value || []);
                        items.splice(index + 1, 0, cloned);
                        onChange(items);
                    };
                    let reachedMaxItems = false;
                    if (field.maxItems && value.length >= field.maxItems) {
                        reachedMaxItems = true;
                    }
                    return (
                        <div>
                            <label style={{ marginBottom: 0 }}>{field.label}</label>
                            {field.hint ? (
                                <div style={{ marginBottom: '4px', fontSize: '15px' }}>{field.hint}</div>
                            ) : null}
                            {field.hint_html ? (
                                <div style={{ marginBottom: '4px', fontSize: '15px' }} dangerouslySetInnerHTML={{ __html: field.hint_html }}></div>
                            ) : null}
                            <div>
                                {value.map((item, index) => (
                                    <div key={field.name + index} style={styles.repeatableItem}>
                                        <div style={styles.repeatableActions}>
                                            <button
                                                type="button"
                                                onClick={() => deleteItem(index)}
                                                style={styles.repeatableActionBtn}
                                            >
                                                <Dashicon icon="trash" />
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => moveItem(index, index - 1)}
                                                style={styles.repeatableActionBtn}
                                            >
                                                <Dashicon icon="arrow-up-alt" />
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => moveItem(index, index + 1)}
                                                style={styles.repeatableActionBtn}
                                            >
                                                <Dashicon icon="arrow-down-alt" />
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => duplicateItem(index)}
                                                style={styles.repeatableActionBtn}
                                            >
                                                <Dashicon icon="admin-page" />
                                            </button>
                                        </div>
                                        {field.fields.map((subField, subIndex) => (
                                            <div key={subField.name + subIndex} style={{ padding: 4 }}>
                                                {this.renderField(subField, item[subField.name], (value) => {
                                                    updateItem(subField, index, value);
                                                })}
                                                {subField.type != 'repeatable' && subField.hint ? (
                                                    <div style={{ marginBottom: '.5rem', marginTop: '-0.3rem', fontSize: '15px' }}>{subField.hint}</div>
                                                ) : null}
                                                {subField.type != 'repeatable' && subField.hint_html ? (
                                                    <div style={{ marginBottom: '.5rem', marginTop: '-0.3rem', fontSize: '15px' }} dangerouslySetInnerHTML={{ __html: subField.hint_html }}></div>
                                                ) : null}
                                            </div>
                                        ))}
                                    </div>
                                ))}
                            </div>
                            <div>
                                <Button
                                    variant="primary"
                                    style={{ borderRadius: 6 }}
                                    onClick={() => !reachedMaxItems && addItem()}
                                    disabled={reachedMaxItems}
                                >
                                    Add item
                                </Button>
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
                    display: 'flex',
                    flexDirection: 'row',
                    alignContent: 'center',
                    marginTop: 4,
                }}
            >
                <div style={{ position: 'relative' }}>
                    <div className="components-color-picker__swatch">
                        <div
                            className="components-color-picker__active"
                            style={{
                                backgroundColor: value,
                                width: 20,
                                height: 20,
                                borderRadius: '100%',
                                position: 'absolute',
                                right: 10,
                                top: '50%',
                                transform: 'translate(0, -50%)',
                            }}
                        />
                        <div style={{ position: 'absolute', right: 24, top: '100%' }}>
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
                            padding: '5px 10px',
                            borderRadius: 6,
                            border: '1px solid #ccc',
                            ...(visible ? { borderColor: 'rgb(0, 124, 186)', boxShadow: '0 0 0 0.5px #007cba' } : {}),
                        }}
                    />
                </div>
            </div>
        </>
    );
}

const styles = {
    repeatableItem: {
        border: '1px solid #ccc',
        borderRadius: 6,
        padding: 10,
        paddingLeft: 40,
        marginBottom: 10,
        position: 'relative',
    },
    repeatableActions: {
        position: 'absolute',
        left: 5,
        top: '50%',
        transform: 'translateY(-50%)',
        width: 26,
        display: 'flex',
        flexDirection: 'column',
        gap: 6,
    },
    repeatableActionBtn: {
        webkitAppearance: 'none',
        background: 'rgba(0, 0, 0, 0.07)',
        border: 'none',
        height: 26,
        width: 26,
        padding: 0,
        borderRadius: '100%',
        fontFamily: 'monospace',
        fontSize: 12,
        verticalAlign: 'middle',
        textAlign: 'center',
    },
    langButtonsContainer: {
        position: 'absolute',
        top: 10,
        right: 10,
    },
};
