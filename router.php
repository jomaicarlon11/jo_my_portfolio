<?php
    require_once 'vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    function build_debug_SQL($query, $params) {
        foreach ($params as $key => $value) {
            $replacement = is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
            $query = str_replace(':' . $key, $replacement, $query);
        }
        return $query;
    }

    function AES256_Encryptor($cryptography, $data_value){
        $encyptor_PIN = 265700;
        $response_result = "";

        // $url = "http://". $_SERVER['HTTP_HOST'] . ':5000/'. basename(dirname(__FILE__)) ."/"."AES256_Encryptor/".$cryptography."_jomai";
        $url = "http://127.0.0.1:5000/AES256_Encryptor/".$cryptography."_jomai";

        $api_data_body = [
            'text' => $data_value,
            'pin' => $encyptor_PIN
        ];

        $jsonData = json_encode($api_data_body);

        // Set up cURL
        $curl_handle = curl_init($url);

        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_POST, true);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ]);

        // Execute and get the response
        $curl_response = curl_exec($curl_handle);

        if (curl_errno($curl_handle)) {
            // echo "<br>" . "cURL error: " . curl_error($curl_handle) . "<br>";
        } 
        
        else {
            $response_result = json_decode($curl_response, true)['result'];
        }
        curl_close($curl_handle);

        // if (class_exists('COM')) {
        //     $encryptor_dll = new COM("Jomai_AES256.iamoj_encryptor");
        //     $encryptor_class = "jomai_".$cryptography;
        //     $response_result = $encryptor_dll->$encryptor_class($data_value, $encyptor_PIN);
        // } 

        return $response_result;
    }

    function s3ndEmail($email_receiver, $email_subject, $email_body, $attachment_path){
        $email_setup = new PHPMailer(true);
        $email_sender_username = AES256_Encryptor("decrypt", "U2FsdGVkX1+P614g7ddNy/+HTQ/2J5jS8VBffO0MDhWSexk13Ohp3RBNYzXj7j6Zw5HmizW5DGH05sXoVpvFjQ==");
        $email_sender_password = AES256_Encryptor("decrypt", "U2FsdGVkX1+L4jvfh22paz1wY1SO5d2a0lkIIU7+3oTrQNGugOmwXuNXIsYFAF16");

        try {
            // Server settings
            $email_setup->isSMTP();
            $email_setup->Host       = 'smtp.gmail.com';
            $email_setup->SMTPAuth   = true;
            $email_setup->Username   = $email_sender_username;
            $email_setup->Password   = $email_sender_password;
            $email_setup->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $email_setup->Port       = 465;
        
            // Recipients
            $email_setup->setFrom($email_sender_username, "Jo My Portfolio");
            $email_setup->addAddress($email_receiver);
        
            // Content
            $email_setup->isHTML(true);
            $email_setup->CharSet = 'UTF-8';
            $email_setup->Subject = $email_subject;
            $email_setup->Body    = $email_body;

            // Attachment
            if($attachment_path != "") $email_setup->addAttachment($attachment_path);
            
            $email_setup->send();
            
            return "SENT!!!";
        } catch (Exception $e) {
            // echo "|||||".$e->getMessage()."|||||";
            return "Error: {$email_setup->ErrorInfo} || Exception: {$e->getMessage()}";
        }
    }

    function s3nding_email($email_address, $f0rm_name, $f0rm_subject, $f0rm_message){
        $subject = "Get In Touch - JoMyPortfolio";
        $body = '
                <div style="font-family: Arial, sans-serif; font-size: 15px; color: #333;">
                
                    <p style="margin: 0 0 12px 0;">Dear Mr./Ms. '.$f0rm_name.',</p>
                
                    <p style="margin: 0 0 30px 0;">This is to inform you that I’ve received your message! 💌</p>
                
                    <!-- BORDER BOX START -->
                    <div style="
                        border: 1px solid #ccc;
                        padding: 15px;
                        margin: 0 0 25px 0;
                        border-radius: 6px;
                        background: #fafafa;
                    ">
                
                        <p style="margin: 0 0 12px 0;">
                            <i><b>Subject: </b>'.$f0rm_subject.'</i>
                        </p>
                
                        <p style="margin: 0;"><i><b>Message:</b></i></p>
                        <p style="margin: 0;">
                            <i>'.nl2br($f0rm_message).'</i>
                        </p>
                
                    </div>
                    <!-- BORDER BOX END -->
                
                    <p style="margin: 0 0 12px 0;">Thank you so much for contacting me. ☺️</p>
                
                    <p style="margin: 0 0 5px 0;">Best regards,</p>
                    <p style="margin: 0;"><b>John Malachi M. Carlon</b></p>
                    <p style="margin: 0;"><i>Software Developer</i></p>
                
                </div>
        ';
        
        return s3ndEmail($email_address, $subject, $body, "");
    }

    include("send_message.php");
?>