<?php
$data = file_get_contents('php://input');
require_once "TelegramBot.php"; // Ensure the path to TelegramBot.php is correct
$bot = new Bot('6673737844:AAFH5KJGasI13_URSiEpCgMsYRpgeoxoeuo');
$json = json_decode($data, true);


$keyboard = [
    ['Đồng Ý'],
    ['Từ Chối'],
    ['Pet chứa nội dung xấu độc'],
    ['Có hành vi spam pet'],
    ['Người dùng điền thiếu thông tin'],
    ['Trừ ⭐']
];

$chat_id = '5070388369';
$base_url = 'https://happet.actives-sitemanagements.cloud/UploadAvt/';

$sql_update = "SELECT * FROM pets_tmp ORDER BY time_uppset DESC LIMIT 1";
$query_update = mysqli_query($conn, $sql_update);

if (mysqli_num_rows($query_update) > 0) {
    while ($row = mysqli_fetch_array($query_update)) {
        $name = $row['name_pet'];
        $type = $row['type_pet'];
        $color = $row['colour_pet'];
        $address = $row['address_pet'];
        $province = $row['province'];
        $district = $row['district'];
        $ward = $row['ward'];
        $note = $row['note_pet'];
        $time = $row['time_uppset'];
        $avt = $row['avt_pet'];
        $id_user = $row['id_account'];
        $id_pet = $row['id_pet'];


        $sql_user = "SELECT names FROM account WHERE id_account = '$id_user'";
        $query_user = mysqli_query($conn, $sql_user);
        while ($rows = mysqli_fetch_array($query_user)) {
            $name_user = $rows['names'];


            $icon = '';
            if ($type == 'Mèo') {
                $icon = '😺😺😺';
            } else {
                $icon = '🐶🐶🐶';
            }

            $caption_extra = '';
            $img_url = $base_url . $avt;
            $link = 'https://happet.actives-sitemanagements.cloud/Detail.php?idpet=' . $id_pet;
            $caption = "Thông Báo Có Pet Mới $icon Cần Bạn Phải Phê Duyệt!!!\n";
            $caption .= "Tên: $name\n";
            $caption .= "Loại: $type\n";
            $caption .= "Màu: $color\n";
            $caption .= "Địa chỉ: $address - $ward - $district - $province\n";
            $caption .= "Ghi chú: $note\n";
            $caption .= "Thời gian: $time\n";
            $caption .= "Người đăng: $name_user\n";
            $caption .= "Xem --> : $link\n";
            $caption_extra .= "ID PET:  $id_pet .Hãy Rep Đúng Dòng Này Chứa Đúng ID pet!!! ";
            $bot->sendPhoto([
                'chat_id' => $chat_id,
                'photo' => $img_url
            ]);
            // $bot->sendMessage($chat_id, $caption);
            $bot->sendMessageWithKeyboard($chat_id, $caption, $keyboard);
            $bot->sendMessage($chat_id, $caption_extra);
        }
    }
}
