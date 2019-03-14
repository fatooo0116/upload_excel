<?php


add_action('wp_ajax_csv_upload_action', 'csv_upload_func2');
add_action('wp_ajax_nopriv_csv_upload_action', 'csv_upload_func2'); // Allow front-end submission

function csv_upload_func2(){

  $parent_post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;  // The parent ID of our attachments
  $valid_formats = array("xlsx"); // Supported file types
  $max_file_size = 1024 * 500; // in kb
  $max_image_upload = 120; // Define how many images can be uploaded to the current post
  $wp_upload_dir = wp_upload_dir();
  $path = $wp_upload_dir['path'] . '/';
  $count = 0;
  $create = 0;
  $all = 0;
  $error = 0;



  // Image upload handler
  if( $_SERVER['REQUEST_METHOD'] == "POST" ){

        foreach ( $_FILES['files']['name'] as $f => $name ) {
              $extension = pathinfo( $name, PATHINFO_EXTENSION );
              $new_filename = 'eeddx'. '.' . $extension;

              if ( $_FILES['files']['error'][$f] == 0 ) {
                  // Check if image size is larger than the allowed file size
                  if ( $_FILES['files']['size'][$f] > $max_file_size ) {
                      $upload_message['error'][] = "$name is too large!.";
                      continue;

                  } elseif( ! in_array( strtolower( $extension ), $valid_formats ) ){
                      $upload_message['error'][] = "$name is not a valid format";
                      continue;

                  } else{



                      // If no errors, upload the file...
                      if( move_uploaded_file( $_FILES["files"]["tmp_name"][$f], $path.$new_filename )) {
                          $count++;
                          $filename = $path.$new_filename;
                          // $filetype = wp_check_filetype( basename( $filename ), null );

                          // $output = array();
                          /*
                          $output = array(
                            'total_create' => 0,
                            'account_success' => array(),
                            'account_error' => array()
                          );
                          */


                          include 'Classes/PHPExcel.php';
                            header("Content-Type:text/html; charset=utf-8");
                            //設定要被讀取的檔案，經過測試檔名不可使用中文
                            $file = $filename;
                            try {
                                $objPHPExcel = PHPExcel_IOFactory::load($file);
                            } catch(Exception $e) {
                                die('Error loading file "'.pathinfo($file,PATHINFO_BASENAME).'": '.$e->getMessage());
                            }

                          $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

                          // print_r($sheetData);

                          /*

                            $my_post = array(
                              'post_title'    => wp_strip_all_tags( $_POST['post_title'] ),
                              'post_content'  => $_POST['post_content'],
                              'post_status'   => 'publish',
                              'post_author'   => 1,
                              'post_category' => array( 8,39 )
                            );

                            // Insert the post into the database
                            wp_insert_post( $my_post );

                          */


                          foreach($sheetData as $key => $mypost){
                            if($key !==1){

                              $idObj = get_cat_ID($mypost['C']);


                              /*
                              $my_post = array(
                                'post_title'    => wp_strip_all_tags( $_POST['post_title'] ),
                                'post_content'  => $_POST['post_content'],
                                'post_status'   => 'publish',
                                'post_author'   => 1,
                                'post_category' => array( 8,39 )
                              );
                              wp_insert_post( $my_post );

                              */

                              $year  = '20'.substr($mypost['A'] ,6 ,2)."-".substr($mypost['A'] ,0 ,2)."-".substr($mypost['A'] ,3 ,2);

                              $my_post = array(
                                 'post_date' => $year,
                                'post_title' => $mypost['B'],
                                // 'post_content' => $mypost['F'],
                                'post_author'   => 1,
                                'post_status'   => 'publish',
                                'post_excerpt' => $mypost['B'],
                              );

                              $bigevent = array(274);
                              if($idObj){
                                $bigevent[] = $idObj;
                              }

                               $post_ID = wp_insert_post( $my_post );
                               wp_set_post_categories( $post_ID, $bigevent);


                               if($mypost['F']){
                                 $my_post = array(
                                      'ID'           => $post_ID,
                                      'post_content' => $mypost['F'],
                                  );
                                  wp_update_post( $my_post );
                               }

                               if($post_ID){
                                 $create++;
                               }else{
                                 $error++;
                               }
                               $all++;
                              //  echo $post_ID;

                            }
                          }




                          // Insert attachment to the database
                      }else{
                        echo "no move";
                      }/* Move upload file  */

                      $output['total']  = $create;
                      $output['all']  = $all;
                      $output['error']  = $error;
                  }
              }
          } /*  End foreach */
  } // Loop through each error then output it to the screen

// print_r($output);
  echo json_encode($output);

  // $output['total_create'] = $i - 2;


  exit();

}



function mb_fgetcsv(&$handle, $length = null, $d = ",", $e = '"') {
 $d = preg_quote($d);
 $e = preg_quote($e);
 $_line = "";
 $eof=false;
 while ($eof != true) {
  $_line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
  $itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
  if ($itemcnt % 2 == 0){
   $eof = true;
  }
 }

 $_csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($_line));

 $_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
 preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
 $_csv_data = $_csv_matches[1];

 for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
  $_csv_data[$_csv_i] = preg_replace("/^" . $e . "(.*)" . $e . "$/s", "$1", $_csv_data[$_csv_i]);
  $_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
 }

 return empty ($_line) ? false : $_csv_data;
}
