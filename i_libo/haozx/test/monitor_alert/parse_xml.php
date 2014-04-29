<?php

$xml = new SimpleXMLElement('/home/hzx/code/elephant_tk/src/etc/monitor_alert/example/contact_eyou.xml', 0, true);

function xml_to_array($xml) {
	$data = array();
	$attr = $xml->attributes();
	if ($attr->count()) {
		$data = array_values((array)$attr)[0];	
	}
	if ($xml->count()) {
		$names = array();
		foreach ($xml as $value) {
			$name = $value->getName();
			if (!isset($names[$name])) {
				$data[$name] = xml_to_array($value);	
			} else {
				if ($names[$name] > 1) {
					array_push($data[$name], xml_to_array($value));
				} else {
					$tmp_data = $data[$name];
					$data[$name] = array();
					array_push($data[$name], $tmp_data, xml_to_array($value));
				}
			}
			$names[$name] = isset($names[$name]) ? ($names[$name] + 1) : 1;
		}
	} else {
		if (!empty($data)) {
			$data['@value'] = (string)$xml;	
		} else {
			$data = (string) $xml;
		}
		return $data;
	}

	return $data;
}

var_dump(xml_to_array($xml));
