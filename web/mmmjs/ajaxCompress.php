<?php
function ajaxCompressJavascript($sJS)
{
	$sJS = str_replace("\r","",$sJS);
	
	$literal_strings = array();
	
	$lines = explode("\n",$sJS);
	$clean = "";
	$inComment = false;
	$literal = "";
	$inQuote = false;
	$escaped = false;
	$quoteChar = "";
	
	for($i=0;$i<count($lines);$i++)
	{
		$line = $lines[$i];
		$inNormalComment = false;
	
		for($j=0;$j<strlen($line);$j++)
		{
			$c = substr($line,$j,1);
			$d = substr($line,$j,2);
	
			if(!$inQuote && !$inComment)
			{
				if(($c=="\"" || $c=="'") && !$inComment && !$inNormalComment)
				{
					$inQuote = true;
					$inComment = false;
					$escaped = false;
					$quoteChar = $c;
					$literal = $c;
				}
				else if($d=="/*" && !$inNormalComment)
				{
					$inQuote = false;
					$inComment = true;
					$escaped = false;
					$quoteChar = $d;
					$literal = $d;	
					$j++;	
				}
				else if($d=="//") //ignore string markers that are found inside comments
				{
					$inNormalComment = true;
					$clean .= $c;
				}
				else
				{
					$clean .= $c;
				}
			}
			else //allready in a string so find end quote
			{
				if($c == $quoteChar && !$escaped && !$inComment)
				{
					$inQuote = false;
					$literal .= $c;
	
					$clean .= "___" . count($literal_strings) . "___";
	
					array_push($literal_strings,$literal);
	
				}
				else if($inComment && $d=="*/")
				{
					$inComment = false;
					$literal .= $d;
	
					$clean .= "___" . count($literal_strings) . "___";
	
					array_push($literal_strings,$literal);
	
					$j++;
				}
				else if($c == "\\" && !$escaped)
					$escaped = true;
				else
					$escaped = false;
	
				$literal .= $c;
			}
		}
		if($inComment) $literal .= "\n";
		$clean .= "\n";
	}
	$lines = explode("\n",$clean);
	
	for($i=0;$i<count($lines);$i++)
	{
		$line = $lines[$i];
	
		$line = preg_replace("/\/\/(.*)/","",$line);
	
		$line = trim($line);
	
		$line = preg_replace("/\s+/"," ",$line);
	
		$line = preg_replace("/\s*([!\}\{;,&=\|\-\+\*\/\)\(:])\s*/","\\1",$line);
	
		$lines[$i] = $line;
	}
	
	$sJS = implode("\n",$lines);
	
	$sJS = preg_replace("/[\n]+/","\n",$sJS);
	
	$sJS = preg_replace("/;\n/",";",$sJS);
	
	$sJS = preg_replace("/[\n]*\{[\n]*/","{",$sJS);
	
	for($i=0;$i<count($literal_strings);$i++)
		$sJS = str_replace("___".$i."___",$literal_strings[$i],$sJS);
	
	return $sJS;
}
?>