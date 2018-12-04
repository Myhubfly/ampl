<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Refresh" content="3; url=../feedback.html">
    <meta http-equiv="content-type" content="text/html" charset="utf-8">
</head>
<body bgcolor="silver">
<?php
if(isset($_POST['name']) && isset($_POST['mail']) && isset($_POST['text']) && isset($_POST['phone']))
{
    $nam = $_POST['name'];
    $phon = $_POST['phone'];
    $text = $_POST['text'];
    $mail = $_POST['mail'];
    if($nam=="" or $mail=="" or $text=="" or $phon=="")
    {
        echo "<h2>Поля не заполнены</h2>";
    }else
    {
        $nam = trim(strip_tags($nam));
        $mail = htmlspecialchars($mail,ENT_QUOTES);
        $text = htmlspecialchars(strip_tags($text),ENT_QUOTES);
        $phon = htmlspecialchars($phon, ENT_QUOTES); 
        if(!preg_match("/[0-9a-z]+@[0-9a-z_^\.]+\.[a-z]{2,3}/i", $mail))
        {
            echo "<h2>email неправильно заполнен!</h2>";
            return false;
        }
        if(strlen($mail)<"7")
        {
            echo "<h2>Е-маил не может быть короче 7-и символов</h2><br>";
            return false;
        }
        if(strlen($nam)>"22")
        {
            echo "<h2>Login не может быть  длиннее 22-х символов</h2><br>";
            return false;
        }
        if(strlen($mail) > "35")
        {
            echo "<h2>Е-маил не может быть длиннее 35-ти символов</h2><br>";
            return false;
        }
        if(!preg_match("/[0-9]/i", $phon))
        {
            echo "<h2>Телефон не может быть отправлен в буквенном выржении, только числа!</h2><br>";
            return false;
        }
        if(!empty($_FILES['fileFF']['tmp_name'])) {
            if ($_FILES['fileFF']['size'] < 1000000) {
            $fileName = $_FILES['fileFF']['name'];
            $tmpName = $_FILES['fileFF']['tmp_name'];
		    $path = "upload/$fileName";
		    move_uploaded_file($tmpName, $path);
		    $fp = fopen($path,"rb");   
            if (!$fp)   
            { print "Ne wozmozno otkrit";   
              exit();   
            }  
			
			$file = fread($fp, filesize($path));   
            fclose($fp); 
            $name = "$fileName"; // в этой переменной надо сформировать имя файла (без всякого пути)  
            $EOL = "\r\n"; // ограничитель строк, некоторые почтовые сервера требуют \n - подобрать опытным путём
			$boundary = md5(date('r', time()));
		    $headers    = "MIME-Version: 1.0;$EOL";   
            $headers   .= "Content-Type: multipart/mixed; boundary=\"$boundary\"$EOL";  
            $headers   .= "From: Флагман@админ.ru";
			$theme = "Сообщение с сайта ФЛАГМАН Автор:$nam Почта:$mail Телефон:$phon";
			$theme = "=?utf-8?B?".base64_encode($theme)."?=";
            $to ="flagcom@mail.ru";	
            $soob = "Cообщение:$text";
			
			$multipart  = "--$boundary$EOL";   
            $multipart .= "Content-Type: text/html; charset= utf-8\r\n";   
            $multipart .= "Content-Transfer-Encoding: base64$EOL";   
            $multipart .= $EOL; // раздел между заголовками и телом html-части 
            $multipart .= chunk_split(base64_encode($soob)); 
			
			$multipart .=  "$EOL--$boundary$EOL";   
            $multipart .= "Content-Type: application/octet-stream; name=\"$name\"$EOL";   
            $multipart .= "Content-Transfer-Encoding: base64$EOL";   
            $multipart .= "Content-Disposition: attachment; filename=\"$name\"$EOL";   
            $multipart .= $EOL; // раздел между заголовками и телом прикрепленного файла 
            $multipart .= chunk_split(base64_encode($file)); 
			$multipart .= "$EOL--$boundary--$EOL"; 

            $go=mail($to,$theme, $multipart, $headers);
                if($go==true)
                {
                    echo "<h2 style='text-align:center;'>Письмо отправлено!</h2>";
		    	    exit;
                }
				}else{echo "<h2 style='text-align:center;'>Недопустимый размер!</h2>";}
		    } else
			{		
		        $to ="flagcom@mail.ru";
				$theme = "Сообщение с сайта ФЛАГМАН";
			    $theme = "=?utf-8?B?".base64_encode($theme)."?=";
		        $soob = "Автор:$nam\nПочта:$mail\nТелефон:$phon\n Cообщение:$text";
                $go=mail($to,$theme,$soob, join("\r\n",array("from:Флагман@админ.ru", "Reply-to:С сайта ФЛАГМАН","Content-type:text/plain; charset= utf-8\r\n")));
                    if($go==true)
                    {
                    echo "<h2>Письмо отправлено!</h2>";
		        	exit;
                    }
		    }
        }
}
?>
</body>
</html>