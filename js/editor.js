// First, make sure to include Monaco Editor scripts in your HTML:
// <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs/loader.min.js"></script>

// Configure AMD loader for Monaco
require.config({
    paths: {
        'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.36.1/min/vs'
    }
});

// Initialize Monaco Editor
require(['vs/editor/editor.main'], function() {
    // Create container for the editor
    const editorContainer = document.getElementById('editor-container');
    
    // Initialize the editor
    const editor = monaco.editor.create(editorContainer, {
        value: '// Start coding here...\n',
        language: 'javascript', // Default language
        theme: 'vs-dark', // Dark theme
        automaticLayout: true,
        minimap: { enabled: true },
        fontSize: 14,
        lineNumbers: 'on',
        scrollBeyondLastLine: false,
        roundedSelection: false,
        wordWrap: 'on'
    });

    // Language selector
    function changeLanguage(language) {
        monaco.editor.setModelLanguage(editor.getModel(), language);
    }

    // Run code function
    function runCode() {
        const code = editor.getValue();
        try {
            // For JavaScript
            if (editor.getModel().getLanguageId() === 'javascript') {
                eval(code);
            }
            // Add handlers for other languages here
        } catch (error) {
            console.error('Execution error:', error);
        }
    }

    // Add event listeners for window resize
    window.addEventListener('resize', () => {
        editor.layout();
    });
});