<?php
class ModelExtensionModuleCustomShipping extends Model {
	public function addCustomShipping($data) {
		$data['status'] = isset($data['status']) ? $data['status'] : 0; 
		$this->db->query("INSERT INTO ".DB_PREFIX."custom_shipping SET country_name = '" . $data['country_name'] . "', zone_name = '" . $data['zone_name'] . "', total = '" .  (float)$data['total'] . "', country_id = '" .  (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', rate = '" .(float)$data['rate']. "', rate_type = '" .$data['rate_type']. "', etd = '" .$this->db->escape($data['etd']). "', total_weight = '".(int)$data['total_weight']."', for_every = '".(int)$data['for_every']."', cost = '".(float)$data['cost']."', status = '" . (int)$data['status'] . "'");
		$custom_shipping_id = $this->db->getLastId();
		if (isset($data['weight_range'])) {
			foreach ($data['weight_range'] as $weight) {
				$this->db->query("INSERT INTO ".DB_PREFIX."custom_shipping_weight_range SET custom_shipping_id = '".(int)$custom_shipping_id."', from_weight = '".(float)$weight['from_weight']."', to_weight = '".(float)$weight['to_weight']."', rate = '".(float)$weight['rate']."' ");
			}
		}
		$this->cache->delete('custom_shipping');
		return $custom_shipping_id;
	}

	public function editCustomShipping($custom_shipping_id, $data) {
		$data['status'] = isset($data['status']) ? $data['status'] : 0; 
		$this->db->query("UPDATE ".DB_PREFIX."custom_shipping SET country_name = '" . $data['country_name'] . "', zone_name = '" . $data['zone_name'] . "', total = '" .  (float)$data['total'] . "', country_id = '" .  (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', rate = '" .(float)$data['rate']. "', rate_type = '" .$data['rate_type']. "', etd = '" .$this->db->escape($data['etd']). "', total_weight = '".(int)$data['total_weight']."', for_every = '".(int)$data['for_every']."', cost = '".(float)$data['cost']."', status = '" . (int)$data['status'] . "' WHERE custom_shipping_id='".(int)$custom_shipping_id."'");
		$this->db->query("DELETE FROM ".DB_PREFIX."custom_shipping_weight_range WHERE custom_shipping_id = '".(int)$custom_shipping_id."'");
		if (isset($data['weight_range'])) {
			foreach ($data['weight_range'] as $weight) {
				$this->db->query("INSERT INTO ".DB_PREFIX."custom_shipping_weight_range SET custom_shipping_id = '".(int)$custom_shipping_id."', from_weight = '".(float)$weight['from_weight']."', to_weight = '".(float)$weight['to_weight']."', rate = '".(float)$weight['rate']."' ");
			}
		}
		$this->cache->delete('custom_shipping');
		return $custom_shipping_id;
	}

	public function getWeightRange($custom_shipping_id) {
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."custom_shipping_weight_range WHERE custom_shipping_id = '".(int)$custom_shipping_id."' ");
		return $query->rows;
	}

	public function deleteCustomShipping($custom_shipping_id) {
		$this->db->query("DELETE FROM ".DB_PREFIX."custom_shipping WHERE custom_shipping_id = '" . (int)$custom_shipping_id . "'");
		$this->db->query("DELETE FROM ".DB_PREFIX."custom_shipping_weight_range WHERE custom_shipping_id = '".(int)$custom_shipping_id."'");
		$this->cache->delete('custom_shipping');
	}

	public function getCustomShipping($custom_shipping_id) {
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."custom_shipping WHERE custom_shipping_id = '" . (int)$custom_shipping_id . "'");

		return $query->row;
	}

	public function getCustomShippings($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM ".DB_PREFIX."custom_shipping";

			$sort_data = array(
				'rate',
				'zone_id'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY custom_shipping_id";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);

			return $query->rows;
		} else {
			$custom_shipping_data = $this->cache->get('custom_shipping');

			if (!$custom_shipping_data) {
				$query = $this->db->query("SELECT * FROM ".DB_PREFIX."custom_shipping ORDER BY custom_shipping_id DESC");

				$custom_shipping_data = $query->rows;

				$this->cache->set('custom_shipping', $custom_shipping_data);
			}

			return $custom_shipping_data;
		}
	}

	public function getTotalCustomShippings() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM custom_shipping");

		return $query->row['total'];
	}

	public function installTable() {
		$sqls = [];
		$sqls[] = " CREATE TABLE IF NOT EXISTS `".DB_PREFIX."custom_shipping` (
			  `custom_shipping_id` int(11) NOT NULL AUTO_INCREMENT,
			  `country_name` varchar(40) NOT NULL DEFAULT '',
			  `zone_name` varchar(40) NOT NULL DEFAULT '',
			  `city_name` varchar(40) NOT NULL DEFAULT '',
			  `country_id` int(11) NOT NULL,
			  `zone_id` int(11) NOT NULL,
			  `city_id` int(11) NOT NULL,
			  `rate` decimal(15,4) NOT NULL,
			  `etd` varchar(12) NOT NULL DEFAULT '',
			  `rate_type` varchar(32) NOT NULL DEFAULT '',
			  `total_weight` decimal(15,4) NOT NULL,
			  `for_every` decimal(15,4) NOT NULL,
			  `cost` decimal(15,4) NOT NULL,
			  `total` decimal(15,4) NOT NULL,
			  `status` tinyint(1) NOT NULL,
			  PRIMARY KEY (`custom_shipping_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";

		$sqls[] = " CREATE TABLE IF NOT EXISTS `".DB_PREFIX."custom_shipping_weight_range` (
			  `custom_shipping_weight_range_id` int(11) NOT NULL AUTO_INCREMENT,
			  `custom_shipping_id` int(11) NOT NULL,
			  `from_weight` decimal(15,4) NOT NULL,
			  `to_weight` decimal(15,4) NOT NULL,
			  `rate` decimal(15,4) NOT NULL,
			  PRIMARY KEY (`custom_shipping_weight_range_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
		foreach ($sqls as $sql) {
			$this->db->query($sql);
		}
		
	}

	public function uninstallTable() {
		$this->cache->delete('custom_shipping');

		$sql = " DROP TABLE IF EXISTS ".DB_PREFIX."custom_shipping";

			if(!$this->db->query($sql)) {
				return false;
			} else {
				return true;
			}
		}
	}
