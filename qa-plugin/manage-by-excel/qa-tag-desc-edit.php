<?php

  class qa_tag_descriptions_edit_page {

      function match_request($request)
      {
          $parts=explode('/', $request);

          return $parts[0]=='tag-edit';
      }

      function process_request($request)
      {
        require_once QA_INCLUDE_DIR.'db/metas.php';
        require_once QA_INCLUDE_DIR.'db/users.php';
        require_once QA_INCLUDE_DIR.'db/categories.php';
        require_once QA_INCLUDE_DIR.'db/cat_users.php';
        require_once QA_INCLUDE_DIR.'app/users.php';
        require_once QA_INCLUDE_DIR.'SimpleXLSX.php';
        // require_once QA_INCLUDE_DIR.'qa-db.php';
        $db = qa_service('database');
        // print_r($db->query('SELECT * FROM qa_users')->fetchAllAssoc());
        $parts=explode('/', $request);
        $tag=$parts[0];
  
        $qa_content=qa_content_prepare();
        $qa_content['title']='Edit the description for '.qa_html($tag);

        if (qa_clicked('doupload')) {
            // return qa_post_text('test_input');
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
                        // echo '<tr>';
                        // for ( $i = 0; $i < $cols; $i ++ ) {
                            // echo '<td>' . ( isset( $r[ $i ] ) ? $r[ $i ] : '&nbsp;' ) . '</td>';
                            
                        // }
                        // echo '</tr>';
                        if(isset($r[4]))//if email is set
                        {
                            $orgid = $r[1];
                            $username = $r[2].' '.$r[3];
                            $email = $r[4];
                            $user_id = qa_db_user_find_by_orgid($orgid);

                            if(empty($user_id)){
                                $user_id = qa_db_user_create($email, null, $username, 100, null, $orgid);
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
        return $qa_content;
      }

  }