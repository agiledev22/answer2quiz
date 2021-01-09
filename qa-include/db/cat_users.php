<?php
if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}
function delete_catusers_by_category($category_id)
{
    qa_db_query_sub(
		'DELETE FROM ^cat_users WHERE category_id=#',
		$category_id
	);
}
function insert_catuser($category_id, $user_id)
{
	qa_db_query_sub(
		'INSERT INTO ^cat_users (category_id, user_id) ' .
		'VALUES (#, #)',
		$category_id, $user_id
	);
}