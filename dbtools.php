<?php
    function create_connection(){
        $link = mysqli_connect("sql102.infinityfree.com", "if0_38646806", "Apple897897") or
        die("連線錯誤" . mysqli_connect_error());

        return $link;
    }

    function execute_sql($link, $db, $sql){
        mysqli_select_db($link, $db) or
        die("連線資料庫錯誤" . mysqli_errno($link));

        $result = mysqli_query($link, $sql);
        return $result;
    }
?>