<?php
class CompanyInfo extends MY_Controller{
    private $indexPage = "company_info";
	public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Company Info";
		$this->data['headData']->controller = "companyInfo";
        $this->data['headData']->pageUrl = "companyInfo";
	}
	
	public function index(){
        $this->data['dataRow'] = $this->masterModel->getCompanyInfo();
        $this->data['countryData'] = $this->party->getCountries();
        $this->load->view($this->indexPage,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
    
        if(empty($data['company_name']))
            $errorMessage['company_name'] = "Company Name is required.";

        if(empty($data['company_email']))
            $errorMessage['company_email'] = "Company Email is required.";

        if(empty($data['company_contact']))
            $errorMessage['company_contact'] = "Contact No. is required.";

        if(empty($data['company_city_id']))
            $errorMessage['company_city_id'] = "City Name is required.";

        if(empty($data['company_state_id']))
            $errorMessage['company_state_id'] = "State Name is required.";

        if(empty($data['company_country_id']))
            $errorMessage['company_country_id'] = "Country Name is required.";

        if(empty($data['company_address']))
            $errorMessage['company_address'] = "Address is required.";
            
        if(empty($data['company_pincode']))
            $errorMessage['company_pincode'] = "Pincode is required.";
       
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $this->printJson($this->masterModel->saveCompanyInfo($data));
        endif;
    }
}
?>