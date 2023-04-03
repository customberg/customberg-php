const { Component } = window.Laraberg.wordpress.element;
const { DropZone, FormFileUpload, Button, Dashicon } = window.Laraberg.wordpress.components;
import PropTypes from 'prop-types';

class ButtonUpload extends Component {
    static propTypes = {
        /** The current value */
        value: PropTypes.string.isRequired,
        /** The change event. Gets called on upload successfully or file removed. */
        onChange: PropTypes.func.isRequired,
        /** The type of upload component. Currently only file or image */
        type: PropTypes.oneOf(['file', 'image']),
        block_slug: PropTypes.string.isRequired,
        /** Dot path to this field configuration */
        self_path: PropTypes.string,
        /** Allowed file extensions, as array */
        allowed_types: PropTypes.arrayOf(PropTypes.string).isRequired,
        multiple: PropTypes.bool,
    }
    static defaultProps = {
        type: 'file',
        multiple: false,
    }
    constructor() {
        super(...arguments);
        this.state = {
            type: this.props?.type || 'file',
            loading: false,
            value: this.props?.value || '',
        };
        this.multiple = this.props?.multiple || false;
    }

    upload = (files, onChange, index = true) => {
        this.setState({ loading: index });
        const options = { data: {} };
        if (this.props?.block_slug) {
            options.data.block_slug = this.props?.block_slug;
        }
        if (this.props?.self_path) {
            options.data.self_path = this.props?.self_path;
        }
        window
            .CustombergUploadAction(files, options)
            .then((data) => {
                this.setState({ loading: false });
                if (data?.files && data.files?.length > 0) {
                    onChange(data.files);
                }
                if (data.errors?.length > 0) {
                    data.errors.forEach(error => {
                        new Noty({ type: 'error', text: error }).show();
                    });
                }
            })
            .catch((error) => {
                this.setState({ loading: false });
            });
    };

    remove = (e, onChange) => {
        e.preventDefault();
        e.stopPropagation();
        onChange?.(null);
        return false;
    };
    parseMultipleFiles(value) {
        let files = [];
        try {
            files = JSON.parse(value);
        } catch (e) { files = [] }
        return files;
    }

    componentDidUpdate(prevProps) {
        if (prevProps.value != this.props.value) {
            this.setState({ value: this.props.value });
        }
    }
    
    getFilename(value) {
        return ('' + value).split('/').pop();
    }

    render() {
        if (this.props.type == 'file') return this.renderFile()
        if (this.props.type == 'image') return this.renderImage()
        return null;
    }
    
    renderImage = () => {
        return (
            <div
                style={{
                    width: '100%',
                    display: 'flex',
                    flexDirection: 'row',
                    alignItems: 'center',
                    borderRadius: 6,
                    border: '1px solid #ccc',
                    padding: 20,
                }}
            >
                <div
                    style={{
                        backgroundColor: '#e4e4e4',
                        width: 150,
                        height: 150,
                        flexShrink: 0,
                        borderRadius: 6,
                    }}
                >
                    {this.state.value ? (
                        <img src={this.state.value} style={{ width: '100%', height: '100%', objectFit: 'contain' }} />
                    ) : (
                        <div style={{ width: '100%', height: '100%', display: 'flex' }}>
                            {this.state.loading !== false ? (
                                <div className="customberg-bt-spinner" style={{ margin: 'auto' }} />
                            ) : null}
                        </div>
                    )}
                </div>
                <div style={{ flexGrow: 1 }}>
                    {this.state.value ? (
                        <div style={{ padding: 20 }}>
                            <b style={{ display: 'block', marginBottom: 6 }}>
                                {this.getFilename(this.state.value)}
                            </b>
                            <a href={this.state.value} target='_blank' style={{ fontSize: '80%', fontWeight: '400' }}>
                                {this.state.value}
                            </a>
                            <div style={{ display: 'flex', flexDirection: 'row', marginTop: 6 }}>
                                <Button
                                    variant="secondary" isSmall={true} isDestructive={true}
                                    icon="trash" iconPosition="left"
                                    onClick={(e) => this.remove(e, this.props?.onChange)} style={{ borderRadius: 6 }}
                                >
                                    Remove
                                </Button>
                            </div>
                        </div>
                    ) : (
                        <>
                            <p style={{ marginBottom: 10, paddingLeft: 12 }}>
                                Drag and drop the image here
                            </p>
                            <DropZone onFilesDrop={(files) => {
                                this.upload(files, (urls) => this.props?.onChange?.(urls[0]))
                            }} />
                            <FormFileUpload
                                // docs: https://wordpress.github.io/gutenberg/?path=/docs/components-formfileupload--default
                                accept={this.props?.allowed_types?.map?.(ext => '.'+ext).join(',')}
                                onClick={(event) => (event.target.value = '' )} // this allows selecting the same file twice
                                onChange={(event) => {
                                    this.upload(event.currentTarget.files, (urls) => this.props?.onChange?.(urls[0]))
                                }}
                            >
                                <div style={{ display: 'flex', flexDirection: 'row' }}>
                                    <Button variant="secondary" style={{ borderRadius: 6 }}>
                                        Or click Upload
                                    </Button>
                                </div>
                            </FormFileUpload>
                        </>
                    )}
                </div>
            </div>
        );
    }
    
    renderFile = () => {
        if (! this.multiple) {
            return this.renderFileItem(0, this.state.value, this.props?.onChange, (files) => {
                this.upload(files, (urls) => this.props?.onChange?.(urls[0]), 0)
            });
        }
        let files = this.parseMultipleFiles(this.state.value);
        return (
            [...files, ''].map((item, index) => (
                <div key={'key-'+index+item}>
                    {this.renderFileItem(
                        index, item,
                        (value) => {
                            let files = this.parseMultipleFiles(this.state.value);
                            files.splice(index, 1);
                            this.props?.onChange(JSON.stringify(files));
                        },
                        (files) => {
                            this.upload(files, (urls) => {
                                let files = this.parseMultipleFiles(this.state.value);
                                files[index] = urls[0];
                                if (urls.length > 1) {
                                    urls.forEach((url, ii) =>  {
                                        if (ii > 0) files.push(url)
                                    })
                                }
                                this.props?.onChange(JSON.stringify(files));
                            }, index)
                        }
                    )}
                </div>
            ))
        );
    }
    
    renderFileItem = (index, value = '', onChange, onUpload) => {
        return (
            <div
                style={{
                    width: '100%',
                    display: 'flex',
                    flexDirection: 'row',
                    alignItems: 'center',
                    borderRadius: 6,
                    border: '1px solid #ccc',
                    padding: '10px 0px',
                    marginTop: index == 0 ? 0 : 6,
                }}
            >
                {this.state.loading === index ? (
                    <>
                        <div style={{ width: 20, height: 20, flexShrink: 0, marginLeft: 10 }}>
                            <div className="customberg-bt-spinner" style={{ margin: 'auto', width: 20, height: 20 }} />
                        </div>
                        <b style={{ display: 'block', paddingLeft: 10 }}>
                            Uploading ...
                        </b>
                    </>
                ) : value ? (
                    <>
                        <div style={{ flexShrink: 0, width: 20, height: 20, marginLeft: 10 }}>
                            {this.fileIcon(value)}
                        </div>
                        <div style={{ flexGrow: 1, paddingLeft: 10 }}>
                            <a href={value} target='_blank' style={{ fontWeight: 'bold' }}>
                                {this.getFilename(value)}
                            </a>
                        </div>
                        <div style={{ flexShrink: 0, display: 'flex', flexDirection: 'row', marginRight: 10 }}>
                            <Button
                                variant="secondary" isSmall={true} isDestructive={true}
                                icon="trash" iconPosition="left"
                                onClick={(e) => this.remove(e, onChange)} style={{ borderRadius: 6 }}
                            >
                                Remove
                            </Button>
                        </div>
                    </>
                ) : (
                    <>
                        <DropZone onFilesDrop={(files) => onUpload(files)} />
                        <FormFileUpload
                            // docs: https://wordpress.github.io/gutenberg/?path=/docs/components-formfileupload--default
                            accept={this.props?.allowed_types?.map?.(ext => '.'+ext).join(',')}
                            onClick={(event) => (event.target.value = '' )} // this allows selecting the same file twice
                            onChange={(event) => onUpload(event.currentTarget.files)}
                            multiple={this.multiple}
                        >
                            <div style={{ display: 'flex', flexDirection: 'row', alignItems: 'center' }}>
                                <Button variant="secondary" style={{ borderRadius: 6, marginRight: 10 }}>
                                    Upload
                                </Button>
                                <div>
                                    Drag and drop or click to upload
                                    {this.multiple ? ' multiple files ' : ' a file '}
                                    here
                                </div>
                            </div>
                        </FormFileUpload>
                    </>
                )}
            </div>
        );
    }
    
    fileIcon = (fileName) => {
        const ext = ('' + fileName).split('.').pop();
        let icon = 'media-default';
        if (['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'bmp'].indexOf(ext) !== -1) {
            icon = 'format-image';
        }
        if (['mp4', 'webm', 'mkv', 'flv', 'avi', 'mov', 'm4p', 'mpeg', 'm4v'].indexOf(ext) !== -1) {
            icon = 'format-video';
        }
        if (['mp3', 'flac', 'm4a', 'wav', 'wma', 'aac'].indexOf(ext) !== -1) {
            icon = 'media-audio';
        }
        if (['pdf', 'doc', 'docx', 'xml'].indexOf(ext) !== -1) {
            icon = 'media-document';
        }
        if (['ppt', 'pptx'].indexOf(ext) !== -1) {
            icon = 'media-interactive';
        }
        if (['xls', 'xlsx', 'csv'].indexOf(ext) !== -1) {
            icon = 'media-spreadsheet';
        }
        if (['zip', 'tar', 'bz', 'bz2', 'rar'].indexOf(ext) !== -1) {
            icon = 'media-archive';
        }
        if (ext == 'txt') icon = 'media-text';
        return <Dashicon icon={icon} />
    }
}

export default ButtonUpload;
