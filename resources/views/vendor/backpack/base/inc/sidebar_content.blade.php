<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="nav-icon la la-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>


<!-- Users, Roles Permissions -->
<li class="nav-item nav-dropdown">
  <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> Authentication</a>
  <ul class="nav-dropdown-items">
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-group"></i> <span>Roles</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
  </ul>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-cogs"></i> Advanced</a>
    <ul class="nav-dropdown-items">
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('elfinder') }}"><i class="nav-icon la la-files-o"></i> <span>File manager</span></a></li>
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('backup') }}"><i class="nav-icon la la-hdd-o"></i> <span>Backups</span></a></li>
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('log') }}"><i class="nav-icon la la-terminal"></i> <span>Logs</span></a></li>
    </ul>
</li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('faculty') }}"><i class="nav-icon la la-cog"></i> <span>Faculty</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('events') }}"><i class="nav-icon la la-cog"></i> <span>Events</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('booking') }}"><i class="nav-icon la la-cog"></i> <span>Booking</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('invoices') }}"><i class="nav-icon la la-cog"></i> <span>Invoices</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('SponsorPlatinums') }}"><i class="nav-icon la la-cog"></i> <span>Sponsor Platinums</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('customers') }}"><i class="nav-icon la la-cog"></i> <span>Students</span></a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-cogs"></i>Configuration</a>
    <ul class="nav-dropdown-items">
 <li class="nav-item"><a class="nav-link" href="{{ backpack_url('faq') }}"><i class="nav-icon la la-cog"></i> <span>Faqs</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('whoweare') }}"><i class="nav-icon la la-cog"></i> <span>WhoWeAre</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('wayuse') }}"><i class="nav-icon la la-cog"></i> <span>Way use</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('siteaddress') }}"><i class="nav-icon la la-cog"></i> <span>Site Address</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('termsconditions') }}"><i class="nav-icon la la-cog"></i> <span>Terms & Conditions</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('canceleventsub') }}"><i class="nav-icon la la-cog"></i> <span>Cacncel Subscriptions</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('contact') }}"><i class="nav-icon la la-cog"></i> <span>Contact Us</span></a></li>

    </ul>
</li>