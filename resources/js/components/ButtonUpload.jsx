const { Component } = window.Laraberg.wordpress.element;
const { DropZone, FormFileUpload, Button } = window.Laraberg.wordpress.components;

class ButtonUpload extends Component {
    constructor() {
        super(...arguments);
        this.state = {
            value: this.props?.value || '',
            loading: false,
        };
    }

    upload = (files) => {
        this.setState({ loading: true });
        window
            .CustombergUploadAction(files)
            .then((data) => {
                this.setState({ loading: false });
                if (data && data.length > 0) {
                    this.props?.onChange?.(data[0]);
                }
            })
            .catch((error) => {
                // upload.onError(error.message);
                this.setState({ loading: false });
            });
    };

    remove = (e) => {
        e.preventDefault();
        e.stopPropagation();
        this.props?.onChange?.(null);
        return false;
    };

    componentDidUpdate(prevProps) {
        if (prevProps.value != this.props.value) {
            this.setState({ value: this.props.value });
        }
    }

    render() {
        let fileName = '';
        if (this.state.value) {
            fileName = ('' + this.state.value).split('/').pop();
        }
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
                            {this.state.loading ? (
                                <div className="customberg-bt-spinner" style={{ margin: 'auto' }} />
                            ) : null}
                        </div>
                    )}
                </div>
                <div style={{ flexGrow: 1 }}>
                    {this.state.value ? (
                        <div style={{ padding: 20 }}>
                            <b style={{ display: 'block', marginBottom: 6 }}>{fileName}</b>
                            <small>{this.state.value}</small>
                            <div style={{ display: 'flex', flexDirection: 'row', marginTop: 6 }}>
                                <Button variant="secondary" style={{ borderRadius: 6 }} onClick={(e) => this.remove(e)}>
                                    Remove
                                </Button>
                            </div>
                        </div>
                    ) : (
                        <>
                            <p style={{ marginBottom: 10, paddingLeft: 12 }}>Drop the image here</p>
                            <DropZone onFilesDrop={(files) => this.upload(files)} />
                            <FormFileUpload
                                accept="image/*"
                                onChange={(event) => this.upload(event.currentTarget.files)}
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
}

export default ButtonUpload;
