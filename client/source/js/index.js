import Vue from 'vue/dist/vue.min.js';
import Autocomplete from '@trevoreyre/autocomplete-vue';
import '@trevoreyre/autocomplete-vue/dist/style.css'

import JsonViewer from 'vue-json-viewer'
Vue.use(Autocomplete);
Vue.use(JsonViewer);
const axios = require('axios');

let api = function(data) {
    let form_data = new FormData;
    for(let key in data) {
        form_data.append(key, data[key]);
    }
    return axios.post(wtp_mixcode.ajax_url, form_data);
}

document.querySelector('#mixcode').parentNode.setAttribute('dir', 'ltr');
let app = new Vue({
    beforeMount() {
       api({
           action: 'mixcode_get_first_data'
       }).then(e => {
           let data = e.data;
           this.templates = data.templates;           
           let postTypes = [];
           for(let postType in data.post_types) {
            postTypes.push(postType);
           }
           this.postTypes = postTypes;
           this.loading = false;
       }).catch(e => {
           alert(e.message);
       });
    },
    computed: {
        step3Valid() {
            return this.dataKey
        },
        step2Valid() {
            return this.postType && this.postId
        },
        step1Valid() {
            return this.templateId
        },
        step4Valid() {
            return  this.step3Valid && this.step2Valid && this.step1Valid
        },
        dataKeys() {
            let names = [];
            for(let key in this.jsonData) {
                names.push(key);
            }
            return names;
        },
        postsTitles() {
            let titles = [];
            for(let post of this.posts) {
                titles.push( post.post_title + ':' + post.ID);
            }
            return titles
        },
        templateTitles() {
            let titles = [];
            for(let post of this.templates) {
                titles.push( post.post_title + ':' + post.ID);
            }
            return titles
        }
    },
    data() {
        return {
            activeStep: 1,
            postType: null,
            loading: true,
            templates: [],
            postId: null,
            postsLoaded: false,
            postSelected: null,
            templateSelected: null,
            dataKey: null,
            templateId: null,
            posts: [],
            postTypes: [],
            jsonData: {}
        }
    },
    watch: {
        postType(s) {
            this.postId = null;
            this.postSelected = null;
            this.postsLoaded = false;
            api({
                action: 'mixcode_get_posts',
                post_type: s
            }).then(e => {
                let data = e.data;
                this.posts = data.posts;
                this.postsLoaded = true;
            }).catch(e => {
                alert(e.message);
            })
        },
        activeStep(n) {
            if(n==3 && this.postType && this.postId) {
                this.loading = true;
                api({
                    action: 'mixcode_get_acf_fields',
                    post_id: this.postId
                }).then(e => {
                    let data = e.data;
                    this.jsonData = data;
                    this.loading = false;
                }).catch(e => {
                    alert(e.message);
                })
            }
        },
        templateId(s) {
            if(s) {
                this.activeStep = 2;
            }
        },
        postId(s) {
            if(s && this.postType) {
                this.activeStep = 3
            }
        },
        dataKey(s) {
            if(s && this.step4Valid) {
                this.activeStep = 4;
            }
        }
    },
    methods: {
        onResetCache() {
            this.loading = true;
            api({
                action: 'mixcode_remove_cache',
            }).then(e => {
                alert('The cache has been successfully removed!');
                this.loading = false;
            }).catch(e => {
                alert(e.message);
            })
        },
        clearTemplateId() {
            this.templateId = null;
            this.templateSelected = null;
        },
        clearPostId() {
            this.postId = null;
            this.postSelected = null;
        },
        setTemplate() {
            let selectTemplate = this.$refs['select_template'];
            this.templateId = selectTemplate.value;
        },
        setPost(result) {
            let data = result.split(':');
            let post_id = data[data.length - 1];
            this.postId = post_id;
            this.postSelected = result;
        },
        setTemplate(result) {
            let data = result.split(':');
            let post_id = data[data.length - 1];
            this.templateId = post_id;
            this.templateSelected = result;
        },
        setPostType() {
            let select = this.$refs['post_type'];
            this.postType = select.value;
        },     
        searchTemplate(input) {
            if (input.length < 1) { return [] }
            return this.templateTitles.filter(post => {
                return post.toLowerCase().indexOf(input.toLowerCase()) != -1;
            });            
        },
        searchPosts(input) {
            if (input.length < 1) { return [] }
            return this.postsTitles.filter(post => {
                return post.toLowerCase().indexOf(input.toLowerCase()) != -1;
            });
          },
          onStep2() {
            if(this.step1Valid) {
                this.activeStep = 2;
            }
          },
          onStep3() {
            if(this.step2Valid && this.step1Valid) {
                this.activeStep = 3;
            }
          },
          onGenerator() {
            if(this.step4Valid) {
                this.activeStep = 4;
            }
          },
          setDataKey() {
              let select = this.$refs['data_key'];
              this.dataKey = select.value;
          }
    }
}).$mount('#mixcode')