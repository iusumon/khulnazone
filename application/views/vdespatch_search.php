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
			<p class="top_heading">Search Despatch Register</p>
			<ul>
				<li><a href="#entryform">Enter Information</a></li>
				<li><a href="#view">Result</a></li>
			</ul>

			<div align="center" id="entryform">
				<form action="" method="POST">

					<table> 
                                                <tr>
                                                    <td><label for="from_date"> From </label></td>
                                                    <td><input name="from_date" id="from_date" readonly/> </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td><label for="to_date"> To </label></td>
                                                    <td><input name="to_date" id="to_date" readonly/> </td>
                                                </tr>
                                            

						<tr>
							<td><label for="category_name">Category </label></td>
<!--                                                        <td><input name="category_name" id="category_name" type="text" maxlength="50" size="30" value="<?php echo set_value('GroupName'); ?>"/></td>-->
                                                        <td><div id="category_name"> </div></td>
						</tr>

                                                <tr>
							<td><label for="particulars">Particulars </label></td>
							<td><input name="particulars" id="particulars" type="text" maxlength="100" size="50"/></td>
						</tr>
                                                
                                                <tr>
							<td><label for="sender">Sender/Recipients </label></td>
<!--							<td><input name="sender" id="sender" type="text" maxlength="100" size="50"/></td>-->
                                                        <td><div id="sender"> </div></td>
						</tr>
                                                
                                                <tr>
							<td><label for="agent">Courier Agents/Employee </label></td>
							<td><input name="agent" id="agent" type="text" maxlength="100" size="50"/></td>
						</tr>
					</table>
					<table>
						<tr>
							<td> <input type="button" name="entrySubmit" id="entrySubmit" value="Search"/></td>
							<td> <input type="button" name="Exit" id="exit" value="Exit"/></td>
						</tr>
					</table>
					<input type="hidden" id="category_id" name="category_id"/>
				</form>		
			</div>
                        
                        <div id="view">
				<?php echo $table_data; ?>
                            
				<table class="center">
					<tr><td> <input type="button" name="Exit" id="close" value="Search Again"/></td></tr>
				</table>
			</div>

		</div> <!-- end tabs  -->

		<script type="text/javascript">
			var updateUrl = 'despatch_category/update_data',
			deleteUrl = 'despatch_category/delete_data',
			delHref,
			updateHref,
			updateId,
			update_position,
			dataTable;

			$(function() {
				//---------------------------------------------------------------
				$('#tabs ').tabs({
					fx: {height: 'toggle', opacity: 'toggle'}
		
				}).css('width', '750px').css('margin', '0 auto');
                                
                                $("#from_date, #to_date").datepicker({
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
                                    $("#from_date, #to_date").val(prettyDate);
                                }
	
				//--------------------------------------------------------------
				$('#msgDialog').dialog({
					autoOpen:false,
		
					buttons: {
						'OK': function() {
							$(this).dialog('close');
							$('#category_name').focus();
						}
					}
				});
	
				//-----------------------------------------------------------------
	
				$('#entrySubmit').button().click(function() {
                                        var jsonStr = [];
					jsonStr = {"from_date":$('#from_date').val(),
                                                   "to_date":$('#to_date').val(),
                                                   "category_name":$('#category_name_input').val(),
                                                   "particulars":$('#particulars').val(),
                                                   "sender":$('#sender_input').val(),
                                                   "agent":$('#agent').val()};
//                                         jsarray = $.toJSON(jsonStr);
//                                         window.open('<?php echo site_url("sales_invoice/show_report"); ?>'+ "/" + jsarray, "report");
                                         
					$.ajax({
						url: '<?php echo site_url("despatch_search/search_data"); ?>',
						type: 'POST',
                                                dataType:'json',
						data: {'jsarray': $.toJSON(jsonStr)},
				
						success: function(response) {
							if(response.length > 0){
                                                            for(var i in response) {
                                                                dataTable.fnAddData([
                                                                    response[i].id,
                                                                    response[i].date,
                                                                    response[i].sender,
                                                                    response[i].category_name,
                                                                    response[i].particulars,
                                                                    response[i].name
                                                                ], false);
                                                                dataTable.fnDraw(true);
                                                                $('#tabs').tabs('select', 1);
//                                                              $("#invoice_id_filter").removeAttr("disabled");
                                                            } 
                                                        } else {
                                                                $('#msgDialog > p').html("No records!");
                                                                $('#msgDialog').dialog('option', 'title', 'Failed').dialog('open');
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
					window.location.href = "<?php echo site_url("login"); ?>";
				});
	
				//-----------------------------------------------------------------------
	
				$('#close').button().click(function() {
//					window.location.href = "<?php echo site_url("login/load_main"); ?>";
                                        dataTable.fnClearTable(true);
                                        $('#tabs').tabs('select', 0);
				});
	
				//----------------------------------------------------------------------
	
				function clear_field() {
					//to clear the current form field
					$(':text').val('');
                                        
                                        show_date();
                                        $('#category_id').val('');
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
								{sClass:"left", "sWidth":"150px"},
								{sClass:"left", "sWidth":"70px"},
								{sClass:"left", "sWidth":"90px"},
								{sClass:"left", "sWidth":"90px"},
								{sClass:"left", "sWidth":"200px"},
								{sClass:"center", "sWidth":"90px"}
							]
						});
					}
                                        
                                        $('#category_name').flexbox('despatch_search/get_category_name', { selectBehavior: false, watermark: '', paging: true, allowInput: true, autoCompleteFirstMatch: false});
                                        $('#category_name_input').css('width', '300px');
                                        
                                        $('#sender').flexbox('despatch_search/get_sender_name', { selectBehavior: false, watermark: '', paging: true, allowInput: true, autoCompleteFirstMatch: false});
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
								url: '<?php echo site_url("despatch_category/delete_data"); ?>' + '/' + $('#category_id').val(),
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
//					clear_field();
                                        dataTable.fnClearTable(true);
				});
				//--------------------------------------------------------------------------------
				
				$("#records").delegate("a.updateBtn", "click", function() {
					update_position = dataTable.fnGetPosition($(this).parents('tr')[0]);
					delHref = $(this).attr('href');
					updateId = $(this).parents('tr').children('td:eq(0)').text();
					$.ajax({
						url: '<?php echo site_url("despatch_category/getById") . "/"; ?>' + updateId,
						dataType: 'json',
						success: function(response){
							$('#category_id').val(updateId);
							$('#category_name').val(response.category_name);
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
                                        jsonStr = {"category_id":$('#category_id').val(),
                                                   "category_name":$('#category_name').val(),
                                                   "remarks":$('#remarks').val()};
                                        $.ajax({
						url: '<?php echo site_url("despatch_category/update_data"); ?>',
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