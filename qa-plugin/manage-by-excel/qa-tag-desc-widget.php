<?php

  class qa_tag_descriptions_widget {

      function allow_template($template)
      {
          return ($template=='tag');
      }
      function allow_region($region)
      {
          return true;
      }
    
      function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
      {
        require_once QA_INCLUDE_DIR.'db/metas.php';

        $parts=explode('/', $request);
        $tag=$parts[1];
  
        $description=qa_db_tagmeta_get($tag, 'description');
        $editurlhtml=qa_path_html('tag-edit/'.$tag);
  
        if (strlen($description)) {
            echo qa_html($description);
            echo ' - <a href="'.$editurlhtml.'">edit</a>';
        } else
            echo '<a href="'.$editurlhtml.'">Create tag description</a>';
      }
  }