<!-- CKeditor -->
<?php
    $field['extra_plugins'] = isset($field['extra_plugins']) ? implode(',', $field['extra_plugins']) : "embed,widget";

    $defaultOptions = [
        "filebrowserBrowseUrl" => backpack_url('elfinder/ckeditor'),
        "extraPlugins" => $field['extra_plugins'],
        "embed_provider" => "//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}",
    ];

    $field['options'] = array_merge($defaultOptions, $field['options'] ?? []);
?>

<?php echo $__env->make('crud::fields.inc.wrapper_start', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <label><?php echo $field['label']; ?></label>
    <?php echo $__env->make('crud::fields.inc.translatable_icon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <textarea
        name="<?php echo e($field['name']); ?>"
        data-init-function="bpFieldInitCKEditorElement"
        data-options="<?php echo e(trim(json_encode($field['options']))); ?>"
        <?php echo $__env->make('crud::fields.inc.attributes', ['default_class' => 'form-control'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    	><?php echo e(old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? ''); ?></textarea>

    
    <?php if(isset($field['hint'])): ?>
        <p class="help-block"><?php echo $field['hint']; ?></p>
    <?php endif; ?>
<?php echo $__env->make('crud::fields.inc.wrapper_end', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>





<?php if($crud->fieldTypeNotLoaded($field)): ?>
    <?php
        $crud->markFieldTypeAsLoaded($field);
    ?>

    
    <?php $__env->startPush('crud_fields_scripts'); ?>
        <script src="<?php echo e(asset('packages/ckeditor/ckeditor.js')); ?>"></script>
        <script src="<?php echo e(asset('packages/ckeditor/adapters/jquery.js')); ?>"></script>
        <script>
            function bpFieldInitCKEditorElement(element) {


                //when removing ckeditor field from page html the instance is not properly deleted.
                //this event is triggered in repeatable on deletion so this field can intercept it
                //and properly delete the instances so it don't throw errors of unexistent elements in page that has initialized ck instances.
                element.on('backpack_field.deleted', function(e) {
                    $ck_instance_name = element.siblings("[id^='cke_editor']").attr('id');

                    //if the instance name starts with cke_ it was an auto-generated name from ckeditor
                    //that happens because in repeatable we stripe the field names used by ckeditor, so it renders a random name
                    //that starts with cke_
                    if($ck_instance_name.startsWith('cke_')) {
                        $ck_instance_name = $ck_instance_name.substr(4);
                    }
                    //we fully destroy the instance when element is deleted from the page.
                    CKEDITOR.instances[$ck_instance_name].destroy(true);
                });
                // trigger a new CKEditor
                element.ckeditor(element.data('options'));
            }
        </script>
    <?php $__env->stopPush(); ?>

<?php endif; ?>



<?php /**PATH /home/acakw/public_html/booking/vendor/backpack/crud/src/resources/views/crud/fields/ckeditor.blade.php ENDPATH**/ ?>