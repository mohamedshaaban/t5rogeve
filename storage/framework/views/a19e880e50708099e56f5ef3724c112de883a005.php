<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('dashboard')); ?>"><i class="nav-icon la la-dashboard"></i> <span><?php echo e(trans('backpack::base.dashboard')); ?></span></a></li>


<!-- Users, Roles Permissions -->
<li class="nav-item nav-dropdown">
  <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> <?php echo e(trans('admin.Authentication')); ?></a>
  <ul class="nav-dropdown-items">
    <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('users')); ?>"><i class="nav-icon la la-user"></i> <span><?php echo e(trans('admin.Users')); ?></span></a></li>
    <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('role')); ?>"><i class="nav-icon la la-group"></i> <span><?php echo e(trans('admin.Roles')); ?></span></a></li>
  </ul>
</li>
<?php if(backpack_user()->hasPermissionTo('manage faculty')): ?>
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('faculty')); ?>"><i class="nav-icon la la-cog"></i> <span><?php echo e(trans('admin.Faculty')); ?></span></a></li>
<?php endif; ?>
<?php if(backpack_user()->hasPermissionTo('manage notifications')): ?>
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('notifications')); ?>"><i class="nav-icon la la-cog"></i> <span><?php echo e(trans('admin.Notifications')); ?></span></a></li>
<?php endif; ?>
<?php if(backpack_user()->hasPermissionTo('manage polls')): ?>
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('polls')); ?>"><i class="nav-icon la la-cog"></i> <span><?php echo e(trans('admin.Polls')); ?></span></a></li>
<?php endif; ?>

<?php if(backpack_user()->hasPermissionTo('manage events')): ?>
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('events')); ?>"><i class="nav-icon la la-cog"></i> <span><?php echo e(trans('admin.Events')); ?></span></a></li>
<?php endif; ?>
<?php if(backpack_user()->hasPermissionTo('manage expiredevents')): ?>
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('expiredevents')); ?>"><i class="nav-icon la la-cog"></i> <span><?php echo e(trans('admin.ExpiredEvents')); ?></span></a></li>
<?php endif; ?>
<?php if(backpack_user()->hasPermissionTo('manage booking')): ?>
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('booking')); ?>"><i class="nav-icon la la-cog"></i> <span><?php echo e(trans('admin.Bookings')); ?></span></a></li>
<?php endif; ?>
<?php if(backpack_user()->hasPermissionTo('manage customers')): ?>
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('customers')); ?>"><i class="nav-icon la la-cog"></i> <span><?php echo e(trans('admin.Students')); ?></span></a></li>
<?php endif; ?>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-cogs"></i><?php echo e(trans('admin.Configuration')); ?></a>
    <ul class="nav-dropdown-items">

        <?php if(backpack_user()->hasPermissionTo('manage faq')): ?>
 <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('faq')); ?>"><i class="nav-icon la la-cog"></i> <span><?php echo e(trans('admin.Faqs')); ?></span></a></li>
        <?php endif; ?>
        <?php if(backpack_user()->hasPermissionTo('manage whoweare')): ?>
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('whoweare')); ?>"><i class="nav-icon la la-cog"></i> <span><?php echo e(trans('admin.WhoWeAre')); ?></span></a></li>
        <?php endif; ?>
        <?php if(backpack_user()->hasPermissionTo('manage siteaddress')): ?>
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('siteaddress')); ?>"><i class="nav-icon la la-cog"></i> <span><?php echo e(trans('admin.Site Address')); ?></span></a></li>
        <?php endif; ?>
        <?php if(backpack_user()->hasPermissionTo('manage terms')): ?>
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('termsconditions')); ?>"><i class="nav-icon la la-cog"></i> <span><?php echo e(trans('admin.Terms & Conditions')); ?></span></a></li>
        <?php endif; ?>
        <?php if(backpack_user()->hasPermissionTo('manage contact')): ?>
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('contact')); ?>"><i class="nav-icon la la-cog"></i> <span><?php echo e(trans('admin.Contact Us')); ?></span></a></li>
        <?php endif; ?>


    </ul>
</li><?php /**PATH /home/acakw/public_html/booking/resources/views/vendor/backpack/base/inc/sidebar_content.blade.php ENDPATH**/ ?>