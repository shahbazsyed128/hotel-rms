<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Sales extends MX_Controller {

        public function __construct(){
 		parent::__construct();
            
        $this->db->query('SET SESSION sql_mode = ""');
 		    $this->load->model(array(
 			    'Sales_Model' 
 		    )); 
            
        }


        public function index() {
            $data['todayOrders'] = $this->Sales_Model->todayorder();
            $data['todaySales'] = $this->Sales_Model->todayamount();
            $data['monthlyOrders'] = $this->Sales_Model->monthlysaleorder();
            $data['monthlySales'] = $this->Sales_Model->monthlysaleamount();
            $data['employees'] = $this->Sales_Model->employees(); 
            $this->load->view('Sales', $data);


        }

    }
?>