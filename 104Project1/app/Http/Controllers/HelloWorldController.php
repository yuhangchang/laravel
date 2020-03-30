<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use JamesGordo\CSV\Parser;
use League\CLImate\CLImate;
use Exception;

class HelloWorldController extends Controller
{
    /**
    *
    *@OA\Info(
    *   version = "1.0.0",
    *   title = "maskdata"
    *),
    *@OA\Get(
    *   path="/test",
    *   summary="mask data",
    *   @OA\Parameter(
    *       name = "address",
    *       description = "地址",
    *       required = true,
    *       in = "query",
    *       @OA\Schema(
    *           type = "string"
    *       )
    *
    *   ),
    *   @OA\Response(
    *     response=200,
    *     description="successful",
    *   ),
    *)
    */
    public function downloadFile()
    {
        $maskDataUrl = "http://data.nhi.gov.tw/Datasets/Download.ashx?rid=A21030000I-D50001-001&l=https://data.nhi.gov.tw/resource/mask/maskdata.csv";
        if (time() - filemtime("maskdata.csv") > 300) {
            unlink("maskdata.csv");
        }
        if (is_file("maskdata.csv") === false) {
            if (file_put_contents("maskdata.csv", file_get_contents($maskDataUrl))) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
    public function HelloWorld(Request $request)
    {
        if (self::downloadFile() === false) {
            print("下載檔案錯誤");
            // exit();
        }
        $datas = new Parser("maskdata.csv");
        $outPutDatas = [];
        $input = "";
        $input = $request->query('address');
        foreach ($datas->all() as $data) {
            if (strpos($data->醫事機構地址, $input) !== false && $data->成人口罩剩餘數 != 0) {
                $temp = $data;
                unset($temp->醫事機構代碼, $temp->醫事機構電話, $temp->兒童口罩剩餘數, $temp->來源資料時間);
                $outPutDatas[] = (array)$temp;
            }
        }

        usort($outPutDatas, function ($a, $b) {
            return $b['成人口罩剩餘數'] - $a['成人口罩剩餘數'];
        });

        if ($outPutDatas) {
            return view('hello_world')->with('datas', $outPutDatas);
        } else {
            printf("查無資料\n");
        }
    }
    public function date(Request $request)
    {
        $weekarray=array("日","一","二","三","四","五","六");
        $date = explode('-', $request->query('date'));
        // return ($date);
        // return date('Y-m-d', strtotime($request->query('date')))."<br>".$request->query('date');
        // if (!checkdate($date[1], $date[2], $date[0])) {
        if (date('Y-m-d', strtotime($request->query('date'))) != $request->query('date')) {
            // return JsonResponse::create([
            //   'error' => true
            //
            // ]);
            throw new Exception("日期格式錯誤");
        } else {
            return $request->query('date')."星期".$weekarray[date('w', strtotime($request->query('date')))];
        }
    }
}
