import '../css/customberg.css';

const { blocks } = window.Laraberg.wordpress;
blocks.setCategories([...(blocks.getCategories() || []), { title: 'Customberg', slug: 'customberg' }]);

import './CustombergEditFields';

import './CustombergUploadAction';
