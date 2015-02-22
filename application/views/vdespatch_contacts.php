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
			<p class="top_heading">Despatch Contacts</p>
			<ul>
				<li><a href="#view">View List</a></li>
				<li><a href="#entryform">Enter New Contact </a></li>
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
							<td><label for="contact_name"> Name</label></td>
                                                        <td><input name="contact_name" id="contact_name" type="text" maxlength="50" size="30" value="<?php echo set_value('GroupName'); ?>"/></td>
						</tr>

                                                <tr>
							<td><label for="address">Address </label></td>
							<td><input name="address" id="address" type="text" maxlength="150" size="50"/></td>
						</tr>
                                                
                                                <tr>
							<td><label for="phone">Phone </label></td>
							<td><input name="phone" id="phone" type="text" maxlength="50" size="30"/></td>
						</tr>
                                                
                                                <tr>
							<td><label for="mobile">mobile </label></td>
							<td><input name="mobile" id="mobile" type="text" maxlength="50" size="30"/></td>
						</tr>
                                                
                                                <tr>
							<td><label for="fax">Fax No. </label></td>
							<td><input name="fax" id="fax" type="text" maxlength="50" size="30"/></td>
						</tr>
                                                
                                                <tr>
							<td><label for="email">E-mail </label></td>
							<td><input name="email" id="email" type="text" maxlength="50" size="30"/></td>
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
					<input type="hidden" id="contact_id" name="contact_id"/>
				</form>		
			</div>

		</div> <!-- end tabs  -->

		<script type="text/javascript">
			var updateUrl = 'despatch_contacts/update_data',
			deleteUrl = 'despatch_contacts/delete_data',
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
							$('#contact_name').focus();
						}
					}
				});
	
				//-----------------------------------------------------------------
	
				$('#entrySubmit').button().click(function() {
                                        var jsonStr = [];
					jsonStr = {"contact_name":$('#contact_name').val(),
                                                   "address":$('#address').val(),
                                                   "phone":$('#phone').val(),
                                                   "mobile":$('#mobile').val(),
                                                   "fax":$('#fax').val(),
                                                   "email":$('#email').val(),
                                                   "remarks":$('#remarks').val()};
					$.ajax({
						url: '<?php echo site_url("despatch_contacts/save_data"); ?>',
						type: 'POST',
                                                dataType:'json',
						data: {'jsarray': $.toJSON(jsonStr)},
				
						success: function(response) {
							if(response.valid == "Success") {
								$('#msgDialog > p').html("Data Saved Successfully");
								$('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
								$('#contact_id').val(response.contact_id);
								//Show new records in the data table
								dataTable.fnAddData([response.contact_id, 
									$('#contact_name').val(),
									$('#address').val(),
									$('#phone').val(),
									$('#mobile').val(),
									$('#fax').val(),
									$('#email').val(),
									$('#remarks').val(),
									'<a class="updateBtn" href="' + updateUrl + '/' + response.contact_id + '">Update/Edit</a>'
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
                                        $('#contact_id').val('');
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
								{sClass:"center"},
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
								url: '<?php echo site_url("despatch_contacts/delete_data"); ?>' + '/' + $('#contact_id').val(),
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
						url: '<?php echo site_url("despatch_contacts/getById") . "/"; ?>' + updateId,
						dataType: 'json',
						success: function(response){
							$('#contact_id').val(updateId);
							$('#contact_name').val(response.name);
							$('#address').val(response.address);
							$('#phone').val(response.phone);
							$('#mobile').val(response.mobile);
							$('#fax').val(response.fax);
							$('#email').val(response.email);
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
                                        jsonStr = {"contact_id":$('#contact_id').val(),
                                                   "contact_name":$('#contact_name').val(),
                                                   "address":$('#address').val(),
                                                   "phone":$('#phone').val(),
                                                   "mobile":$('#mobile').val(),
                                                   "fax":$('#fax').val(),
                                                   "email":$('#email').val(),
                                                   "remarks":$('#remarks').val()};
                                        $.ajax({
						url: '<?php echo site_url("despatch_contacts/update_data"); ?>',
						type: 'POST',
						dataType:'json',
						data: {'jsarray': $.toJSON(jsonStr)},
				
						success: function(response) {
							$('#msgDialog > p').html(response.valid);
							if(response.valid == "Success") {
                                                                $('#msgDialog > p').html("Data Updated Successfully");
								$('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
                                                                $('#contact_id').val(response.contact_id);
                                                                
                                                                //Update the values in Data Table after updating records
                                                                dataTable.fnUpdate([$('#contact_id').val(), 
                                                                                $('#contact_name').val(),
                                                                                $('#address').val(),
                                                                                $('#phone').val(),
                                                                                $('#mobile').val(),
                                                                                $('#fax').val(),
                                                                                $('#email').val(),
                                                                                $('#remarks').val(),
                                                                                '<a class="updateBtn" href="' + updateUrl + '/' + $('#contact_id').val() + '">Update/Edit</a>'
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