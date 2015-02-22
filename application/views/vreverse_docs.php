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
			<p class="top_heading">Reverse Despatch Documents</p>
			<ul>
				<li><a href="#entryform">Reverse</a></li>
				<li><a href="#list">List</a></li>
			</ul>

			<div align="center" id="entryform">
				<form action="" method="POST">

					<table> 

						<tr>
							<td><label for="inward_id">Inward Register Serial</label></td>
<!--                                                        <td><input name="inward_id" id="inward_id" type="text" maxlength="50" size="50" /></td>-->
                                                        <td><div id="inward_id"> </div></td>
						</tr>

                                                <tr>
							<td><label for="rec_status">Received </label></td>
                                                        <td>
                                                            <select name="rec_status" id="rec_status" >
                                                                <option value="no">N/A</option>
                                                                <option value="ok">OK</option>
                                                            </select>
                                                        </td>
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
<!--					<input type="hidden" id="category_id" name="category_id"/>-->
				</form>		
			</div>
                        
                        <div id="list">
				<?php //echo $table_data; ?>
                            
				<table class="center">
					<tr><td> <input type="button" name="Exit" id="close" value="Exit"/></td></tr>
				</table>
			</div>

		</div> <!-- end tabs  -->

		<script type="text/javascript">
			var updateUrl = 'reverse_docs/update_data',
			deleteUrl = 'reverse_docs/delete_data',
			delHref,
			updateHref,
			updateId,
			update_position,
			dataTable;

			$(function() {
				//---------------------------------------------------------------
				$('#tabs ').tabs({
					fx: {height: 'toggle', opacity: 'toggle'}
		
				}).css('width', '600px').css('margin', '0 auto');
				
				//---------------------------------------------------------------
				
				display_data();
	
				//--------------------------------------------------------------
				$('#msgDialog').dialog({
					autoOpen:false,
		
					buttons: {
						'OK': function() {
							$(this).dialog('close');
							$('#inward_id_input').focus();
						}
					}
				});
	
				//-----------------------------------------------------------------
	
				$('#entrySubmit').button().click(function() {
//                                        var is_valid_test = false;
//                                        
//                                        if($('#inward_id_input').val() != $('#inward_id_hidden').val()){
//                                            is_valid_test = true;
//                                        } 
//                                         if(is_valid_test == true){
                                                    var jsonStr = [];
                                                    jsonStr = {"inward_id":$('#inward_id_input').val(),
                                                               "rec_status":$('#rec_status').val()};
                                                    $.ajax({
                                                            url: '<?php echo site_url("reverse_docs/save_data"); ?>',
                                                            type: 'POST',
                                                            dataType:'json',
                                                            data: {'jsarray': $.toJSON(jsonStr)},

                                                            success: function(response) {
                                                                    if(response.valid == "Success") {
                                                                            $('#msgDialog > p').html("Data Updated Successfully");
                                                                            $('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
                                                                            $('#category_id').val(response.category_id);
                                                                            //Show new records in the data table
//                                                                            dataTable.fnAddData([response.inward_id, 
//                                                                                    $('#rec_status').val(),
//                                                                                    '<a class="updateBtn" href="' + updateUrl + '/' + response.inward_id + '">Update/Edit</a>'
//                                                                            ]);
                                                                            clear_field();
                                                                    } else {
                                                                            $('#msgDialog > p').html(response.valid);
                                                                            $('#msgDialog').dialog('option', 'title', 'Warning').dialog('open');
                                                                    }
                                                            }
                                                    });
//                                              } else {
//                                                  $('#msgDialog > p').html("Enter Correct Serial");
//                                                $('#msgDialog').dialog('option', 'title', 'Warning').dialog('open');
//                                              }
			
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
                                       // $('#category_id').val('');
					$("#update").button("disable");
					$("#delete").button("disable");
					$("#entrySubmit").button("enable");
                                        $("#update").button("disable");
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
								{sClass:"center"}
							]
						});
					}
                                        
                                        $('#inward_id').flexbox('reverse_docs/get_inward_id', { selectBehavior: false, watermark: 'Enter/Select Serial', paging: true, allowInput: true, autoCompleteFirstMatch: false});
                                        $('#inward_id_input').css('width', '300px');
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
								url: '<?php echo site_url("reverse_docs/delete_data"); ?>' + '/' + $('#category_id').val(),
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
                                        $('#inward_id').val(updateId);
//					$.ajax({
//						url: '<?php echo site_url("reverse_docs/getById") . "/"; ?>' + updateId,
//						dataType: 'json',
//						success: function(response){
//						}
//					});
					$("#update").button("disable");
					$("#delete").button("enable");
					$("#entrySubmit").button("enable");
					$('#tabs').tabs('select', 1);
					return false;
				});
	
				//--------------------------------------------------------------------------------
				$('#update').button().click(function() {
                                        var jsonStr = [];
                                        jsonStr = {"category_id":$('#category_id').val(),
                                                   "category_name":$('#category_name').val(),
                                                   "remarks":$('#remarks').val()};
                                        $.ajax({
						url: '<?php echo site_url("reverse_docs/update_data"); ?>',
						type: 'POST',
						dataType:'json',
						data: {'jsarray': $.toJSON(jsonStr)},
				
						success: function(response) {
							$('#msgDialog > p').html(response.valid);
							if(response.valid == "Success") {
                                                                $('#msgDialog > p').html("Data Updated Successfully");
								$('#msgDialog').dialog('option', 'title', 'Success').dialog('open');
                                                                $('#category_id').val(response.category_id);
                                                                
                                                                //Update the values in Data Table after updating records
                                                                dataTable.fnUpdate([$('#category_id').val(), 
                                                                                $('#category_name').val(),
                                                                                $('#remarks').val(),
                                                                                '<a class="updateBtn" href="' + updateUrl + '/' + $('#category_id').val() + '">Update/Edit</a>'
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