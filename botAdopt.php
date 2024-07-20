<?php
$data = file_get_contents('php://input');
require_once "TelegramBot.php";
$bot = new Bot('6673737844:AAFH5KJGasI13_URSiEpCgMsYRpgeoxoeuo');
$json = json_decode($data, true);

// Fetch adoption status
$sql_update = "SELECT * FROM adopt WHERE id_account_boss= '$IdAccount' OR id_account = '$IdAccount' ORDER BY time_adopt DESC LIMIT 1";
$query_update = mysqli_query($conn, $sql_update);

if (mysqli_num_rows($query_update) > 0) {
    while ($row_adopt = mysqli_fetch_array($query_update)) {
        $status = $row_adopt['trangthai'];
        $pet_id = $row_adopt['pet_id'];
        $id_account = $row_adopt['id_account'];
        $id_account_boss = $row_adopt['id_account_boss'];

        // Fetch account details
        $sql = "SELECT * FROM account WHERE (id_account = '$id_account' OR id_account = '$id_account_boss') AND tele_id !='0'";
        $query = mysqli_query($conn, $sql);

        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_array($query)) {
                $chat_id = $row['tele_id'];
                $id_table_account = $row['id_account'];
                $base_url = 'https://happet.actives-sitemanagements.cloud/UploadAvt/';
                if ($id_table_account === $id_account_boss) {
                    $sql = "SELECT tele_id FROM account WHERE id_account = '$id_account_boss' AND tele_id !='0'";
                    $querys = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($querys) > 0) {
                        while ($row = mysqli_fetch_array($querys)) {
                            $chat_idss = $row['tele_id'];
                        }

                        $sql_user = "SELECT * FROM pets WHERE id_pet = '$pet_id'";
                        $query_user = mysqli_query($conn, $sql_user);

                        while ($row_pet = mysqli_fetch_array($query_user)) {
                            $id_pet_boss = $row_pet['id_pet'];
                            $address = $row_pet['address_pet'];
                            $province = $row_pet['province'];
                            $district = $row_pet['district'];
                            $name_pet = $row_pet['name_pet'];
                            $type = $row_pet['type_pet'];
                            $color = $row_pet['colour_pet'];
                            $ward = $row_pet['ward'];
                            $avt = $row_pet['avt_pet'];
                            $id_user = $row_pet['id_account'];

                            $sql_user_name = "SELECT names FROM account WHERE id_account = '$id_account'";
                            $query_user_name = mysqli_query($conn, $sql_user_name);

                            while ($row_user = mysqli_fetch_array($query_user_name)) {
                                $name_user = $row_user['names'];
                                $img_url = $base_url . $avt;
                                $link = 'https://happet.actives-sitemanagements.cloud/Detail.php?idpet=' . $pet_id;
                                $caption = "Thông Báo $name_user Vừa Yêu Cầu Nhận Nuôi Pet Của Bạn\n";
                                $caption .= "Tên: $name_pet\n";
                                $caption .= "Loại: $type\n";
                                $caption .= "Màu: $color\n";
                                $caption .= "Địa chỉ: $address - $ward - $district - $province\n";
                                $caption .= "Hãy Phản Hồi Họ Nhé!!!\n";

                                $keyboard = [
                                    ['Đồng Ý👍🏿👍🏿👍🏿'],
                                    ['Từ Chối👎👎👎'],
                                ];

                                $bot->sendPhoto([
                                    'chat_id' => $chat_idss,
                                    'photo' => $img_url,
                                    'caption' => $caption
                                ]);

                                $bot->sendMessageWithKeyboard($chat_idss, "ID: " . $id_pet_boss . " . " . $name_user . " . Rất Thích Nó!!!.Bạn phải trả lời đoạn chat dòng này . Hãy chọn nút đồng ý hoặc từ chối pet:", $keyboard);
                            }
                        }
                    }
                }
                if ($IdAccount === $id_account) {
                    $sql = "SELECT tele_id FROM account WHERE id_account = '$id_account' AND tele_id !='0'";
                    $query = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_array($query)) {
                            $chat_ids = $row['tele_id'];
                        }

                        $sql_user = "SELECT * FROM pets WHERE id_pet = '$pet_id'";
                        $query_user = mysqli_query($conn, $sql_user);

                        while ($row_pet = mysqli_fetch_array($query_user)) {
                            $address = $row_pet['address_pet'];
                            $province = $row_pet['province'];
                            $district = $row_pet['district'];
                            $name_pet = $row_pet['name_pet'];
                            $type = $row_pet['type_pet'];
                            $color = $row_pet['colour_pet'];
                            $ward = $row_pet['ward'];
                            $avt = $row_pet['avt_pet'];

                            $sql_user_name = "SELECT names FROM account WHERE id_account = '$id_account_boss'";
                            $query_user_name = mysqli_query($conn, $sql_user_name);

                            while ($row_user = mysqli_fetch_array($query_user_name)) {
                                $name_user = $row_user['names'];
                                $img_url = $base_url . $avt;
                                $link = 'https://happet.actives-sitemanagements.cloud/Detail.php?idpet=' . $pet_id;
                                $caption = "Thông Báo Bạn Vừa Nhận Nuôi Pet\n";
                                $caption .= "Tên: $name_pet\n";
                                $caption .= "Loại: $type\n";
                                $caption .= "Màu: $color\n";
                                $caption .= "Địa chỉ: $address - $ward - $district - $province\n";
                                $caption .= "Chủ Pet: $name_user\n";
                                $caption .= "Hãy Chờ Phản Hồi Từ Họ Nhé!!!\n";

                                $bot->sendPhoto([
                                    'chat_id' => $chat_ids,
                                    'photo' => $img_url,
                                    'caption' => $caption
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
} else {
    // Handle case when there is no adoption status
    // echo "NOOOOO";
}
