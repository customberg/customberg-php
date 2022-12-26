const { Component } = window.Laraberg.wordpress.element;
const { DropZone, FormFileUpload, Button } = window.Laraberg.wordpress.components;

class ButtonUpload extends Component {
    constructor() {
        super(...arguments);
        this.state = {
            value: this.props?.value || '',
        };
    }

    upload = (files) => {};

    componentDidUpdate(prevProps) {
        if (prevProps.value != this.props.value) {
            this.setState({ value: this.props.value });
        }
    }

    render() {
        return (
            <div style={{ width: '100%', display: 'flex', flexDirection: 'row', alignItems: 'center' }}>
                <p style={{ marginBottom: 20 }}>Drop the image here</p>
                <DropZone onFilesDrop={(files) => this.upload(files)} />
                <FormFileUpload accept="image/*" onChange={(event) => this.upload(event.currentTarget.files)}>
                    <Button variant="secondary">Or click Upload</Button>
                </FormFileUpload>
            </div>
            // {/* {this.state.value ? (
            //     <img src={this.state.value} style={{ width: 150, height: 150, objectFit: 'cover' }} />
            // ) : (
            //     <div style={{ width: 150, height: 150, backgroundColor: '#ccc', display: 'inline-block' }} />
            // )}
            // <button className="button-upload-component" style={{ marginLeft: 30 }} onClick={() => this.open()}>
            //     Choose file
            // </button> */}
            // </div>
        );
    }
}

export default ButtonUpload;
