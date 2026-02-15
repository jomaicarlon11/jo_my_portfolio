<?php
    include("_dbconfig.php");
    header('Content-Type: application/json');

    $message = [
        "resp0nse_status" => "success!!!",
        "resp0nse_message" => "",
        "sql_debugger" => "",
    ];

    // For debugging: Uncomment this to check the raw POST data sent
    // echo json_encode($_POST);

    $SQL = "";
    $array_datasss = [];

    try{
        if (
            isset($_POST['form_name'])
            && isset($_POST['form_email'])
            && isset($_POST['form_code'])
            && isset($_POST['form_subject'])
            && isset($_POST['form_message'])
            ) 
        {
            $array_datasss = [ '_XXX_reference_code' => $_POST["form_code"], ];
            $stat3m3nt = $pdo_db_connection->prepare("SELECT COUNT(*) AS 'count' FROM ".$pdo_database_name.".reference_codes WHERE ref_code = :_XXX_reference_code ;"); $stat3m3nt->execute($array_datasss);
            $c0unt_reference_code = $stat3m3nt->fetch(PDO::FETCH_ASSOC)['count'];

            if (!filter_var($_POST['form_email'], FILTER_VALIDATE_EMAIL)){
                $message["resp0nse_status"] = "validation!!!";
                $message["resp0nse_message"] = "The Email Address is invalid format.";
            }

            else if($c0unt_reference_code < 1){
                $message["resp0nse_status"] = "validation!!!";
                $message["resp0nse_message"] = "Reference code is invalid.";
            } 
            
            else{
                
                $array_datasss = [
                    '_XXX_name' => strtoupper($_POST['form_name']),
                    '_XXX_email' => strtoupper($_POST['form_email']),
                    '_XXX_reference_code' => strtoupper($_POST['form_code']),
                    '_XXX_subject' => strtoupper($_POST['form_subject']),
                    '_XXX_message' => strtoupper($_POST['form_message']),
                ];

                $SQL =" INSERT INTO ".$pdo_database_name.".get_in_touch (";
                $SQL.=" name";
                $SQL.=" ,email";
                $SQL.=" ,ref_code";
                $SQL.=" ,subject";
                $SQL.=" ,message";
                $SQL.=" ) VALUES (";
                $SQL.=" :_XXX_name";
                $SQL.=" ,:_XXX_email";
                $SQL.=" ,:_XXX_reference_code";
                $SQL.=" ,:_XXX_subject";
                $SQL.=" ,:_XXX_message";
                $SQL.=" );";

                if ($pdo_db_connection->prepare($SQL)->execute($array_datasss)) {
                    $message["sql_debugger"] = build_debug_SQL($SQL, $array_datasss);

                    if ($send_email = s3nding_email(strtoupper($_POST['form_email']), strtoupper($_POST['form_name']), $_POST['form_subject'], $_POST['form_message']) != "SENT!!!"){
                        $message["resp0nse_status"] = "error!!!";
                        $message["resp0nse_message"] = "Sending Email Error||".$send_email;
                    }
                } 
                
                else {

                    $message["resp0nse_status"] = "error!!!";
                    $message["resp0nse_message"] = "Execute query failed";
                    $message["sql_debugger"] = build_debug_SQL($SQL, $array_datasss);
                }
            }
            
        } 
        
        else {
            $message["resp0nse_status"] = "error!!!";
            $message["resp0nse_message"] = "Failed to ISSET";
        }
    }

    catch (Exception $e) {
        $message["resp0nse_status"] = "error!!!";
        $message["resp0nse_message"] = $e->getMessage();
        $message["sql_debugger"] = build_debug_SQL($SQL, $array_datasss);
    }

    
    // Comment this for debugging mode
    $message["sql_debugger"] = "";
    echo json_encode($message);
?>