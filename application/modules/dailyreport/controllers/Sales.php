<?php 
    defined('BASEPATH') OR exit('No direct script access allowed');
    use Dompdf\Dompdf;
    use Dompdf\Options;

  

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
            $data['report'] = $this->Sales_Model->todayReport(); 
            $this->load->view('Sales', $data);
        }

        

        public function export_pdf() {

            $options = new Options();   
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);


            $data['report'] = $this->Sales_Model->todayReport();
            $html = $this->load->view('Sales_pdf', $data, true);

            // Load dompdf
            $options = new Options();
            $options->set('isRemoteEnabled', true); // allow external styles/images if needed
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Output the PDF to browser
            $dompdf->stream("hotel_daily_report.pdf", array("Attachment" => false));
        }


    }
?>
