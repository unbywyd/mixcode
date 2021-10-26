
<div class="app-mixcode" id="mixcode">
    <div class="app-mixcode-loader" v-if="loading" role="alert">Loading...</div>
    <div class="app-mixcode-header">
        <h2 class="app-mixcode-heading">ACF Mixcode Generator</h2>
        <div class="app-mixcode-desc">
            <p>Fill out just 3 steps and get a shortcode that you can use anywhere</p>
        </div>
        <div class="app-mixcode-stepper">
            <div class="app-mixcode-stepper-step" :class="{'disabled': !step1Valid && activeStep != 1, 'completed': step1Valid}">
                <h3 class="app-mixcode-stepper-step-heading">
                    <button :aria-expanded="activeStep == 1" aria-controls="mixcode_step_region_1" @click.prevent="activeStep = 1" id="mixcode_step_btn_1" :disable="activeStep == 1" :aria-disabled="activeStep == 1">
                        <span><span class="app-mixcode-stepper-step-number">1</span> Template selection <span class="app-mixcode-stepper-step-desc">Choose which template to apply to the data</span></span>
                        <svg class="ungic-icon" role="presentation" aria-labelledby="un_im1i1ql5m" width="2em" height="2em">
                            <title id="un_im1i1ql5m">Caret down</title>
                            <use xlink:href="#svg-icon-Caret_down"></use>
                        </svg>
                    </button>
                </h3>
                <div v-if="activeStep == 1" class="app-mixcode-stepper-step-region" id="mixcode_step_region_1" aria-labelledby="mixcode_step_btn_1" role="region">
                    <p v-if="!loading && !templates.length" class="app-mixcode-alert error">
                    No templates
                    </p>
                    <span class="app-mixcode-selected" @click.prevent="clearTemplateId"  tabindex="-1" role="button" aria-label="Reset" v-if="templateSelected">
                        <span v-html="templateSelected"></span>
                        <button @click.prevent="clearTemplateId"><svg class="ungic-icon" role="img" aria-labelledby="un_2cspqyglz" width="2em" height="2em">
                                <title id="un_2cspqyglz">Clear data</title>
                                <use xlink:href="#svg-icon-close_circle"></use>
                            </svg></button>
                    </span>
                    <div v-if="!templateSelected">
                        <label for="template_id">Template</label>
                        <autocomplete id="template_id" :disabled="!loading && !templates.length" @submit="setTemplate" :defaultvalue="templateSelected" :search="searchTemplate" aria-label="Search template by title" placeholder="Search template by title"></autocomplete>              
                    </div>
                </div>
            </div>
            <div class="app-mixcode-stepper-step" :class="{'disabled': !step2Valid && activeStep != 2, 'completed': step2Valid}">
                <h3 class="app-mixcode-stepper-step-heading">
                    <button :aria-expanded="activeStep == 2" aria-controls="mixcode_step_region_2" id="mixcode_step_btn_2" @click.prevent="onStep2" :disabled="activeStep == 2" :aria-disabled="activeStep == 2">
                        <span><span class="app-mixcode-stepper-step-number">2</span> Selecting a data document <span class="app-mixcode-stepper-step-desc">Please select your post with ACF fields</span></span>
                        <svg class="ungic-icon" role="presentation" aria-labelledby="un_zplc9pqlv" width="2em" height="2em">
                            <title id="un_zplc9pqlv">Caret down</title>
                            <use xlink:href="#svg-icon-Caret_down"></use>
                        </svg>
                    </button>
                </h3>
                <div v-if="activeStep == 2" class="app-mixcode-stepper-step-region" id="mixcode_step_region_2" aria-labelledby="mixcode_step_btn_2" role="region">
                    <div class="app-mixcode-form">
                        <div>
                            <label for="post_type">Post type</label>
                            <select id="post_type" name="post_type" class="app-mixcode-select" @change="setPostType" ref="post_type">
                                <option value="" :selected="!postType" disabled="">Post type</option>
                                <option :value="post" :selected="postType == post" v-for="post of postTypes" :v-key="post" v-html="post"></option>
                            </select>
                        </div>
                        <br>
                        <div>
                            <p v-if="postType && !posts.length && postsLoaded" class="app-mixcode-alert error">
                            This post type does not contain any posts.
                            </p>
                            <span class="app-mixcode-selected" tabindex="-1" role="button" aria-label="Reset" @click.prevent="clearPostId" v-if="postSelected">
                                <span v-html="postSelected"></span>
                                <button @click.prevent="clearPostId"><svg class="ungic-icon" role="img" aria-labelledby="un_jhd7ae66e" width="2em" height="2em">
                                        <title id="un_jhd7ae66e">Clear datat</title>
                                        <use xlink:href="#svg-icon-close_circle"></use>
                                    </svg></button>
                            </span>
                            <div v-if="!postSelected">
                                <label for="post_selected">Post</label>
                                <autocomplete id="post_selected" @submit="setPost" :defaultvalue="postSelected" :disabled="!postType && !posts.length" :search="searchPosts" aria-label="Search post by title" placeholder="Search post by title"></autocomplete>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-mixcode-stepper-step" :class="{'disabled': !step3Valid && activeStep != 3, 'completed': step3Valid }">
                <h3 class="app-mixcode-stepper-step-heading">
                    <button :aria-expanded="activeStep == 3" aria-controls="mixcode_step_region_3" id="mixcode_step_btn_3" @click.prevent="onStep3" :disabled="activeStep == 3" :aria-disabled="activeStep == 3">
                        <span><span class="app-mixcode-stepper-step-number">3</span> Data selection <span class="app-mixcode-stepper-step-desc">Choose which fields to include in the template</span></span>
                        <svg class="ungic-icon" role="presentation" aria-labelledby="un_m5nzyebom" width="2em" height="2em">
                            <title id="un_m5nzyebom">Caret down</title>
                            <use xlink:href="#svg-icon-Caret_down"></use>
                        </svg>
                    </button>
                </h3>
                <div v-if="activeStep == 3" class="app-mixcode-stepper-step-region" id="mixcode_step_region_3" aria-labelledby="mixcode_step_btn_3" role="region">   
                    <p v-if="!dataKeys.length" class="app-mixcode-alert error">
                    This post does't contain ACF fields
                    </p>
                    <p v-else>Select the ACF field that will be passed to the template</p>                    
                    <div>
                        <label for="data_key">ACF field</label>
                        <select @change="setDataKey" id="data_key" ref="data_key" class="app-mixcode-select">
                            <option value="" disabled="" :selected="!dataKey">Select</option>
                            <option value="all" v-if="Object.keys(dataKeys).length" :selected="dataKey == 'all'">Pass all ACF fields</option>
                            <option :value="key" :selected="dataKey == key" v-for="key in dataKeys" v-html="key"></option>
                        </select>
                        <br>
                    </div>
                    <json-viewer :value="jsonData" :expand-depth="5" copyable="" boxed="" sort=""></json-viewer>
                </div>
            </div>
            <div class="app-mixcode-stepper-step" :class="{'disabled': activeStep != 4 && !step4Valid }">
                <h3 class="app-mixcode-stepper-step-heading">
                    <button :aria-expanded="activeStep == 4" @click.prevent="onGenerator" aria-controls="mixcode_step_region_4" id="mixcode_step_btn_4" :disabled="activeStep == 4" :aria-disabled="activeStep == 4">
                        <span>Get shortcode <span class="app-mixcode-stepper-step-desc">Get a ready-to-use shortcode</span></span>
                        <svg class="ungic-icon" role="presentation" aria-labelledby="un_xld55pj3d" width="2em" height="2em">
                            <title id="un_xld55pj3d">Caret down</title>
                            <use xlink:href="#svg-icon-Caret_down"></use>
                        </svg>
                    </button>
                </h3>
                <div v-if="activeStep == 4" class="app-mixcode-stepper-step-region" id="mixcode_step_region_4" aria-labelledby="mixcode_step_btn_4" role="region">
                    <div class="app-mixcode-shortcode">[mixcode tid="<span v-html="templateId"></span>" pid="<span v-html="postId"></span>" <span v-if="dataKey != 'all' && dataKey">field="<span v-html="dataKey"></span>"</span>]</div>
                </div>
            </div>
        </div>
        <br>
        <hr>
        <br>
        <div class="app-mixcode-footer">
            <p>All templates are generated into files, the cache is deleted automatically after updating the templates, but if you suddenly need to manually reset the cache, use this button</p>
            <button class="app-mixcode-btn" @click.prevent="onResetCache">Reset template cache</button>
        </div>
    </div>
</div>
<svg class="ungic-svg-sprite" style="display:none">
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentcolor" id="svg-icon-Caret_down">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.53 8.97a.75.75 0 0 1 0 1.06l-4 4a.75.75 0 0 1-1.06 0l-4-4a.75.75 0 1 1 1.06-1.06L12 12.44l3.47-3.47a.75.75 0 0 1 1.06 0z"></path>
    </symbol>
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentcolor" id="svg-icon-Caret_left">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.03 7.47a.75.75 0 0 1 0 1.06L10.56 12l3.47 3.47a.75.75 0 1 1-1.06 1.06l-4-4a.75.75 0 0 1 0-1.06l4-4a.75.75 0 0 1 1.06 0z"></path>
    </symbol>
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentcolor" id="svg-icon-Caret_right">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.97 7.47a.75.75 0 0 1 1.06 0l4 4a.75.75 0 0 1 0 1.06l-4 4a.75.75 0 1 1-1.06-1.06L13.44 12 9.97 8.53a.75.75 0 0 1 0-1.06z"></path>
    </symbol>
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentcolor" id="svg-icon-Caret_up">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.53 14.03a.75.75 0 0 1-1.06 0L12 10.56l-3.47 3.47a.75.75 0 0 1-1.06-1.06l4-4a.75.75 0 0 1 1.06 0l4 4a.75.75 0 0 1 0 1.06z"></path>
    </symbol>
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentcolor" id="svg-icon-Check">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M18.03 7.97a.75.75 0 0 1 0 1.06l-7 7a.75.75 0 0 1-1.06 0l-4-4a.75.75 0 1 1 1.06-1.06l3.47 3.47 6.47-6.47a.75.75 0 0 1 1.06 0z"></path>
    </symbol>
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentcolor" id="svg-icon-close_circle">
        <path d="M12 22.75C6.07 22.75 1.25 17.93 1.25 12S6.07 1.25 12 1.25 22.75 6.07 22.75 12 17.93 22.75 12 22.75zm0-20C6.9 2.75 2.75 6.9 2.75 12S6.9 21.25 12 21.25s9.25-4.15 9.25-9.25S17.1 2.75 12 2.75z"></path>
        <path d="M9.17 15.58c-.19 0-.38-.07-.53-.22a.754.754 0 0 1 0-1.06l5.66-5.66c.29-.29.77-.29 1.06 0 .29.29.29.77 0 1.06L9.7 15.36c-.14.15-.34.22-.53.22z"></path>
        <path d="M14.83 15.58c-.19 0-.38-.07-.53-.22L8.64 9.7a.754.754 0 0 1 0-1.06c.29-.29.77-.29 1.06 0l5.66 5.66c.29.29.29.77 0 1.06-.15.15-.34.22-.53.22z"></path>
    </symbol>
</svg>