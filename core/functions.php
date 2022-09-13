<?php

function redirect_to($path){
	die("<meta http-equiv='refresh' content=0;URL='".$path."' />");
}

/*** =============================== DEPARTMENT FUNCTIONS ==============================  */
function getPayrollPeriods(){
	global $db;
	$sql = 'SELECT * FROM `tbl_payrollperiod`';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([]);
	if($res){
		$periods = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $periods;
	}
	return false;	
}
function getAllDepartments(){
	global $db;
	$sql = 'SELECT * FROM gmdepartments';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([]);
	if($res){
		$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $departments;
	}
	return false;	
}
function getEmployeeImage($empcode){
	global $db;
	$sql = 'SELECT * FROM tbl_employee WHERE empcode = ? LIMIT 1';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([$empcode]);
	if($res){
		$employee = $stmt->fetch(PDO::FETCH_ASSOC);
		return $employee;
	}
	return false;	
}
function getUserImage($userid){
	global $db;
	$sql = 'SELECT * FROM tblusers WHERE id = ? LIMIT 1';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([$userid]);
	if($res){
		$employee = $stmt->fetch(PDO::FETCH_ASSOC);
		return $employee;
	}
	return false;	
}

function getEmployeeSchedule($empcode){
	global $db;
	$sql = 'SELECT * FROM vwassociateschedules WHERE empcode = ? LIMIT 1';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([$empcode]);
	if($res){
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data;
	}
	return false;	
}

function getAllScheduleNames(){
	global $db;
	$sql = 'SELECT * FROM tbl_schedtable';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([]);
	if($res){
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	return false;	
}

function getHeadDepartmentByHeadId($empcode){
	global $db;
	$sql = 'SELECT * FROM tbl_department WHERE head_empcode = ? LIMIT 1';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([$empcode]);
	if($res){
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data;
	}
	return false;	
}
/*
Getting the current employment status of the employee.
*/
function getEmployeeStatusById($empcode){
	global $db;
	$sql = 'SELECT jobstatuscode FROM tbl_employee WHERE empcode = ? LIMIT 1';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([$empcode]);
	if($res){
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data['jobstatuscode'];
	}
	return false;	
}


/*
Get all employees
*/
function getEmployees(){
	global $db;
	$sql = 'SELECT * FROM tbl_employee ORDER BY name ASC';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([]);
	if($res){
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	return false;	
}

function checkIfHeadById($empcode){
	global $db;
	$found = false;
	$sql = 'SELECT * FROM tbl_department WHERE head_empcode = ? LIMIT 1';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([$empcode]);
	if($res){
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		if($data){
			$found = true;
		}		
	}
	return $found;	
}

function getAssociateSchedule($empcode){
	global $db;
	$sql = 'SELECT * FROM tbl_assignedsched WHERE empcode = ? LIMIT 1';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([$empcode]);
	if($res){
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data;
	}
	return false;	
}

function getLogByEmpcodeAndDate($empcode,$targetdate){
	global $db;
	$sql = 'SELECT * FROM tbl_log WHERE empcode = ? AND sdate = ? LIMIT 1';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([$empcode, $targetdate]);
	if($res){
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data;
	}
	return false;	
}


function getLeaveTypes(){
	global $db;
	$sql = 'SELECT * FROM tbl_leavetypes';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute();
	if($res){
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	return false;	
}


// function getTotalHours($empcode){
// 	global $db;
// 	$sql = 'SELECT sum() FROM tbl_payrollgeneration WHERE empcode = ?';
// 	$stmt = $db->prepare($sql);
	
// 	$res = $stmt->execute([$empcode]);
// 	if($res){
// 		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
// 		return $data;
// 	}
// 	return false;	
// }
function getTotalEarnings($userid){
	global $db;
	$sql = 'SELECT IFNULL(SUM(amount),0) as income FROM vwsales WHERE userid = ? LIMIT 1';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([$userid]);
	if($res){
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data['income'];
	}
	return false;	
}

function getTotalVendos($userid){
	global $db;
	$sql = 'SELECT count(id) as id FROM tblvendos WHERE userid = ? LIMIT 1';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([$userid]);
	if($res){
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data['id'];
	}
	return false;	
}

function getTotalCustomersServed($userid){
	global $db;
	$sql = 'SELECT count(distinct(macaddress)) as customer FROM vwsales WHERE userid = ? LIMIT 1';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([$userid]);
	if($res){
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data['customer'];
	}
	return false;	
}

function getDevicesById($id){
	global $db;
	$sql = 'SELECT * FROM devices WHERE id = ? LIMIT 1';
	$stmt = $db->prepare($sql);
	
	$res = $stmt->execute([$id]);
	if($res){
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data;
	}
	return false;	
}

// formats money to a whole number or with 2 decimals; includes a dollar sign in front
function formatMoney($number, $cents = 1) { // cents: 0=never, 1=if needed, 2=always
	if (is_numeric($number)) { // a number
	  if (!$number) { // zero
		$money = ($cents == 2 ? '0.00' : '0'); // output zero
	  } else { // value
		if (floor($number) == $number) { // whole number
		  $money = number_format($number, ($cents == 2 ? 2 : 0)); // format
		} else { // cents
		  $money = number_format(round($number, 2), ($cents == 0 ? 0 : 2)); // format
		} // integer or decimal
	  } // value
	  return 'Php. '.$money;
	} // numeric
  } // formatMoney



?>
