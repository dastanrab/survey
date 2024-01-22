<?php

namespace App\Http\Controllers\Api;

use App\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SystemRequest;
use App\Http\Requests\Api\SystemUpdateRequest;
use App\Models\System;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(\App\Classes\Helper::response_body(true,'لیست  سیستم ها',[],\App\Models\System::query()->get()));
    }
    public function show($system_id): \Illuminate\Http\JsonResponse
    {
        $system=System::query()->where('id',$system_id)->first();
        if ($system)
        {
            return response()->json(Helper::response_body(true,'اطلاعات سیستم',[],$system));

        }else{
            return response()->json(Helper::response_body(false,'موردی یافت نشد'),404);

        }
    }
    public function create(SystemRequest $request)
    {
        DB::beginTransaction();
        try {
            $inputes=$request->validated();
            $inputes['client_secret']=Helper::generate_client_key();
            $category=\App\Models\System::query()->create($inputes);
            DB::commit();
            return response()->json(Helper::response_body(true,'سیستم با موفقیت ایجاد شد',[],$category));
        }catch (\Exception $exception)
        {
            DB::rollBack();
            return response()->json(Helper::response_body(false,'خطا در ثبت سیستم'),500);

        }
    }
    public function update(SystemUpdateRequest $request,$system_id)
    {
        $system=System::query()->where('id',$system_id)->first();
        if ($system)
        {
            DB::beginTransaction();
            try {
                $inputes=$request->validated();
                $inputes['client_secret']=Helper::generate_client_key();
                $system->update($inputes);
                DB::commit();
                return response()->json(Helper::response_body(true,'سیستم با موفقیت ویرایش شد',[],$system));
            }catch (\Exception $exception)
            {
                DB::rollBack();
                return response()->json(Helper::response_body(false,'خطا در ویرایش دسته بندی'),500);

            }

        }else{
            return response()->json(Helper::response_body(false,'موردی یافت نشد'),404);
        }
    }
    public function destroy($system_id)
    {
        $system=System::query()->where('id',$system_id)->first();
        if ($system)
        {
            try {
                $system->delete();
                DB::commit();
                return response()->json(Helper::response_body(true,'سیستم با موفقیت حذف شد'));
            }catch (\Exception $exception)
            {
                DB::rollBack();
                return response()->json(Helper::response_body(false,'خطا در حذف'),500);

            }

        }else{
            return response()->json(Helper::response_body(false,'موردی یافت نشد'),404);

        }
    }

}
