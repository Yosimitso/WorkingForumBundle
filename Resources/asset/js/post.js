
jQuery(document).ready(() => {
    /**
     * Initialize the post editor
     */
    jQuery('.wf_textarea_post').markdown({
        language: storeJs.locale,
        onPreview: (e) => {
            const converter = new Showdown.converter({extensions: ['twitter', 'github']});
            const html = converter.makeHtml(nl2br(e.getContent()), storeJs.assetPath);

            return html;
        },
        fullscreen: {enable: false}
    });


    /**
     * Clear post editor content draft
     */
    jQuery('#wf_form_post').submit((e) => {
        if (getCookie(`post_editor_${storeJs.postEditorId}`)) {
            e.preventDefault();
            clearInterval(saveTimeout);
            eraseCookie(`post_editor_${storeJs.postEditorId}`);
            e.target.submit();
        }
    });

    /**
     * nl2br function missing in js
     * @param {string} str
     */
    nl2br = (str) =>  {
        const breakTag = '<br />';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, `$1${breakTag}$2`);
    }

    /**
     * Get a cookie by its name
     * @param name
     * @return string
     */
    getCookie = (name) => {
        const nameEQ = `${name}=`;
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    /**
     * Timer to save post editor content
     */
    setTimeout(() => {
        getSavedPostEditor()
    }, 1000)

    /**
     * Get the post draft saved for this thread
     */
    getSavedPostEditor = () => {
        const postEditor = document.getElementsByClassName('wf_textarea_post')[0],
            threadId = document.getElementsByClassName('wf_thread')[0].getAttribute('data-id'),
            postSaved = getCookie(`post_editor_${threadId}`);

        if (postSaved) {
            postEditor.value = postSaved;
        }
    }

    /**
     * Set a cookie
     * @param name
     * @param value
     * @param days
     */
    setCookie = (name, value, days) => {
        let expires = '';
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = `; expires=${date.toUTCString()}`;
        }
        document.cookie = `${name}=${(value || '')}${expires}; path=/`;
    }

    /**
     * Erase a cookie
     * @param name
     */
    eraseCookie = (name) => {
        const d = new Date(); //Create an date object
        d.setTime(d.getTime() - (1000*60*60*24)); //Set the time to the past. 1000 milliseonds = 1 second
        const expires = `expires=${d.toGMTString()}`; //Compose the expiration date
        window.document.cookie = `${name}=; ${expires}; path=/`; //Set the cookie with name and the expiration date
    }

    const saveTimeout = setInterval(() => {
        savePostEditor()
    }, 30000);

    /**
     * Save the post editor content as draft
     */
    savePostEditor = () => {
        const postEditor = document.getElementsByClassName('wf_textarea_post')[0];

        if (!postEditor || !postEditor.value) {
            return;
        }

        setCookie(`post_editor_${storeJs.postEditorId}`, postEditor.value, 30);
        const dateSaved = new Date();

        if (jQuery('#saved_draft_msg').length) {
            const msg = `${storeJs.trans['message.post_saved_draft']} ${dateSaved.getHours()}:${dateSaved.getMinutes()<10?'0':''}${dateSaved.getMinutes()}`;
            jQuery('#saved_draft_msg').html(msg);
        } else {
            const msg = `
                <div id="saved_draft_msg" class="wf_small_message">
                ${storeJs.trans['message.post_saved_draft']} ${dateSaved.getHours()}:${dateSaved.getMinutes()<10?'0':''}${dateSaved.getMinutes()}
                </div>`;
            jQuery('.md-header').after(msg);
        }
    }
});
