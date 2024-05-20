<?php

class ControllerExtensionModuleCustomShipping extends Controller
{
	public function index() {
		 // HPCS
            $this->load->language('extension/shipping/custom_shipping');
            $this->load->model('catalog/product');
           
            $data['estimasi'] = array();
            $data['product_id'] = $this->request->get['product_id'];
            //$etd = $this->model_catalog_product->getEtd($this->request->get['product_id']);
            $data['etd'] = $this->model_catalog_product->getEtd($this->request->get['product_id']);
            $customshipping = $this->model_catalog_product->getCustomShippings();
            $product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
            foreach ($customshipping as $custom) {
                $status = 1;
                $berat = isset($product_info['weight']) ? $product_info['weight'] : 0;
                $unit = isset($product_info['weight_class_id']) ? $this->weight->getUnit($product_info['weight_class_id']) : $this->weight->getUnit($this->config->get('config_weight_class'));

            // format in KG
                if ($unit == 'g') {
                $berat = round($berat / 1000,1,PHP_ROUND_HALF_UP);
                } elseif ($unit == 'kg') {
                    $berat = round($berat,1,PHP_ROUND_HALF_UP);
                }
                     if(!empty($custom['etd'])){
                      $etds = explode("-",$custom['etd']);

                         if (count($etds) > 1){
                            $etd = $etds[1];
                          } else {
                            $etd = $custom['etd'];
                          }
                      } else {
                        $etd = 0;
                      }
                if ($custom['rate_type'] == 'weight_range') {
                        $weight_range = $this->model_catalog_product->getWeightRange($custom['custom_shipping_id'],$berat);
                        if (!$weight_range) {
                            $status = 0;
                        }
                }
                if ($status) {
                 $data['estimasi'][] = array(
                     'id' => $custom['custom_shipping_id'],
                     'zone' => $custom['zone_name'],
                     'country' => $custom['country_name'],
                     'city' => $custom['city_name'],
                     'etd' => $etd
                 );
                }

            }
        return $this->response->setOutput($this->load->view('extension/module/custom_shipping', $data));
	}
}