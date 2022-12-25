
window.CustombergUploadAction = async (files) => {
    const fd = new FormData();
    const csrf = document.querySelector('meta[name=csrf-token]').getAttribute('content');
    if (csrf) fd.append('_token', csrf);

    for (var i = 0; i < files.length; i++) {
        fd.append('files[]', files[i]);
    }
    const resp = await fetch(window.CustombergConfig.routes_file_upload, {
        method: 'POST',
        body: fd,
    });
    const json = await resp.json();
    return json;
};
