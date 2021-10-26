<div class="app-mixcode" id="mixcode">
    <div class="app-mixcode-translates" id="mixcode-translates">
    <div class="app-mixcode-loader" v-if="loading" role="alert">Loading...</div>
        <h2>Polylang translations</h2>
        <p>Add original English string, then use the given slug in templates</p>
        <div class="app-mixcode-translates-row" v-for="str in strs" :v-key="str.id">
            <div class="app-mixcode-translates-cell has-control">
                <span>
                    <label :for="'mixcode_string_' + str.id">String</label>
                    <input type="text" :id="'mixcode_string_' + str.id" @input.prevent="setValue(str, $event)" :value="str.value" class="app-mixcode-input">
                    <p v-if="str.hasError" class="app-mixcode-alert error">Duplicate value</p>
                </span>
                <button @click.prevent="onCopy(str.value)">Copy</button>
            </div>
            <div class="app-mixcode-translates-cell" hidden>
                <span>
                    <label :for="'mixcode_slug_string_'  + str.id">Slug</label>
                    <input type="text" disabled="" :id="'mixcode_slug_string_'  + str.id" :value="getSlug(str)" class="app-mixcode-input">
                </span>               
            </div>
            <div class="app-mixcode-translates-controls">
                <a href="#" role="button" @click.prevent="onRemove(str)"><i class="dashicons-trash dashicons" aria-label="Remove"></i></a>
            </div>
        </div>
        <div class="app-mixcode-translates-footer">
            <button class="app-mixcode-translates-btn add" :disabled="lastEmpty || lastHasError" @click.prevent="addNew">Add new string <svg class="ungic-icon" role="img" aria-labelledby="un_ay8h904uc" width="2em" height="2em">
                    <title id="un_ay8h904uc">pluc circle m</title>
                    <use xlink:href="#svg-icon-pluc_circle_m"></use>
                </svg></button>
            <button class="app-mixcode-translates-btn submit" @click.prevent="onSave" :disabled="lastHasError || isEmpty">Save</button>
        </div>
    </div>
</div>
<svg class="ungic-svg-sprite" style="display:none">   
    <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentcolor" id="svg-icon-pluc_circle_m">
        <path d="M13 11h4v2h-4v4h-2v-4H7v-2h4V7h2v4z"></path>
        <path fill-rule="evenodd" clip-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12zm10-8a8 8 0 1 0 0 16 8 8 0 0 0 0-16z"></path>
    </symbol>    
</svg>