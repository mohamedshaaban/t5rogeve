<?php if($crud->hasAccess('bulkClone') && $crud->get('list.bulkActions')): ?>
	<a href="javascript:void(0)" onclick="bulkCloneEntries(this)" class="btn btn-sm btn-secondary bulk-button"><i class="la la-copy"></i> <?php echo e(trans('backpack::crud.clone')); ?></a>
<?php endif; ?>

<?php $__env->startPush('after_scripts'); ?>
<script>
	if (typeof bulkCloneEntries != 'function') {
	  function bulkCloneEntries(button) {

	      if (typeof crud.checkedItems === 'undefined' || crud.checkedItems.length == 0)
	      {
  	        new Noty({
	          type: "warning",
	          text: "<strong><?php echo trans('backpack::crud.bulk_no_entries_selected_title'); ?></strong><br><?php echo trans('backpack::crud.bulk_no_entries_selected_message'); ?>"
	        }).show();

	      	return;
	      }

	      var message = "<?php echo trans('backpack::crud.bulk_clone_are_you_sure'); ?>";
	      message = message.replace(":number", crud.checkedItems.length);

	      // show confirm message
	      swal({
			  title: "<?php echo trans('backpack::base.warning'); ?>",
			  text: message,
			  icon: "warning",
			  buttons: {
			  	cancel: {
				  text: "<?php echo trans('backpack::crud.cancel'); ?>",
				  value: null,
				  visible: true,
				  className: "bg-secondary",
				  closeModal: true,
				},
			  	delete: {
				  text: "<?php echo e(trans('backpack::crud.clone')); ?>",
				  value: true,
				  visible: true,
				  className: "bg-primary",
				}
			  },
			}).then((value) => {
				if (value) {
					var ajax_calls = [];
		      		var clone_route = "<?php echo e(url($crud->route)); ?>/bulk-clone";

					// submit an AJAX delete call
					$.ajax({
						url: clone_route,
						type: 'POST',
						data: { entries: crud.checkedItems },
						success: function(result) {
						  // Show an alert with the result
		    	          new Noty({
				            type: "success",
				            text: "<strong><?php echo trans('backpack::crud.bulk_clone_sucess_title'); ?></strong><br>"+crud.checkedItems.length+" <?php echo trans('backpack::crud.bulk_clone_sucess_message'); ?>"
				          }).show();

						  crud.checkedItems = [];
						  crud.table.draw(false);
						},
						error: function(result) {
						  // Show an alert with the result
		    	          new Noty({
				            type: "danger",
				            text: "<strong><?php echo trans('backpack::crud.bulk_clone_error_title'); ?></strong><br>"+crud.checkedItems.length+" <?php echo trans('backpack::crud.bulk_clone_error_message'); ?>"
				          }).show();
						}
					});
				}
			});
      }
	}
</script>
<?php $__env->stopPush(); ?><?php /**PATH /home/acakw/public_html/booking/vendor/backpack/crud/src/resources/views/crud/buttons/bulk_clone.blade.php ENDPATH**/ ?>