import os
import re

files = [
    r'c:\laragon\www\gentix-apps\resources\views\superadmin\events\form.blade.php',
    r'c:\laragon\www\gentix-apps\resources\views\organizer\events\create.blade.php',
    r'c:\laragon\www\gentix-apps\resources\views\organizer\events\edit.blade.php',
    r'c:\laragon\www\gentix-apps\resources\views\organizer\settings\terms.blade.php'
]

replacement = r'''<!-- Quill CSS & JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editorEl = document.getElementById('terms_editor');
        var inputEl = document.getElementById('terms_conditions_input');
        if (!editorEl || !inputEl) return;

        var quill = new Quill(editorEl, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }]
                ]
            },
            placeholder: 'Tuliskan S&K...'
        });

        if (quill.root.innerHTML === '<p><br></p>' && inputEl.value) {
            quill.root.innerHTML = inputEl.value;
        }

        quill.on('text-change', function() {
            inputEl.value = quill.root.innerHTML;
        });

        var form = inputEl.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                inputEl.value = quill.root.innerHTML;
            });
        }

        // Style adjustments
        var toolbar = editorEl.previousElementSibling;
        if (toolbar && toolbar.classList.contains('ql-toolbar')) {
            toolbar.style.border = 'none';
            toolbar.style.borderBottom = '1px solid #f3f4f6';
        }
    });
</script>'''

for filepath in files:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Find the <script> block that starts with (function() { and ends with })(); </script>
    pattern = r'<script>\s*\(function\(\)\s*\{\s*function loadAsset.*?\}\)\(\);\s*</script>'
    
    new_content, count = re.subn(pattern, replacement, content, flags=re.DOTALL)
    
    if count > 0:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f'Updated {filepath}')
    else:
        print(f'Pattern not found in {filepath}')
