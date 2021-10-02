<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="nav-icon la la-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>


<!-- Users, Roles Permissions -->
<li class="nav-item nav-dropdown">
  <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> {{ trans('admin.Authentication') }}</a>
  <ul class="nav-dropdown-items">
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('users') }}"><i class="nav-icon la la-user"></i> <span>{{ trans('admin.Users') }}</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-group"></i> <span>{{ trans('admin.Roles') }}</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('admin.Permissions') }}</span></a></li>
  </ul>
</li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('faculty') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Faculty') }}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('notifications') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Notifications')}}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('polls') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Polls')}}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('amenities') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.amenities')}}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('events') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Events')}}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('expiredevents') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.ExpiredEvents')}}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('booking') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Bookings')}}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('customers') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Students')}}</span></a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-cogs"></i>{{ trans('admin.Configuration')}}</a>
    <ul class="nav-dropdown-items">
 <li class="nav-item"><a class="nav-link" href="{{ backpack_url('faq') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Faqs')}}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('whoweare') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.WhoWeAre')}}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('siteaddress') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Site Address')}}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('termsconditions') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Terms & Conditions')}}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('contact') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Contact Us')}}</span></a></li>

    </ul>
</li>