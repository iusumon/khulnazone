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
            <p class="top_heading">Despatch Outward Register</p>
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
                        <td>Serial: <input type="text" name="serial_id_filter" id="serial_id_filter" /></td>
                        <td>Receipt No: <input type="text" name="serial_receipt_no" id="serial_receipt_no" /></td>
                    </tr>
                    <tr><td> <input type="button" name="Exit" id="close" value="Exit"/></td> <td></td><td></td> <td></td></tr>
                </table>
            </div>

            <div align="center" id="entryform">
                <form action="" method="POST">
                    <table> 
                        <tr>
                            <td><label for="out_date"> Date </label></td>
                            <td><input name="out_date" id="out_date" readonly/> </td>
                        </tr>

                        <tr>
                            <td><label for="recipient">Recipient</label></td>
                            <td><div id="recipient"> </div></td>
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
                            <td><label for="service_charge">Service charge </label></td>
                            <td><input name="service_charge" id="service_charge" type="text" maxlength="10" size="20"/></td>
                        </tr>
                        
                        <tr>
                            <td><label for="receipt_no">Courier Receipt No </label></td>
                            <td><input name="receipt_no" id="receipt_no" type="text" maxlength="10" size="20"/></td>
                        </tr>

                        <tr>
                            <td><label for="agent">Courier/Postal Agent </label></td>
                            <td><div id="agent"> </div></td>
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
                    <input type="hidden" id="outward_id" name="outward_id"/>
                </form>		
            </div>

        </div> <!-- end tabs  -->

        <script type="text/javascript">
            var updateUrl = 'outward_register/update_data',
            deleteUrl = 'outward_register/delete_data',
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
                                
                $("#out_date, #date_filter, #date_filter1").datepicker({
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
                    $("#out_date, #date_filter, #date_filter1").val(prettyDate);
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
                            if($('#agent_input').val() != $('#agent_hidden').val()){
                                is_valid_test = true;
                            } else {
                                is_valid_test = false;
                            }
                    }
                    
                    if(is_valid_test == true){
                        
                        var jsonStr = [];
                        jsonStr = {"outward_id":$('#outward_id').val(),
                            "out_date":$('#out_date').val(),
                            "recipient":$('#recipient_input').val(),
                            "category_id":$('#category_hidden').val(),
                            "particulars":$('#particulars').val(),
                            "service_charge":$('#service_charge').val(),
                            "receipt_no":$('#receipt_no').val(),
                            "agent_id":$('#agent_hidden').val(),
                            "remarks":$('#remarks').val()};
                        $.ajax({
                            url: '<?php echo site_url("outward_register/save_data"); ?>',
                            type: 'POST',
                            dataType:'json',
                            data: {'jsarray': $.toJSON(jsonStr)},

                            success: function(response) {
                                if(response.valid == "Success") {
                                    $('#msgDialog > p').html(response.outward_id + " - Saved Successfully");
                                    $('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
                                    $('#outward_id').val(response.outward_id);
                                    //Show new records in the data table
                                    dataTable.fnAddData([response.outward_id, 
                                        $('#out_date').val(),
                                        $('#recipient_input').val(),
                                        $('#category_input').val(),
                                        $('#particulars').val(),
                                        $('#receipt_no').val(),
                                        $('#agent_input').val(),
                                        '<a class="updateBtn" href="' + updateUrl + '/' + response.outward_id + '">Update/Edit</a>'
                                    ]);
                                    clear_field();
                                } else {
                                    $('#msgDialog > p').html(response.valid);
                                    $('#msgDialog').dialog('option', 'title', 'Warning').dialog('open');
                                }
                            }
                        });
                    } else {
                        $('#msgDialog > p').html("Enter Correct Category or Agent");
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
                    $('#outward_id').val('');
                    $('#service_charge').val(0);
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
                                {sClass:"left"},
                                {sClass:"center"}
                            ]
                        });
                    }
                    
                    $('#recipient').flexbox('outward_register/get_recipient_name', { selectBehavior: false, watermark: 'Enter/Select Recipient', paging: true, allowInput: true, autoCompleteFirstMatch: false});
                    $('#recipient_input').css('width', '300px');
                    
                    $('#category').flexbox('outward_register/get_category_name', { selectBehavior: false, watermark: 'Enter/Select Category', paging: true, allowInput: true, autoCompleteFirstMatch: false});
                    $('#category_input').css('width', '300px');
                                        
                    $('#agent').flexbox('outward_register/get_agent_name', { selectBehavior: false, watermark: 'Enter/Select Agent', paging: true, allowInput: true, autoCompleteFirstMatch: false});
                    $('#agent_input').css('width', '300px');
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
                                url: '<?php echo site_url("outward_register/delete_data"); ?>' + '/' + $('#outward_id').val(),
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
                        url: '<?php echo site_url("outward_register/getById") . "/"; ?>' + updateId,
                        dataType: 'json',
                        success: function(response){
                            $('#outward_id').val(updateId);
                            $('#out_date').val(response.date);
                            $('#recipient_input').val(response.recipient);
                            $('#category_input').val(response.category_name);
                            $('#category_hidden').val(response.category_id);
                            $('#particulars').val(response.particulars);
                            $('#service_charge').val(response.service_charge);
                            $('#receipt_no').val(response.receipt_no);
                            $('#agent_input').val(response.agent_name);
                            $('#agent_hidden').val(response.agent_id);
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
                            if($('#agent_input').val() != $('#agent_hidden').val()){
                                is_valid_test = true;
                            } else {
                                is_valid_test = false;
                            }
                    }
                    
                    if(is_valid_test == true) {
                        var jsonStr = [];
                        jsonStr = {"outward_id":$('#outward_id').val(),
                            "out_date":$('#out_date').val(),
                            "recipient":$('#recipient_input').val(),
                            "category_id":$('#category_hidden').val(),
                            "particulars":$('#particulars').val(),
                            "service_charge":$('#service_charge').val(),
                            "receipt_no":$('#receipt_no').val(),
                            "agent_id":$('#agent_hidden').val(),
                            "remarks":$('#remarks').val()};
                        $.ajax({
                            url: '<?php echo site_url("outward_register/update_data"); ?>',
                            type: 'POST',
                            dataType:'json',
                            data: {'jsarray': $.toJSON(jsonStr)},

                            success: function(response) {
                                $('#msgDialog > p').html(response.valid);
                                if(response.valid == "Success") {
                                    $('#msgDialog > p').html("Data Updated Successfully");
                                    $('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
                                    $('#outward_id').val(response.outward_id);

                                    //Update the values in Data Table after updating records
                                    dataTable.fnUpdate([$('#outward_id').val(), 
                                        $('#out_date').val(),
                                        $('#recipient_input').val(),
                                        $('#category_input').val(),
                                        $('#particulars').val(),
                                        $('#receipt_no').val(),
                                        $('#agent_input').val(),
                                        '<a class="updateBtn" href="' + updateUrl + '/' + $('#outward_id').val() + '">Update/Edit</a>'
                                    ], update_position);
                                    clear_field();
                                } else {
                                    $('#msgDialog').dialog('option', 'title', 'Warning').dialog('open');
                                }
                            }
                        });
                     } else {
                        $('#msgDialog > p').html("Enter Correct Category or Agent");
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
                        url: '<?php echo site_url("outward_register/filter_data"); ?>' + '/' + $('#date_filter').val() + '/' + $('#date_filter1').val(),
                        dataType: 'json',
                        success: function(response){
                            if(response.length > 0){
                                for(var i in response) {
                                    dataTable.fnAddData([
                                        response[i].id,
                                        response[i].date,
                                        response[i].recipient,
                                        response[i].category_name,
                                        response[i].particulars,
                                        response[i].receipt_no,
                                        response[i].name,
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
                                url: '<?php echo site_url("outward_register/filter_serial_id"); ?>' + '/' + $('#serial_id_filter').val(),
                                dataType: 'json',
                                success: function(response){
                                    if(response.length > 0){
                                        for(var i in response) {
                                            dataTable.fnAddData([
                                                response[i].id,
                                                response[i].date,
                                                response[i].recipient,
                                                response[i].category_name,
                                                response[i].particulars,
                                                response[i].receipt_no,
                                                response[i].name,
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
                $('#serial_receipt_no').keyup(function(e) {
                    if (e.keyCode == 13) {
                        
                        if($('#serial_receipt_no').val().length > 0) {
                            $("#serial_receipt_no").attr("disabled", "disabled");
                            dataTable.fnClearTable(true);
                            $('#records tbody tr').remove();

                            $.ajax({
                                url: '<?php echo site_url("outward_register/filter_receipt_no"); ?>' + '/' + $('#serial_receipt_no').val(),
                                dataType: 'json',
                                success: function(response){
                                    if(response.length > 0){
                                        for(var i in response) {
                                            dataTable.fnAddData([
                                                response[i].id,
                                                response[i].date,
                                                response[i].recipient,
                                                response[i].category_name,
                                                response[i].particulars,
                                                response[i].receipt_no,
                                                response[i].name,
                                                response[i].Action
                                            ], false);
                                            dataTable.fnDraw(true);
                                            $("#serial_receipt_no").removeAttr("disabled");
                                        }
                                    } else {
                                        $("#serial_receipt_no").removeAttr("disabled");
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