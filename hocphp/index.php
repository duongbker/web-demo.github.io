<?php
    //print_r($_POST);
    $loi="";
    if( isset($_POST['btndangky']) == true ) {
        $tendangnhap =$_POST['tendangnhap'];
        $matkhau =$_POST['matkhau'];
        $email =$_POST['email'];
        $ngaysinh =$_POST['ngaysinh'];
        $phai =$_POST['phai'];
        $hoten =$_POST['hoten'];

        if (strlen($tendangnhap)==0) {$loi .="Chưa nhập tên đăng nhập <br/>";}
        if (strlen($tendangnhap)< 5) {$loi .="Tên đăng nhập quá ngắn <br/>";}
        if (strlen($matkhau)<6) {$loi .="Mật khẩu quá ngắn (chưa đủ mạnh) <br/>";}
        if (strlen($hoten)==0) {$loi .="Chưa nhập Họ và tên <br/>";}
        if (strlen($email)<5) {$loi .="Email nhập nhập không đúng <br/>";}
        if (strlen($ngaysinh)==0) {$loi .="Vui lòng nhập ngày sinh <br/>";}

        //echo "<p>Tên đăng nhập: $tendangnhap Mật khẩu: $matkhau Họ và Tên: $hoten Email: $email Ngày Sinh: $ngaysinh Phái: $phai</p>";
        if ($loi==""){
            $conn = new PDO("mysql:host=localhost;dbname=hocphp;charset=utf8", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql ="INSERT INTO users SET tendangnhap= ?, matkhau= ?, email =?, hoten= ?, ngaysinh= ?, phai = ?, randomkey= ?";
            $st = $conn->prepare($sql); //tạo prepare statement
            $rd = substr( md5(rand(0,99999)) ,0, 20);
            $st->execute([$tendangnhap, $matkhau, $email, $hoten, $ngaysinh, $phai, $rd]);
            $id = $conn->lastInsertId();
            GuiMail($email, $hoten,$id, $rd);
            
            //echo "<b>Đã chèn thành công người dùng</b>";
            //header("location:trangchu.html"); nếu sài header thì ở trên <?php chỉ cần có khoảng trống or trong code có nhúng HTML thì lỗi
            //echo "<script> document.location='trangchu.html'; </script>";
        }
    }
?>

<?php
    function GuiMail($email, $hoten, $idUser, $randomkey) {
        
        require "PHPMailer-master/src/PHPMailer.php"; 
        require "PHPMailer-master/src/SMTP.php"; 
        require 'PHPMailer-master/src/Exception.php'; 
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);//true:enables exceptions
        try {
            $mail->SMTPDebug = 2; //0,1,2: chế độ debug. khi chạy ngon thì chỉnh lại 0 nhé
            $mail->isSMTP();  
            $mail->CharSet  = "utf-8";
            $mail->Host = 'smtp.gmail.com';  //SMTP servers
            $mail->SMTPAuth = true; // Enable authentication
            $mail->Username = 'hocphp147@gmail.com'; // SMTP username
            $mail->Password = 'bungno14';   // SMTP password
            $mail->SMTPSecure = 'ssl';  // encryption TLS/SSL 
            $mail->Port = 465;  // port to connect to                
            $mail->setFrom('hocphp147@gmail.com', 'Duong Admin' ); 
            $mail->addAddress($email, $hoten); //mail và tên người nhận  
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = 'Thư kích hoạt tài khoản';
            $noidungthu = '<h4>Thư kích hoạt tài khoản của bạn!</h4>
                Link kích hoạt:
                <a href="http://localhost/hocphp/kichhoat.php?id={$idUser}&randomkey={$randomkey}">Nhấp vào đây</a>
            '; 
            $mail->Body = $noidungthu;
            $mail->smtpConnect( array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                    "allow_self_signed" => true
                )
            ));
            $mail->send();
            echo 'Đã gửi mail xong';
        } catch (Exception $e) {
            echo 'Error: ', $mail->ErrorInfo;
        }
        
    }
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<form style="width: 600px" class="border border-primary border-2 rounded m-auto p-2" method="POST">
    <?php if ($loi != "") { ?>
        <div class="alert alert-danger"><?php echo $loi ?></div>
    <?php } ?>
  <div class="mb-3">
    <label for="tendangnhap" class="form-label">Tên Đăng Nhập</label>
    <input type="text" class="form-control" id="tendangnhap" name="tendangnhap" placeholder="Nhập tên đăng nhập">
    <div id="loiTenDangNhap" class="form-text"></div>
  </div>
  <div class="mb-3">
    <label for="matkhau" class="form-label">Mật Khẩu</label>
    <input type="password" class="form-control" id="matkhau" name="matkhau" placeholder="Nhập mật khẩu" autocomplete="new-password">
  </div>
  <div class="mb-3">
    <label for="hoten" class="form-label">Họ và Tên</label>
    <input type="text" class="form-control" id="hoten" name="hoten" placeholder="Nhập họ và tên của bạn">
    <div id="loiHoTen" class="form-text"></div>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="Nhập Email">
    <div id="loiEmail" class="form-text"></div>
  </div>
  <div class="mb-3">
    <label for="ngaysinh" class="form-label">Ngày Sinh</label>
    <input type="date" class="form-control" id="ngaysinh" name="ngaysinh" placeholder="Nhập ngày sinh của bạn">
    <div id="loiNgaySinh" class="form-text"></div>
  </div>
  <div class="mb-3">
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="phai" id="nam" value="1">
        <label class="form-check-label" for="nam">Nam</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="phai" id="nu" value="0">
        <label class="form-check-label" for="nu">Nữ</label>
    </div>
  </div>
  <button type="submit" name="btndangky" value="Ok" class="btn btn-primary">Đăng Ký</button>
</form>