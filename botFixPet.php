<?php
// Lấy dữ liệu từ Telegram khi có tin nhắn
// include('../connect.php');

$data = file_get_contents('php://input');
require_once "TelegramBot.php"; // Đảm bảo rằng đường dẫn đến file TelegramBot.php là chính xác
$bot = new Bot('6673737844:AAFH5KJGasI13_URSiEpCgMsYRpgeoxoeuo');
$json = json_decode($data, true);

//  $keyboard = [
//             ['/start'] 
//         ];
$sql = "SELECT tele_id FROM account WHERE tele_id!='0'";
$query = mysqli_query($conn, $sql);
if (mysqli_num_rows($query) > 0) {


    while ($row = mysqli_fetch_array($query)) {
        $chat_id = $row['tele_id'];
        // echo $chat_id; 
        $base_url = 'https://happet.actives-sitemanagements.cloud/UploadAvt/';
        $sql_update = "SELECT * FROM pets ORDER BY  time_uppset DESC LIMIT 1";
        $query_update = mysqli_query($conn, $sql_update);
        if ($query && mysqli_num_rows($query_update) > 0) {
            while ($row = mysqli_fetch_array($query_update)) {
                $name = $row['name_pet'];
                $type = $row['type_pet'];
                $color = $row['colour_pet'];
                $address = $row['address_pet'];
                $note = $row['note_pet'];
                $time = $row['time_uppset'];
                $avt = $row['avt_pet'];
                $id_user = $row['id_account'];
                $id_pet = $row['id_pet'];
                $sql_user = "SELECT names FROM account WHERE id_account = '$id_user'";
                $query_user = mysqli_query($conn, $sql_user);
                while ($rows = mysqli_fetch_array($query_user)) {
                    $name_user = $rows['names'];
                    // sent
                    $icon = '';
                    if ($type = 'Mèo') {
                        $icon = '😺😺😺';
                    } else {
                        $icon = '🐶🐶🐶';
                    }
                    $img_url = $base_url . $avt;
                    // echo $img_url;
                    $link = 'https://happet.actives-sitemanagements.cloud/Detail.php?idpet=' . $id_pet;
                    $caption = "Thông Báo Có Pet Mới Được Sửa  $icon  \n";
                    $caption .= "Tên: $name\n";
                    $caption .= "Loại: $type\n";
                    $caption .= "Màu: $color\n";
                    $caption .= "Địa chỉ: $address\n";
                    $caption .= "Ghi chú: $note\n";
                    $caption .= "Thời gian: $time\n";
                    $caption .= "Người đăng: $name_user\n";
                    $caption .= "Xem --> : $link\n";
                    $bot->sendPhoto([
                        'chat_id' => $chat_id,
                        'photo' => $img_url
                    ]);
                    $bot->sendMessage($chat_id, $caption);
                }
            }
        }
    }
    // $update_sql = "UPDATE pets SET  WHERE id_pet='{$row['id_pet']}'";
    // mysqli_query($conn, $update_sql);
}
