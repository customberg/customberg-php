
window.CustombergUploadAction = async (files, options = {}) => {
    const fd = new FormData();
    const csrf = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    if (csrf) fd.append('_token', csrf);

    for (var i = 0; i < files.length; i++) {
        fd.append('files[]', files[i]);
    }
    if (typeof options.data != 'undefined') {
        Object.entries(options.data ? options.data : {}).forEach(function (item) {
            fd.append(item[0], item[1]);
        });
    }
    const resp = await fetch(window.CustombergConfig.routes_file_upload, {
        method: 'POST',
        body: fd,
    });
    const json = await resp.json();
    return json;
};
