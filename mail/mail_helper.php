<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//tự động load toàn bộ thư viện (PHPMailer, …)
require __DIR__ . '/../vendor/autoload.php'; // QUAN TRỌNG

//$email nhận, tiêu đề, nội dung
function sendMail($to, $subject, $body)
{

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        //server gửi mail (gmail)
        $mail->Host = MAIL_HOST;
        //bật xác thực (login)
        $mail->SMTPAuth = true;
        //tài khoản + mật khẩu SMTP TỪ mail_config.php
        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASS;
        //tls → mã hóa kết nối
        $mail->SMTPSecure = 'tls';
        //587 → port SMTP Gmail
        $mail->Port = 587;
        // sửa lỗi chữ trong mail
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        //email gửi + tên hiển thị
        $mail->setFrom(MAIL_USER, MAIL_NAME);
        //$mail->addAddress($to); có thể thêm nhìu ng
        $mail->addAddress($to);

        //cho phép html
        $mail->isHTML(true);
        //gán tiêu đề nd
        $mail->Subject = $subject;
        $mail->Body = $body;

        //gửi
        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
