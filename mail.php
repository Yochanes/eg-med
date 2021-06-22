<?php


require_once "SendMailSmtpClass.php";

$mailSMTP = new SendMailSmtpClass('info-avtonomerspb@yandex.ru', 'ZqaXwsCed123456', 'ssl://smtp.yandex.ru', 465, "utf-8");

$from[0] = "ROBOT"; // Имя отправителя
$from[1] = "gromsam@yandex.ru"; // почта отправителя

$to = 'grifon713@yandex.ru';

// Configure your Subject Prefix and Recipient here
//$subjectPrefix = '[Contact via website]';
//$emailTo       = 'gromsam@yandex.ru';
$errors = array(); // array to hold validation errors
$data   = array(); // array to pass back data



        $subject = 'Нижняя форма с сайта';
//        $name    = stripslashes(trim($_POST['form_name']));
        $email  = stripslashes(trim('gromsam@mail.ru'));
        $form_phone   = 'form_phone';
        $message = 'form_message';

        if (empty($email)) {
            $errors['name'] = 'Email обязательное поле.';
        }else{
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Некорректный Email.';
            }else{
                $from[1] = $email;
            }
        }

        if (!empty($form_phone)) {
            $from[0] = $form_phone;
        }
        if (empty($form_phone)) {
            $errors['form_phone'] = 'Телефон обязательное поле.';
        }
        if (!empty($form_phone)) {
            $from[0] = date("d.m.Y")."_".$form_phone;
        }

        $body = '<strong>Телефон: </strong>'.$form_phone.'<br />
                 <strong>Email: </strong>'.$email.'<br />
                 <strong>Сообщение: </strong>'.nl2br($message).'<br />';

        $res_message = 'Ваша сообщение отправлено';


    // if there are any errors in our errors array, return a success boolean or false
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors']  = $errors;
    } else {
        $headers  = "MIME-Version: 1.1" . PHP_EOL;
        $headers .= "Content-type: text/html; charset=utf-8" . PHP_EOL;

        $result =  $mailSMTP->send($to, $subject, $body, $from);

        if($result === true){
            $data['success'] = true;
            $data['message'] = $res_message;
        }else{
            $data['errors'] =  $result;
        }
    }
    // return all our data to an AJAX call
    echo json_encode($data);


die();













