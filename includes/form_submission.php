<?php
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class Form_Reports extends WP_List_Table
{

	public $limit = 10;
	public $_column_headers ;
	public $items ;

	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items()
	{
		$columns = $this->get_columns();
		$this->current_actions();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		$this->process_bulk_action(); 
		$data = $this->table_data();
		if(!empty($data)){
			usort( $data, array( &$this, 'sort_data' ) );
			$perPage = $this->limit;
			$currentPage = $this->get_pagenum();
			$totalItems = count($data);
			$this->set_pagination_args( array(
				'total_items' => $totalItems,
				'per_page'    => $perPage
			));
			$data = array_slice($data, (($currentPage-1) * $perPage), $perPage);
		}
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;

	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns()
	{

		global $wpdb;
		$shortcodes = $_REQUEST['formrecord'];
		$data = $wpdb->get_results("select meta_key, meta_value from wp_smackleadbulider_formsubmission_record where shortcode_name='$shortcodes'",ARRAY_A);
		$columns = array_column($data, 'meta_key', 'meta_key' );
		$columns['cb'] = '<input type="checkbox" />';
		$columns = array('cb' => $columns['cb']) + $columns;
		$columns = array_map('ucwords', $columns);

		return $columns;
	}

	function column_cb($item) {

		return sprintf(
			'<input type="checkbox" name="delete[]" value="%s" />', $item['id']
		);
	}
	function get_bulk_actions() {
		$actions = array(
			'delete'    => 'Delete'
		);
		return $actions;
	}

	function process_bulk_action() {        
		global $wpdb;
		// $actions = $this->current_action();
		$shortcodes = $_REQUEST['formrecord'];

		if( 'delete'===$this->current_action() ) {

			$deleteform = $_POST['delete'];
			foreach ($deleteform as $deleteform_key => $deleteform_value) {

				$wpdb->query("delete from wp_smackleadbulider_formsubmission_record where shortcode_name='$shortcodes' and form_id='$deleteform_value'");
			}
		}        
	}
	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @return Mixed
	 */
	private function sort_data( $a, $b )
	{
		// Set defaults
		$orderby = 'id';
		$order = 'desc';

		// If orderby is set, use this as the sort column
		if(!empty($_GET['orderby']))
		{
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order
		if(!empty($_GET['order']))
		{
			$order = $_GET['order'];
		}

		$result = strnatcmp( $a[$orderby], $b[$orderby] );

		if($order === 'desc')
		{
			return $result;
		}

		return -$result;
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns()
	{
		return array('id' => array('id', false));
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns()
	{
		return array();
	}


	/**
	 * Get the table data
	 *
	 * @return Array
	 */
	private function table_data()
	{
		global $wpdb;
		$metadata=[];
		$shortcodes = $_REQUEST['formrecord'];
		$formid = $wpdb->get_results("select DISTINCT form_id from wp_smackleadbulider_formsubmission_record where shortcode_name='$shortcodes'",ARRAY_A);
		$shortcode_formid = array_column($formid, 'form_id');

		foreach ($shortcode_formid as $meta_key => $meta_value) {
			$data[] = $wpdb->get_results("select form_id, meta_key, meta_value from wp_smackleadbulider_formsubmission_record where shortcode_name='$shortcodes' and form_id='$meta_value'",ARRAY_A);

		}
		if(!empty($data)){
			foreach ($data as $key => $value) {

				for($i=0; $i<count($value); $i++) {
					$metadata[$key]['id'] = $value[$i]['form_id'];
					$metadata[$key][$value[$i]['meta_key']] = $value[$i]['meta_value'];
				}

			}
			return $metadata;
		}


	}

	/**
	 * return assessment id
	 * @param string $type
	 * @param integer $user_id
	 * @return string $assessmentId
	 */

	// Used to display the value of the id column
	public function column_id($item)
	{
		return $item['id'];
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default($item, $column_name)
	{

		return $item[$column_name];

	}
}
