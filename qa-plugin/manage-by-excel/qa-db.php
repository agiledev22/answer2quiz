<?php

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