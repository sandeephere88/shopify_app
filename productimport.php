<?php


function downloadProducts($api_key, $password, $store_url)
{
      
		$product_count1 = "https://api_key:$password@$store_urladmin/products/count.json";
		$data = file_get_contents($product_count1);
		$result= json_decode($data);
		$product_count = $result->count; 
        echo "the product count is". $product_count;
        $page = 1;
        $total_products = 0;
        $i = 0;

        while($total_products < $product_count){
            // create curl resource 
            $ch = curl_init(); 
			
            // set url 
            //curl_setopt($ch, CURLOPT_URL, "http://$api_key:$password@$store_url/admin/products.xml?limit=250&#38;page=$page"); 
            curl_setopt($ch, CURLOPT_URL, "https://$api_key:$password@$store_url/admin/products.xml"); 

            //return the transfer as a string 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            // $output contains the output string 
            $xmlString = htmlspecialchars(curl_exec($ch)); 
            // close curl resource to free up system resources 
            curl_close($ch);
            

            $xml = simplexml_load_string(html_entity_decode($xmlString), 'SimpleXMLElement', LIBXML_NOCDATA);
            var_dump($xml); //exit();
            
            //$products = $xml-'&gt;'{'product'};
            $products = $xml->product;

            //
            $total_products = $total_products + sizeof($products);
            //

            foreach($products as $product)
            {

                $con = mysqli_connect("localhost", "database_username", "database_password","your_database_name") or die(mysqli_error());
                echo $i." of $product_count...\n";

             
                $id             = $product->id;
                $body             = $product->body;
                $body = mysqli_real_escape_string($con,$body);
                $body_html         = $product->body_html;
                $body_html = mysqli_real_escape_string($con,$body_html);
                $created_at     = $product->created_at;
                $handle         = $product->handle;
                $handle = mysqli_real_escape_string($con,$handle);

                $product_type     = $product->product_type;
                $published_at     = $product->published_at;
                $title             = $product->title;
                $title = mysqli_real_escape_string($con,$title);

                $updated_at     = $product->updated_at;
                $vendor         = $product->vendor;
                $tags             = $product->tags;
                //print_r($con);

                // Insert a row of information into the table "products" 
                mysqli_query($con,"INSERT INTO products (id, body, body_html, created_at, handle, product_type, published_at, title, updated_at, vendor, tags) 
                VALUES('$id', '$body', '$body_html', '$created_at', '$handle', '$product_type', '$published_at', '$title', '$updated_at', '$vendor', '$tags') 
                ON duplicate KEY UPDATE id = '$id', body = '$body', body_html = '$body_html', created_at = '$created_at', handle = '$handle', product_type = '$product_type', published_at = '$published_at', title = '$title', updated_at = '$updated_at', vendor = '$vendor', tags = '$tags' ") or die(mysqli_error($con));

                

                $i++;
            }
            //
            $page++;
        }

}
	$api_key = 'api_key_generated_from_shopify';
    $password = 'api_password_generated_from_shopify';
    $store_url = 'your_storeurl.myshopify.com';
	downloadProducts($api_key, $password, $store_url);
?>
