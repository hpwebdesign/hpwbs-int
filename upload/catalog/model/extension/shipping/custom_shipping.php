<?php
class ModelExtensionShippingCustomShipping extends Model {
  function getQuote($address) {
    $this->load->language('extension/shipping/custom_shipping');
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_free_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('shipping_custom_shipping_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}
    if ($status) {
      $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "custom_shipping WHERE (country_id ='" . (int) $address['country_id'] . "' OR country_id = 0) AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = 0) AND status = 1 ORDER BY country_id DESC, zone_id DESC");
        
        
        $costs = array();
        
     if ($query->num_rows) {
      $status = true;
    } else {

        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "custom_shipping WHERE (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = 0) AND status = 1 ORDER BY country_id DESC, zone_id DESC");
          
              
            if ($query->num_rows) {
                $status = true;  
            } else {
                $status = false;
            }
             
    }
    }

        
        
    $method_data = array();

    if ($status) {
      $costs = $query->row;
      if ($this->cart->getSubTotal() < $costs['total']) {
			  $status = false;
		  }
      $quote_data = array();
            
               $berat = $this->cart->getWeight();
               $unit=$this->weight->getUnit($this->config->get('config_weight_class_id'));

            // format in KG
                if($unit == 'g') {
               $berat = round($berat / 1000,1,PHP_ROUND_HALF_UP);
               } elseif ($unit == 'kg') {
                $berat = round($berat,1,PHP_ROUND_HALF_UP);
               }
           
            $cost = $costs['rate'];
            
            if($costs['rate_type'] == "perkg") {
                $berat = explode('.',$berat);

                if(empty($berat[1]))
                { 
                    $berat[1]=0;
                   }
                    /* penentuan harga */

                if ((float)$berat[0] < 1){
                $cost = $cost;  
                $berat=$berat[0].".".$berat[1]; 
                }
                elseif((float)$berat[1]<=3){
                $cost = $berat[0] * $cost;
                $berat=$berat[0];
                }
                else{
                $cost = ($berat[0]+1) * $cost;
                $berat=$berat[0].".".$berat[1];
                }
            } elseif ($costs['rate_type'] == "flat")  {
              if ($unit == 'kg'){
                if ($berat > $costs['total_weight']){
                    $selisih = $berat - $costs['total_weight'];
                    $cost = $costs['rate'] + ($selisih * $costs['cost'] / $costs['for_every']);
                } else {
                   $cost = $costs['rate'];
                }
              } else{
                 $cost = $costs['rate'];
              }
            } else {
              $q = $this->db->query("SELECT cswr.rate FROM ".DB_PREFIX."custom_shipping_weight_range cswr JOIN ".DB_PREFIX."custom_shipping cs ON (cswr.custom_shipping_id = cs.custom_shipping_id) WHERE cswr.from_weight <= ".(float)$berat." AND cswr.to_weight >= ".(float)$berat." AND ((cs.zone_id = '" . (int)$address['zone_id'] . "' OR cs.zone_id = 0) OR ((cs.country_id ='".(int)$address['country_id']."' OR cs.country_id = 0) AND cs.zone_id = '0')) AND cs.status = 1 ORDER BY cswr.rate DESC");
              if ($q->num_rows) {
                  $cost = $q->row['rate'];
              } else {
                $status = false;
              }
            }


             $this->load->model('tool/image');
             if ($this->config->get('c_shipping_image') && is_file(DIR_IMAGE . $this->config->get('c_shipping_image'))) {
      $thumb =  $this->model_tool_image->resize($this->config->get('c_shipping_image'), 60, 30);
    } else {
      $thumb = $this->model_tool_image->resize('no_image.png', 60, 30);
    }
             if ($this->config->get('c_shipping_image') && substr($this->config->get('c_shipping_image'),0,4) != 'http') {
                 $image = $thumb;
                } else {
                 $image = $thumb;
                }
      if ($status) {
        $title =  $this->config->get('c_shipping_name') ? $this->config->get('c_shipping_name') : $this->language->get('text_name');
        if ($this->config->get('c_shipping_discount_status')) {
             if ($this->cart->getSubTotal() > (float)$this->config->get('c_shipping_discount_total')) {
                $title .= ' ('. $this->config->get('c_shipping_discount_percent').'% discount)';
                $cost = $cost - ($cost * ((float)$this->config->get('c_shipping_discount_total') / 100));
             }
        }
      $quote_data['custom_shipping'] = array(
        'code'         => 'custom_shipping.custom_shipping',
        'title'        => $title,
        'cost'         => $cost,
        'text_kg'      => $this->language->get('text_kg'),
        'text_day'     => $this->language->get('text_day'),
        'weight'       => $berat,
        'etd'          => $costs['etd'],
        'image'        => $image,
        'tax_class_id' => $this->config->get('shipping_custom_shipping_tax_class_id'),
        'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get('shipping_custom_shipping_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
      );
            
               

      $method_data = array(
        'code'       => 'custom_shipping',
        'title'      => $this->config->get('c_shipping_name') ? $this->config->get('c_shipping_name') : $this->language->get('text_title'),
        'quote'      => $quote_data,
        'sort_order' => $this->config->get('shipping_custom_shipping_sort_order'),
        'error'      => false
      );
    }
    }
    return $method_data;
    
  }
}