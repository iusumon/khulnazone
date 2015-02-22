<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <?php $this->load->view('jquery_include'); ?>

        <title><?php echo $this->session->userdata('prj_name'); ?></title>
    </head>

    <body>
        <div id="menu" title="IBBL Zonal Office, Khulna- Current User: <?php echo $this->session->userdata('user'); ?>">
            <div id="accordion">

                <h3><a href="#">Menu-Despatch Section</a> </h3>
                <div>
                    <ul>
                        <li><?php echo anchor('inward_register', 'Inward Register'); ?></li>
                        <li><?php echo anchor('outward_register', 'Outward Register'); ?></li>
                        <li><?php echo anchor('courier_payment', 'Courier Service Payment'); ?></li>
                        <li><?php echo anchor('courier_agents', 'Courier/Postal Agent List'); ?></li>
                        <li><?php echo anchor('employee_details', 'Employee Details'); ?></li>
                        <li><?php echo anchor('despatch_category', 'Despatch Category'); ?></li>
                        <li><?php echo anchor('despatch_contacts', 'Despatch Contact List'); ?></li>
                        <li><?php echo anchor('reverse_docs', 'Reverse Documents'); ?></li>
                    </ul>
                </div>
                
                <h3><a href="#">Menu-Guide Book</a> </h3>
                <div>
                    <ul>
                        <li><?php echo anchor('branch_info', 'Branch Information'); ?></li>
                        <li><?php echo anchor('manager_info', 'Manager Information'); ?></li>
                        <li><?php echo anchor('contacts_info', 'Address Book'); ?></li>
                    </ul>
                </div>
                
                <h3><a href="#">Menu-Increment Approval Management</a> </h3>
                <div>
                    <ul>
                        <li><?php echo anchor('increment_entry', 'Increment Data Entry'); ?></li>
                        <li><?php echo anchor('increment_print', 'Increment Letter Print'); ?></li>
                    </ul>
                </div>
                
                <h3><a href="#">Menu-Report</a> </h3>
                <div>
                    <ul>
                        <li><?php echo anchor('branch_info/show_report', 'Show All Branches Information'); ?> </li>
                        <li><?php echo anchor('manager_info/show_report', 'Show All Managers Information'); ?> </li>
                        <li><?php echo anchor('contacts_info/show_report', 'Show Address Book'); ?> </li>
                    </ul>
                </div>
                
                <h3><a href="#">Exit</a> </h3>
                <div>
                    <ul>
                        <li><?php echo anchor('login/exit_all', 'Exit'); ?></li>
                    </ul>
                </div>	
            </div>
            <p align="center"><?php echo "Your IP Address: " . $_SERVER['REMOTE_ADDR']; ?> </p>
        </div>

        <script type="text/javascript">
            $(function(){
                $('#menu').dialog({
                    draggable: false,
                    width: 800,
                    height: 550
                });
                $('#accordion').accordion();
                $('a').css('text-decoration', 'none');
            })
        </script>
    </body>
</html>
