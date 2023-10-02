<?php

namespace SBX\FrontCRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SBX\FrontCRM\Models\Setting;

class CRMController extends Controller
{
    public function getSetting(Request $request) {
        $setting = Setting::where('slug', $request->slug)->orderby('id', 'ASC')->get();
        return response()->json($setting);
    }

    public function setSetting(Request $request) {
        $setting = Setting::where('key', $request->key)->first();
        if(!isset($setting->id)){
            $setting = new Setting();
        }

        $setting->slug = $request->slug;
        $setting->key = $request->key;
        if($request->hasFile('value')){
            $setting->value = $this->image_upload($request->value);
            $setting->is_image = 1;
        }else{
            $setting->value = $request->value;
        }
        $setting->save();

        return 1;
    }

    public function image_upload($image)
    {
        $file_name = $image->getClientOriginalName();
        $file_name = substr($file_name, 0, strpos($file_name, "."));
        $name = "uploads/cms/" . $file_name . "_" . time() . rand(100000, 999999) . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path() . '/uploads/cms/';
        $image->move($destinationPath, $name);

        return $name;
    }
}