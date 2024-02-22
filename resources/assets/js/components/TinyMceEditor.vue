<template>
    <textarea class="form-control tinymce">{{ initValue }}</textarea>
</template>

<script>
    

    export default {
        props: [ 'initValue', 'disabled' ],
        mounted: function() {
            const self = this

            tinymce.init({
                selector: 'textarea.tinymce',
                height: 500,
                 themes: "modern",
                init_instance_callback: function (editor) {
                    editor.on('keyup', function (e) {
                    self.content = e.target.innerHTML
                    });
                }
            })
        },
       
    }
</script>