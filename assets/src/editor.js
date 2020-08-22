__webpack_public_path__ = wordpress.plugins_url + 'assets/dist/'
import * as monaco from 'monaco-editor/esm/vs/editor/editor.api.js'
import * as babel from '@babel/standalone'
import uglify from 'uglifyjs-browser'

class Editor {
    constructor() {
        this.vars()
        this.init()
    }

    vars() {
        this.assetPath = wordpress.plugins_url
        this.ajaxUrl = wordpress.ajax_url
        window.error = null
    }

    init() {
        this.editor()
        this.loadJS()
        this.loadTheme()
        this.loadMinify()
        this.loadLanguange()
        this.jQueryEditor()
    }

    editor() {
        this.monacoEditor = monaco.editor.create(document.getElementById('monaco-editor'), {
            languange: 'javascript'
        })

        this.monacoEditor.onKeyUp(() => {
            if (window.error) {
                Editor.setError()
            }
            
            if (window.isSaved == true) {
                jQuery('.save-button').text('Save')
                window.isSaved =false    
            }
        })
    }

    compileJS() {
        jQuery('.save-button').addClass('is-busy').text('Saving...')
        
        let unsavedJS = this.monacoEditor.getValue()
        let result = unsavedJS

        try {
            result = babel.transform(result, { presets: ['env'] }).code
            
            if ( this.ifMinify === 'on' ) result = uglify.minify(result).code
            
            this.saveJS(unsavedJS, result)
        } catch (error) {
            Editor.setError(error)
        }
    }

    saveJS(rawJS, compiledJS) {
        let data = {
            'action': 'ijs_save_js',
            'js': rawJS,
            'compiledjs': compiledJS
        }

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                console.log(response)
                jQuery('.save-button').removeClass('is-busy').text('Saved!')
                window.isSaved = true
            }
        })
    }

    loadJS() {
        let data = {
            'action': 'ijs_get_js'
        }

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                this.monacoEditor.setValue(response);
            }
        })
    }

    loadTheme() {
        let data = {
            'action': 'ijs_get_theme'
        }

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                monaco.editor.setTheme(response)
                jQuery('#selectTheme').val(response)
            }
        })
    }

    changeTheme(theme) {
        monaco.editor.setTheme(theme)

        let data = {
            'action': 'ijs_save_theme',
            'theme': theme
        }

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                console.log(response)
            }
        })
    }

    changeMinify(value) {
        this.ifMinify = value

        let data = {
            'action': 'ijs_save_minify',
            'minify': value
        }

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                console.log(response)
            }
        })
    }

    loadMinify() {
        let data = {
            'action': 'ijs_get_minify'
        }

        jQuery.ajax({
            url: this.ajaxUrl,
            type: 'post',
            data: data,
            success: (response) => {
                console.log(response)
                this.ifMinify = response
                jQuery('#selectMinify').val(response)
            }
        })
    }

    loadLanguange() {
        monaco.editor.setModelLanguage(this.monacoEditor.getModel() ,'javascript');
    }

    static setError(error, line) {
        window.error = error

        if (error) {
            if (line) {
                error = `Line ${line}: ${error}`
            }
            jQuery('.ijs-error-block').fadeIn()
            jQuery('.save-button').text('Oops!').addClass('button-danger').removeClass('is-busy')
            jQuery('.ijs-error-container').text(error)
        } else {
            window.error = null
            jQuery('.ijs-error-block').fadeOut()
            jQuery('.save-button').text('Save!').removeClass('button-danger').removeClass('is-busy')
        }
    }

    jQueryEditor() {
        let self = this
        
        jQuery(window).bind('keydown', function(event) {
            if (event.ctrlKey || event.metaKey) {
                switch (String.fromCharCode(event.which).toLowerCase()) {
                case 's':
                    event.preventDefault()
                    self.compileJS()
                    break
                }
            }
        })
        jQuery('.save-button').click( function() {
            self.compileJS() 
        })
        jQuery('#selectTheme').change( function() { 
            self.changeTheme(jQuery(this).val()) 
        })
        jQuery('#selectMinify').change( function() {
            self.changeMinify(jQuery(this).val())
        })
    }
}

new Editor()
