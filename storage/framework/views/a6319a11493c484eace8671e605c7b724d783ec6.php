

<?php
    $filter->options['quiet_time'] = $filter->options['quiet_time'] ?? $filter->options['delay'] ?? 500;
?>

<li filter-name="<?php echo e($filter->name); ?>"
    filter-type="<?php echo e($filter->type); ?>"
    filter-key="<?php echo e($filter->key); ?>"
	class="nav-item dropdown <?php echo e(Request::get($filter->name)?'active':''); ?>">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo e($filter->label); ?> <span class="caret"></span></a>
    <div class="dropdown-menu p-0 ajax-select">
	    <div class="form-group mb-0">
            <select
                id="filter_<?php echo e($filter->key); ?>"
                name="filter_<?php echo e($filter->name); ?>"
                class="form-control input-sm select2"
                placeholder="<?php echo e($filter->placeholder); ?>"
                data-filter-key="<?php echo e($filter->key); ?>"
                data-filter-type="select2_ajax"
                data-filter-name="<?php echo e($filter->name); ?>"
                data-select-key="<?php echo e($filter->options['select_key'] ?? 'id'); ?>"
                data-select-attribute="<?php echo e($filter->options['select_attribute'] ?? 'name'); ?>"
                data-language="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>"
                filter-minimum-input-length="<?php echo e($filter->options['minimum_input_length'] ?? 2); ?>"
                filter-method="<?php echo e($filter->options['method'] ?? 'GET'); ?>"
                filter-quiet-time="<?php echo e($filter->options['quiet_time']); ?>"
            >
				<?php if(Request::get($filter->name)): ?>
					<option value="<?php echo e(Request::get($filter->name)); ?>" selected="selected"> <?php echo e(Request::get($filter->name.'_text') ?? 'Previous selection'); ?> </option>
				<?php endif; ?>
			</select>
	    </div>
    </div>
  </li>







<?php $__env->startPush('crud_list_styles'); ?>
    <!-- include select2 css-->
    <link href="<?php echo e(asset('packages/select2/dist/css/select2.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css')); ?>" rel="stylesheet" type="text/css" />
    <style>
	  .form-inline .select2-container {
	    display: inline-block;
	  }
	  .select2-drop-active {
	  	border:none;
	  }
	  .select2-container .select2-choices .select2-search-field input, .select2-container .select2-choice, .select2-container .select2-choices {
	  	border: none;
	  }
	  .select2-container-active .select2-choice {
	  	border: none;
	  	box-shadow: none;
	  }
	  .select2-container--bootstrap .select2-dropdown {
	  	margin-top: -2px;
	  	margin-left: -1px;
	  }
	  .select2-container--bootstrap {
	  	position: relative!important;
	  	top: 0px!important;
	  }
    </style>
<?php $__env->stopPush(); ?>





<?php $__env->startPush('crud_list_scripts'); ?>
	<!-- include select2 js-->
    <script src="<?php echo e(asset('packages/select2/dist/js/select2.full.min.js')); ?>"></script>
    <?php if(app()->getLocale() !== 'en'): ?>
    <script src="<?php echo e(asset('packages/select2/dist/js/i18n/' . str_replace('_', '-', app()->getLocale()) . '.js')); ?>"></script>
    <?php endif; ?>

    <script>
        jQuery(document).ready(function($) {
            // trigger select2 for each untriggered select2 box
            //TODO: Is it really necessary to foreach an ID when it must be UNIQUE ?
            $('#filter_<?php echo e($filter->key); ?>').each(function () {

            	// if the filter has already been initialised, do nothing
            	if ($(this).attr('data-initialised')) {
            		return;
            	} else {
	            	$(this).attr('data-initialised', 'true');
            	}

            	var filterName = $(this).attr('data-filter-name');
                var filterKey = $(this).attr('data-filter-key');
                var selectAttribute = $(this).attr('data-select-attribute');
                var selectKey = $(this).attr('data-select-key');

            	$(this).select2({
				    theme: "bootstrap",
				    minimumInputLength: $(this).attr('filter-minimum-input-length'),
	            	allowClear: true,
	        	    placeholder: $(this).attr('placeholder'),
					closeOnSelect: false,
					dropdownParent: $(this).parent('.form-group'),
				    // tags: [],
				    ajax: {
				        url: '<?php echo e($filter->values); ?>',
				        dataType: 'json',
				        type: $(this).attr('filter-method'),
				        delay: $(this).attr('filter-quiet-time'),

				        processResults: function (data) {
                            //it's a paginated result
                            if(Array.isArray(data.data)) {
                                if(data.data.length > 0) {
                               return {
                                    results: $.map(data.data, function (item) {
                                     return {
                                        text: item[selectAttribute],
                                        id: item[selectKey]
                                    }
                                })
                                };
                                }
                            }else{
                                //it's non-paginated result
                                return {
                                    results: $.map(data, function (item, i) {
                                        return {
                                            text: item,
                                            id: i
                                        }
                                    })
                                };
                            }
				        }
				    }
				}).on('change', function (evt) {
					var val = $(this).val();
					var val_text = $(this).select2('data')[0]?$(this).select2('data')[0].text:null;
					var parameter = filterName;

			    	// behaviour for ajax table
					var ajax_table = $('#crudTable').DataTable();
					var current_url = ajax_table.ajax.url();
					var new_url = addOrUpdateUriParameter(current_url, parameter, val);
					new_url = addOrUpdateUriParameter(new_url, parameter + '_text', val_text);
					new_url = normalizeAmpersand(new_url.toString());

					// replace the datatables ajax url with new_url and reload it
					ajax_table.ajax.url(new_url).load();

					// add filter to URL
					crud.updateUrl(new_url);

					// mark this filter as active in the navbar-filters
					if (URI(new_url).hasQuery(filterName, true)) {
						$('li[filter-key='+filterKey+']').addClass('active');
					}
					else
					{
						$("li[filter-key="+filterKey+"]").removeClass("active");
						$("li[filter-key="+filterKey+"]").find('.dropdown-menu').removeClass("show");
					}
				});

				// when the dropdown is opened, autofocus on the select2
				$('li[filter-key='+filterKey+']').on('shown.bs.dropdown', function () {
					$('#filter_'+filterKey).select2('open');
				});

				// clear filter event (used here and by the Remove all filters button)
				$('li[filter-key='+filterKey+']').on('filter:clear', function(e) {
					$('li[filter-key='+filterKey+']').removeClass('active');
	                $('#filter_'+filterKey).val(null).trigger('change');
				});
            });
        });
    </script>
<?php $__env->stopPush(); ?>


<?php /**PATH /home/acakw/public_html/booking/vendor/backpack/crud/src/resources/views/crud/filters/select2_ajax.blade.php ENDPATH**/ ?>