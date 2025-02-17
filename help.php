<nav class="navbar navbar-dark bg-dark main-dashboard-cip other-pages">
	<a class="navbar-brand" href="<?php echo esc_url(admin_url('admin.php?page=clock-in-portal')); ?>"><i class="fas fa-home"></i></a>
	<a class="navbar-brand" href="#"><?php esc_html_e('Documentation', CIP_FREE_TXTDM); ?></a>
	<div class="form-inline my-2 my-lg-0">
		<a class="navbar-brand" href="<?php echo esc_url(admin_url('admin.php?page=cip-settings')); ?>"><i class="fas fa-cog"></i></a>
	</div>
</nav>
<div class='row'>
	<div style="padding:20px 20px 0px 16px;" class="cip_free col-md-8">
		<div class="cip_heading  modal-content" style="width:100%; padding:20px; margin-bottom:20px">
			<h1><?php esc_html_e('How to Configure', CIP_FREE_TXTDM); ?></h1>
		</div>
		<div class="modal-content" style="width:100%; padding:20px; margin-bottom:20px">
			<h2 class="cip_free_title"><?php esc_html_e('Admin End', CIP_FREE_TXTDM); ?></h2>
			<div class="tab-content">
				<h3><?php esc_html_e('Step 1 : Create a New Designation', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('After installing the plugin, first we will create a new designation for staff members', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('To make a new designation, we will follow the steps given below:', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('1. Go to designation menu (Clock in Portal --->> Designations)', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('2. Click on Add staff designation button and setup the all required field with new designation name.', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('After these steps, Now a new designation will added after default designation.', CIP_FREE_TXTDM); ?></p>
				<h4><?php esc_html_e('Note : The default designation will be updated according to the requirement....', CIP_FREE_TXTDM); ?></h4>
			</div>
			<div class="tab-content">
				<h3><?php esc_html_e('Step 2 : Add User as Staff', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('After Creating the new designation, Now we will move to add your existing and new users as staff list', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('Follow the below steps:', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('1. Go to Staff menu (Clock in Portal --->> Staff)', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('2. Click on Add Users as Staff button, after clicking a popup box appers. In this box you can see a list of your existing users. select a user', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('3. After Selected a user, All required information will show on popup form. Now setup the designation and status for it', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('4. Click on Add staff button', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('After these steps, Selected user will added on list.', CIP_FREE_TXTDM); ?></p>
				<h4><?php esc_html_e('Note : Registered New Staff button redirect on add new user page to create a new users for your staff list', CIP_FREE_TXTDM); ?></h4>
			</div>
			<div class="tab-content">
				<h3><?php esc_html_e('Step 3 : Staff Activate for clock in portal', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('For this step you need to give them permission by activate/deactivate option.', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('Follow the below steps:', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('1. Go to Staff menu (Clock in Portal --->> Staff)', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('2. Click on a update button, in the right side of all users list. after clicking a popup box appers. In this box you can see all information about selected user.', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('3. After clicking a popup box appers. In this box you can see all information about selected user. change the status as activate (if not activated)', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('4. Click on Update staff button', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('After these steps, Selected user will activated.', CIP_FREE_TXTDM); ?></p>
			</div>
		</div>
		<div class="modal-content" style="width:100%; padding:20px; margin-bottom:20px">
			<h2 class="cip_free_title"><?php esc_html_e('Staff End', CIP_FREE_TXTDM); ?></h2>
			<div class="tab-content">
				<h3><?php esc_html_e('Dashboard', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('After login as staff. All users will Office IN/Out and Lunch IN/Out ', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('Also submit daily reports', CIP_FREE_TXTDM); ?></p>
			</div>
			<div class="tab-content">
				<h3><?php esc_html_e('Leave Request', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('A authoried activate staff members will show a leave request page with clock in portal plugin page', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('To make a new leave request, we will follow the steps given below:', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('1. Go to  leave request menu (Clock in Portal --->> Leave Request)', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('2. Click on Add New request button and set the all required field leave request title, short description, start and end date', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('After these steps, Now a new leave request will added on list.', CIP_FREE_TXTDM); ?></p>
				<h4><?php esc_html_e('Note : The request to leave can be updated according to the requirement, but before the administrator has not approved or rejected it.', CIP_FREE_TXTDM); ?></h4>
			</div>
			<div class="tab-content">
				<h3><?php esc_html_e('Holiday', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('All upcoming holiday and events will show on this page', CIP_FREE_TXTDM); ?></p>
			</div>
			<div class="tab-content">
				<h3><?php esc_html_e('Reports', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('Get daily to yearly reports and export it. ', CIP_FREE_TXTDM); ?></p>
			</div>
		</div>

		<div class="cip_heading  modal-content" style="width:100%; padding:20px; margin-bottom:20px">
			<h1><?php esc_html_e('Shortcode For Frontend Portal', CIP_FREE_TXTDM); ?></h1>
		</div>
		<div class="modal-content" style="width:100%; padding:20px; margin-bottom:20px">
			<div class="tab-content">
				<h3><?php esc_html_e('To use shortcode [WL_CIP_PORTAL] ', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('Just put this shortcode in any post/page to get frontend portal for users \'[WL_CIP_PORTAL]\' ', CIP_FREE_TXTDM); ?></p>
			</div>
		</div>
		<div class="cip_heading  modal-content" style="width:100%; padding:20px; margin-bottom:20px">
			<h1><?php esc_html_e('Other Options', CIP_FREE_TXTDM); ?></h1>
		</div>
		<div class="modal-content" style="width:100%; padding:20px; margin-bottom:20px">

			<div class="tab-content">
				<h3><?php esc_html_e('Dashboard', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('After login as staff. All users list will show on this section with Office IN/Out and Lunch IN/Out time', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('Also see and update submit daily reports and time', CIP_FREE_TXTDM); ?></p>
			</div>
			<div class="tab-content">
				<h3><?php esc_html_e('Add a New Holiday/Event', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('In this section. You can add a upcoming event and holiday for your staff', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('To add a new Holiday/Event, we will follow the steps given below:', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('1. Go to holiday/event menu (Clock in Portal --->> Holiday/event)', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('2. Click on Add new holiday/event button and setup the all required field with new holiday/event name.', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('After these steps, Now a new holiday/event will added on list.', CIP_FREE_TXTDM); ?></p>
			</div>
			<div class="tab-content">
				<h3><?php esc_html_e('Staff leave request', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('In this section. You can see all staff requests for leave and take a action as approved and cancelled', CIP_FREE_TXTDM); ?></p>
			</div>
			<div class="tab-content">
				<h3><?php esc_html_e('Manage Reports', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('In this section. You can see/edited your staff reports as daily/monthly/year based ', CIP_FREE_TXTDM); ?></p>
			</div>
		</div>
		<div class="modal-content" style="width:100%; padding:20px; margin-bottom:20px">
			<div class="tab-content">
				<h3><?php esc_html_e('Settings', CIP_FREE_TXTDM); ?></h3>
				<p><?php esc_html_e('General Settings : Set Time & date formate ', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('Staff Settings : Show/Hide holiday and report submission section ', CIP_FREE_TXTDM); ?></p>
				<p><?php esc_html_e('Message and text : All Message and text will set as your choice ', CIP_FREE_TXTDM); ?></p>
			</div>
		</div>
	</div>
	
</div>