<?php

namespace App\Http\Controllers\Api;

use App\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PollRequest;
use App\Http\Requests\Api\UpdatePollRequest;
use App\Models\Category;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    public function index()
    {

        return response()->json(Helper::response_body(true,'لیست نظر سنجی ها',[],Poll::query()->get()));

    }
    public function show($poll_id): \Illuminate\Http\JsonResponse
    {
        $poll=Category::query()->where('id',$poll_id)->first();
        if ($poll)
        {
            return response()->json(Helper::response_body(true,'لیست نظر سنجی ها',[],$poll));

        }else{
            return response()->json(Helper::response_body(false,'موردی یافت نشد'),404);

        }
    }
    public function create(PollRequest $request)
    {

            DB::beginTransaction();
            try {
                $inputes=$request->validated();
                $poll=Poll::query()->create($inputes);
                DB::commit();
                return response()->json(Helper::response_body(true,'نظرسنجی با موفقیت ایجاد شد',[],$poll));
            }catch (\Exception $exception) {
                DB::rollBack();
                return response()->json(Helper::response_body(false, 'خطا در ثبت نظرسنجی'), 500);


            }
    }
    public function update(UpdatePollRequest $request,$poll_id)
    {
        $poll=Poll::query()->where('id',$poll_id)->first();
        if ($poll)
        {
            DB::beginTransaction();
            try {
                $inputes=$request->validated();
                $poll->update($inputes);
                DB::commit();
                return response()->json(Helper::response_body(true,'نظرسنجی با موفقیت ویرایش شد',[],$poll));
            }catch (\Exception $exception)
            {
                DB::rollBack();
                return response()->json(Helper::response_body(false,'خطا در ویرایش نظرسنجی'),500);

            }

        }else{
            return response()->json(Helper::response_body(false,'موردی یافت نشد'),404);

        }
    }
    public function destroy($poll_id)
    {
        $poll=Poll::query()->where('id',$poll_id)->first();
        if ($poll)
        {
            $poll->delete();
            return response()->json(Helper::response_body(true,'با موفقیت حذف شد'));


        }else{
            return response()->json(Helper::response_body(false,'موردی یافت نشد'),404);

        }
    }
}
