<?php
//thay session_start(); session_status trả về trạng thái session: chưa có session
//Viết này tránh nhìu file gọi cùng lúc
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
