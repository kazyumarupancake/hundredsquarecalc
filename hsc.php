<?php
ini_set('display_errors', "On");
mb_internal_encoding('UTF-8');
require('vendor/autoload.php');

/**
 * Via composer, TCPDF and kazyumaru\papersize are installed.
 */

/**
 * 
BSD License
 Copyright (c) 2021-2024, Kazyumaru
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this
   list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright notice,
   this list of conditions and the following disclaimer in the documentation
   and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those
of the authors and should not be interpreted as representing official policies,
either expressed or implied, of the FreeBSD Project.
 * 
 */

$hyaku = new hyakumasu;
$hyaku->generate(); //hundred square calculation sheets are generated. 

class hyakumasu{
    protected $quiztype; //quiztype tashi,add,hiki,subt,kake,mult,wari,div
    protected $sheetnum; //number of sheets. if null, set 1.
    protected $lang; // language jp,en
    protected $label; //labels
    protected $ps;
    protected $gFont;
    protected $mFont;
    protected $output_stamp;
    public function __construct(){
        $this->ps = new Kazyumaru\Papersize;
        if(empty($_GET['quiztype'])){$this->error('ar10001 Not set quiz type.');/** quiz type */}else{$this->quiztype = $_GET['quiztype'];}
        if(empty($_GET['sheetnum'])){$this->sheetnum = 1;/** dafault sets 1 */}else{$this->sheetnum = $_GET['sheetnum'];}
        if(empty($_GET['lang'])){$this->lang= "jp";/** dafault sets 1 */}else{$this->lang = $_GET['lang'];;}   
        $this->label = $this->enjp($this->lang);
        try {
            $font = new TCPDF_FONTS();
            /** IPA font installed in  
             * Requiring Japanese fonts. 
            */
            $this->mFont = $font->addTTFfont('vendor/tecnickcom/tcpdf/fonts/ipaexm.ttf');
            $this->gFont = $font->addTTFfont('vendor/tecnickcom/tcpdf/fonts/ipaexg.ttf'); 
        } catch (Exception $e) {
            $this->error('ar10003 font file error, there are something wrong with TCPDF_FONTS');
        }
        
        $this->output_stamp = date('Y-m-d H:i:s')." ".$this->get_ipaddr();
        /** Datetimne, IPaddr */
    }
    public function error($error){echo $error;/**output string shows error. */ die;}
    public function enjp($lang){
        $language = array('en'=>array('headtitle'=>"100 square calculation",
        'subtitle'=>'Please calculate according to the formula corresponding to the Symbol.',
        'subtitleminus'=>'Subtract the smaller number from the larger number.'),
        'jp'=>array('headtitle'=>"100ます計算",
        'subtitle'=>'記号にしたがって計算しましょう。',
        'subtitleminus'=>'大きいほうから小さいほうの数字を引きましょう。')
        );
        return $language[$lang];
    }

    public function generate(){
        switch($this->quiztype){
            case 'tashi':
            case 'add':
                /** */
                $pdf = $this->create_1();
                $pdf->Output();
                die;
            case 'hiki':
            case 'subt':
                $pdf = $this->create_2();
                $pdf->Output();
                die;
            case 'kake':
            case 'mult':
                $pdf = $this->create_3();
                $pdf->Output();
                die;
            case 'wari':
            case 'div':
                $pdf = $this->create_4();
                $pdf->Output();
                die;
            default:
                $this->error('ar10002. undetected quiz type');
        }
    }
    public function create_1(){
        //addition
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0,0,0,0);
        $pdf->setFontSubsetting(true);
        $a4 = $this->ps->set('A4','p'); //set paper info
        for($pageNo = 1; $pageNo <= $this->sheetnum; $pageNo++) {
            $pdf->AddPage();  //add pages
            // set cell padding
            $pdf->setCellPaddings(1, 1, 1, 1);
            // set cell margins
            $pdf->setCellMargins(1, 1, 1, 1);
            $pdf->SetFont($this->gFont, '',10.5);
            $pdf->SetFillColor(225, 225, 225);  //背景を白に設定
            $pdf->SetTextColor(0, 0, 0);  //フォントを黒に設定
            //単語テストタイトル部分表示
            $pdf->MultiCell(($a4->width),10,$this->label['headtitle'],0,"C",false,0,0,7,true,0,false,true,10,"M",false);
            //名前欄の出力
            $namebox = " 月　　日 名前：　";
            $pdf->MultiCell(60,10,$namebox,0,"L",false,0,13,18,true,0,false,true,10,"M",false);
            $linestyle = array('width'=>0.2,'cap'=>'round','dash'=>0,'join'=>'round','color'=>array(0,0,0));
            $pdf->Line(7,27,82,27,$linestyle);
            $pdf->Ln();
            $pdf->SetFont($this->mFont,'',10.5);
            $pdf->MultiCell($pdf->GetStringWidth($this->label['subtitle'],$this->mFont,null,10.5)+10, 0, $this->label['subtitle'], 0, 'L', false, 0, 27, 29 , true);
            $pdf->Ln();
            $pdf->SetFont($this->gFont,'',10.5);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetLineWidth(0.3);
            $pdf->setCellMargins(0,0,0,0);
            $pdf->setCellPaddings(1,1,1,1);
            $pdf->SetMargins(55.2,27,0,0);
            $w = 30;
            $pdf->Ln();
            $yoko = ['＋'];
            $yoko_array = $this->rand_int();
            $yoko = array_merge($yoko,$yoko_array);
            $tate = $this->rand_int();
            //head
            $pdf->SetFont($this->gFont, '',14);
            $pdf->Cell(9,9,$yoko[0], 1, 0, 'C', 0);
            $pdf->SetFont($this->gFont, '',12);
            for($j = 1; $j <= 10; ++$j) {
                $pdf->Cell(9,9,$yoko[$j], 1, 0, 'C', 0);
            }
            $pdf->Ln();
            for($i = 0; $i < 10; ++$i) {
                for($j = 0; $j < 11; ++$j) {
                    if($j===0){
                        $pdf->SetFont($this->gFont, '',12);
                        $pdf->Cell(9,9, $tate[$i], 1, 0, 'C', 0);
                    }else{
                        $pdf->SetFont($this->gFont, '',10.5);
                        $pdf->Cell(9,9, "", 1, 0, 'C', 0);
                    }
                }
                $pdf->Ln();
            }
            $pdf->SetAbsXY(55.2,163);
        
            $pdf->SetFont($this->gFont, '',14);
            $pdf->Cell(9,9,$yoko[0], 1, 0, 'C', 0);
            $pdf->SetFont($this->gFont, '',12);
            for($j = 1; $j <= 10; ++$j) {
                $pdf->Cell(9,9,$yoko[$j], 1, 0, 'C', 0);
            }
            $pdf->Ln();
            for($i = 0; $i <= 9; ++$i) {
                for($j = 0; $j <= 10; ++$j) {
                    if($j===0){
                        $pdf->SetFont($this->gFont, '',12);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,9, $tate[$i], 1, 0, 'C', 0);
                    }else{
                        $ans = $tate[$i] + $yoko[$j];
                        $pdf->SetFont($this->gFont, '',10);
                        $pdf->SetTextColor(255, 0, 0);
                        $pdf->Cell(9,9, $ans, 1, 0, 'C', 0);
                    }
                }
                $pdf->Ln();
            }
            $linestyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => '5,10', 'color' => array(0, 0, 0));
            $pdf->Line(20,148.5,190,148.5, $linestyle);
        
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont($this->mFont, '',7);
            $pdf->SetFillColor(225, 225, 225);
            $pdf->MultiCell($pdf->GetStringWidth($this->output_stamp,$this->mFont,null,8)+10, 0, $this->output_stamp/**.$this->output_stamp2*/, 0, 'R', 1, 0, $a4->width - $pdf->GetStringWidth($this->output_stamp,$this->mFont,null,8) -20, 2 , true,0,true,true,0,"T",false);

        }
        $pdf->lastPage();
        return $pdf;
    }
    public function create_2(){
        //100ます引き算
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0,0,0,0);
        $pdf->setFontSubsetting(true);
        $a4 = $this->ps->set('A4','p');
        for($pageNo = 1; $pageNo <= $this->sheetnum; $pageNo++) {
            $pdf->AddPage();  //ページ追加
            // set cell padding
            $pdf->setCellPaddings(1, 1, 1, 1);
            // set cell margins
            $pdf->setCellMargins(1, 1, 1, 1);
            $pdf->SetFont($this->gFont, '',10.5);
            $pdf->SetFillColor(225, 225, 225);  //背景を白に設定
            $pdf->SetTextColor(0, 0, 0);  //フォントを黒に設定
            //単語テストタイトル部分表示
            $pdf->MultiCell(($a4->width),10,"100ます計算 引き算",0,"C",false,0,0,7,true,0,false,true,10,"M",false);
            //名前欄の出力
            $namebox = " 月　　日 名前：　";
            $pdf->MultiCell(60,10,$namebox,0,"L",false,0,13,18,true,0,false,true,10,"M",false);
            $linestyle = array('width'=>0.2,'cap'=>'round','dash'=>0,'join'=>'round','color'=>array(0,0,0));
            $pdf->Line(7,27,82,27,$linestyle);
            $pdf->Ln();
            $pdf->SetFont($this->mFont, '',10.5);
            $pdf->MultiCell($pdf->GetStringWidth("□ 上から横の数字を引きなさい。",$this->mFont,null,10.5)+10, 0, "□ 上から横の数字を引きなさい。", 0, 'L', false, 0, 27, 29 , true);
            $pdf->Ln();
            $pdf->SetFont($this->gFont, '',10.5);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetLineWidth(0.3);
            $pdf->setCellMargins(0,0,0,0);
            $pdf->setCellPaddings(1,1,1,1);
            $pdf->SetMargins(55.2,27,0,0);
            $w = 30;
            $pdf->Ln();
            $yoko = ['−'];
            $yoko = array_merge($yoko,$this->rand_int_2dig());
            $tate = $this->rand_int();
            //head
            $pdf->SetFont($this->gFont, '',15);
            $pdf->Cell(9,9,$yoko[0], 1, 0, 'C', 0);
            $pdf->SetFont($this->gFont, '',12);
            for($j = 1; $j <= 10; ++$j) {
                $pdf->Cell(9,9,$yoko[$j], 1, 0, 'C', 0);
            }
            $pdf->Ln();
            for($i = 0; $i < 10; ++$i) {
                for($j = 0; $j < 11; ++$j) {
                    if($j===0){
                        $pdf->SetFont($this->gFont, '',12);
                        $pdf->Cell(9,9, $tate[$i], 1, 0, 'C', 0);
                    }else{
                        $pdf->SetFont($this->gFont, '',10.5);
                        $pdf->Cell(9,9, "", 1, 0, 'C', 0);
                    }
                }
                $pdf->Ln();
            }
            $pdf->SetAbsXY(55.2,163);
            $pdf->SetFont($this->gFont, '',15);
            $pdf->Cell(9,9,$yoko[0], 1, 0, 'C', 0);
            $pdf->SetFont($this->gFont, '',12);
            for($j = 1; $j <= 10; ++$j) {
                $pdf->Cell(9,9,$yoko[$j], 1, 0, 'C', 0);
            }
            $pdf->Ln();
            for($i = 0; $i <= 9; ++$i) {
                for($j = 0; $j <= 10; ++$j) {
                    if($j===0){
                        $pdf->SetFont($this->gFont, '',12);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,9, $tate[$i], 1, 0, 'C', 0);
                    }else{
                        $ans = $yoko[$j] - $tate[$i];
                        $pdf->SetFont($this->gFont, '',10);
                        $pdf->SetTextColor(255, 0, 0);
                        $pdf->Cell(9,9, $ans, 1, 0, 'C', 0);
                    }
                }
                $pdf->Ln();
            }
            $linestyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => '5,10', 'color' => array(0, 0, 0));
            $pdf->Line(20,148.5,190,148.5, $linestyle);
        
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont($this->mFont, '',7);
            $pdf->SetFillColor(225, 225, 225);
            //$pdf->MultiCell($pdf->GetStringWidth($this->output_stamp,$this->mFont,null,8)+10, 0, $this->output_stamp.$this->output_stamp2, 0, 'R', 1, 0, $a4->width - $pdf->GetStringWidth($this->output_stamp,$this->mFont,null,8) -20, 2 , true,0,true,true,0,"T",false);

        }
        $pdf->lastPage();
        return $pdf;
    }
    public function create_3(){
        //100ます掛け算
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0,0,0,0);
        $pdf->setFontSubsetting(true);
        $a4 = $this->ps->set('A4','p');
        for($pageNo = 1; $pageNo <= $this->sheetnum; $pageNo++) {
            $pdf->AddPage();  //ページ追加
            // set cell padding
            $pdf->setCellPaddings(1, 1, 1, 1);
            // set cell margins
            $pdf->setCellMargins(1, 1, 1, 1);
            $pdf->SetFont($this->gFont, '',10.5);
            $pdf->SetFillColor(225, 225, 225);  //背景を白に設定
            $pdf->SetTextColor(0, 0, 0);  //フォントを黒に設定
            //単語テストタイトル部分表示
            $pdf->MultiCell(($a4->width),10,"100ます計算 掛け算",0,"C",false,0,0,7,true,0,false,true,10,"M",false);
            //名前欄の出力
            $namebox = " 月　　日 名前：　";
            $pdf->MultiCell(60,10,$namebox,0,"L",false,0,13,18,true,0,false,true,10,"M",false);
            $linestyle = array('width'=>0.2,'cap'=>'round','join'=>'round','dash'=>0,'color'=>array(0,0,0));
            $pdf->Line(7,27,82,27,$linestyle);
            $pdf->Ln();
            $pdf->SetFont($this->mFont, '',10.5);
            $pdf->MultiCell($pdf->GetStringWidth("□ 上と横の数字を掛けなさい。",$this->mFont,null,10.5)+10, 0, "□ 上から横の数字を引きなさい。", 0, 'L', false, 0, 27, 29 , true);
            $pdf->Ln();
            $pdf->SetFont($this->gFont, '',10.5);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetLineWidth(0.3);
            $pdf->setCellMargins(0,0,0,0);
            $pdf->setCellPaddings(1,1,1,1);
            $pdf->SetMargins(55.2,27,0,0);
            $w = 30;
            $pdf->Ln();
            $yoko = ['×'];
            $yoko = array_merge($yoko,$this->rand_int());
            $tate = $this->rand_int();
            //head
            $pdf->SetFont($this->gFont, '',15);
            $pdf->Cell(9,9,$yoko[0], 1, 0, 'C', 0);
            $pdf->SetFont($this->gFont, '',12);
            for($j = 1; $j <= 10; ++$j) {
                $pdf->Cell(9,9,$yoko[$j], 1, 0, 'C', 0);
            }
            $pdf->Ln();
            for($i = 0; $i < 10; ++$i) {
                for($j = 0; $j < 11; ++$j) {
                    if($j===0){
                        $pdf->SetFont($this->gFont, '',12);
                        $pdf->Cell(9,9, $tate[$i], 1, 0, 'C', 0);
                    }else{
                        $pdf->SetFont($this->gFont, '',10.5);
                        $pdf->Cell(9,9, "", 1, 0, 'C', 0);
                    }
                }
                $pdf->Ln();
            }
            $pdf->SetAbsXY(55.2,163);
            $pdf->SetFont($this->gFont, '',15);
            $pdf->Cell(9,9,$yoko[0], 1, 0, 'C', 0);
            $pdf->SetFont($this->gFont, '',12);
            for($j = 1; $j <= 10; ++$j) {
                $pdf->Cell(9,9,$yoko[$j], 1, 0, 'C', 0);
            }
            $pdf->Ln();
            for($i = 0; $i <= 9; ++$i) {
                for($j = 0; $j <= 10; ++$j) {
                    if($j===0){
                        $pdf->SetFont($this->gFont, '',12);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,9, $tate[$i], 1, 0, 'C', 0);
                    }else{
                        $ans = $yoko[$j] * $tate[$i];
                        $pdf->SetFont($this->gFont, '',10);
                        $pdf->SetTextColor(255, 0, 0);
                        $pdf->Cell(9,9, $ans, 1, 0, 'C', 0);
                    }
                }
                $pdf->Ln();
            }
            $linestyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => '5,10', 'color' => array(0, 0, 0));
            $pdf->Line(20,148.5,190,148.5, $linestyle);
        
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont($this->mFont, '',7);
            $pdf->SetFillColor(225, 225, 225);
            //$pdf->MultiCell($pdf->GetStringWidth($this->output_stamp,$this->mFont,null,8)+10, 0, $this->output_stamp.$this->output_stamp2, 0, 'R', 1, 0, $a4->width - $pdf->GetStringWidth($this->output_stamp,$this->mFont,null,8) -20, 2 , true,0,true,true,0,"T",false);
        }
        $pdf->lastPage();
        return $pdf;
    }
    public function create_4(){
        //100ます計算割り算
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0,0,0,0);
        $pdf->setFontSubsetting(true);
        //$papersize = new PaperSize;
        $a4 = $this->ps->set('A4','p');
        //$output_stamp = date('Y-m-d H:i:s').$_SESSION['sbpro_id']." ".get_ipaddr();
        for($pageNo = 1; $pageNo <= $this->sheetnum; $pageNo++) {
            $pdf->AddPage();  //ページ追加
            // set cell padding
            $pdf->setCellPaddings(1, 1, 1, 1);
            // set cell margins
            $pdf->setCellMargins(1, 1, 1, 1);
            $pdf->SetFont($this->gFont, '',10.5);
            $pdf->SetFillColor(225, 225, 225);  //背景を白に設定
            $pdf->SetTextColor(0, 0, 0);  //フォントを黒に設定
            //単語テストタイトル部分表示
            $pdf->MultiCell(($a4->width),10,"100ます計算 割り算",0,"C",false,0,0,7,true,0,false,true,10,"M",false);
            //名前欄の出力
            $namebox = " 月　　日 名前：　";
            $pdf->MultiCell(60,10,$namebox,0,"L",false,0,13,18,true,0,false,true,10,"M",false);
            $linestyle = array('width'=>0.2,'cap'=>'round','join'=>'round','dash'=>0,'color'=>array(0,0,0));
            $pdf->Line(7,27,82,27,$linestyle);
            $pdf->Ln();
            $pdf->SetFont($this->gFont, '',10.5);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetLineWidth(0.3);
            $pdf->setCellMargins(0,0,0,0);
            $pdf->setCellPaddings(1,1,1,1);
            $pdf->SetMargins(39,27,0,0);
            $pdf->Ln();
            $kuku = $this->kuku();
            $dan_array = $this->dan_array();
            for($k=0;$k<4;$k++){
                $dan = $dan_array[$k];
                $kuku_set[$k] = $kuku[$dan];
                shuffle($kuku_set[$k]);
                $kuku[$dan] = $kuku_set[$k];
                for($i=0;$i<1;$i++){
                    for($j=0;$j<=10;$j++){
                        if($j===0){
                            $contents = "÷";
                            $pdf->SetFontSize(16);
                        }else{
                            $pdf->SetFontSize(10.5);
                            $contents = $kuku_set[$k][$j-1]["dend"];
                        }
                        $pdf->Cell(12,9,$contents, 1, 0, 'C', 0);
                    }
                    $pdf->Ln();
                    for($j=0;$j<=10;$j++){
                        if($j===0){
                            $contents = $dan;
                        }else{
                            $contents = "";
                        }
                        $pdf->Cell(12,9,$contents, 1, 0, 'C', 0);
                    }
                    $pdf->Ln();
                }
                $pdf->Ln();
            }
            $pdf->Ln();
            $pdf->SetAbsY(163);
            for($k=0;$k<4;$k++){
                $dan = $dan_array[$k];
                $kuku_set[$k] = $kuku[$dan];
               
                for($i=0;$i<1;$i++){
                    for($j=0;$j<=10;$j++){
                        if($j===0){
                            $contents = "÷";
                            $pdf->SetFontSize(16);
                        }else{
                            $contents = $kuku_set[$k][$j-1]["dend"];
                            $pdf->SetFontSize(10.5);
                        }
                        $pdf->SetTextColor(0,0,0);
                        $pdf->Cell(12,9,$contents, 1, 0, 'C', 0);
                    }
                    $pdf->Ln();
                    for($j=0;$j<=10;$j++){
                        if($j===0){
                            $pdf->SetTextColor(0,0,0);
                            $pdf->Cell(12,9,$dan, 1, 0, 'C', 0);
                        }else{
                            $pdf->SetTextColor(255,0,0);
                            $contents = $kuku_set[$k][$j-1]["sor"];
                            $pdf->Cell(12,9,$contents, 1, 0, 'C', 0);
                        }
                    }
                    $pdf->Ln();
                }
                $pdf->Ln();
            }
        
            $linestyle = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => '5,10', 'color' => array(0, 0, 0));
            $pdf->Line(20,148.5,190,148.5, $linestyle);
        
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont($this->mFont, '',7);
            $pdf->SetFillColor(225, 225, 225);
            //$pdf->MultiCell($pdf->GetStringWidth($this->output_stamp,$this->mFont,null,8)+10, 0, $this->output_stamp.$this->output_stamp2, 0, 'R', 1, 0, $a4->width - $pdf->GetStringWidth($this->output_stamp,$this->mFont,null,8) -20, 2 , true,0,true,true,0,"T",false);

        }
        $pdf->lastPage();
        return $pdf;
    }
    public function rand_int():array{
        $array = range(0,9);
        shuffle($array);
        return $array;
    }
    public function rand_int_2dig():array{
        $array = range(10,19);
        shuffle($array);
        return $array;
    }
    public function dan_array():array{
        $array = range(1,9);
        shuffle($array);
        return $array;
    }
    public function kuku():array{
        $kuku = [];
        $kuku[0] = null;
        for($quo=1;$quo<=9;$quo++){
            $dan = [];
            for($j=1;$j<=10;$j++){
                $dan[] = array("dend"=>$quo*$j,"sor"=>$j); 
            }
            $kuku[$quo] = $dan; 
        }
        return $kuku;
    }
    public function get_ipaddr():String{
        if (!empty(getenv('HTTP_CLIENT_IP'))) {
            return getenv('HTTP_CLIENT_IP');
        } elseif (!empty(getenv('HTTP_X_FORWARDED_FOR'))) {
            return getenv('HTTP_X_FORWARDED_FOR');
        } else {
            return getenv('REMOTE_ADDR');
        }
    }
}

?>