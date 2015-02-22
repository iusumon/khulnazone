<?php
class Model_db_integrity_check extends Model {
    function __construct() {
        parent::Model();
    }
    
    function check_integrity($child_table, $field_name, $value) {
        $this->db->where($field_name, $value, TRUE);
        $this->db->from($child_table);
        $count = $this->db->count_all_results();
        if ($count > 0) {
            return false;
        } else{
            return true;
        }
    }
}
?>
