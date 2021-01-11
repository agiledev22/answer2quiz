<?php
  /*
    Plugin Name: Manage By Excel
    Plugin URI:
    Plugin Description: Allows tag descriptions to be displayed as tooltips
    Plugin Version: 1.0
    Plugin Date: 2021-01-10
    Plugin Author:
    Plugin Author URI:
    Plugin License: GPLv2
    Plugin Minimum Question2Answer Version: 1.5
    Plugin Update Check URI:
  */
  if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
  }

  qa_register_plugin_module(
    'page', // type of module
    'qa-upload-excel.php', // PHP file containing module class
    'qa_upload_excel_page', // name of module class
    'Upload Excel File' // human-readable name of module
  );