<?php 
view_header(lang('欢迎')); 
global $admin_type;  
do_action($admin_type.".index");  
view_footer();
