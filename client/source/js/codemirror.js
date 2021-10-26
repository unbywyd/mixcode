import CodeMirror from "codemirror/lib/codemirror.js";
import "codemirror/lib/codemirror.css";

import "codemirror/addon/edit/closebrackets.js";
import "codemirror/addon/edit/closetag.js";
import "codemirror/addon/edit/matchbrackets.js";
import "codemirror/addon/edit/matchtags.js";
import "codemirror/mode/htmlmixed/htmlmixed.js";
import "codemirror/mode/css/css.js";
import "codemirror/mode/javascript/javascript.js";
import "codemirror/mode/twig/twig.js";
import "codemirror/theme/dracula.css";

import emmet from '@emmetio/codemirror-plugin';
emmet(CodeMirror);

document.querySelectorAll('.mixcode_codemirror').forEach(el => {
   CodeMirror.fromTextArea(el, {
        lineNumbers: true,
        theme: 'dracula',
        tabSize: 1,
        autoCloseBrackets: true,
        matchBrackets: true,
        matchTags: {bothTags: true},
        autoCloseTags: true,
        direction: 'ltr',        
        mode: el.getAttribute('data-mode'),
        extraKeys: {
            'Tab': 'emmetExpandAbbreviation',
            'Esc': 'emmetResetAbbreviation',
            'Enter': 'emmetInsertLineBreak'
        }
    });
});
let style = document.createElement('style');
style.type = 'text/css';
let css = '.CodeMirror { background-color: #282A36 !important;}';
document.querySelector('head').appendChild(style);

if (style.styleSheet) {
    // This is required for IE8 and below.
    style.styleSheet.cssText = css;
} else {
style.appendChild(document.createTextNode(css));
}
