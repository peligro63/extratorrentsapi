<?php

class extratorrent { 
	
	public $baseurl = "http://extratorrent.cc/rss.xml";
	
	
	/*
	 * Reading remote url into string 
	 * 
	 * @param string URL of the page 
	 * 
	 * @return string XML document 
	 * */
	public function getXMLstring($url) { 
		
		libxml_use_internal_errors(true);
		$xml = file_get_contents($url);
		return $xml;
		
	}
	
	/*
	 * Convert XML string to valid PHP object 
	 * 
	 * @param string XML document 
	 * 
	 * @return object Parsed xml
	 * */
	public function XMLtoObject($xml) {
		
		libxml_use_internal_errors(true);
		$xml = html_entity_decode($xml);
		$loadedXML = simplexml_load_string($xml);
		
		if(is_bool($loadedXML) && $loadedXML == false) {
			die(var_dump(libxml_get_errors()));
		} 
		
		return $loadedXML;
		
	}
	
	/* 
	 * Main search function which returns list of item objects for specified query 
	 * 
	 * @param string Search query 
	 * 
	 * @return array List of mapped objects or empty array when no results are found
	 * */
	public function search($query) { 
		
		$query = $this->baseurl . '?type=search&search=' . urlencode($query);
		return $this->mapItems($this->getXMLstring($query));
		
	}
	
	/*
	 * Popular torrents list 
	 * 
	 * @return array List of mapped objects or empty array when no results are found
	 * */
	public function popular() {
		
		$query = $this->baseurl . '?type=popular';
		return $this->mapItems($this->getXMLstring($query));
		
	}
	
	/*
	 * 
	 * @param string XML data 
	 * 
	 * @return array List of mapped objects. Every object contains: 
	 * 
	 * "title" 			-- Title 
	 * "datePublished" 	-- Published date 
	 * "category" 		-- Category
	 * "pagelink" 		-- Link of the torrent page
	 * "torrentFile"	-- Direct link to torrent file 
	 * "size" 			-- Size of the file
	 * "seeders" 		-- Number of seeders 
	 * "leechers" 		-- Number of leechers  
	 * 
	 * */
	public function mapItems($xml) {
		
		$xml = $this->XMLtoObject($xml);
		$itemsblock = array();

		foreach($xml->channel->item as $item) {
			
			$new = new StdClass;
			
			$new->title = $item->title->__toString();
			$new->datePublished = date('F j, Y', strtotime($item->pubDate->__toString()));
			$new->category =  $item->category->__toString();
			$new->pagelink = $item->link->__toString();
			$new->torrentFile = $item->enclosure->attributes()[0]->__toString();
			$new->size = $this->formatFileSize($item->size->__toString());
			$new->seeders = $item->seeders->__toString();
			$new->leechers = $item->leechers->__toString();
			
			$itemsblock[] = $new;
			
		}
		
		return $itemsblock;
	}
	
	
	public function formatFileSize($size) { 
		
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$power = $size > 0 ? floor(log($size, 1024)) : 0;
		return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    
	}

	
}

