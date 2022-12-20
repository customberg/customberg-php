
function CustombergPreviewBlock(props) {
    let previewId = String(Math.round(Math.random() * 10000000000000000));

    let iframeContent = (content) => {
        let loadEl = document.getElementById(previewId + 'load');
        let iframeEl = document.getElementById(previewId);
        if (!iframeEl) return;

        // put preview content in iframe
        loadEl.style.display = 'none';
        iframeEl.contentWindow.document.body.style.overflow = 'hidden';
        iframeEl.contentWindow.document.body.append($(`
            <div>
                ${content}
                <style>
                    img { max-width: 100%; max-height: 100%; height: auto !important; object-fit: contain; }
                    .mask {display:none;}
                    .swiper-slide {margin-bottom:20px;}
                </style>
            </div>
        `).get(0));

        // recalculate iframe height
        let updateIframeHeight = () => {
            iframeEl.style.maxHeight = '6000px';
            if (iframeEl.contentWindow) {
                iframeEl.style.height = iframeEl.contentWindow.document.body.offsetHeight + 'px';
            }
        };
        updateIframeHeight();
        setTimeout(updateIframeHeight, 500);
        setTimeout(updateIframeHeight, 1000);
        iframeEl.contentWindow.onload = updateIframeHeight;
    };

    // process preview blade view on componentdidmount
    $.ajax({
        method: 'POST',
        url: '/admin/touch-media-gutenberg/preview',
        data: JSON.stringify({
            slug: props.block.slug,
            attributes: props.attributes || [],
        }),
        contentType: 'application/json; charset=UTF-8',
        success: (result) => {
            iframeContent(result);
        },
    });

    return (
        <div>
            <div
                id={previewId + 'load'}
                style={{
                    width: '100%', height: 200, background: '#fafafa', border: '1px solid #e4e4e4',
                    display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
                }}
            >
                <div className="customberg-bt-spinner" />
            </div>
            <iframe
                id={previewId}
                style={{ width: '100%', height: 200, background: 'transparent', pointerEvents: 'none' }}
            ></iframe>
        </div>
    );
}

export default CustombergPreviewBlock;
