<?php

namespace App\Http\Controllers;

use App\Http\Model\GpsData;
use App\Http\Model\ImeiLog;
use App\Http\Model\Imeis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use DB;

class IndexController extends Controller
{
    public function test()
    {
        return ([
            'code' => 0,
            'msg' => '接口测试',
            'data' => [],
        ]);
    }
    //获取当前所有设备
    public function imeis(Request $request)
    {
        $dataTmp = [];
        //备注 密码
        if($traceid = $request->input('traceId'))
        {
            $data = Imeis::query()->where('imei',$traceid)->get();
        }
        elseif($key = $request->input('key'))
        {
            $like = "%".$key."%";
            $data = Imeis::query()->where("remarks","like",$like)->get();
        }
        else
        {
            $data = Imeis::query()->get();
        }

        if(empty($data))
        {
            $dataTmp=[];
        }
        else
        {
            /*$dataTmp = [
                "loc_time"=> 1413526632,
                "location"=> [
                    119.83801525967,
                    31.81347898519
                ],
                "track_name"=> "宋D66754",
                "track_id"=> 516977414
            ];*/
            foreach($data as $key=>$row)
            {
                $dataGps = GpsData::query()->where('imei',$row['imei'])->orderBy('created_at','desc')->first();
                if(empty($dataGps))
                {
                    //填充默认值
                    $dataTmp[$key]["loc_time"]= 1413526632;
                    $dataTmp[$key]["location"]= [119.83801525967, 31.81347898519];
                    $dataTmp[$key]['address'] = '测试地址';
                }
                else
                {
                    //填充默认值
                    $dataTmp[$key]["loc_time"]= strtotime($dataGps->datetime);
                    $dataTmp[$key]["location"]= $this->wgs84tobd09($dataGps->longitude, $dataGps->latitude);
                    $dataTmp[$key]['address'] = $this->getLonLLat($dataTmp[$key]["location"]);
                }
                $dataTmp[$key]['track_name'] = $row['remarks'];
                $dataTmp[$key]['track_id'] = $row['imei'];
            }
        }
        return ([
            'status' => 0,
            'size'=>count($dataTmp),
            'total'=>count($dataTmp),
            'message' => 'success',
            'pois' => $dataTmp,
        ]);
    }

    public function gettrace()
    {
        $gpsData = GpsData::query()->where('datetime','>',date('Y-m-d 00:00:00',time()-600))->orderBy('created_at','desc')->groupBy('imei')->get();
        return ([
            'status' => 0,
            'message' => 'success',
            'actives'=>count($gpsData),
            'service'=>['name'=>'GPS轨迹'],
        ]);
    }

    //获取设备某个时间段的轨迹数据
    public function getgpsbyimei(Request $request)
    {
        $imei = $request->input('ids');
        if(empty($imei))
        {
            return ([
                $imei=>[
                    'status' => 1,
                    'total'=>0,
                    'message' => '参数错误',
                    'pois' => [],
                ]
            ]);
        }
        $dataTmp=[];
        $stime = $request->input('start_time',strtotime(date('Y-m-d 00:00:00')));
        $etime = $request->input('end_time',strtotime(date('Y-m-d 23:59:59')));
        $data = GpsData::query()->where('imei',$imei)->whereBetween('datetime',[date("Y-m-d H:i:s",$stime),date("Y-m-d H:i:s",$etime)])->orderBy('datetime','desc')->get();
        if(empty($data))
        {
            $dataTmp=[];
        }
        else
        {
            foreach($data as $key=>$row)
            {
                $tmp = $this->wgs84tobd09($row['longitude'], $row['latitude']);
                $dataTmp[$key] = [$tmp[0],$tmp[1],strtotime($row['datetime'])];
            }
        }
        return ([
            $imei=>[
                'status' => 0,
                'total'=>count($dataTmp),
                'message' => '轨迹数据',
                'pois' => $dataTmp,
            ]
        ]);
    }

    public function getgooglegps(Request $request)
    {
        $imei = $request->input('ids');
        if(empty($imei))
        {
            return ([
                    'status' => 1,
                    'total'=>0,
                    'message' => '参数错误',
                    'pois' => [],
            ]);
        }
        $dataTmp=[];
        $lastData = GpsData::query()->where('imei',$imei)->orderBy('datetime','desc')->first();
        if(empty($lastData) || !isset($lastData->datetime))
        {
            return ([
                'status' => 2,
                'total'=>0,
                'message' => '暂无数据',
                'pois' => [],
            ]);
        }
        $stime = $request->input('start_time',strtotime(date('Y-m-d 00:00:00',strtotime($lastData->datetime))));
        $etime = $request->input('end_time',strtotime(date('Y-m-d 23:59:59',strtotime($lastData->datetime))));
        $data = GpsData::query()->where('imei',$imei)->whereBetween('datetime',[date("Y-m-d H:i:s",$stime),date("Y-m-d H:i:s",$etime)])->orderBy('datetime','desc')->get();
        if(empty($data))
        {
            $dataTmp=[];
        }
        else
        {
            foreach($data as $key=>$row)
            {
                $tmp = $this->wgs84togcj02($row['longitude'], $row['latitude']);
                $dataTmp[$key] = [$tmp[0],$tmp[1],strtotime($row['datetime'])];
            }
        }
        return ([
                'status' => 0,
                'total'=>count($dataTmp),
                'message' => '轨迹数据',
                'pois' => $dataTmp,
        ]);
    }



    /**根据位置获取经纬度
     * @param $area
     * @return mixed
     */
    protected function getLonLLat($area)
    {
        $address = '';
        list($lng,$lat) = $area;
        if($lat && $lng)
        {
            $url = 'http://api.map.baidu.com/reverse_geocoding/v3/?ak=BqQhc1umUeXi8Vl2QfYZCx8bxkTwvGxE&output=json&location='.$lat.','.$lng;
            $content = file_get_contents($url);
            $place = json_decode($content,true);
            $address = $place['result']['formatted_address'];
        }
        return $address;
    }



    /**
     * WGS84与百度坐标系 (BD-09) 的转换
     */
    public function wgs84tobd09($lng, $lat){
        $cj2=$this->wgs84togcj02($lng,$lat);
        return $this->gcj02tobd09($cj2[0],$cj2[1]);
    }
    /**
     * 火星坐标系 (GCJ-02) 与百度坐标系 (BD-09) 的转换
     * 即谷歌、高德 转 百度
     */
    public function gcj02tobd09($lng, $lat) {
        $x_PI = 3.14159265358979324 * 3000.0 / 180.0;
        $lat = +$lat;
        $lng = +$lng;
        $z = sqrt($lng * $lng + $lat * $lat) + 0.00002 * sin($lat * $x_PI);
        $theta = atan2($lat, $lng) + 0.000003 * cos($lng * $x_PI);
        $bd_lng = $z * cos($theta) + 0.0065;
        $bd_lat = $z * sin($theta) + 0.006;
        return [$bd_lng, $bd_lat];
    }
    /**
     * WGS84转GCj02
     */
    public function wgs84togcj02($lng, $lat) {
        $ee = 0.00669342162296594323;
        $a = 6378245.0;
        $lat = +$lat;
        $lng = +$lng;
        if ($this->out_of_china($lng, $lat)) {
            return [$lng, $lat];
        } else {
            $dlat = $this->transformlat($lng - 105.0, $lat - 35.0);
            $dlng = $this->transformlng($lng - 105.0, $lat - 35.0);
            $radlat = $lat / 180.0 * M_PI;
            $magic = sin($radlat);
            $magic = 1 - $ee * $magic * $magic;
            $sqrtmagic = sqrt($magic);
            $dlat = ($dlat * 180.0) / (($a * (1 - $ee)) / ($magic * $sqrtmagic) * M_PI);
            $dlng = ($dlng * 180.0) / ($a / $sqrtmagic * cos($radlat) * M_PI);
            $mglat = $lat + $dlat;
            $mglng = $lng + $dlng;
            return [$mglng, $mglat];
        }
    }
    public function transformlat($lng, $lat) {
        $lat = +$lat;
        $lng = +$lng;
        $ret = -100.0 + 2.0 * $lng + 3.0 * $lat + 0.2 * $lat * $lat + 0.1 * $lng * $lat + 0.2 * sqrt(abs($lng));
        $ret += (20.0 * sin(6.0 * $lng * M_PI) + 20.0 * sin(2.0 * $lng * M_PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($lat * M_PI) + 40.0 * sin($lat / 3.0 * M_PI)) * 2.0 / 3.0;
        $ret += (160.0 * sin($lat / 12.0 * M_PI) + 320 * sin($lat * M_PI / 30.0)) * 2.0 / 3.0;
        return $ret;
    }

    public function transformlng($lng, $lat) {
        $lat = +$lat;
        $lng = +$lng;
        $ret = 300.0 + $lng + 2.0 * $lat + 0.1 * $lng * $lng + 0.1 * $lng * $lat + 0.1 * sqrt(abs($lng));
        $ret += (20.0 * sin(6.0 * $lng * M_PI) + 20.0 * sin(2.0 * $lng * M_PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($lng * M_PI) + 40.0 * sin($lng / 3.0 * M_PI)) * 2.0 / 3.0;
        $ret += (150.0 * sin($lng / 12.0 * M_PI) + 300.0 * sin($lng / 30.0 * M_PI)) * 2.0 / 3.0;
        return $ret;
    }
    /**
     * 判断是否在国内，不在国内则不做偏移
     */
    public function out_of_china($lng, $lat) {
        $lat = +$lat;
        $lng = +$lng;
        // 纬度3.86~53.55,经度73.66~135.05
        return !($lng > 73.66 && $lng < 135.05 && $lat > 3.86 && $lat < 53.55);
    }
}
