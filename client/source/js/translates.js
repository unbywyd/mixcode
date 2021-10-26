import Vue from 'vue/dist/vue.min.js';
const axios = require('axios');

function copyToClipboard(textToCopy) {
    // navigator clipboard api needs a secure context (https)
    if (navigator.clipboard && window.isSecureContext) {
        // navigator clipboard api method'
        return navigator.clipboard.writeText(textToCopy);
    } else {
        // text area method
        let textArea = document.createElement("textarea");
        textArea.value = textToCopy;
        // make the textarea out of viewport
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        textArea.style.top = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        return new Promise((res, rej) => {
            // here the magic happens
            document.execCommand('copy') ? res() : rej();
            textArea.remove();
        });
    }
}

let api = function(data) {
    let form_data = new FormData;
    for(let key in data) {
        form_data.append(key, data[key]);
    }
    return axios.post(wtp_mixcode.ajax_url, form_data);
}

document.querySelector('#mixcode').parentNode.setAttribute('dir', 'ltr');

new Vue({
    data: {
        strings: [],
        lastHasError: null,
        loading: true
    },
    beforeMount() {
        api({
            action: 'mixcode_get_translations'
        }).then(e => {
            if(e.data) {
                let data = JSON.parse(decodeURIComponent(e.data));
                this.strings = data;
            }
            this.loading = false;
        }).catch(e => {
            alert(e.message);
        });
    },
    computed: {
        strs() {
            let strs = this.strings.map(e => {
                e.id = this.strings.indexOf(e);
                return e;
            });
         
            return strs;
        }, 
        lastEmpty() {
            if(!this.strings.length) {
                return false
            }
            let last = this.strings[this.strings.length - 1];
            if(last.value && last.value.trim() != '') {
                return false
            }
            return true
        },
        isEmpty() {
            let prev = this.strings.find(e => e.value.trim() != '');
            return !prev
        }       
    },
    methods: {
        onRemove(e) {
            this.strings.splice(this.strings.indexOf(e), 1)
        },
        getSlug(str) {
            if(str.slug && str.slug.trim() != '') {
                return str.slug;
            }
            return str.value.replace(/[^a-zA-Z0-9]+/gmi, '_').toLowerCase();
        },
        addNew() {
            this.strings.push({
                value: ''
            });
        },
        setValue(i, $event) {
            let prev = this.strings.find(e => e.value.toLowerCase() ==  $event.target.value.toLowerCase() && i != e);
            if(prev) {
                this.lastHasError = true;
                i.hasError = true
            } else {
                this.lastHasError = false
                i.hasError = false
            }
           i.value = $event.target.value;
        },
        onCopy(slug) {
            copyToClipboard(slug).then(e => {
                alert('Successfully copied');
            });
        },
        onSave() {
            this.loading = true;
            api({
                action: 'mixcode_save_translations',
                data: encodeURIComponent(JSON.stringify(this.strings))
            }).then(e => {       
                alert('Successfully saved!');        
                this.loading = false;
            }).catch(e => {
                alert(e.message);
            });
        }
    }
}).$mount('#mixcode-translates');

