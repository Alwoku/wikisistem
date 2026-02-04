<!-- редактор для статей -->
<template>
    <div>
        <div v-if="edit">

        <v-md-editor
            v-model="content"
            @change="updateText"
            height="300px" 
            mode="edit"
            :include-level="[1,2,3,4,5]"
            :toc-nav-position-right="true"
            :default-show-toc="defaultShowToc"
            placeholder="Введите текст"
            right-toolbar="toc fullscreen"
            :codemirror-config="{
                lineNumbers: false,
                mode: 'xml',
            }"
            ></v-md-editor>
        </div>

        <div class="block" v-else>

            <v-md-editor
                mode="preview"
                height="300px" 
                v-model="content"></v-md-editor>
        </div>
    </div>
</template>
<script>

import VMdEditor from '@kangc/v-md-editor/lib/codemirror-editor';
import '@kangc/v-md-editor/lib/style/codemirror-editor.css';
import githubTheme from '@kangc/v-md-editor/lib/theme/github.js';
import '@kangc/v-md-editor/lib/theme/style/github.css';
import enUS from '@kangc/v-md-editor/lib/lang/ru-RU';
import createHljsTheme from '@kangc/v-md-editor/lib/theme/hljs';

import Prism from 'prismjs';
import hljs from 'highlight.js';

import createCopyCodePlugin from '@kangc/v-md-editor/lib/plugins/copy-code/index';
import '@kangc/v-md-editor/lib/plugins/copy-code/copy-code.css';


// Resources for the codemirror editor
import Codemirror from 'codemirror';
// mode
import 'codemirror/mode/markdown/markdown';
import 'codemirror/mode/javascript/javascript';
import 'codemirror/mode/css/css';
import 'codemirror/mode/htmlmixed/htmlmixed';
import 'codemirror/mode/vue/vue';
// edit
import 'codemirror/addon/edit/closebrackets';
import 'codemirror/addon/edit/closetag';
import 'codemirror/addon/edit/matchbrackets';
// placeholder
import 'codemirror/addon/display/placeholder';

// scrollbar
import 'codemirror/addon/scroll/simplescrollbars';
import 'codemirror/addon/scroll/simplescrollbars.css';
// style
import 'codemirror/lib/codemirror.css';

VMdEditor.Codemirror = Codemirror;

VMdEditor.use(githubTheme, {
    Hljs: hljs,
    Prism,
    codeHighlightExtensionMap: {
        vue: 'js',
        php: 'js',
        c: 'js',
        bash: 'xml'
    },
});
VMdEditor.lang.use('ru-RU', enUS);

export default {
    components:{ VMdEditor },

    props:{
        /**
         * текст 
         */
        text:{
            type:String,
            required: true
        },
        /**
         * значение переключения режимов перактирования 
         */
        edit:{
            type:Boolean,
            required:true,
            default:true
        },

        defaultShowToc:{
            type:Boolean,
            required:false,
            default:true,
        }
    },
    data() {
        return {
            content: this.text
        }
    },

    watch:{
        text: function (val){
            if (val == this.content) {
                return;
            }
            this.content = this.text
        }
    },

    emits:["update:text"],
    created() {
        
    },
    methods: {
        updateText(value){
            this.$emit('update:text', value)
        }
    },
}
</script>