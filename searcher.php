<HTML>
	<HEAD><TITLE>Searcher</TITLE></HEAD>
<BODY>
<FORM action="searcher.php" method="POST">
	<INPUT type="text" name="text" placeholder="search word" size="30" maxlength="30"><BR>
	<INPUT type="checkbox" name="check[]" value="1"/>Fulltext Search<BR>
	<INPUT type="checkbox" name="check[]" value="2"/>Metatag Search<BR>
	<INPUT type="submit" name="submit" value="Submit"/><BR>
</FORM>

<?php
$con=mysql_connect("localhost","shiyu","123456");

if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("indexer", $con);

function searchFullText($word){
	$queryWordId="select word_id from words where word='$word'";
	$resWordId=mysql_query($queryWordId);
	if(isset($resWordId) && (mysql_num_rows($resWordId)==0)){
		echo "no file contains word ".$word."<BR>";
	}
	else{
		$row=mysql_fetch_row($resWordId);
		$word_id=$row[0];
		$queryFileCount="select file_id,count from file_word where word_id='$word_id'";
		$resFileCount=mysql_query($queryFileCount);
		$searcherResult=array();
		while($row1=mysql_fetch_array($resFileCount)){
			$file_id=$row1["file_id"];
			$count=$row1["count"];
			
			$queryFile="select file_name,file_url from files where file_id='$file_id'";
			$resFile=mysql_query($queryFile);
			$row2=mysql_fetch_row($resFile);
			$file_name=$row2[0];
			$file_url=$row2[1];
			if(!empty($file_name)){
				$searcherResult[$file_name]=$count;
			}
			else{
				$searcherResult[$file_url]=$count;
			}						

		}
		arsort($searcherResult);
		foreach ($searcherResult as $key=>$value) {
			if(!empty($key)){
				$key=str_replace("\\", "/",$key );
				//$key1="file:///".$key;
				echo "<a href=$key>".$key."</a>"."      ".$value." iteration(s) of fulltext search "."<BR>";
			}

		}
				
	}
}

function searchMetaTag($word){
	$queryFileId="select file_id from meta_info where type='$word'";
	$resFileId=mysql_query($queryFileId);
	if(isset($resFileId) && (mysql_num_rows($resFileId)==0)){
		echo "no file contains metatag ".$word."<BR>";
	}
	else{
		$searcherResult=array();
		while($row=mysql_fetch_array($resFileId)){
			$file_id=$row[0];
			$queryFile="select file_name,file_url from files where file_id='$file_id'";
			$resFile=mysql_query($queryFile);
			$row2=mysql_fetch_row($resFile);
			$file_name=$row2[0];
			$file_url=$row2[1];
			if(!empty($file_name)){
				$searcherResult[$file_name]=1;
			}
			else{
				$searcherResult[$file_url]=1;
			}		
		}
		arsort($searcherResult);
		foreach ($searcherResult as $key=>$value) {
			if(!empty($key)){
				$key=str_replace("\\", "/",$key );
				echo "<a href=$key>"."file:///".$key."</a>"."      ".$value." iteration(s) of metatag search "."<BR>";
			}

		}
	}
}

if(isset($_POST["text"])){
	$word=$_POST["text"];
	//echo $word;
	if(isset($_POST["check"])){
		$arr=$_POST["check"];
		if(count($arr)==2){
			searchFullText($word);
			searchMetaTag($word);
		}
		else{
			if($arr[0]==1){
				searchFullText($word);
			}
			else{
				searchMetaTag($word);
			}
			
		}
	}	
	
	
}


?>
</BODY>
</HTML>