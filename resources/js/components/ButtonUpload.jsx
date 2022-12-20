const { Component } = window.Laraberg.wordpress.element;

class ButtonUpload extends Component {

    constructor() {
        super(...arguments);
        this.state = {
            value: '',
        }
        if (this.props.type) this.type = this.props.type;
        if (this.props.value) this.state.value = this.props.value;
    }
    type = 'image'
    prefix = '/admin/laraberg-filemanager'

    open() {
        window.open(this.prefix + '?type=' + this.type, 'FileManager', 'width=900,height=600');
        window.SetUrl = (items) => {
            if (this.props.onChange) {
                this.props.onChange(items);
            }
        };
    }

    componentDidUpdate(prevProps) {
        if (prevProps.value != this.props.value) {
            this.setState({ value: this.props.value });
        }
    }

    render(){
        return (
            <div style={{width:'100%',display:'flex',flexDirection:'row',alignItems:'center',}}>
                {this.state.value ? (
                    <img src={this.state.value} style={{width: 150, height:150, objectFit:'cover'}} />
                ) : (
                    <div style={{width: 150, height:150, backgroundColor:'#ccc', display:'inline-block'}} />
                )}
                <button className="button-upload-component" style={{marginLeft:30}} onClick={() => this.open()}>
                    Choose file
                </button>
            </div>
        );
    }
}

export default ButtonUpload;
