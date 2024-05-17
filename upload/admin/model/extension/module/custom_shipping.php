<?php
class ModelExtensionModuleCustomShipping extends Model {
	public function addCustomShipping($data) {
		// $this->event->trigger('pre.admin.custom_shipping.add', $data);

		$this->db->query("INSERT INTO custom_shipping SET 	country_name = '" . $data['country_name'] . "', zone_name = '" . $data['zone_name'] . "', city_name = '" . $data['city_name'] . "', total = '" .  (float)$data['total'] . "', country_id = '" .  (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', rate = '" .(float)$data['rate']. "', rate_type = '" .$data['rate_type']. "', etd = '" .$this->db->escape($data['etd']). "', total_weight = '".(int)$data['total_weight']."', for_every = '".(int)$data['for_every']."', cost = '".(float)$data['cost']."', status = '" . (int)$data['status'] . "'");

		$this->cache->delete('custom_shipping');

		// $this->event->trigger('post.admin.custom_shipping.add', $custom_shipping_id);

		return $custom_shipping_id;
	}

	public function editCustomShipping($custom_shipping_id, $data) {
		// $this->event->trigger('pre.admin.custom_shipping.edit', $data);

		$this->db->query("UPDATE custom_shipping SET country_name = '" . $data['country_name'] . "', zone_name = '" . $data['zone_name'] . "', city_name = '" . $data['city_name'] . "', total = '" .  (float)$data['total'] . "', country_id = '" .  (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', rate = '" .(float)$data['rate']. "', rate_type = '" .$data['rate_type']. "', etd = '" .$this->db->escape($data['etd']). "', total_weight = '".(int)$data['total_weight']."', for_every = '".(int)$data['for_every']."', cost = '".(float)$data['cost']."', status = '" . (int)$data['status'] . "' WHERE custom_shipping_id='".(int)$custom_shipping_id."'");

		$this->cache->delete('custom_shipping');

		// $this->event->trigger('post.admin.custom_shipping.edit', $custom_shipping_id);
	}

	public function deleteCustomShipping($custom_shipping_id) {
		// $this->event->trigger('pre.admin.custom_shipping.delete', $custom_shipping_id);

		$this->db->query("DELETE FROM custom_shipping WHERE custom_shipping_id = '" . (int)$custom_shipping_id . "'");

		$this->cache->delete('custom_shipping');

		// $this->event->trigger('post.admin.custom_shipping.delete', $custom_shipping_id);
	}

	public function getCustomShipping($custom_shipping_id) {
		$query = $this->db->query("SELECT * FROM custom_shipping WHERE custom_shipping_id = '" . (int)$custom_shipping_id . "'");

		return $query->row;
	}

	public function getCustomShippings($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM custom_shipping";

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
				$query = $this->db->query("SELECT * FROM custom_shipping ORDER BY zone_id DESC");

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
		$sql = " CREATE TABLE `custom_shipping` (
			  `custom_shipping_id` int(11) NOT NULL AUTO_INCREMENT,
			  `country_name` varchar(40) NOT NULL DEFAULT '',
			  `zone_name` varchar(40) NOT NULL DEFAULT '',
			  `city_name` varchar(40) NOT NULL DEFAULT '',
			  `country_id` int(11) NOT NULL,
			  `zone_id` int(11) NOT NULL,
			  `city_id` int(11) NOT NULL,
			  `rate` decimal(15,4) NOT NULL,
			  `etd` varchar(12) NOT NULL DEFAULT '',
			  `rate_type` varchar(10) NOT NULL DEFAULT '',
			  `total_weight` int(11) NOT NULL,
			  `for_every` int(11) NOT NULL,
			  `cost` decimal(15,4) NOT NULL,
			  `total` decimal(15,4) NOT NULL,
			  `status` tinyint(1) NOT NULL,
			  PRIMARY KEY (`custom_shipping_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";

		//$sqls = "ALTER TABLE " . DB_PREFIX . "product ADD `etd` varchar(40) NULL AFTER `date_modified`;";


	//	$this->db->query($sqls);
		$this->db->query($sql);
	}

	public function uninstallTable() {
		$this->cache->delete('custom_shipping');

		$sql = " DROP TABLE IF EXISTS custom_shipping";

			if(!$this->db->query($sql)) {
				return false;
			} else {
				return true;
			}
		}
	}
