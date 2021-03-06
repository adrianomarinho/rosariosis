<?php
if(empty($_ROSARIO['Menu']))
{
	if (is_null($RosarioModules))
		global $RosarioModules;
	
	foreach($RosarioModules as $module=>$include)
		if($include)
			@include('modules/'.$module.'/Menu.php');

	$profile = User('PROFILE');

	if($profile!='student')
		if(User('PROFILE_ID'))
			$_ROSARIO['AllowUse'] = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='".User('PROFILE_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
		else
			$_ROSARIO['AllowUse'] = DBGet(DBQuery("SELECT MODNAME FROM STAFF_EXCEPTIONS WHERE USER_ID='".User('STAFF_ID')."' AND CAN_USE='Y'"),array(),array('MODNAME'));
	else
	{
		$_ROSARIO['AllowUse'] = DBGet(DBQuery("SELECT MODNAME FROM PROFILE_EXCEPTIONS WHERE PROFILE_ID='0' AND CAN_USE='Y'"),array(),array('MODNAME'));
		$profile = 'parent';
	}

	foreach($menu as $modcat=>$profiles)
	{
		//FJ bugfix remove modules with no programs
		$no_programs_in_module = true;
		
		$programs = $profiles[$profile];
		foreach($programs as $program=>$title)
		{
			if(!is_numeric($program))
			{
//				if($_ROSARIO['AllowUse'][$program] && ($profile!='admin' || !$exceptions[$modcat][$program] || AllowEdit($program)))
				if($program == 'default' && (!empty($_ROSARIO['AllowUse'][$title]) && ($profile!='admin' || empty($exceptions[$modcat][$title]) || AllowEdit($title))))
					$_ROSARIO['Menu'][$modcat]['default'] = $title;
				elseif(!empty($_ROSARIO['AllowUse'][$program]) && ($profile!='admin' || empty($exceptions[$modcat][$program]) || AllowEdit($program)))
				{
					$_ROSARIO['Menu'][$modcat][$program] = $title;
					if (!isset($_ROSARIO['Menu'][$modcat]['default'])) //default to first allowed program if default not allowed
						$_ROSARIO['Menu'][$modcat]['default'] = $program;
					$no_programs_in_module = false;
				}
			}
			else
				$_ROSARIO['Menu'][$modcat][$program] = $title;
		}
		
		if ($no_programs_in_module)
			unset($_ROSARIO['Menu'][$modcat]);
	}

//FJ enable password change for students
	if(User('PROFILE')=='student')
		//unset($_ROSARIO['Menu']['Users']);
		unset($_ROSARIO['Menu']['Users']['parent']['Users/User.php']);
}
?>
