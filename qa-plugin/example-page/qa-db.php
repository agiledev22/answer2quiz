<?php
// function qa_db_page_find_by_title($page_title)
// {
// 	return qa_db_read_all_values(qa_db_query_sub(
// 		'SELECT pageid FROM ^pages WHERE title=$',
// 		$page_title
// 	));
// }

// function qa_db_page_find_last_position()
// {
// 	return qa_db_read_all_values(qa_db_query_sub(
// 		'SELECT position FROM ^pages ORDER BY position DESC LIMIT 1'
// 	));
// }

function prepare_db()
{

    qa_db_query_sub(
        'CREATE TABLE IF NOT EXISTS ^cat_users (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `category_id` int(11) NOT NULL,
            `user_id` int(11) NOT NULL,
            `created_at` datetime NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4;'
    );

    $keycolumns = qa_helper_array_to_keys(qa_db_read_all_values(qa_db_query_sub('SHOW COLUMNS FROM ^categories')));
    if (!isset($keycolumns['admin_id']))
    {
        qa_db_query_sub(
            'ALTER TABLE ^categories
                ADD COLUMN `admin_id` int(11) DEFAULT NULL AFTER backpath;'
            );
	}
	
    $keycolumns = qa_helper_array_to_keys(qa_db_read_all_values(qa_db_query_sub('SHOW COLUMNS FROM ^users')));
    if (!isset($keycolumns['orgid']))
    {
        qa_db_query_sub(
            'ALTER TABLE ^users
                ADD COLUMN `orgid` varchar(255) DEFAULT NULL AFTER wallposts;'
            );
    }
}

function qa_db_user_find_by_orgid($orgid)
{
	return qa_db_read_all_values(qa_db_query_sub(
		'SELECT userid FROM ^users WHERE orgid=$',
		$orgid
	));
}

/**
 * Return array with all values from $array as keys
 * @param array $array
 * @return array
 */
function qa_helper_array_to_keys($array)
{
	return empty($array) ? array() : array_combine($array, array_fill(0, count($array), true));
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

function qa_db_user_create_with_orgid($email, $password, $handle, $level, $ip, $orgid)
{
	require_once QA_INCLUDE_DIR . 'util/string.php';

	$ipHex = bin2hex(@inet_pton($ip));

	if (QA_PASSWORD_HASH) {
		qa_db_query_sub(
			'INSERT INTO ^users (created, createip, email, passhash, level, handle, loggedin, loginip, orgid) ' .
			'VALUES (NOW(), UNHEX($), $, $, #, $, NOW(), UNHEX($), $)',
			$ipHex, $email, isset($password) ? password_hash($password, PASSWORD_BCRYPT) : null, (int)$level, $handle, $ipHex, $orgid
		);
	} else {
		$salt = isset($password) ? qa_random_alphanum(16) : null;

		qa_db_query_sub(
			'INSERT INTO ^users (created, createip, email, passsalt, passcheck, level, handle, loggedin, loginip, orgid) ' .
			'VALUES (NOW(), UNHEX($), $, $, UNHEX($), #, $, NOW(), UNHEX($), $)',
			$ipHex, $email, $salt, isset($password) ? qa_db_calc_passcheck($password, $salt) : null, (int)$level, $handle, $ipHex, $orgid
		);
	}


	return qa_db_last_insert_id();
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