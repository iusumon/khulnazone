<html>

<head>
<title>IBBL Zonal Office, Khulna</title>
<?php $this->load->view('jquery_include'); ?>
</head>

<body>
<div id="msgDialog"><p></p></div>

<div id="login" title="Login">
	<form action="" method="POST">
		<table> <tr>
                        <td><label for="user">User</label></td>
			<td><input name="user" id="user" type="text"/></td>
		</tr>
		<tr>
			<td><label for="passwd">Password </label></td>
			<td><input name="passwd" id="passwd" type="password"/></td>
		</tr>
		<tr><td><hr></td></tr>
		<tr>
			<td align="center"><input value="submit" type="button" id="submit"></td>
		</tr>
		</table>
	<?php echo form_close();?>
</div>


<script type="text/javascript">
$(function() {
	$('#login').dialog({
		draggable:false,
		width: 350,
		height: 190
	});

//--------------------------------------------------------------
	$('#msgDialog').dialog({
		autoOpen:false,
		
		buttons: {
			'OK': function() {
				$(this).dialog('close');
				$('#user').focus();
			}
		}
	});
	
//-----------------------------------------------------------------
	$('#submit').button().click(function() {
			$.post('<?php echo site_url("login/checkLogin"); ?>', $('#login form').serialize(), function(data) {
				if(data == true) {
					window.location.href = "<?php echo site_url('login/load_main'); ?>";
				} else if(data == 'receiver') {
					window.location.href = "<?php echo site_url('receive_docs'); ?>";
                                    
                                } else {
					$('#msgDialog > p').html("Enter Correct User and Password");
					$('#msgDialog').dialog('option', 'title', 'Warning').dialog('open');
				}
			});
	});
//-----------------------------------------------------------------

});
</script>
</body>

</html>
