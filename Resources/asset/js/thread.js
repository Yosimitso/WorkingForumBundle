jQuery(document).ready(function () {

    /**
     * Initialize the post editor
     */
    jQuery(".wf_textarea_post").markdown({
        language: storeJs.locale,
        onPreview: function (e) {
            var converter = new Showdown.converter({extensions: ['twitter', 'github']});
            var html = converter.makeHtml(nl2br(e.getContent()), storeJs.assetPath);
            return html;
        },
        fullscreen: {enable: false}
    });

    /**
     * A modo/admin move the thread
     */
    jQuery('#wf_move_thread_button').click(function () {
        if (!jQuery('#wf_move_thread_button').hasClass('confirm')) {
            jQuery('#move_thread_forum').show();
            jQuery('#wf_move_thread_button').html(storeJs.trans['forum.confirm_move_thread']);
            jQuery('#wf_move_thread_button').addClass('confirm');
        }
        else {
            var target = jQuery('#move_thread_forum').val();
            if (!target) {
                alert('Error: subforum id is empty');
                return false;
            }


            jQuery.ajax({
                type: "POST",
                url: storeJs.routes.workingforum_move_thread,
                crossDomain: false,
                data: 'threadId=' + storeJs.threadId + '&target=' + target,
                dataType: 'json',
                async: false,
                success: function (res) {
                    if (res.res == 'true') {
                        alert(storeJs.trans['forum.move_thread_success']);
                        jQuery('#wf_move_thread_button').addClass('wf_button-grey').html(storeJs.trans["forum.thread_was_moved_to"] + ' ' + res.targetLabel);
                        jQuery('#move_thread_forum').hide();
                    }
                    else {
                        alert('An error occured');
                        return false;
                    }
                }
            });
        }
    });

    /**
     * Clear post editor content draft
     */
    jQuery('#wf_form_post').submit(function (e) {
        if (getCookie('post_editor_' + storeJs.threadId)) {
            e.preventDefault();
            clearInterval(saveTimeout);
            eraseCookie('post_editor_' + storeJs.threadId);
            this.submit();
        }
    });

    /**
     * Get a cookie by its name
     * @param name
     * @return string
     */
    getCookie = function (name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    setTimeout(function () {
        getSavedPostEditor()
    }, 1000)

    /**
     * Get the post draft saved for this thread
     */
    getSavedPostEditor = function () {
        var postEditor = document.getElementsByClassName('wf_textarea_post')[0];
        var threadId = document.getElementsByClassName('wf_thread')[0].getAttribute('data-id');
        var postSaved = getCookie('post_editor_' + threadId);
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
    setCookie = function (name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    /**
     * Erase a cookie
     * @param name
     */
    eraseCookie = function (name) {
        var d = new Date(); //Create an date object
        d.setTime(d.getTime() - (1000*60*60*24)); //Set the time to the past. 1000 milliseonds = 1 second
        var expires = "expires=" + d.toGMTString(); //Compose the expirartion date
        window.document.cookie = name+"="+"; "+expires+"; path=/";//Set the cookie with name and the expiration date
    }

    var saveTimeout = setInterval(function () {
        savePostEditor()
    }, 30000);

    /**
     * Save the post editor content as draft
     */
    savePostEditor = function () {
        var postEditor = document.getElementsByClassName('wf_textarea_post')[0];

        if (!postEditor || !postEditor.value) {
            return;
        }
        setCookie('post_editor_' + storeJs.threadId, postEditor.value, 30);
        var dateSaved = new Date();
        if (jQuery('#saved_draft_msg').length) {
            jQuery('#saved_draft_msg').html(storeJs.trans['message.post_saved_draft'] + ' ' + dateSaved.getHours() + ':' + (dateSaved.getMinutes()<10?'0':'')+dateSaved.getMinutes());
        } else {
            jQuery('.md-header').after('<div id="saved_draft_msg" class="wf_small_message">' + storeJs.trans['message.post_saved_draft'] + ' ' + dateSaved.getHours() + ':' + (dateSaved.getMinutes()<10?'0':'') + dateSaved.getMinutes() + '</div>');
        }
    }

    /**
     * nl2br function missing in js
     * @param {string} str
     * @param {bool} is_xhtml
     */
    nl2br = function (str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }

    /**
     * Quote a message into the post editor
     * @param {int} postId
     */
    quote = function (postId) {
        jQuery('.wf_textarea_post').val(jQuery('.wf_textarea_post').val() + "[quote=" + postId + "]");
        jQuery('.wf_textarea_post').focus();
    }

    /**
     * Report a post
     * @param {string} url
     */
    report = function (url) {
        if (!confirm(storeJs.trans['forum.confirm_report'])) {
            return false;
        }
        jQuery.ajax({
            type: "GET",
            url: url,
            crossDomain: false,
            dataType: 'json',
            async: false,
            success: function (verif) {
                if (verif == 'true') {
                    alert(storeJs.trans['forum.thanks_reporting']);
                }
                else {
                    alert(storeJs.trans['message.error.something_wrong']);
                }
            }
        });
    }

    /**
     * Moderate (censor content) of a post (modo/admin)
     * @param {int} id
     */
    moderate = function (id) {
        var reason = prompt(storeJs.trans['admin.report.why']);
        if (reason != null && reason.trim() != '') {
            jQuery.ajax({
                type: "POST",
                url: storeJs.routes.workingforum_admin_report_action_moderate,
                crossDomain: false,
                data: 'reason=' + reason + '&postId=' + id,
                dataType: 'json',
                async: false,
                success: function (res) {
                    if (res == 'ok') {
                        jQuery('#wf_post\\[' + id+'\\] .wf_post_content').html('<p class="wf_moderate">' + storeJs.trans['forum.post_moderated'] + ' ' + reason + '</p>');
                    }
                }
            });
        } else if (reason != null) {
            alert(storeJs.trans['admin.report.invalid_reason']);
            return;
        }
    }

    /**
     * Positive vote for a post
     * @param {int} id
     * @param {HTMLObjectElement} element
     */
    voteUp = function (id, element) {
        jQuery.ajax({
            type: "POST",
            url: storeJs.routes.workingforum_vote_up,
            crossDomain: false,
            data: 'postId=' + id,
            dataType: 'json',
            async: false,
            success: function (content) {
                if (content.res == 'true') {
                    let img = jQuery(element).html();
                    jQuery(element).remove();
                    jQuery('#voteUpLabel_' + id).html(img + '+ ' + content.voteUp);
                }
                else {
                    alert('Sorry an error occured : ' + content.errMsg);
                }
            }
        });
    }

    /**
     * Unfold the block with enclosed file
     * @param {HTMLObjectElement} arrow
     * @param {int} id
     */

    showEnclosed = function (arrow, id) {
        jQuery('#wf_enclosed_files_' + id).slideDown();
        jQuery(arrow).remove();
    }

    /**
     * Subscribe on new message
     */
    addSubscription = function () {
        jQuery.ajax({
            type: "GET",
            url: storeJs.routes.workingforum_add_subscription,
            crossDomain: false,
            dataType: 'json',
            async: false,
            success: function () {
                jQuery('#wf_add_subscription').html(storeJs.trans['forum.already_subscribed']).addClass('wf_button-grey');
            },
            error: function () {
                alert(storeJs.trans['message.generic_error']);
            }
        });
    }

    /**
     * Cancel subscription on new message
     */
    cancelSubscription = function () {
        jQuery.ajax({
            type: "GET",
            url: storeJs.routes.workingforum_cancel_subscription,
            crossDomain: false,
            dataType: 'json',
            async: false,
            success: function () {
                jQuery('#cancel_subscription').html(storeJs.trans['message.subscription_cancelled']);
            },
            error: function () {
                alert(storeJs.trans['message.generic_error']);
            }
        });
    }
});
