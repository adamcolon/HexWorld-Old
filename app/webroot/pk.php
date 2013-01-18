<?php
	$ch = curl_init("http://parallelkingdom.com/TradeData.aspx");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);

	$tbl = new tableExtractor;
	$tbl->source = $result;
	$tbl->anchor = 'maincontentcontainer';
	$tbl_array = $tbl->extractTable();

	$items = array();
	foreach($tbl_array as $key=>$item){
		$item_index = preg_replace(array('/<span.*">/iU', '/<\/span>/'), array('', ''), $item['ItemName']);
		$item_name = preg_replace('/(.)([A-Z])/', "$1 $2", $item_index);
		
		$items[$item_index]['name'] = $item_name;
		$items[$item_index][$item['PriceType']] = array(
				'Type'=>$item['PriceType']
				,'Price'=>str_replace(',', '', $item['AveragePrice'])
				,'Volume'=>str_replace(',', '', $item['UnitsSold'])
		);
	}

   /*----------------------------------------------------------------------
        Table Extractor
        ===============
        Table extractor is a php class that can extract almost any table
        from any html document/page, and then convert that html table into
        a php array.
        
        Version 1.3
        Compatibility: PHP 4.4.1 +
        Copyright Jack Sleight - www.reallyshiny.com
        This script is licensed under the Creative Commons License.
    ----------------------------------------------------------------------*/
 
    class tableExtractor {
    
        var $source            = NULL;
        var $anchor            = NULL;
        var $anchorWithin    = false;
        var $headerRow        = true;
        var $startRow        = 0;
        var $maxRows        = 0;
        var $startCol        = 0;
        var $maxCols        = 0;
        var $stripTags        = false;
        var $extraCols        = array();
        var $rowCount        = 0;
        var $dropRows        = NULL;
        
        var $cleanHTML        = NULL;
        var $rawArray        = NULL;
        var $finalArray        = NULL;
        
        function extractTable() {
        
            $this->cleanHTML();
            $this->prepareArray();
            
            return $this->createArray();
            
        }
    
 
        function cleanHTML() {
        
            // php 4 compatibility functions
            if(!function_exists('stripos')) {
                function stripos($haystack,$needle,$offset = 0) {
                   return(strpos(strtolower($haystack),strtolower($needle),$offset));
                }
            }
                        
            // find unique string that appears before the table you want to extract
            if ($this->anchorWithin) {
                /*------------------------------------------------------------
                    With thanks to Khary Sharp for suggesting and writing
                    the anchor within functionality.
                ------------------------------------------------------------*/                
                $anchorPos = stripos($this->source, $this->anchor) + strlen($this->anchor);
                $sourceSnippet = strrev(substr($this->source, 0, $anchorPos));
                $tablePos = stripos($sourceSnippet, strrev(("<table"))) + 6;
                $startSearch = strlen($sourceSnippet) - $tablePos;
            }                       
            else {
                $startSearch = stripos($this->source, $this->anchor);
            }
        
            // extract table
            $startTable = stripos($this->source, '<table', $startSearch);
            $endTable = stripos($this->source, '</table>', $startTable) + 8;
            $table = substr($this->source, $startTable, $endTable - $startTable);
        
            if(!function_exists('lcase_tags')) {
                function lcase_tags($input) {
                    return strtolower($input[0]);
                }
            }
            
            // lowercase all table related tags
            $table = preg_replace_callback('/<(\/?)(table|tr|th|td)/is', 'lcase_tags', $table);
            
            // remove all thead and tbody tags
            $table = preg_replace('/<\/?(thead|tbody).*?>/is', '', $table);
            
            // replace th tags with td tags
            $table = preg_replace('/<(\/?)th(.*?)>/is', '<$1td$2>', $table);
                                    
            // clean string
            $table = trim($table);
            $table = str_replace("\r\n", "", $table); 
                            
            $this->cleanHTML = $table;
        
        }
        
        function prepareArray() {
        
            // split table into individual elements
            $pattern = '/(<\/?(?:tr|td).*?>)/is';
            $table = preg_split($pattern, $this->cleanHTML, -1, PREG_SPLIT_DELIM_CAPTURE);    
 
            // define array for new table
            $tableCleaned = array();
            
            // define variables for looping through table
            $rowCount = 0;
            $colCount = 1;
            $trOpen = false;
            $tdOpen = false;
            
            // loop through table
            foreach($table as $item) {
            
                // trim item
                $item = str_replace(' ', '', $item);
                $item = trim($item);
                
                // save the item
                $itemUnedited = $item;
                
                // clean if tag                                    
                $item = preg_replace('/<(\/?)(table|tr|td).*?>/is', '<$1$2>', $item);
 
                // pick item type
                switch ($item) {
                    
 
                    case '<tr>':
                        // start a new row
                        $rowCount++;
                        $colCount = 1;
                        $trOpen = true;
                        break;
                        
                    case '<td>':
                        // save the td tag for later use
                        $tdTag = $itemUnedited;
                        $tdOpen = true;
                        break;
                        
                    case '</td>':
                        $tdOpen = false;
                        break;
                        
                    case '</tr>':
                        $trOpen = false;
                        break;
                        
                    default :
                    
                        // if a TD tag is open
                        if($tdOpen) {
                        
                            // check if td tag contained colspan                                            
                            if(preg_match('/<td [^>]*colspan\s*=\s*(?:\'|")?\s*([0-9]+)[^>]*>/is', $tdTag, $matches))
                                $colspan = $matches[1];
                            else
                                $colspan = 1;
                                                    
                            // check if td tag contained rowspan
                            if(preg_match('/<td [^>]*rowspan\s*=\s*(?:\'|")?\s*([0-9]+)[^>]*>/is', $tdTag, $matches))
                                $rowspan = $matches[1];
                            else
                                $rowspan = 0;
                                
                            // loop over the colspans
                            for($c = 0; $c < $colspan; $c++) {
                                                    
                                // if the item data has not already been defined by a rowspan loop, set it
                                if(!isset($tableCleaned[$rowCount][$colCount]))
                                    $tableCleaned[$rowCount][$colCount] = $item;
                                else
                                    $tableCleaned[$rowCount][$colCount + 1] = $item;
                                    
                                // create new rowCount variable for looping through rowspans
                                $futureRows = $rowCount;
                                
                                // loop through row spans
                                for($r = 1; $r < $rowspan; $r++) {
                                    $futureRows++;                                    
                                    if($colspan > 1)
                                        $tableCleaned[$futureRows][$colCount + 1] = $item;
                                    else                    
                                        $tableCleaned[$futureRows][$colCount] = $item;
                                }
    
                                // increase column count
                                $colCount++;
                            
                            }
                            
                            // sort the row array by the column keys (as inserting rowspans screws up the order)
                            ksort($tableCleaned[$rowCount]);
                        }
                        break;
                }    
            }
            // set row count
            if($this->headerRow)
                $this->rowCount    = count($tableCleaned) - 1;
            else
                $this->rowCount    = count($tableCleaned);
            
            $this->rawArray = $tableCleaned;
            
        }
        
        function createArray() {
            
            // define array to store table data
            $tableData = array();
            
            // get column headers
            if($this->headerRow) {
            
                // trim string
                $row = $this->rawArray[$this->headerRow];
                            
                // set column names array
                $columnNames = array();
                $uniqueNames = array();
                        
                // loop over column names
                $colCount = 0;
                foreach($row as $cell) {
                                
                    $colCount++;
                    
                    $cell = strip_tags($cell);
                    $cell = trim($cell);
                    
                    // save name if there is one, otherwise save index
                    if($cell) {
                    
                        if(isset($uniqueNames[$cell])) {
                            $uniqueNames[$cell]++;
                            $cell .= ' ('.($uniqueNames[$cell] + 1).')';    
                        }            
                        else {
                            $uniqueNames[$cell] = 0;
                        }
 
                        $columnNames[$colCount] = $cell;
                        
                    }                        
                    else
                        $columnNames[$colCount] = $colCount;
                    
                }
                
                // remove the headers row from the table
                unset($this->rawArray[$this->headerRow]);
    
            }
            
            // remove rows to drop
            foreach(explode(',', $this->dropRows) as $key => $value) {
                unset($this->rawArray[$value]);
            }
                                
            // set the end row
            if($this->maxRows)
                $endRow = $this->startRow + $this->maxRows - 1;
            else
                $endRow = count($this->rawArray);
                
            // loop over row array
            $rowCount = 0;
            $newRowCount = 0;                            
            foreach($this->rawArray as $row) {
            
                $rowCount++;
                
                // if the row was requested then add it
                if($rowCount >= $this->startRow && $rowCount <= $endRow) {
                
                    $newRowCount++;
                                    
                    // create new array to store data
                    $tableData[$newRowCount] = array();
                    
                    //$tableData[$newRowCount]['origRow'] = $rowCount;
                    //$tableData[$newRowCount]['data'] = array();
                    $tableData[$newRowCount] = array();
                    
                    // set the end column
                    if($this->maxCols)
                        $endCol = $this->startCol + $this->maxCols - 1;
                    else
                        $endCol = count($row);
                    
                    // loop over cell array
                    $colCount = 0;
                    $newColCount = 0;                                
                    foreach($row as $cell) {
                    
                        $colCount++;
                        
                        // if the column was requested then add it
                        if($colCount >= $this->startCol && $colCount <= $endCol) {
                    
                            $newColCount++;
                            
                            if($this->extraCols) {
                                foreach($this->extraCols as $extraColumn) {
                                    if($extraColumn['column'] == $colCount) {
                                        if(preg_match($extraColumn['regex'], $cell, $matches)) {
                                            if(is_array($extraColumn['names'])) {
                                                $this->extraColsCount = 0;
                                                foreach($extraColumn['names'] as $extraColumnSub) {
                                                    $this->extraColsCount++;
                                                    $tableData[$newRowCount][$extraColumnSub] = $matches[$this->extraColsCount];
                                                }                                        
                                            } else {
                                                $tableData[$newRowCount][$extraColumn['names']] = $matches[1];
                                            }
                                        } else {
                                            $this->extraColsCount = 0;
                                            if(is_array($extraColumn['names'])) {
                                                $this->extraColsCount = 0;
                                                foreach($extraColumn['names'] as $extraColumnSub) {
                                                    $this->extraColsCount++;
                                                    $tableData[$newRowCount][$extraColumnSub] = '';
                                                }                                        
                                            } else {
                                                $tableData[$newRowCount][$extraColumn['names']] = '';
                                            }
                                        }
                                    }
                                }
                            }
                            
                            if($this->stripTags)        
                                $cell = strip_tags($cell);
                            
                            // set the column key as the column number
                            $colKey = $newColCount;
                            
                            // if there is a table header, use the column name as the key
                            if($this->headerRow)
                                if(isset($columnNames[$colCount]))
                                    $colKey = $columnNames[$colCount];
                            
                            // add the data to the array
                            //$tableData[$newRowCount]['data'][$colKey] = $cell;
                            $tableData[$newRowCount][$colKey] = $cell;
                        }
                    }
                }
            }
                    
            $this->finalArray = $tableData;
            return $tableData;
        }    
    }
	
?>
<html>
	<head>
		<title>PK Test</title>
		
		<style type="text/css" title="currentStyle"> 
			@import "https://raw.github.com/DataTables/DataTables/master/media/css/demo_page.css";
			@import "https://raw.github.com/DataTables/DataTables/master/media/css/demo_table.css";
		</style> 
		<script type="text/javascript" src="https://raw.github.com/DataTables/DataTables/master/media/js/jquery.js"></script>
		<script type="text/javascript" src="https://raw.github.com/DataTables/DataTables/master/media/js/jquery.dataTables.js"></script>
		<script>
			$(document).ready(function() {
				$('#trade_data').dataTable({
					"bPaginate": true
					,"bStateSave": true
					,"aaSorting": [[1,'desc']]
				});
			} );
		</script>
	</head>
	<body>
		<h1>PK Test</h1>

		<table id="trade_data">
			<thead>
				<tr>
					<th>Item</th>
					<th>Volume</th>
					<th>Food Value Index</td>
					<th>Gold Volume Value</td>
					<th>Food Volume Value</td>
					<th>Gold For 1</th>
					<th>Gold For 10</th>
					<th>Gold For 100</th>
					<th>Food For 1</th>
					<th>Food For 10</th>
					<th>Food For 100</th>
				</tr>
			</thead>
			<tbody>
		<?php foreach($items as $item):?>
				<tr>
					<td><?=$item['name']?></td>
					<td><?=number_format($item['gold']['Volume']+$item['food']['Volume'],0,null,'')?></td>
					<td><?=(!empty($item['food']['Price']))?number_format($item['gold']['Price']/$item['food']['Price'],3,null,''):0?></td>
					<td><?=number_format($item['gold']['Volume']*$item['gold']['Price'],0,null,'')?></td>
					<td><?=number_format($item['food']['Volume']*$item['food']['Price'],0,null,'')?></td>
					<td><?=number_format($item['gold']['Price'],2,null,'')?></td>
					<td><?=number_format($item['gold']['Price']*10,0,null,'')?></td>
					<td><?=number_format($item['gold']['Price']*100,0,null,'')?></td>
					<td><?=number_format($item['food']['Price'],3,null,'')?></td>
					<td><?=number_format($item['food']['Price']*10,0,null,'')?></td>
					<td><?=number_format($item['food']['Price']*100,0,null,'')?></td>
				</tr>
		<?php endforeach;?>
			</tbody>
		</table>
	</body>
</html>