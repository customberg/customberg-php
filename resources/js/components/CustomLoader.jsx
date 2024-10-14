import React from 'react';

const CustomLoader = (props) => {
    const [Component, setComponent] = React.useState(null);
    React.useEffect(() => {
        try {
            const dynamicComponentFunction = new Function('React', `
                return function(props) {
                    ${props.js}
                };
            `);
            const DynamicComponent = dynamicComponentFunction(React);
            if (typeof DynamicComponent === 'function') {
                setComponent(() => DynamicComponent);
            } else {
                throw new Error('Component is not valid');
            }
        } catch (error) {
            console.error('Error creating component:', error);
        }
    }, [props.js, props.props.activeLang]);

    return Component ? <Component {...props.props} /> : <div>Loading...</div>;
};

export default CustomLoader;
