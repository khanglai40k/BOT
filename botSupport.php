<?php
require('../connect.php');
class Bot
{
    private $bot = NULL;

    function __construct($token)
    {
        $this->bot = $token;
    }

    public function sendMessage($id, $text, $reply = "")
    {
        return $this->GET('sendMessage?chat_id=' . $id . '&text=' . urlencode($text) . '&reply_to_message_id=' . $reply . '&allow_sending_without_reply=true')["ok"];
    }

    public function sendPhoto($params)
    {
        $url = "https://api.telegram.org/bot" . $this->bot . "/sendPhoto";
        return $this->sendRequest($url, $params);
    }
    public function sendVideo($params)
    {
        $url = "https://api.telegram.org/bot" . $this->bot . "/sendVideo";
        return $this->sendRequest($url, $params);
    }
    public function sendMessageWithKeyboard($chat_id, $text, $keyboard)
    {
        $url = "https://api.telegram.org/bot" . $this->bot . "/sendMessage";
        $params = [
            'chat_id' => $chat_id,
            'text' => $text,
            'reply_markup' => json_encode(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true])
        ];
        return $this->sendRequest($url, $params);
    }

    private function GET($param)
    {
        $url = "https://api.telegram.org/bot" . $this->bot . "/" . $param;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return json_decode($resp, 1);
    }

    private function sendRequest($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}

// Lấy dữ liệu từ Telegram khi có tin nhắn
$data = file_get_contents('php://input');
// file_put_contents('data.txt', $data);
$bot = new Bot('6673737844:AAFH5KJGasI13_URSiEpCgMsYRpgeoxoeuo');

$json = json_decode($data, true);
$keyboard = [
    ['🚀 Giới Thiệu Web Của Bạn!!!'], // Button 1
    ['🐶 Cách để đăng tải pet của bạn'], // Button 2
    ['♥️♥️♥️ Cách để nhận nuôi pet nhanh nhất'],
    ['😭 Tôi quên mật khẩu web bây giờ phải làm sao!!!'],
    ['🍾 Lưu ý về chính sách khi nhận nuôi pet'],
    ['Khác'],
    ['Từ Chối👎👎👎'],
    ['Đồng Ý👍🏿👍🏿👍🏿']

];


// Khi người dùng nhập dữ liệu và gửi 
if (isset($json['message']['text']) || isset($json["message"]["photo"]) || isset($json["message"]["video"])) {



    function getPetDetails($conn, $pet_id)
    {
        $sql = "SELECT * FROM pets WHERE id_pet='$pet_id'";
        return mysqli_query($conn, $sql);
    }

    function getAdoptionDetails($conn, $pet_id, $id_account_boss)
    {
        $sql = "SELECT id_account, trangthai FROM adopt WHERE pet_id='$pet_id' AND id_account_boss='$id_account_boss'";
        return mysqli_query($conn, $sql);
    }

    function updateAdoptionStatus($conn, $id_account_boss, $id_account, $status)
    {
        $sql = "UPDATE adopt SET trangthai='$status' WHERE id_account_boss='$id_account_boss' AND id_account='$id_account'";
        return mysqli_query($conn, $sql);
    }

    function getAccountDetails($conn, $id_account)
    {
        $sql = "SELECT * FROM account WHERE id_account='$id_account' AND tele_id !='0'";
        return mysqli_query($conn, $sql);
    }

    function sendMessage($bot, $chat_id, $message)
    {
        $bot->sendMessage($chat_id, $message);
    }
    // dữ liệu mình nhận
    $message = $json['message']['text'];
    $chat_id = $json["message"]["chat"]["id"];
    if (isset($json['message']['reply_to_message']['message_id']) || isset($json['message']['reply_to_message']['text'])) {

        $rep_id_user = $json['message']['reply_to_message']['message_id'];
        $mess_user = $json['message']['reply_to_message']['text'];
    }
    // lệnh lấy ảnh chất lg cao API tele
    if (isset($json["message"]["photo"])) {
        $photo = end($json["message"]["photo"]);
        $file_id = $photo['file_id'];
    }
    if (isset($json["message"]["video"])) {
        $video = $json["message"]["video"];
        $file_id_video = $video["file_id"];
    }

    $first_name = $json['message']['from']['first_name'];
    if ($chat_id == '5070388369') {
        // xác định cái nhân viên đang trả lời tin nhắn nào của mình, 
        // sau đó lấy id trong tin nhắn đó(vì bot gửi kèm cả id rồi)
        $rep_id = $json['message']['reply_to_message']['message_id'];
        $mess = $json['message']['reply_to_message']['text'];
        //         if($message = 'Trừ ⭐')
        // {
        //      $bot->sendMessage($chat_id, "Nhập tên nhân vật bạn muốn trừ sao !!!");

        // }
        // elseif($message != 'Trừ ⭐') {
        //     $minus = "SELECT start FROM account WHERE names= '$message'";
        //     $query_minus = mysqli_query($conn,$minus);
        //     if(mysqli_num_rows($query_minus) > 0){
        //         if($row = mysqli_fetch_array($query_minus)){
        //             $start = $row['start'];
        //             if($start >= '1'){
        //             $start_now = $start -1;
        //             $update_start = "UPDATE account SET start='$start_now' WHERE names='$message'";
        //             mysqli_query($conn,$update_start);
        //             $bot->sendMessage($chat_id, "Tên Nhân Vật: $message , Số sao hiện tại còn lại: $start_now");
        //             }else{
        //                  $bot->sendMessage($chat_id, "Số sao người này ko đủ để trừ");
        //             }
        //         }
        //     }else{
        //          $bot->sendMessage($chat_id, "Tên Nhân Vật Không Hợp Lệ !!!");
        //     }
        // }  
        // cái đoạn này là  đang tách lấy cái id ra thôi á ko có gì
        if (preg_match('/id:\s?(\d+)/i', $mess, $matches)) {
            if (isset($photo)) {
                $params = [
                    'chat_id' => $matches[1],
                    'photo' => $file_id,
                    'caption' => 'AD gửi Bạn Ảnh Nhé!!!'
                ];
                $bot->sendPhoto($params);
            }


            if (isset($video)) {
                $params = [
                    'chat_id' => $matches[1],
                    'video' => $file_id_video,
                    'caption' => 'AD gửi Bạn Video Nhé!!!'
                ];
                $bot->sendVideo($params);
            }
            $bot->sendMessage($matches[1], $message);
        }
        // Phê duyệt pet
        elseif (preg_match('/PET\s*:\s*(\d+)/i', $mess, $matches_pet)) {
            $id_pet_tmp = $matches_pet[1];
            // $bot->sendMessage($chat_id,  $id_pet_tmp); ok
            $check_noti = "SELECT * FROM pets_tmp WHERE id_pet='$id_pet_tmp'";
            $query_check = mysqli_query($conn, $check_noti);
            if (mysqli_num_rows($query_check) > 0) {

                if ($message == 'Đồng Ý') {

                    $sql_accepted = "INSERT INTO pets(name_pet, type_pet, colour_pet, address_pet, note_pet, id_account, avt_pet, time_uppset, province, district, ward)
                         SELECT name_pet, type_pet, colour_pet, address_pet, note_pet, id_account, avt_pet, time_uppset, province, district, ward
                         FROM pets_tmp
                         WHERE id_pet = '$id_pet_tmp'";
                    $query = mysqli_query($conn, $sql_accepted);

                    $id_pet_new = $conn->insert_id;

                    $sql_select = "INSERT INTO myPet(id_pet, id_account, pet_trangthai)
                       SELECT id_pet, id_account, 'Đăng Tải'
                       FROM pets
                       WHERE id_pet = '$id_pet_new'";
                    $query_insert = mysqli_query($conn, $sql_select);

                    if ($query && $query_insert) {
                        $bot->sendMessage($chat_id, "Pet Đã Được Thêm!!!,sẽ có thông báo cho toàn sever pet mới ngay sau đây !!!");
                        $new = "SELECT tele_id FROM account WHERE tele_id != '0'";
                        $query_new = mysqli_query($conn, $new);

                        while ($row_new = mysqli_fetch_array($query_new)) {
                            $chat_id_user = $row_new['tele_id'];
                            $base_url = 'https://happet.actives-sitemanagements.cloud/UploadAvt/';
                            $sql_update = "SELECT * FROM pets ORDER BY time_uppset DESC LIMIT 1";
                            $query_update = mysqli_query($conn, $sql_update);

                            if ($query && mysqli_num_rows($query_update) > 0) {
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
                                        // Send notification
                                        $icon = ($type == 'Mèo') ? '😺😺😺' : '🐶🐶🐶';
                                        $img_url = $base_url . $avt;
                                        $link = 'https://happet.actives-sitemanagements.cloud/Detail.php?idpet=' . $id_pet;
                                        $caption = "Thông Báo Có Pet Mới  $icon  \n";
                                        $caption .= "Tên: $name\n";
                                        $caption .= "Loại: $type\n";
                                        $caption .= "Màu: $color\n";
                                        $caption .= "Địa chỉ: $address - $ward - $district - $province\n";
                                        $caption .= "Ghi chú: $note\n";
                                        $caption .= "Thời gian: $time\n";
                                        $caption .= "Người đăng: $name_user\n";
                                        $caption .= "Xem --> : $link\n";

                                        $bot->sendPhoto([
                                            'chat_id' => $chat_id_user,
                                            'photo' => $img_url
                                        ]);
                                        $bot->sendMessage($chat_id_user, $caption);
                                        $delete_tmp = "DELETE FROM pets_tmp WHERE id_pet='$id_pet_tmp'";
                                        mysqli_query($conn, $delete_tmp);
                                    }
                                }
                            }
                        }
                    } else {
                        $bot->sendMessage($chat_id, "Có Lỗi");
                    }
                } else {
                    $bot->sendMessage($chat_id, "Bạn đã từ chối pet của người dùng. Trong trường hợp họ dùng telegram họ sẽ có thông báo!!!");
                    $check_id = "SELECT * FROM pets_tmp WHERE id_pet ='$id_pet_tmp'";
                    $query_check = mysqli_query($conn, $check_id);
                    if ($row = mysqli_fetch_array($query_check)) {
                        $id_accounter = $row['id_account'];
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
                        $base_url = 'https://happet.actives-sitemanagements.cloud/UploadAvt/';
                        $select_id_tele = "SELECT tele_id FROM account WHERE id_account='$id_accounter' AND tele_id!= '0'";
                        $query_select_id = mysqli_query($conn, $select_id_tele);
                        if (mysqli_num_rows($query_select_id) > 0) {
                            if ($row_id = mysqli_fetch_array($query_select_id)) {
                                $id_tele = $row_id['tele_id'];
                            }
                            // Send notification
                            $icon = ($type == 'Mèo') ? '😺😺😺' : '🐶🐶🐶';
                            $img_url = $base_url . $avt;
                            $caption = "Thông Báo Pet của bạn bị từ chối  $icon  \n";
                            $caption .= "Tên: $name\n";
                            $caption .= "Loại: $type\n";
                            $caption .= "Màu: $color\n";
                            $caption .= "Địa chỉ: $address - $ward - $district - $province\n";
                            $caption .= "Ghi chú: $note\n";
                            $caption .= "Thời gian: $time\n";
                            $bot->sendPhoto([
                                'chat_id' => $id_tele,
                                'photo' => $img_url
                            ]);
                            $bot->sendMessage($id_tele, $caption);
                            $bot->sendMessage($id_tele, "Lý Do: $message");
                            $bot->sendMessage($id_tele, "Hãy Nhắn với chúng tôi để nhận ngay trợ giúp nhé!!!");
                        } else {
                            $bot->sendMessage($chat_id, "Thât tiếc khi người dùng ko dùng TELEGRAM ");
                        }
                    }
                }
            }
        } else {
            $bot->sendMessage($chat_id, "Pet này bạn đã nhận nuôi rồi!!!");
        }
    } else {
        if ($message == '/start') {
            $bot->sendMessageWithKeyboard($chat_id, "Xin chào $first_name. Chọn một trong các lựa chọn dưới đây:", $keyboard);
        } elseif ($message == '🚀 Giới Thiệu Web Của Bạn!!!') {
            $bot->sendMessage($chat_id, "Giới thiệu về PetExchange 🐾

        Chào mừng bạn đến với PetExchange 🐶🐱, nền tảng trao đổi thú cưng hàng đầu! Chúng tôi hiểu rằng thú cưng không chỉ là những người bạn bốn chân mà còn là thành viên quan trọng trong gia đình bạn. Với PetExchange, chúng tôi mong muốn kết nối những người yêu thú cưng với nhau, tạo cơ hội để các bạn tìm được những người bạn mới đáng yêu và phù hợp nhất.
        
        Tại sao chọn PetExchange? 🌟
        
        Mạng lưới rộng khắp 🌍: PetExchange cung cấp một cộng đồng rộng lớn của những người yêu thú cưng, giúp bạn dễ dàng tìm kiếm và trao đổi thú cưng phù hợp.
        Thông tin minh bạch 📋: Mọi thông tin về thú cưng đều được cung cấp chi tiết và chính xác, từ tình trạng sức khỏe đến tính cách, giúp bạn có quyết định đúng đắn.
        Hỗ trợ tận tâm ❤️: Đội ngũ của chúng tôi luôn sẵn sàng hỗ trợ bạn trong quá trình tìm kiếm và trao đổi thú cưng, đảm bảo trải nghiệm của bạn luôn suôn sẻ và hài lòng.
        An toàn và bảo mật 🔒: Chúng tôi cam kết bảo mật thông tin cá nhân của bạn và đảm bảo mọi giao dịch đều diễn ra an toàn.
        Các tính năng nổi bật 🌟
        
        Tìm kiếm thú cưng 🔍: Dễ dàng tìm kiếm thú cưng theo loài, giống, độ tuổi, và nhiều tiêu chí khác.
        Đăng tin trao đổi 📣: Đơn giản đăng tin tìm chủ mới hoặc trao đổi thú cưng với các thành viên khác trong cộng đồng.
        Cộng đồng yêu thú cưng 🐾: Tham gia vào các diễn đàn, nhóm thảo luận và sự kiện để chia sẻ kinh nghiệm và kết nối với những người có cùng sở thích.
        Hãy tham gia PetExchange ngay hôm nay! 🚀
        
        Nếu bạn đang tìm kiếm một người bạn mới cho gia đình hoặc muốn tìm chủ mới đáng tin cậy cho thú cưng của mình, PetExchange là nơi dành cho bạn. Hãy tham gia cùng chúng tôi để trải nghiệm một cộng đồng yêu thương và gắn kết, nơi mọi thú cưng đều có cơ hội tìm được ngôi nhà mới tràn đầy yêu thương.
        
        PetExchange - Nơi kết nối những trái tim yêu thương thú cưng! 🏡❤️");
            exit();
        } elseif ($message == '🐶 Cách để đăng tải pet của bạn') {
            $bot->sendMessage($chat_id, "Tự tìm tòi");
        } elseif ($message == '♥️♥️♥️ Cách để nhận nuôi pet nhanh nhất') {
            $bot->sendMessage($chat_id, "**Làm thế nào để nhận nuôi thú cưng nhanh chóng trên PetExchange 🐾**

        Nhận nuôi thú cưng là một hành trình tuyệt vời và đầy ý nghĩa. Trên PetExchange, chúng tôi luôn mong muốn giúp bạn tìm được người bạn bốn chân hoàn hảo nhất một cách nhanh chóng và dễ dàng. Dưới đây là một số mẹo giúp bạn nhận nuôi thú cưng nhanh hơn:
        
        1. **Chăm chỉ truy cập trang web của chúng tôi 📲**
           - Hãy thường xuyên lướt trang web PetExchange để cập nhật các tin đăng mới nhất. Những thú cưng cần tìm nhà mới thường được đăng tải hàng ngày, và việc kiểm tra thường xuyên sẽ giúp bạn không bỏ lỡ bất kỳ cơ hội nào.
        
        2. **Đăng ký nhận thông báo 📧**
           - Đăng ký nhận thông báo qua email để nhận tin mới nhất về các thú cưng đang cần nhận nuôi. Điều này giúp bạn nhanh chóng biết được khi nào có thú cưng phù hợp với tiêu chí của bạn.
        
        3. **Theo dõi trang mạng xã hội của chúng tôi 📱**
           - Hãy theo dõi PetExchange trên các mạng xã hội như Facebook, Instagram, và Twitter để cập nhật tin tức và các bài đăng về thú cưng mới nhất. Chúng tôi thường xuyên chia sẻ thông tin về các thú cưng đang cần nhận nuôi trên các kênh này.
        
        4. **Sử dụng bộ lọc tìm kiếm thông minh 🔍**
           - Sử dụng các bộ lọc tìm kiếm để thu hẹp danh sách thú cưng theo các tiêu chí như loại, giống, tuổi, và giới tính. Điều này giúp bạn dễ dàng tìm thấy thú cưng phù hợp nhất.
        
        5. **Liên hệ nhanh chóng và chủ động 📞**
           - Khi bạn tìm thấy một thú cưng phù hợp, hãy liên hệ ngay với người đăng tin. Sự nhanh chóng và chủ động trong việc liên lạc sẽ tăng cơ hội nhận nuôi thú cưng của bạn.
        
        6. **Chuẩn bị sẵn sàng 🏡**
           - Hãy chuẩn bị sẵn sàng môi trường sống và các vật dụng cần thiết cho thú cưng trước khi nhận nuôi. Điều này không chỉ giúp bạn sẵn sàng khi tìm được thú cưng mà còn thể hiện sự nghiêm túc của bạn đối với việc nhận nuôi.
        
        7. **Tham gia vào cộng đồng PetExchange 🐕**
           - Tham gia vào các diễn đàn, nhóm thảo luận, và sự kiện của PetExchange. Kết nối với những người yêu thú cưng khác có thể mang lại cho bạn thông tin và cơ hội nhận nuôi thú cưng một cách nhanh chóng.
        
        Hãy chăm chỉ truy cập và tương tác trên PetExchange để tăng cơ hội nhận nuôi thú cưng nhanh nhất có thể. Chúng tôi luôn ở đây để hỗ trợ bạn trong hành trình tìm kiếm người bạn bốn chân hoàn hảo.
        
        **PetExchange - Nơi kết nối những trái tim yêu thương thú cưng! 🏡❤️**");
        } elseif ($message == '😭 Tôi quên mật khẩu web bây giờ phải làm sao!!!') {
            $bot->sendMessage($chat_id, "Đừng lo lắng, Hãy chat với bot này theo đường link: https://t.me/pespassbot");
        } elseif ($message == '🍾 Lưu ý về chính sách khi nhận nuôi pet') {
            $bot->sendMessage($chat_id, "**Chính sách nhận nuôi thú cưng tại PetExchange 🐾**

        Tại PetExchange, chúng tôi luôn đặt lợi ích và sự an toàn của thú cưng lên hàng đầu. Chính sách của chúng tôi nhằm đảm bảo rằng mọi thú cưng đều nhận được sự chăm sóc và yêu thương mà chúng xứng đáng có được. Dưới đây là một số quy định quan trọng mà chúng tôi yêu cầu tất cả những người nhận nuôi phải tuân thủ:
        
        1. **Không đánh đập thú cưng 🚫**
           - Chúng tôi tuyệt đối không chấp nhận bất kỳ hình thức bạo lực nào đối với thú cưng. Mọi hành vi đánh đập, hành hạ, hoặc ngược đãi thú cưng đều bị nghiêm cấm. Những hành vi này không chỉ gây tổn thương về thể chất mà còn ảnh hưởng nghiêm trọng đến tâm lý của thú cưng.
        
        2. **Cung cấp môi trường sống an toàn và lành mạnh 🏡**
           - Người nhận nuôi phải đảm bảo rằng thú cưng có một môi trường sống an toàn, sạch sẽ và thoải mái. Điều này bao gồm không gian sinh hoạt đủ rộng, nơi trú ẩn ấm áp, và điều kiện vệ sinh tốt.
        
        3. **Đảm bảo chăm sóc y tế đầy đủ 💉**
           - Thú cưng cần được chăm sóc y tế đầy đủ, bao gồm tiêm phòng, kiểm tra sức khỏe định kỳ, và điều trị kịp thời khi có vấn đề sức khỏe. Người nhận nuôi phải cam kết cung cấp những dịch vụ y tế cần thiết cho thú cưng.
        
        4. **Cung cấp chế độ dinh dưỡng hợp lý 🍲**
           - Người nhận nuôi cần đảm bảo rằng thú cưng nhận được chế độ ăn uống dinh dưỡng và phù hợp với từng loài, giống, và tuổi tác. Nước uống sạch và thực phẩm chất lượng là điều cần thiết để thú cưng phát triển khỏe mạnh.
        
        5. **Yêu thương và dành thời gian cho thú cưng ❤️**
           - Thú cưng không chỉ cần thức ăn và chỗ ở, mà còn cần tình yêu thương và sự chú ý. Người nhận nuôi phải dành thời gian chơi đùa, huấn luyện, và chăm sóc thú cưng mỗi ngày để chúng cảm thấy được yêu thương và an toàn.
        
        6. **Theo dõi và báo cáo tình trạng thú cưng 📋**
           - Chúng tôi khuyến khích người nhận nuôi thường xuyên cập nhật tình trạng của thú cưng và báo cáo với PetExchange nếu có bất kỳ vấn đề nào phát sinh. Điều này giúp chúng tôi đảm bảo rằng thú cưng luôn được chăm sóc tốt nhất.
        
        Bằng cách tuân thủ các chính sách này, bạn không chỉ đảm bảo sự an toàn và hạnh phúc cho thú cưng của mình mà còn góp phần xây dựng một cộng đồng yêu thương và trách nhiệm. Chúng tôi tin rằng với sự quan tâm và chăm sóc đúng mức, mọi thú cưng đều có thể sống một cuộc sống vui vẻ và khỏe mạnh.
        
        **PetExchange - Nơi kết nối những trái tim yêu thương thú cưng! 🏡❤️**");
        } elseif ($message  == 'Khác') {
            $bot->sendMessage($chat_id, "Vui lòng nhập câu hỏi của bạn chúng tôi sẽ chuyến tới nhân viên phục vụ!!!");
        } elseif (isset($message) && $message == 'Đồng Ý👍🏿👍🏿👍🏿') {
            if (isset($mess_user)) {
                if (preg_match('/id:\s?(\d+)/i', $mess_user, $matches)) {
                    $pet_id = $matches[1];
                    $query = getPetDetails($conn, $pet_id);

                    if (mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_array($query)) {
                            $id_account_boss = $row['id_account'];
                            $query_boss = getAdoptionDetails($conn, $pet_id, $id_account_boss);

                            while ($row_boss = mysqli_fetch_array($query_boss)) {
                                $id_account = $row_boss['id_account'];
                                $status = $row_boss['trangthai'];
                            }

                            if ($status == 'Chấp nhận') {
                                sendMessage($bot, $chat_id, "Quá giới hạn chấp nhận cho một con Pet!!!");
                            } else {
                                if (updateAdoptionStatus($conn, $id_account_boss, $id_account, 'Chấp nhận')) {
                                    $query_find = getAccountDetails($conn, $id_account);

                                    if (mysqli_num_rows($query_find) > 0) {
                                        while ($row_find = mysqli_fetch_array($query_find)) {
                                            $tele_id_find = $row_find['tele_id'];
                                            sendMessage($bot, $tele_id_find, "Chúc mừng bạn đã có người cho bạn nuôi pet hãy vào web nhé!!!,ID chủ: " . $chat_id);
                                        }
                                    } else {
                                        sendMessage($bot, $chat_id, "Chúng tôi đang cố gắng liên hệ họ! Vì họ không dùng Telegram!!!");
                                    }

                                    $bot->sendMessageWithKeyboard($chat_id, "Bạn đã chấp nhận bàn giao Pet. Hãy tận hưởng khoảng thời gian cuối nhé (Pet tự động đánh dấu ở HOME)!!!" . "ID người đó: " . $tele_id_find, $keyboard);
                                } else {
                                    sendMessage($bot, $chat_id, "Đã có lỗi. Hãy xem lại pet của bạn!!!");
                                }
                            }
                        }
                    } else {
                        sendMessage($bot, $chat_id, "Đã có lỗi. Hãy xem lại pet của bạn");
                    }
                } else {
                    sendMessage($bot, $chat_id, "Bạn phải rep đúng dòng để chúng tôi xác định được bạn đồng ý pet nào!!!");
                }
            }
        } elseif (isset($message) && $message == 'Từ Chối👎👎👎') {
            if (isset($mess_user)) {
                if (preg_match('/id:\s?(\d+)/i', $mess_user, $matches)) {
                    $pet_id = $matches[1];
                    $query = getPetDetails($conn, $pet_id);

                    if (mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_array($query)) {
                            $id_account_boss = $row['id_account'];
                            $query_boss = getAdoptionDetails($conn, $pet_id, $id_account_boss);

                            while ($row_boss = mysqli_fetch_array($query_boss)) {
                                $id_account = $row_boss['id_account'];
                            }

                            if (updateAdoptionStatus($conn, $id_account_boss, $id_account, 'Từ Chối')) {
                                $query_find = getAccountDetails($conn, $id_account);

                                if (mysqli_num_rows($query_find) > 0) {
                                    while ($row_find = mysqli_fetch_array($query_find)) {
                                        $tele_id_find = $row_find['tele_id'];
                                        sendMessage($bot, $tele_id_find, "Rất tiếc bạn không được nhận nuôi pet !!! $pet_id");
                                    }
                                } else {
                                    sendMessage($bot, $chat_id, "Chúng tôi đang cố gắng liên hệ họ! Vì họ không dùng Telegram!!!");
                                }

                                $bot->sendMessageWithKeyboard($chat_id, "Bạn đã từ chối bàn giao pet!!!", $keyboard);
                            } else {
                                sendMessage($bot, $chat_id, "Đã có lỗi. Hãy xem lại pet của bạn!!!");
                            }
                        }
                    } else {
                        sendMessage($bot, $chat_id, "Đã có lỗi. Hình như pet đã bị xoá!!!");
                    }
                } else {
                    sendMessage($bot, $chat_id, "Bạn phải rep đúng dòng để chúng tôi xác định được bạn đồng ý pet nào!!!");
                }
            } else {
                sendMessage($bot, $chat_id, "Đã có lỗi. Hãy xem lại pet của bạn!!!");
            }
        } else {
            // Chuyển câu hỏi của người dùng đến nhân viên
            if (isset($photo)) {
                $params = [
                    'chat_id' => 5070388369,
                    'photo' => $file_id,
                    'caption' => 'Đây là ảnh của ' .  $first_name . 'ID: ' . $chat_id
                ];
                $bot->sendPhoto($params);
            }
            if (isset($video)) {
                $params = [
                    'chat_id' => 5070388369,
                    'video' => $file_id_video,
                    'caption' => 'Đây là ảnh của ' .  $first_name . 'ID: ' . $chat_id
                ];
                $bot->sendVideo($params);
            }
            $bot->sendMessage(5070388369, "ID: $chat_id , Name: $first_name, Nội dung: $message");
            $bot->sendMessage($chat_id, "Câu hỏi của bạn đã được chuyển tới nhân viên, vui lòng chờ phản hồi nhé!!!");
        }
    }
}
// $bot->sendMessageKeyboard($chat_id, $keyboard);
