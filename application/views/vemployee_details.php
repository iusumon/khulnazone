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
			<p class="top_heading">Employee Details</p>
			<ul>
				<li><a href="#view">View List</a></li>
				<li><a href="#entryform">Enter New Employee</a></li>
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
							<td><label for="personal_id">Personal ID</label></td>
                                                        <td><input name="personal_id" id="personal_id" type="text" maxlength="15" size="30" </td>
						</tr>
                                                
						<tr>
							<td><label for="name">Name</label></td>
                                                        <td><input name="name" id="name" type="text" maxlength="100" size="50" </td>
						</tr>

                                                <tr>
							<td><label for="designation">Designation </label></td>
							<td><input name="designation" id="designation" type="text" maxlength="50" size="50"/></td>
						</tr>
                                                
                                                <tr>
							<td><label for="join_date">Joining Date </label></td>
                                                        <td><input name="join_date" id="join_date" readonly/> </td>
						</tr>
                                                
                                                <tr>
							<td><label for="last_prom_date">Last Promotion Date </label></td>
                                                        <td><input name="last_prom_date" id="last_prom_date" readonly/> </td>
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
					<input type="hidden" id="employee_id" name="employee_id"/>
				</form>		
			</div>

		</div> <!-- end tabs  -->

		<script type="text/javascript">
			var updateUrl = 'employee_details/update_data',
			deleteUrl = 'employee_details/delete_data',
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
                                
                                $("#join_date, #last_prom_date").datepicker({
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
					$("#join_date, #last_prom_date").val(prettyDate);
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
                                        var jsonStr = [];
					jsonStr = {"personal_id":$('#personal_id').val(),
                                                   "name":$('#name').val(),
                                                   "designation":$('#designation').val(),
                                                   "join_date":$('#join_date').val(),
                                                   "last_prom_date":$('#last_prom_date').val(),
                                                   "remarks":$('#remarks').val()};
					$.ajax({
						url: '<?php echo site_url("employee_details/save_data"); ?>',
						type: 'POST',
                                                dataType:'json',
						data: {'jsarray': $.toJSON(jsonStr)},
				
						success: function(response) {
							if(response.valid == "Success") {
								$('#msgDialog > p').html("Data Saved Successfully");
								$('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
								$('#employee_id').val(response.employee_id);
								//Show new records in the data table
								dataTable.fnAddData([response.employee_id, 
									$('#personal_id').val(),
									$('#name').val(),
									$('#designation').val(),
									$('#join_date').val(),
									$('#remarks').val(),
									'<a class="updateBtn" href="' + updateUrl + '/' + response.employee_id + '">Update/Edit</a>'
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
                                        show_date();
                                        $('#employee_id').val('');
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
								url: '<?php echo site_url("employee_details/delete_data"); ?>' + '/' + $('#employee_id').val(),
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
						url: '<?php echo site_url("employee_details/getById") . "/"; ?>' + updateId,
						dataType: 'json',
						success: function(response){
							$('#employee_id').val(updateId);
							$('#personal_id').val(response.emp_id);
							$('#name').val(response.name);
							$('#designation').val(response.designation);
							$('#join_date').val(response.join_date);
							$('#last_prom_date').val(response.last_prom_date);
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
                                        jsonStr = {"employee_id":$('#employee_id').val(),
                                                   "personal_id":$('#personal_id').val(),
                                                   "name":$('#name').val(),
                                                   "designation":$('#designation').val(),
                                                   "join_date":$('#join_date').val(),
                                                   "last_prom_date":$('#last_prom_date').val(),
                                                   "remarks":$('#remarks').val()};
                                        $.ajax({
						url: '<?php echo site_url("employee_details/update_data"); ?>',
						type: 'POST',
						dataType:'json',
						data: {'jsarray': $.toJSON(jsonStr)},
				
						success: function(response) {
							$('#msgDialog > p').html(response.valid);
							if(response.valid == "Success") {
                                                                $('#msgDialog > p').html("Data Updated Successfully");
								$('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
                                                                $('#employee_id').val(response.employee_id);
                                                                
                                                                //Update the values in Data Table after updating records
                                                                dataTable.fnUpdate([$('#employee_id').val(), 
                                                                                $('#personal_id').val(),
                                                                                $('#name').val(),
                                                                                $('#designation').val(),
                                                                                $('#join_date').val(),
                                                                                $('#remarks').val(),
                                                                                '<a class="updateBtn" href="' + updateUrl + '/' + $('#employee_id').val() + '">Update/Edit</a>'
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