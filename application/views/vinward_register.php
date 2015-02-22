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
            <p class="top_heading">Despatch Inward Register</p>
            <ul>
                <li><a href="#view">View List</a></li>
                <li><a href="#entryform">Enter New Record</a></li>
            </ul>

            <div id="view">
                <?php echo $table_data; ?>

                <!--Table for View Tab Buttons (Date Filter/Exit)-->
                <table class="left">
                    <tr>
                        <td> Show Between <input type="text" name="date_filter" id="date_filter" readonly/></td>
                        <td> And <input type="text" name="date_filter" id="date_filter1" readonly/></td>
                        <td><input type="button" name="filter" id="filter" value="Show Report" /></td>
                        <td>Enter Serial: <input type="text" name="serial_id_filter" id="serial_id_filter" /></td>
                    </tr>
                    <tr><td> <input type="button" name="Exit" id="close" value="Exit"/></td> <td></td><td></td> <td></td></tr>
                </table>
            </div>

            <div align="center" id="entryform">
                <form action="" method="POST">
                    <table> 
                        <tr>
                            <td><label for="inw_date"> Date </label></td>
                            <td><input name="inw_date" id="inw_date" readonly/> </td>
                        </tr>

                        <tr>
                            <td><label for="sender">Sender</label></td>
                            <td><div id="sender"> </div></td>
<!--                            <td><input name="sender" id="sender" type="text" maxlength="150" size="50" </td>-->
                        </tr>

                        <tr>
                            <td><label for="category">Category</label></td>
                            <td><div id="category"> </div></td>
                        </tr>


                        <tr>
                            <td><label for="particulars">Particulars </label></td>
                            <td><input name="particulars" id="particulars" type="text" maxlength="255" size="70"/></td>
                        </tr>

                        <tr>
                            <td><label for="employee">Designated Employee </label></td>
                            <td><div id="employee"> </div></td>
                        </tr>

                        <tr>
                            <td><label for="remarks">Remarks </label></td>
                            <td><input name="remarks" id="remarks" type="text" maxlength="100" size="30"/></td>
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
                    <input type="hidden" id="inward_id" name="inward_id"/>
                </form>		
            </div>

        </div> <!-- end tabs  -->

        <script type="text/javascript">
            var updateUrl = 'inward_register/update_data',
            deleteUrl = 'inward_register/delete_data',
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
                                
                $("#inw_date, #date_filter, #date_filter1").datepicker({
                    dateFormat: 'dd-mm-yy'
                });
				
                //---------------------------------------------------------------
				
                display_data();
                //---------------------------------------------------------------
                //To show the current date in the Date Field
                function show_date(){
                    var myDate = new Date();
                    var month = myDate.getMonth() + 1;
                    var prettyDate =  myDate.getDate() + '-' + month + '-' + myDate.getFullYear();
                    $("#inw_date, #date_filter, #date_filter1").val(prettyDate);
                }
	
                //--------------------------------------------------------------
                $('#msgDialog').dialog({
                    autoOpen:false,
		
                    buttons: {
                        'OK': function() {
                            $(this).dialog('close');
                            $('#name').focus();
                        }
                    }
                });
	
                //-----------------------------------------------------------------
	
                $('#entrySubmit').button().click(function() {
                    var is_valid_test = false;
                    if($('#category_input').val() != $('#category_hidden').val()){
                        is_valid_test = true;
                    } 
                    
                    if(is_valid_test == true) {
                            if($('#employee_input').val() != $('#employee_hidden').val()){
                                is_valid_test = true;
                            } else {
                                is_valid_test = false;
                            }
                    }
                    
                    if(is_valid_test == true){
                        
                        var jsonStr = [];
                        jsonStr = {"inward_id":$('#inward_id').val(),
                            "inw_date":$('#inw_date').val(),
                            "sender":$('#sender_input').val(),
                            "category_id":$('#category_hidden').val(),
                            "particulars":$('#particulars').val(),
                            "emp_id":$('#employee_hidden').val(),
                            "remarks":$('#remarks').val()};
                        $.ajax({
                            url: '<?php echo site_url("inward_register/save_data"); ?>',
                            type: 'POST',
                            dataType:'json',
                            data: {'jsarray': $.toJSON(jsonStr)},

                            success: function(response) {
                                if(response.valid == "Success") {
                                    $('#msgDialog > p').html(response.inward_id + " - Saved Successfully");
                                    $('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
                                    $('#inward_id').val(response.inward_id);
                                    //Show new records in the data table
                                    dataTable.fnAddData([response.inward_id, 
                                        $('#inw_date').val(),
                                        $('#sender_input').val(),
                                        $('#category_input').val(),
                                        $('#particulars').val(),
                                        $('#remarks').val(),
                                        '<a class="updateBtn" href="' + updateUrl + '/' + response.inward_id + '">Update/Edit</a>'
                                    ]);
                                    clear_field();
                                } else {
                                    $('#msgDialog > p').html(response.valid);
                                    $('#msgDialog').dialog('option', 'title', 'Warning').dialog('open');
                                }
                            }
                        });
                    } else {
                        $('#msgDialog > p').html("Enter Correct Category or Employee");
                        $('#msgDialog').dialog('option', 'title', 'Warning').dialog('open');
                    }
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
                    show_date();
                    $('#inward_id').val('');
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
                    $('#category').flexbox('inward_register/get_category_name', { selectBehavior: false, watermark: 'Enter/Select Category', paging: true, allowInput: true, autoCompleteFirstMatch: false});
                    $('#category_input').css('width', '300px');
                                        
                    $('#employee').flexbox('inward_register/get_employee_name', { selectBehavior: false, watermark: 'Enter/Select Employee', paging: true, allowInput: true, autoCompleteFirstMatch: false});
                    $('#employee_input').css('width', '300px');
                    
                    $('#sender').flexbox('inward_register/get_sender_name', { selectBehavior: false, watermark: 'Enter/Select Sender', paging: true, allowInput: true, autoCompleteFirstMatch: false});
                    $('#sender_input').css('width', '300px');
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
                                url: '<?php echo site_url("inward_register/delete_data"); ?>' + '/' + $('#inward_id').val(),
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
                        url: '<?php echo site_url("inward_register/getById") . "/"; ?>' + updateId,
                        dataType: 'json',
                        success: function(response){
                            $('#inward_id').val(updateId);
                            $('#inw_date').val(response.date);
                            $('#sender_input').val(response.sender);
                            $('#category_input').val(response.category_name);
                            $('#category_hidden').val(response.category_id);
                            $('#particulars').val(response.particulars);
                            $('#employee_input').val(response.emp_name);
                            $('#employee_hidden').val(response.emp_id);
                                                        
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
                    var is_valid_test = false;
                    if($('#category_input').val() != $('#category_hidden').val()){
                        is_valid_test = true;
                    } 
                    
                    if(is_valid_test == true) {
                            if($('#employee_input').val() != $('#employee_hidden').val()){
                                is_valid_test = true;
                            } else {
                                is_valid_test = false;
                            }
                    }
                    
                    if(is_valid_test == true) {
                        var jsonStr = [];
                        jsonStr = {"inward_id":$('#inward_id').val(),
                            "inw_date":$('#inw_date').val(),
                            "sender":$('#sender_input').val(),
                            "category_id":$('#category_hidden').val(),
                            "particulars":$('#particulars').val(),
                            "emp_id":$('#employee_hidden').val(),
                            "remarks":$('#remarks').val()};
                        $.ajax({
                            url: '<?php echo site_url("inward_register/update_data"); ?>',
                            type: 'POST',
                            dataType:'json',
                            data: {'jsarray': $.toJSON(jsonStr)},

                            success: function(response) {
                                $('#msgDialog > p').html(response.valid);
                                if(response.valid == "Success") {
                                    $('#msgDialog > p').html("Data Updated Successfully");
                                    $('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
                                    $('#inward_id').val(response.inward_id);

                                    //Update the values in Data Table after updating records
                                    dataTable.fnUpdate([$('#inward_id').val(), 
                                        $('#inw_date').val(),
                                        $('#sender_input').val(),
                                        $('#category_input').val(),
                                        $('#particulars').val(),
                                        $('#remarks').val(),
                                        '<a class="updateBtn" href="' + updateUrl + '/' + $('#inward_id').val() + '">Update/Edit</a>'
                                    ], update_position);
                                    clear_field();
                                } else {
                                    $('#msgDialog').dialog('option', 'title', 'Warning').dialog('open');
                                }
                            }
                        });
                     } else {
                        $('#msgDialog > p').html("Enter Correct Category or Employee");
                        $('#msgDialog').dialog('option', 'title', 'Warning').dialog('open');
                    }
			
                });
                //--------------------------------------------------------------------------------
                $('#delete').button().click(function() {
                    $('#delConfDialog').dialog('open');
                });
                //--------------------------------------------------------------------------------
                //To filter for showing data in the view tab
                $("#filter").button().click(function(){
                    $("#filter").button("disable");
                    dataTable.fnClearTable(true);
                    $('#records tbody tr').remove();

                    $.ajax({
                        url: '<?php echo site_url("inward_register/filter_data"); ?>' + '/' + $('#date_filter').val() + '/' + $('#date_filter1').val(),
                        dataType: 'json',
                        success: function(response){
                            if(response.length > 0){
                                for(var i in response) {
                                    dataTable.fnAddData([
                                        response[i].id,
                                        response[i].date,
                                        response[i].sender,
                                        response[i].category_name,
                                        response[i].particulars,
                                        response[i].remarks,
                                        response[i].Action
                                    ], false);
                                    dataTable.fnDraw(true);
                                    $("#filter").button("enable");
                                }
                            } else {
                                $("#filter").button("enable");
                            }
                        }
                    });
                });
                //--------------------------------------------------------------------------------
                $('#serial_id_filter').keyup(function(e) {
                    if (e.keyCode == 13) {
                        
                        if($('#serial_id_filter').val().length > 0) {
                            $("#serial_id_filter").attr("disabled", "disabled");
                            dataTable.fnClearTable(true);
                            $('#records tbody tr').remove();

                            $.ajax({
                                url: '<?php echo site_url("inward_register/filter_serial_id"); ?>' + '/' + $('#serial_id_filter').val(),
                                dataType: 'json',
                                success: function(response){
                                    if(response.length > 0){
                                        for(var i in response) {
                                            dataTable.fnAddData([
                                                response[i].id,
                                                response[i].date,
                                                response[i].sender,
                                                response[i].category_name,
                                                response[i].particulars,
                                                response[i].remarks,
                                                response[i].Action
                                            ], false);
                                            dataTable.fnDraw(true);
                                            $("#serial_id_filter").removeAttr("disabled");
                                        }
                                    } else {
                                        $("#serial_id_filter").removeAttr("disabled");
                                    }
                                }
                            })
                        }
                    }
                });    
                //--------------------------------------------------------------------------------
            })
        </script>
    </body>
</html>