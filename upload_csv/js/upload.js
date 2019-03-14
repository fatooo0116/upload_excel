/*  CSV Upload  */


(function($){


$("#csv_upload").live("click",function(){
                    var fd = new FormData();
                    var files_data = $('#csv_file_upload .files-data'); // The <input type="file" /> field

                    // Loop through each data and create an array file[] containing our files data.
                    $.each($(files_data), function(i, obj) {
                        $.each(obj.files,function(j,file){
                            fd.append('files[' + j + ']', file);
                        })
                    });



                    // our AJAX identifier
                    fd.append('action', 'csv_upload_action');


                    // if($("#admin_accmember").attr('cur_acc')!=''){
                      // fd.append('acc', $("#admin_accmember").attr('cur_acc'));
                    // }


                    $.ajax({
                        type: 'POST',
                        url: xajax,
                        data: fd,
                        contentType: false,
                        processData: false,
                        success: function(response){

                            console.log(response);
                            var res = JSON.parse(response);
                            console.log(res);
                            $("#success").text(res.all);
                            $("#error").text(res.error);
                            $("#total").text(res.total);
                        }
                    });
});

})(jQuery);
