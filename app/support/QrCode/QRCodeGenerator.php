<?php

namespace App\support\QrCode;

use App\Models\Promo;
use Fpdf\Fpdf;
use Illuminate\Support\Facades\Auth;
use setasign\Fpdi\Fpdi;


class QRCodeGenerator
{

    public $user_id;
//    const dst_big_x = 88;
//    const dst_big_y = 228;
    const dst_big_x = 477;
    const dst_big_y = 805;
//    const dst_small_x = 413;
//    const dst_small_y = 120;
    const dst_small_x = 590;
    const dst_small_y = 176;

    const input_image_business_car1 = 'qrcodes/card2.jpg';
    const input_image_simple_card = 'qrcodes/banner.jpg';

    public  $big_name ;
    public  $small_name;
    public $card_name;
    public $business_card_name;
    public $text;
    private $company;
    private $number;
    private $email;
    private $address;

    private $user;

    public function __construct($text){
        $this->user = \auth()->user();
        $this->user_id = $this->user->id;
        if(isset($text)){
            $this->big_name = 'qr_'.$this->user_id;
            $this->small_name = 'qrsmall_'.$this->user_id;
            $this->text = $text;
            $this->bc_1_name = 'qrcodes/img1.jpg';
            $this->create_business_card();
            $this->create_simple_card();
            $this->card_name = 'qrcodes/card_'.$this->big_name.'.png';
            $this->business_card_name = 'qrcodes/card_'.$this->small_name.'.png';
            if(!file_exists(public_path('/qrcodes/vizitka_'.$this->user_id.'.pdf'))){
                $this->create_pdf(false);
            }


        }
    }
    public function touchPromo($user){
        if($user->promo===null){
            Promo::firstOrCreate([
                'user_id'=>$user->id,
                'name'=>'',
                'phone'=>$user->setting->number,
                'email'=>$user->setting->email,
                'address'=>'',
                'generated'=>false
            ]);
        }
    }
    public function saveData(){

        if($this->user->promo===null){
            Promo::firstOrCreate([
                'user_id'=>$this->user_id,
                'name'=>$this->company,
                'phone'=>$this->number,
                'email'=>$this->email,
                'address'=>$this->address,
                'generated'=>true
            ]);
        }else{
            $this->user->promo->name = $this->company;
            $this->user->promo->phone = $this->number;
            $this->user->promo->email = $this->email;
            $this->user->promo->address = $this->address;
            $this->user->promo->generated = true;
            $this->user->promo->save();
        }
    }
    /**
     * @param mixed $address
     */
    public function setAddress($address): void
    {
        $this->address = trim($address) ?? '';
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company): void
    {
        $this->company = trim($company) ?? '';
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number): void
    {
        $this->number = trim($number) ?? '';
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = trim($email) ?? '';
    }
    public function pdf_part_one($pdf){

        $pdf->AddPage();
        $filename = '/qrcodes/CARbusinessCard.pdf';
        $pdf->setSourceFile(public_path($filename));
        $template = $pdf->importPage(1);

        $size = $pdf->getTemplateSize($template);

        $pdf->useTemplate($template, 0, 0,$size['width'], $size['height'],true);
        $pdf->Image( public_path('qrcodes/white.jpg'),0, 0,40,50);
        $pdf->AddFont('OpenSans','B','Calibri.php');
        $pdf->SetFont('OpenSans', 'B', 16);
        $y = 4;
        $x = 6;
        $i = 8;
        $text = $this->company;

        $newtext = wordwrap($text, 24, "==", false);
        $arr = explode('==',$newtext);

        foreach ($arr as $item){
            $item = iconv('UTF-8', 'cp1251', $item);
            $pdf->text($x,$y += 7, $item);
        }




        $pdf->SetFont('OpenSans', 'B', 11);

        $reportSubtitle = $this->number;
        $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);

        $pdf->text($x, $y += 11, $reportSubtitle);


        $reportSubtitle = $this->email;
        $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);

        $pdf->text($x, $y += $i, $reportSubtitle);
        $text = $this->address;

        $newtext = wordwrap($text, 30, "==", false);
        $arr = explode('==',$newtext);
        foreach ($arr as $key => $item){
            $item = iconv('UTF-8', 'cp1251', $item);
            if($key==0){
                $pdf->text($x,$y += 7, $item);
            }else{
                $pdf->text($x,$y += 4, $item);
            }
        }


        $pdf->AddPage();
        $template = $pdf->importPage(2);

        $size = $pdf->getTemplateSize($template);

        $pdf->useTemplate($template, 0, 0,$size['width'], $size['height'],true);
        $pdf->Image( public_path('qrcodes/qrqrsmall_'.Auth::user()->id.'.png'),52.65, 15.44, 23.1, 23.1);

        $pdf->SetDrawColor(255,221,24);
        $pdf->SetFillColor(255,221,24   );
        $pdf->Rect(4.6, 28, 30, 30, 'F');

        $pdf->AddFont('arial','','arial.php');
        $pdf->SetFont('arial', '', 9);
        $reportSubtitle = '1. Наведите камеру';
        $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
        $pdf->SetTextColor(36,28,36);
        $pdf->text(6.6, 32, $reportSubtitle);

        $pdf->AddFont('OpenSans','','Calibri.php');
        $pdf->SetFont('OpenSans', '', 9);
        $reportSubtitle = 'телефона на QR-код';
        $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
        $pdf->SetTextColor(36,28,36);
        $pdf->text(6.6, 35.7, $reportSubtitle);

        $pdf->AddFont('OpenSans','','Calibri.php');
        $pdf->SetFont('OpenSans', '', 9);
        $reportSubtitle = '2. Заполните заявку';
        $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
        $pdf->SetTextColor(36,28,36);
        $pdf->text(6.6, 39.6, $reportSubtitle);

        $pdf->AddFont('OpenSans','','Calibri.php');
        $pdf->SetFont('OpenSans', '', 9);
        $reportSubtitle = 'и получите оценку';
        $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
        $pdf->SetTextColor(36,28,36);
        $pdf->text(6.6, 43.5, $reportSubtitle);


        return $pdf;

    }
    public function pdf_part_two($pdf=null){
        if($pdf!==null){
            $pdf->AddPage();
            $filename = '/qrcodes/CARplakat.pdf';
            $pdf->setSourceFile(public_path($filename));

            $template = $pdf->importPage(1);

            $size = $pdf->getTemplateSize($template);
//75.8, 192.4
            $pdf->useTemplate($template, 0, 0,$size['width'], $size['height'],true);
            $pdf->Image( public_path('qrcodes/qrqr_'.Auth::user()->id.'.png'),74.8, 146, 151, 151);

            $pdf->SetDrawColor(255,255,255);
            $pdf->SetFillColor(255,255,255);
            $pdf->Rect(4.6, 386, 300, 11, 'F');

            $pdf->SetDrawColor(255,255,255);
            $pdf->SetFillColor(255,255,255);
            $pdf->Rect(130, 396, 230, 11, 'F');

//            $pdf->SetDrawColor(255,255,255);
//            $pdf->SetFillColor(255,255,255);
//            $pdf->Rect(50, 310, 230, 50, 'F');
            //и получите оценку

//            $pdf->AddFont('arial','','arial.php');
//            $pdf->SetFont('arial', '', 28);
//            $reportSubtitle = '1. Наведите камеру';
//            $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
//            $pdf->SetTextColor(36,28,36);
//            $pdf->text(50, 335, $reportSubtitle);
//
//                $pdf->AddFont('arial','','arial.php');
//                $pdf->SetFont('arial', '', 28);
//                $reportSubtitle = 'телефона на QR-код';
//                $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
//                $pdf->SetTextColor(36,28,36);
//                $pdf->text(50, 346, $reportSubtitle);
//
//            $pdf->AddFont('arial','','arial.php');
//            $pdf->SetFont('arial', '', 28);
//            $reportSubtitle = '2. Заполните заявку';
//            $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
//            $pdf->SetTextColor(36,28,36);
//            $pdf->text(160, 335, $reportSubtitle);
//
//            $pdf->AddFont('arial','','arial.php');
//            $pdf->SetFont('arial', '', 28);
//            $reportSubtitle = 'и получите оценку';
//            $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
//            $pdf->SetTextColor(36,28,36);
//            $pdf->text(160, 346, $reportSubtitle);


            $pdf->AddFont('arial','','arial.php');
            $pdf->SetFont('arial', '', 15);
            $reportSubtitle = $this->company;
            $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
            $pdf->SetTextColor(136,136,136);
            $pdf->text(14.6, 395, $reportSubtitle);

//            $reportSubtitle = 'сертифицированный партнёр';
//            $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
//            $pdf->SetTextColor(179,179,179);
//            $pdf->text(25.5, 402.3, $reportSubtitle);

//            $pdf->AddFont('arial_bold','','arial_bold.php');
//            $pdf->SetFont('arial_bold', '', 14);
//            $reportSubtitle = 'SKYvin.ru';
//            $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
//            $pdf->SetTextColor(179,179,179);
//            $pdf->text(94.6, 402.3, $reportSubtitle);


            $pdf->AddFont('arial','','arial.php');
            $pdf->SetFont('arial', '', 15);
            $reportSubtitle = $this->number.', '.$this->email;
            $x = $pdf->GetPageWidth() - $pdf->GetStringWidth($reportSubtitle) - 15.4;
            $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
            $pdf->SetTextColor(136,136,136);
            $pdf->text($x, 395, $reportSubtitle);

            $reportSubtitle = $this->address;
            $reportSubtitle = iconv('UTF-8', 'cp1251', $reportSubtitle);
            $x = $pdf->GetPageWidth() - $pdf->GetStringWidth($reportSubtitle) - 15.4;
            $pdf->SetTextColor(136,136,136);
            $pdf->text($x, 402.3, $reportSubtitle);

            return $pdf;

        }else{
            $pdf = new Fpdi();
            $pdf = $this->pdf_part_two($pdf);
            $pdf->Output('F', public_path('/qrcodes/plakat_'.$this->user_id.'.pdf'));

        }

    }
    public function make_font($name){
        require(base_path() . '/vendor/setasign/fpdf/makefont/makefont.php');
        MakeFont(public_path($name),'cp1251');
    }
    public function create_pdf($preview=false){
        $pdf = new Fpdi();
        $pdf = $this->pdf_part_one($pdf);
        if(!$preview){

            $pdf->Output('F', public_path('/qrcodes/vizitka_'.Auth::user()->id.'.pdf'));

            $imagick = new \Imagick();
            $imagick->readImage(public_path('/qrcodes/vizitka_'.Auth::user()->id.'.pdf'));
        }else{
            $this->pdf_part_two($pdf);
            $pdf->Output();
        }
    }
    public function pdf_preview()
    {
        $this->saveData();
//        $this->create_pdf(true);
        $this->create_pdf(false);
        $this->pdf_part_two();
    }

    public function getBigName(): string
    {
        return $this->big_name;
    }

    /**
     * @return string
     */
    public function getSmallName(): string
    {
        return $this->small_name;
    }

    public function pdf_generator(){
        $this->create_pdf(false);
    }

    public function create_business_card(){
        $this->generate_qr_code(260,$this->small_name);
        $this->imagecopymerge( self::input_image_business_car1,'qrcodes/qr'.$this->small_name.'.png',self::dst_small_x,self::dst_small_y,$this->small_name);
    }
    public function create_simple_card(){
        $this->generate_qr_code(800,$this->big_name);
        $this->imagecopymerge( self::input_image_simple_card,'qrcodes/qr'.$this->big_name.'.png',self::dst_big_x,self::dst_big_y,$this->big_name);

        // $img = imagecreatetruecolor(1500, 120);
        // $bg = imagecolorallocate ( $img, 255, 255, 255 );
        // imagefilledrectangle($img,0,0,1500,120,$bg);
        // imagejpeg($img,"qrcodes/myimg.jpg",100);

        // $this->imagecopymerge( self::input_image_simple_card,"qrcodes/myimg.jpg",397    ,140,$this->big_name,$this->big_name);
        // //logo_head_desk.png
        // $this->imagecopymerge( self::input_image_simple_card,"qrcodes/logo.png",397    ,140,$this->big_name,$this->big_name);
    }


    public function generate_qr_code($size,$name){
        \QrCode::size($size)
            ->format('png')
            ->generate($this->text, public_path('qrcodes/qr'.$name.'.png'));
    }
    public function imagecopymerge($image1,$image2,$dst_big_x,$dst_big_y,$name){
//        $image1 = 'qrcodes/img.png';
//        $image2 = 'qrcodes/qrcode.png';

        list($width,$height) = getimagesize($image2);

        $image1 = imagecreatefromstring(file_get_contents($image1));
        $image2 = imagecreatefromstring(file_get_contents($image2));

        imagecopymerge($image1,$image2,$dst_big_x,$dst_big_y,0,0,$width,$height,100);

        header('Content-Type:image/png');
        // imagepng($image1);
        imagepng($image1,'qrcodes/card_'.$name.'.png');
    }
    public function break_into_multi_lines($str='',$length = 30){
        // $output = substr($string, 0, strpos($string, ' '));
        if(strlen($str)<$length){
            return [true,$str];
        }

        if ( strpos($str, ' ') > 17 &&  strpos($str, ' ') < 25  ){
            return [false,substr($str,0,  strpos($str, ' ') ), substr($str,strpos($str, ' '), $length)];
        }else{
            return [false,substr($str,0,  $length ), substr($str,$length, $length)];
        }
    }

}
