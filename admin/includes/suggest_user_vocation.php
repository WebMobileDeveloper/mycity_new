<?php
date_default_timezone_set('America/Los_Angeles');
set_time_limit(60*10);

session_start();
include_once 'db.php';
include_once 'functions.php';

$_user_role = @$_SESSION['user_role'];



function saveVocationSuggestion($link, $userVocationId, $suggestedVocationId) {
	if ($stmt = mysqli_prepare($link, "INSERT INTO `user_suggest_vocation`(`vocation_id`, `suggested_vocations_id`) VALUES (?, ?)")) {
		 /* bind parameters for markers */
		mysqli_stmt_bind_param($stmt, "ss", $userVocationId, $suggestedVocationId);

		/* execute query */
		mysqli_stmt_execute($stmt);

		$insertId = mysqli_insert_id($link);
		
		/* close statement */
		mysqli_stmt_close($stmt);
		
		return ($insertId > 0) ? TRUE : FALSE;
	}
}

function checkDuplicateSuggestedVocation($link, $userVocationId, $suggestedVocationId) {
	if ($stmt = mysqli_prepare($link, "SELECT COUNT(*) FROM `user_suggest_vocation` WHERE `vocation_id` = ? AND `suggested_vocations_id` = ?")) {
		 /* bind parameters for markers */
		mysqli_stmt_bind_param($stmt, "ss", $userVocationId, $suggestedVocationId);

		/* execute query */
		mysqli_stmt_execute($stmt);

		/* bind result variables */
		mysqli_stmt_bind_result($stmt, $count);

		/* fetch value */
		mysqli_stmt_fetch($stmt);
		/* close statement */
		mysqli_stmt_close($stmt);
		
		return ($count > 0) ? TRUE : FALSE;
	}
}

function getUserVocationSuggestions($link, $start = 0) {
	// check if vocation id is valid
	if ($stmt = mysqli_prepare($link, "SELECT user_suggest_vocation.*,
		user_vocation_name.voc_name AS user_voc_name, suggested_vocation_name.voc_name AS suggested_voc_name 
		FROM `user_suggest_vocation` 
		LEFT JOIN vocations user_vocation_name 
			ON user_suggest_vocation.vocation_id = user_vocation_name.id
		LEFT JOIN vocations suggested_vocation_name 
			ON user_suggest_vocation.suggested_vocations_id = suggested_vocation_name.id LIMIT ?, 10")) {
		 /* bind parameters for markers */
		mysqli_stmt_bind_param($stmt, "s", $start);

		/* execute query */
		mysqli_stmt_execute($stmt);

		/* bind result variables */
		mysqli_stmt_bind_result($stmt, $id, $vocation_id, $suggested_vocations_id, $user_voc_name, $suggested_voc_name);

		$data = [];
		/* fetch value */
		while (mysqli_stmt_fetch($stmt)){
			$data[] = [
				"id" => $id,
				"vocation_id" => $vocation_id,
				"suggested_vocations_id" => $suggested_vocations_id,
				"user_voc_name" => $user_voc_name,
				"suggested_voc_name" => $suggested_voc_name
			];
		}
		/* close statement */
		mysqli_stmt_close($stmt);
		
		return $data;
	}
}

function checkUserSuggestVocationIdExists($link, $id) {
	if ($stmt = mysqli_prepare($link, "SELECT COUNT(*) FROM `user_suggest_vocation` WHERE `id` = ?")) {
		 /* bind parameters for markers */
		mysqli_stmt_bind_param($stmt, "s", $id);

		/* execute query */
		mysqli_stmt_execute($stmt);

		/* bind result variables */
		mysqli_stmt_bind_result($stmt, $count);

		/* fetch value */
		mysqli_stmt_fetch($stmt);
		/* close statement */
		mysqli_stmt_close($stmt);
		
		return ($count > 0) ? TRUE : FALSE;
	}
}

function deleteUserSuggestVocationById($link, $id) {
	if ($stmt = mysqli_prepare($link, "DELETE FROM `user_suggest_vocation` WHERE `id` = ? LIMIT 1")) {
		 /* bind parameters for markers */
		mysqli_stmt_bind_param($stmt, "s", $id);

		/* execute query */
		mysqli_stmt_execute($stmt);

		$rowCount = mysqli_stmt_affected_rows($stmt);
		
		/* close statement */
		mysqli_stmt_close($stmt);
		
		return ($rowCount > 0) ? TRUE : FALSE;
	}
}

if (isset($_POST['userVocationId']) && isset($_POST['suggestedVocationId'])) {
	if($_user_role === 'admin') {
		$userVocationId = filter_input(INPUT_POST, 'userVocationId', FILTER_SANITIZE_STRING);
		$suggestedVocationId = filter_input(INPUT_POST, 'suggestedVocationId', FILTER_SANITIZE_STRING);
		
		$checkDuplicate = checkDuplicateSuggestedVocation($link, $userVocationId, $suggestedVocationId);
		
		if ((checkValidVocation($link, $userVocationId) === true) 
			&& (checkValidVocation($link, $suggestedVocationId) === true)
			&& ($checkDuplicate === FALSE)) {
			$saveVocationSuggestion = saveVocationSuggestion($link, $userVocationId, $suggestedVocationId);
			echo json_encode(["save" => $saveVocationSuggestion]);
			exit;
		} else if ($checkDuplicate === true) {
			echo json_encode(["duplicate" => true]);
			exit;
		}
	}
	exit;
}


if (isset($_GET['getUserSuggestions']) && $_GET['getUserSuggestions'] == 1) {
	echo json_encode(getUserVocationSuggestions($link));
	exit;
}


if (isset($_POST['deleteSuggestedVocationId'])) {
	$userSuggestedVocationId = filter_input(INPUT_POST, "deleteSuggestedVocationId", FILTER_SANITIZE_STRING);
	
	if (checkUserSuggestVocationIdExists($link, $userSuggestedVocationId) === true) {
		// delete
		echo json_encode(["deleted" => deleteUserSuggestVocationById($link, $userSuggestedVocationId)]);
	}
	
	exit;
}


