<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $this->session->userdata('prj_name'); ?></title>
        <?php $this->load->view('jquery_include'); ?>
    </head>

    <body>

        <div id="msgDialog"><p></p></div>

        <div id="delConfDialog" title="Confirm">
            <p>Are you sure?</p>
        </div>

        <div id="tabs">
            <p class="top_heading">Manager's Information</p>
            <ul>
                <li><a href="#view">View List</a></li>
                <li><a href="#entryform">New </a></li>
            </ul>

            <div id="view">
                <?php echo $table_data; ?>

                <table class="center">
                    <tr><td> <input type="button" name="Exit" id="close" value="Exit"/></td></tr>
                </table>
            </div>

            <div align="center" id="entryform">
                <form action="" method="POST">

                    <table> 

                        <tr>
                            <td><label for="manager_name"> Name</label></td>
                            <td><input name="manager_name" id="manager_name" type="text" maxlength="50" size="30" value="<?php echo set_value('GroupName'); ?>"/></td>
                        </tr>

                        <tr>
                            <td><label for="designation">Designation </label></td>
                            <td><div id="designation"> </div></td>
                        </tr>

                        <tr>
                            <td><label for="place_of_posting">Place of Posting </label></td>
                            <td><div id="place_of_posting"> </div></td>
                            
                        </tr>

                        <tr>
                            <td><label for="mobile">mobile </label></td>
                            <td><input name="mobile" id="mobile" type="text" maxlength="50" size="30"/></td>
                        </tr>

                        <tr>
                            <td><label for="remarks">Remarks </label></td>
                            <td><input name="remarks" id="remarks" type="text" maxlength="50" size="30"/></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td> <input type="button" name="entrySubmit" id="entrySubmit" value="Save"/></td>
                            <td> <input type="button" name="update" id="update" value="Update"/></td>
                            <td> <input type="button" name="delete" id="delete" value="Delete"/></td>
                            <td><input type="button" name="clear" id="clear" value="Clear"/></td>
                            <td> <input type="button" name="Exit" id="exit" value="Exit"/></td>
                        </tr>
                    </table>
                    <input type="hidden" id="manager_id" name="manager_id"/>
                </form>		
            </div>

        </div> <!-- end tabs  -->

        <script type="text/javascript">
            var updateUrl = 'manager_info/update_data',
            deleteUrl = 'manager_info/delete_data',
            delHref,
            updateHref,
            updateId,
            update_position,
            dataTable;

            $(function() {
                //---------------------------------------------------------------
                $('#tabs ').tabs({
                    fx: {height: 'toggle', opacity: 'toggle'}
		
                }).css('width', '900px').css('margin', '0 auto');
				
                //---------------------------------------------------------------
				
                display_data();
	
                //--------------------------------------------------------------
                $('#msgDialog').dialog({
                    autoOpen:false,
		
                    buttons: {
                        'OK': function() {
                            $(this).dialog('close');
                            $('#manager_name').focus();
                        }
                    }
                });
	
                //-----------------------------------------------------------------
	
                $('#entrySubmit').button().click(function() {
                    var jsonStr = [];
                    jsonStr = {"manager_name":$('#manager_name').val(),
                        "designation_id":$('#designation_hidden').val(),
                        "place_of_posting_id":$('#place_of_posting_hidden').val(),
                        "mobile":$('#mobile').val(),
                        "remarks":$('#remarks').val()};
                    $.ajax({
                        url: '<?php echo site_url("manager_info/save_data"); ?>',
                        type: 'POST',
                        dataType:'json',
                        data: {'jsarray': $.toJSON(jsonStr)},
				
                        success: function(response) {
                            if(response.valid == "Success") {
                                $('#msgDialog > p').html("Data Saved Successfully");
                                $('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
                                $('#manager_id').val(response.manager_id);
                                //Show new records in the data table
                                dataTable.fnAddData([response.manager_id, 
                                    $('#manager_name').val(),
                                    $('#designation_input').val(),
                                    $('#place_of_posting_input').val(),
                                    $('#mobile').val(),
                                    $('#remarks').val(),
                                    '<a class="updateBtn" href="' + updateUrl + '/' + response.manager_id + '">Update/Edit</a>'
                                ]);
                                clear_field();
                            } else {
                                $('#msgDialog > p').html(response.valid);
                                $('#msgDialog').dialog('option', 'title', 'Warning').dialog('open');
                            }
                        }
                    });
			
                });
	
                //----------------------------------------------------------------------
	
                $('#clear').button().click(function() {
                    clear_field();
		
                });
				
                //-----------------------------------------------------------------------
	
                $('#exit').button().click(function() {
                    window.location.href = "<?php echo site_url("login/load_main"); ?>";
                });
	
                //-----------------------------------------------------------------------
	
                $('#close').button().click(function() {
                    window.location.href = "<?php echo site_url("login/load_main"); ?>";
                });
	
                //----------------------------------------------------------------------
	
                function clear_field() {
                    //to clear the current form field
                    $(':text').val('');
                    $('#manager_id').val('');
                    $("#update").button("disable");
                    $("#delete").button("disable");
                    $("#entrySubmit").button("enable");
                }
	
                //----------------------------------------------------------------------
	
                function display_data(){
                    clear_field();
                    if(typeof dataTable == 'undefined') {
                        dataTable = $('#records').dataTable({
                            "bJQueryUI": true,
                            "sPaginationType": "full_numbers",
                            "bLengthChange": true,
                            "bAutoWidth": false,
                            "aoColumns":[
                                {sClass:"left"},
                                {sClass:"left"},
                                {sClass:"left"},
                                {sClass:"left"},
                                {sClass:"left"},
                                {sClass:"left"},
                                {sClass:"center"}
                            ]
                        });
                    }
                    
                    //to show list for auto complete Designation Text Box
                    $('#designation').flexbox('manager_info/get_designation', { selectBehavior: false, watermark: 'Enter/Select Designation', paging: true, allowInput: true, autoCompleteFirstMatch: false});
                    $('#designation_ctr').css('width', '500px');
                    $('#designation_input').css('width', '300px');
                    
                    //to show list for auto complete Designation Text Box
                    $('#place_of_posting').flexbox('manager_info/get_place_of_posting', { selectBehavior: false, watermark: 'Enter/Select Branch', paging: true, allowInput: true, autoCompleteFirstMatch: false});
                    $('#place_of_posting_ctr').css('width', '500px');
                    $('#place_of_posting_input').css('width', '300px');
                }
	
                //----------------------------------------------------------------------
                $('#delConfDialog').dialog({
                    autoOpen:false,
                    buttons: {
                        'NO': function() {
                            $(this).dialog('close');
                        },
			
                        'Yes': function() {
                            $(this).dialog('close');
				
                            $.ajax({
                                url: '<?php echo site_url("manager_info/delete_data"); ?>' + '/' + $('#manager_id').val(),
                                success: function(response) {
                                    if(response == 'Records deleted successfully'){
                                        $('a[href=' + delHref + ']').parents('tr').fadeOut('slow', function() {
                                            cur_tr = this;
                                            dataTable.fnDeleteRow(cur_tr);
                                        });
                                        $('#msgDialog > p').html(response);
                                        $('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
                                    } else {
                                        $('#msgDialog > p').html(response);
                                        $('#msgDialog').dialog('option', 'title', 'Failed').dialog('open');
                                    }
                                }
                            });
                            clear_field();
                        }
                    }
                });
                //------------------------------------------------------------------------------
	
                $("#records").delegate("a.deleteBtn", "click", function() {
                    delHref = $(this).attr('href');
                    $('#delConfDialog').dialog('open');
                    return false;
                });
	
                //--------------------------------------------------------------------------------

                $('#tabs ul a[href="#entryform"]').click(function(){
                    clear_field();
                });
                //--------------------------------------------------------------------------------
				
                $("#records").delegate("a.updateBtn", "click", function() {
                    update_position = dataTable.fnGetPosition($(this).parents('tr')[0]);
                    delHref = $(this).attr('href');
                    updateId = $(this).parents('tr').children('td:eq(0)').text();
                    $.ajax({
                        url: '<?php echo site_url("manager_info/getById") . "/"; ?>' + updateId,
                        dataType: 'json',
                        success: function(response){
                            $('#manager_id').val(updateId);
                            $('#manager_name').val(response.name);
                            
                            $('#designation_hidden').val(response.designation_id);
                            $('#designation_input').val(response.designation);
                            
                            $('#place_of_posting_hidden').val(response.place_of_posting_id);
                            $('#place_of_posting_input').val(response.place_of_posting);
                            
                            $('#mobile').val(response.mobile_no);
                            $('#remarks').val(response.remarks);
                        }
                    });
                    $("#update").button("enable");
                    $("#delete").button("enable");
                    $("#entrySubmit").button("disable");
                    $('#tabs').tabs('select', 1);
                    return false;
                });
	
                //--------------------------------------------------------------------------------
                $('#update').button().click(function() {
                    var jsonStr = [];
                    jsonStr = {"manager_id":$('#manager_id').val(),
                        "manager_name":$('#manager_name').val(),
                        "designation_id":$('#designation_hidden').val(),
                        "place_of_posting_id":$('#place_of_posting_hidden').val(),
                        "mobile":$('#mobile').val(),
                        "remarks":$('#remarks').val()};
                    $.ajax({
                        url: '<?php echo site_url("manager_info/update_data"); ?>',
                        type: 'POST',
                        dataType:'json',
                        data: {'jsarray': $.toJSON(jsonStr)},
				
                        success: function(response) {
                            $('#msgDialog > p').html(response.valid);
                            if(response.valid == "Success") {
                                $('#msgDialog > p').html("Data Updated Successfully");
                                $('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
                                $('#manager_id').val(response.manager_id);
                                                                
                                //Update the values in Data Table after updating records
                                dataTable.fnUpdate([$('#manager_id').val(), 
                                    $('#manager_name').val(),
                                    $('#designation_input').val(),
                                    $('#place_of_posting_input').val(),
                                    $('#mobile').val(),
                                    $('#remarks').val(),
                                    '<a class="updateBtn" href="' + updateUrl + '/' + $('#manager_id').val() + '">Update/Edit</a>'
                                ], update_position);
                                clear_field();
                            } else {
                                $('#msgDialog').dialog('option', 'title', 'Warning').dialog('open');
                            }
                        }
                    });
			
                });
                //--------------------------------------------------------------------------------
                $('#delete').button().click(function() {
                    $('#delConfDialog').dialog('open');
                });
				
            })
        </script>
    </body>
</html>