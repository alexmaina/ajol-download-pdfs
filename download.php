<?Php
#The aim of this code snippet is to download pdfs from ajol based on the urls on table citations
#Part of the code below was uplifted from the link below
#https://stackoverflow.com/questions/15076323/how-to-download-this-pdf-file-using-php
#This is how pdfs were downloaded for the afroscholar project

require 'data.php';
$con = dbConnect();
##query tabel
$query1 = "SELECT id,doi FROM citation1 where id between 2045 and 2541";
	 $sql=$con->prepare($query1);
		$sql->execute();
	$sql->SetFetchMode(PDO::FETCH_ASSOC);
#loop through results
	while ($row = $sql->fetch()) {
		$url = $row['doi'];
		$id = $row['id'];
		#change the urls from view to download
		$url1 = str_replace('view','download',$url);
		//echo $url1.'<br>';
		#store the urls in an array
		$array2[] = $id;
		$array[] = $url1;
		
	}

		#loop through the arrays and extract the key

		echo '<pre>';
			print_r($array);
			print_r($array2);
			print_r(array_combine($array2,$array));
		echo '</pre>';

//combine array 2 and array to create an array $c whose $key is the pdf number.
//This is important to ensure tha pdfs are not overwritten and that pdf numbering starts
//from the last inserted pdf even if running of the script was interrupted and was started again. 
$c = array_combine($array2,$array);


foreach($c as $key => $value){
		echo $value . '<br>';
    
    #loop through the key and create pdfs named on the basis of the key
			for ($x = 0; $x <= $key; $x++) {
    				//echo "The number is: $x <br>";
				$path = "/var/www/pdf2/$key.pdf";
			} 
			
			$ch = curl_init($value);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_REFERER,$value);
			$data = curl_exec($ch);
			curl_close($ch);
			$result = file_put_contents($path, $data);
			if(!$result){
            			echo "error";
    			}else{
            			echo "success";
    			}
		//insert pdf data into table citation_pdf
		$query2 = "INSERT INTO citation_pdf(citation_id,fulltext_link) VALUES(:key,:path)";
		 $sql=$con->prepare($query2);
		$sql->execute(array(
				':key' => $key,
				':path' => $path
				));

}
