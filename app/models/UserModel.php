<?php

namespace App\Models;

use App\System\Model;

use App\System\Application;



class User extends Model

{

    public ?int $id;
    public $userId;

    public ?string $firstname;
    public ?string $lastname;
    public ?string $name;

    public ?string $surname;

    public ?string $birthday;

    public ?string $sex;

    public ?string $email;

    public ?string $password;

    public ?string $repassword;

    public ?string $contractvalid;

    public $status = "";

    public $mailonaylink;

    public $remembertoken;

    public $remember;

    public $pictureurl;
    public $mailOnay;
    public $rolid;
    public ?string $telephone;
    public ?string $adress;
    public ?string $city;
    public ?string $district;




    /**

     *

     * @return array

     */

    function rules(): array
    {

        return [

            'name' => [self::RULE_REQUIRED],

            'surname' => [self::RULE_REQUIRED],

            'birthday' => [self::RULE_REQUIRED],

            'sex' => [self::RULE_REQUIRED],

            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL],

            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 24]],

            'repassword' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']],

            'contractvalid' => [self::RULE_CHECHED]

        ];
    }


    public function get()
    {
        $status = $this->db->prepare(
            "SELECT u.*, img.path as 'profileimage' FROM users u 
            LEFT JOIN images img on img.elementtypeno = 2 and img.elementtypeid = u.id
            where (:email is null or u.email = :email) and (:id is null or u.id = :id)");
        $status->bindParam(":email",$this->email,($this->email == null ? \PDO::PARAM_NULL :\PDO::PARAM_STR));
        $status->bindParam(":id",$this->id,($this->id == null ? \PDO::PARAM_NULL :\PDO::PARAM_INT));
        $status->execute();
        return $status->fetch(\PDO::FETCH_OBJ);

    }

    public function update()
    {
        $this->db->beginTransaction();
        try {
            $status = $this->db->prepare(
                "UPDATE users SET
                    firstname = coalesce(:firstname,firstname),
                    lastname = coalesce(:lastname,lastname),
                    telephone = coalesce(:telephone,telephone),
                    adress = coalesce(:adress,adress),
                    city = coalesce(:city,city),
                    district = coalesce(:district,district)
                WHERE id = :id
                ");
            $status->bindParam(':firstname',$this->firstname,$this->firstname == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR);
            $status->bindParam(':lastname',$this->lastname,$this->lastname == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR);
            $status->bindParam(':telephone',$this->telephone,$this->telephone == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR);
            $status->bindParam(':adress',$this->adress,$this->adress == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR);
            $status->bindParam(':city',$this->city,$this->city == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR);
            $status->bindParam(':district',$this->district,$this->district == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR);
            $status->bindParam(':id',$this->id,$this->id);
            $status->execute();
            $this->db->commit();
        } catch (\Throwable $th) {
            $this->db->rollBack();
        }
        

    }

    public function createUser(array $params)

    {

        try {
            $selstat = $this->db->prepare("select * from users where email = :email");
            $selstat->bindValue(":email", $this->email);
            $selstat->execute();
            $result = $selstat->fetchAll(\PDO::FETCH_ASSOC);
            if ($result && count($result) > 0) {
                $this->setError(true, "Mail adresi sistemde zaten mevcut lütfen giriş yapın veya şifre sıfırlama maili isteyin.");
                return;
            }

            $this->db->beginTransaction();

            $this->status = 1; //0 deaktif 1 mail gitti 2 onaylandı

            $this->password = password_hash(md5($this->password), PASSWORD_DEFAULT);

            $this->mailonaylink = md5(rand(1, 100000));

            $status = $this->db->prepare(
                "INSERT INTO users (email, firstname, lastname, status, password, eklemezamani, guncellemezamani, mailonaylink)
                VALUES (:lemail, :lname, :lsurname, :lstatus, :lpassword, current_timestamp(), current_timestamp(), :lmailonaylink)"
            );

            $status->bindValue(':lemail', $this->email);

            $status->bindValue(':lname', $this->name);

            $status->bindValue(':lsurname', $this->surname);

            $status->bindValue(':lstatus', $this->status);

            $status->bindValue(':lpassword', $this->password);

            $status->bindValue(':lmailonaylink', $this->mailonaylink);

            $status->execute();


            $this->userId = $this->db->lastInsertId();

            $mail = Application::$app->functions->sendMail(
                $params["email"],
                'Uyelik Kayit',
                '<a href="' . SITEADRESS . 'mailonay/' . $this->mailonaylink . '">Mailinizi onaylamak için lütfen tıklayınız</a>',
                ''
            );

            Application::$app->functions->sendMail(
                "kucukaslanmatbaasi@gmail.com",
                "Yeni Üyelik Talebi Var",
                "email : " . $this->email . "<br>" . "name :" . $this->name . " " . $this->surname,
                ""
            );

            $this->db->commit();

            return true;
        } catch (\Exception $e) {

            //SQLSTATE[45000]: <>: 1644 Kullanıcı Zaten Mevcut

            // $this->setError(true,$e->getMessage());
            $this->db->rollBack();

            $this->setError(true, $e->getMessage());

            return false;
        }
    }



    public function mailOnay($mailonaylink)

    {
        $this->db->beginTransaction();
        try {
            $status = $this->db->prepare("update users set status = 3 where mailonaylink = :ldogrulamakodu");
            $status->bindValue(':ldogrulamakodu', $mailonaylink);
            $status->execute();
            $this->db->commit();
            return true;
        } catch (\Throwable $th) {
            $this->db->rollBack();
            return false;
        }
    }



    public function login()

    {

        $status = $this->db->prepare(
            "SELECT * from users 
             where email = :lemail
                and status = 3");

        $status->bindValue(':lemail', $this->email);

        $status->execute();

        $result = $status->fetch(\PDO::FETCH_ASSOC);

        $status->closeCursor();

        if ($result && password_verify(md5($this->password), $result["password"])) {

            $this->userId = $result["id"];

            $this->email = $result["email"];

            $this->name = $result["firstname"];

            $this->surname = $result["lastname"];

            $this->pictureurl = $result["pictureurl"];
            $this->rolid = $result["rolid"];



            if (isset($this->remember) && $this->remember == "on") {

                $this->db->prepare("DELETE FROM userremembers where userid = $this->userId")->execute();

                $this->remembertoken = bin2hex(openssl_random_pseudo_bytes(32));

                $remember = $this->db->prepare("INSERT INTO userremembers set userid = :userid, remembertoken = :remembertoken,expiretime = :expiretime, userbrowser=:userbrowser");

                $remember->execute(array(

                    "userid" => $this->userId,

                    "remembertoken" => $this->remembertoken,

                    "expiretime" => time() + 604800,

                    "userbrowser" => md5($_SERVER["HTTP_USER_AGENT"])

                ));



                setcookie("REMEMBERAGS", $this->remembertoken, time() + 604801, '/');
            }

            return $this;
        } else {

            $this->setError(true, "Giriş başarısız. Lütfen bilgilerinizi kontrol ediniz");

            return false;
        }
    }

    public function getUserList($userid = null, $email = null)

    {



        $status = $this->db->prepare("select * from users");

        // $status->bindValue(":id", $userid, ($userid == null ? \PDO::PARAM_NULL : \PDO::PARAM_INT));

        // $status->bindValue(":mail", $email, ($email == null ? \PDO::PARAM_NULL : \PDO::PARAM_STR));

        $status->execute();

        $result = $status->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function getUserRemembers($cooktoken, $browser)

    {

        $time = time();

        return $this->db->query("SELECT * FROM userremembers where remembertoken ='{$cooktoken}' and userbrowser = '$browser' and expiretime > $time")->fetch(\PDO::FETCH_ASSOC);
    }



    public function UserVisits($visitip, $page)

    {

        try {

            //Tarayıcı Bilgisi

            $u_agent = $_SERVER['HTTP_USER_AGENT'];

            // ip adresini al ve değişkene ata

            // $ip_adresi = Application::$app->functions->GetIP();

            $ip_adresi = $visitip;

            // geoplugin.net adresine ip adresini ilet ve diğer bilgilere ulaşım sağla

            // $uzak_adres = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip_adresi));





            $url = "http://www.geoplugin.net/php.gp?ip=" . $ip_adresi;

            $c = curl_init();

            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($c, CURLOPT_URL, $url);

            $uzak_adres = unserialize(curl_exec($c));

            curl_close($c);







            // Şehir dönen değeri değişkene ata

            $sehir = $uzak_adres['geoplugin_city'];

            // Ülke dönen değeri değişkene ata

            $ulke = $uzak_adres['geoplugin_countryName'];



            $region =  $uzak_adres['geoplugin_region'];

            $area =  $uzak_adres['geoplugin_areaCode'];

            // var_dump($uzak_adres);



            $status = $this->db->prepare("call save_uservisit(:luseragent,:luserip,:lusercity,:lusercountry,:lregion,:larea,:page)");

            $status->bindValue(":luseragent", $u_agent);

            $status->bindValue(":luserip", $ip_adresi);

            $status->bindValue(":lusercity", $sehir);

            $status->bindValue(":lusercountry", $ulke);

            $status->bindValue(":lregion", $region);

            $status->bindValue(":larea", $area);

            $status->bindValue(":page", $page);

            $status->execute();



            return true;
        } catch (\Throwable $th) {

            return false;
        }
    }

    public function checkEmail($email)

    {

        try {

            $status = $this->db->prepare("select * from users where email = :lemail limit 1");

            $status->bindValue(':lemail', $email);

            $status->execute();

            return $status->fetch(\PDO::FETCH_OBJ);
        } catch (\Exception $e) {

            throw $e;
        }
    }

    public function createUserGmail()

    {

        try {

            $status = $this->db->prepare("call in_users_gmail(@lid,:lemail,:lname,:lsurname,:lpictureurl)");

            $status->bindValue(':lemail', $this->email);

            $status->bindValue(':lname', $this->name);

            $status->bindValue(':lsurname', $this->surname);

            $status->bindValue(':lpictureurl', $this->pictureurl);

            $status->execute();

            $result = $this->db->query("select @lid as userId")->fetchObject();

            $this->userId = $result->userId;

            if ($result) {

                $mail = Application::$app->functions->sendMail(

                    $this->email,
                    'Eschelping\'e hoşgeldiniz.',
                    '<a href="http://eschelping.com/">Eschelping ağında vakit geeçirmek için tıklayınız.</a>',
                    ''
                );

                Application::$app->functions->sendMail(
                    "info@eschelping.com",
                    "google dan üyelik",

                    "email : " . $this->email . "<br>" . "name :" . $this->name . " " . $this->surname,
                    ""
                );

                return true;
            }

            return false;
        } catch (\Exception $e) {

            //SQLSTATE[45000]: <>: 1644 Kullanıcı Zaten Mevcut

            // $this->setError(true,$e->getMessage());

            $this->setError(true, str_replace(">>", ">", str_replace(["SQLSTATE[45000]: <", ":"], "", $e->getMessage())));

            return false;
        }
    }



    public function updatePicture()

    {

        try {

            $status = $this->db->prepare("update users set pictureurl=:lpictureurl where id = :lid");

            $status->bindValue(':lpictureurl', $this->pictureurl);

            $status->bindValue(':lid', $this->userId);

            $status->execute();
        } catch (\Throwable $th) {

            //throw $th;

        }
    }

    public function resetPass()
    {
        $status = $this->db->prepare(
            "UPDATE users
            SET password = :password
            WHERE id = :id "
        );
        $status->bindParam(":password",$this->password);
        $status->bindParam(":id",$this->id);
        $status->execute();
    }
}
