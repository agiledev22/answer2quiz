<?php
/*
	Question2Answer by Gideon Greenspan and contributors
	http://www.question2answer.org/

	File: qa-plugin/example-page/qa-example-page.php
	Description: Page module class for example page plugin


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/
require_once 'qa-db.php';
class qa_excel_page
{
	private $directory;
	private $urltoroot;


	public function load_module($directory, $urltoroot)
	{
		$this->directory = $directory;
		$this->urltoroot = $urltoroot;
	}


	public function suggest_requests() // for display in admin interface
	{
		return array(
			array(
				'title' => 'Excel upload plugin',
				'request' => 'example-plugin-page',
				'nav' => 'M', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
			),
		);
	}


	public function match_request($request)
	{
		$parts=explode('/', $request);

		return $parts[0]=='example-plugin-page';
		// return $request == 'example-plugin-page';

	}

	function init_queries($tableslc)
	{
        prepare_db();
	}

	public function process_request($request)
	{
        require_once QA_INCLUDE_DIR.'db/metas.php';
        require_once QA_INCLUDE_DIR.'db/users.php';
        require_once QA_INCLUDE_DIR.'app/users.php';
        require_once 'SimpleXLSX.php';
		prepare_db();

		$parts=explode('/', $request);
        $tag=$parts[0];
  
        $qa_content=qa_content_prepare();
        $qa_content['title']='Upload Excel File';
		if(qa_post_text('okthen'))
		{
            if (isset($_FILES['excel_file'])) 
            {

                if ( $xlsx = SimpleXLSX::parse( $_FILES['excel_file']['tmp_name'] ) ) 
                {
                    $dim = $xlsx->dimension();
                    $cols = $dim[0];
            
                    $forum_title = "";
                    $admin_id = qa_get_logged_in_userid();
                    $category_id = 0;

                    foreach ( $xlsx->rows() as $k => $r )
                    {
                        //		if ($k == 0) continue; // skip first row
                        if ($k == 0)
                        {
                            $forum_title = $r[2];
                            $category_id = qa_db_category_find_or_create($forum_title, $admin_id);
                            delete_catusers_by_category($category_id);
                        }
                        if($k <= 1) continue;

                        if(isset($r[4]))//if email is set
                        {
                            $orgid = $r[1];
                            $username = $r[2].' '.$r[3];
                            $email = $r[4];
                            $user_id = qa_db_user_find_by_orgid($orgid);

                            if(empty($user_id)){
                                $user_id = qa_db_user_create_with_orgid($email, null, $username, 100, null, $orgid);
                            }
                            else
                            {
                                // print_r($user_id);
                            }
                            insert_catuser($category_id, $user_id);
                        }
                    }
                }
                else 
                {
                    echo SimpleXLSX::parseError();
                }
            }
		}
        // return $qa_content;
		$qa_content = qa_content_prepare();

		$qa_content['title'] = qa_lang_html('excel_page/page_title');
		// $qa_content['error'] = 'An example error';
		$qa_content['custom'] = 'Some <b>custom html</b>';

		$qa_content['form'] = array(
			'tags' => 'method="post" action="' . qa_self_html() . '" enctype="multipart/form-data"',

			'style' => 'wide',

			'ok' => qa_post_text('okthen') ? 'You uploaded excel file!' : null,

			'title' => 'Form title',

			'fields' => array(
				'request' => array(
					'label' => 'The request',
					'tags' => 'name="excel_file"',
					'type' => 'file',
					'value' => qa_html($request),
					// 'error' => qa_html('Another error'),
				),

			),

			'buttons' => array(
				'ok' => array(
					'tags' => 'name="okthen"',
					'label' => 'Upload',
					'value' => '1',
				),
			),

			'hidden' => array(
				'hiddenfield' => '1',
			),
		);

		$qa_content['custom_2'] = '<p><br>More <i>custom html</i></p>';

		return $qa_content;
	}
}
