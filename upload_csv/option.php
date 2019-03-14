<?php




add_action('admin_menu', 'baw_create_csv_upload');

function baw_create_csv_upload() {

    //create new top-level menu
    add_menu_page('Upload CSV', 'Upload CSV', 'moderate_comments', 'xeex.php', 'ruibian_settings_csv_upload','');

    //call register settings function
    add_action( 'admin_init', 'register_csv_upload' );
}




function register_csv_upload() {


}

function ruibian_settings_csv_upload() {
    $ver = '20180505';
    wp_enqueue_script( 'csv_upload', plugins_url( 'js/upload.js', __FILE__ ), array(), $ver, true );

    ?>




    <div class="wrap">
        <div id="app" class="container">
          <div   id="csv_modal">


                <div class="row">
                  <div class="form-group  col-sm-12">
                    <div class="upload-form" >
                         <div class= "upload-response" ></div>
                         <div class = "form-group  upload_input"  id="csv_file_upload">
                             <label><?php __('Select Files:', 'cvf-upload'); ?></label>
                             <input type = "file" name = "files[]" accept = ".xlsx" class = "files-data form-control" multiple />
                         </div>
                    </div><!-- upload-form -->

                    <style media="screen">
                      #info .lix{margin-bottom:10px;}
                    </style>

                    <div id="info">
                      <div class="success">
                        <div class="clearfix">
                          <div class="lix">成功匯入 (<span id="success">0</span>)</div>
                          <div class="lix">未匯入 (<span id="error">0</span>)</div>
                          <div class="lix">Total (<span id="total">0</span>)</div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>




              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info  pull-left"  id="csv_upload"  data-loading-text="Sending..." >Send</button>
              </div>
          </div><!-- /.modal -->


          <script type = "text/javascript">
           var xajax = '<?php echo admin_url( 'admin-ajax.php' );?>';
          </script>
        </div>
    </div>



<!--
    <script src="http://localhost:35729/livereload.js"></script>
    -->

<?php } ?>
