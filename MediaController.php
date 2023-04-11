<?php

namespace Modules\WebsiteSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\WebsiteSetting\Models\Images;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\TempImages;
use ApiHelper;
use File;



class MediaController extends Controller
{


    public $page = 'web_media';
    public $pageview = 'view';
    public $pageadd = 'add';
    public $pagestatus = 'remove';
    public $pageupdate = 'update';

    public function index(Request $request)
    {
        // Validate user page access
        $api_token = $request->api_token;

        if (!ApiHelper::is_page_access($api_token, $this->page, $this->pageview))
            return ApiHelper::JSON_RESPONSE(false, [], 'PAGE_ACCESS_DENIED');


        $current_page = !empty($request->page) ? $request->page : 1;
        $perPage = !empty($request->perPage) ? (int)$request->perPage : ApiHelper::perPageItem();
        $search = $request->search;
        $sortBY = $request->sortBy;
        $ASCTYPE = $request->orderBY;

        $data_query = Images::query();

        if (!empty($search))
            $data_query = $data_query->where("images_ori_name", "LIKE", "%{$search}%");

        $skip = ($current_page == 1) ? 0 : (int)($current_page - 1) * $perPage;

        $user_count = $data_query->count();

        $data_list = $data_query->orderBy('images_id', 'desc')->skip($skip)->take($perPage)->get();
        //$data_list = $data_query->orderBy('images_id', 'desc')->paginate($perPage);

        $data_list = $data_list->map(function ($data) {
            $data->media_image = ApiHelper::getFullImageUrl($data->images_id);
            return $data;
        });

      


        $res = [
            'data' => $data_list,
            'current_page' => $current_page,
            'total_records' => $user_count,
            'total_page' => ceil((int)$user_count / (int)$perPage),
            'per_page' => $perPage,
        ];

        return ApiHelper::JSON_RESPONSE(true, $res, '');
    }



    public function show(Request $request)
    {
        // Validate user page access
        $api_token = $request->api_token;
        $data_query = Images::where('images_id', $request->images_id);

        if (!empty($search))
            $data_query = $data_query->where("images_ori_name", "LIKE", "%{$search}%");

        $data_list = $data_query->get();

        $data_list = $data_list->map(function ($data) {

            $data->media_image = ApiHelper::getFullImageUrl($data->images_id);

            return $data;
        });



        $res = [
            'media_list' => $data_list,

        ];
        return ApiHelper::JSON_RESPONSE(true, $res, '');
    }



    public function store(Request $request)
    {
        $api_token = $request->api_token;


        $image = $insData = array();
        $image_name = '';
        $ext = '';
        $image = $request->file('image');

        if (!empty($image)) {

            //foreach ($files as $file) {
                //  $image_name = md5(rand(1000, 10000));
               // if (!empty($file)) {
                    
                    $times = time();
                    $extension = $image->getClientOriginalExtension();
                    $dir = "temp/".$times;
                    
                    $path = $image->storeAs($dir, $times.'.'.$extension );

                    $tmpimage = TempImages::create([
                        'images_name' => $times,
                        'images_ext' => $extension,
                        'images_directory' => $dir,
                        'images_size' => '',
                    ]);
                    $insertedId = $tmpimage->images_id;

                    ApiHelper::image_upload_with_crop($api_token, $insertedId, 1, $insertedId, 'gallery', true);
                   
                    $insData=[
                        'images_id' => $insertedId
                    ];

              //  }
            //}
        }


        if ($insData)
            return ApiHelper::JSON_RESPONSE(true, $insData, 'SUCCESS_MEDIA_ADD');
        else
            return ApiHelper::JSON_RESPONSE(false, [], 'ERROR_MEDIA_ADD');

    }




    public function destroy(Request $request)
    {
        $api_token = $request->api_token;
        $id = $request->images_id;

        $status = Images::destroy($id);
        if ($status) {
            return ApiHelper::JSON_RESPONSE(true, [], 'SUCCESS_MEDIA_DELETE');
        } else {
            return ApiHelper::JSON_RESPONSE(false, [], 'ERROR_MEDIA_DELETE');
        }
    }
}
