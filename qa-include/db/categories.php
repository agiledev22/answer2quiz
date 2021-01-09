<?php
if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}
function qa_db_category_find_or_create($title, $admin_id)
{
	try{
		$category = qa_db_read_one_assoc(qa_db_query_sub(
			'SELECT * FROM ^categories WHERE title=$ and admin_id=#',
			$title,
			$admin_id
		));
		return $category['categoryid'];

	}
	catch(Exception $e)
	{
		qa_db_query_sub(
			'INSERT INTO ^categories (title, tags, content, backpath, admin_id) ' .
			'VALUES ($, $, $, $, #)',
			$title, $title, $title, $title, $admin_id
		);
	
		return qa_db_last_insert_id();
	}
}