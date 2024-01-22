<?php

namespace App\Http\Controllers\Api;

use App\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateQuestionRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 *
 */
class CategoryController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(Helper::response_body(true,'لیست دسته بندی ها',[],Category::query()->get()));
    }

    public function show($category_id): \Illuminate\Http\JsonResponse
    {
       $category=Category::query()->where('id',$category_id)->first();
       if ($category)
       {
           return response()->json(Helper::response_body(true,'لیست دسته بندی ها',[],$category));

       }else{
           return response()->json(Helper::response_body(false,'موردی یافت نشد'),404);

       }
    }

    public function create(Request $request)
    {

        $validator = Validator::make($request->only(['title','description','image','creator_frotel_id']), ['image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048','title'=>'required|max:80','description'=>'nullable|min:3|max:1000','creator_frotel_id'=>'required|numeric'],[]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $inputes=$validator->validated();
                $inputes['image']= $request->file('image')->store('image', 'public');
                $category=Category::query()->create($inputes);
                DB::commit();
                return response()->json(Helper::response_body(true,'دسته بندی با موفقیت ایجاد شد',[],$category));
            }catch (\Exception $exception)
            {
                DB::rollBack();
                return response()->json(Helper::response_body(false,'خطا در ثبت دسته بندی'),500);

            }


        } else {
            return response()->json(Helper::response_body(false,'خطا در داده های ورودی',$validator->errors()->getMessages(),[]),422);

        }
    }
    public function update(UpdateQuestionRequest $request,$category_id)
    {
        $category=Category::query()->where('id',$category_id)->first();
        if ($category)
        {
            DB::beginTransaction();
            try {
                $inputes=$request->validated();
                $inputes['image']= $request->file('image')->store('image', 'public');
                $category->update($inputes);
                DB::commit();
                return response()->json(Helper::response_body(true,'دسته بندی با موفقیت ویرایش شد',[],$category));
            }catch (\Exception $exception)
            {
                DB::rollBack();
                return response()->json(Helper::response_body(false,'خطا در ویرایش دسته بندی'),500);

            }

        }else{
            return response()->json(Helper::response_body(false,'موردی یافت نشد'),404);

        }
    }
    public function destroy($category_id)
    {
        $category=Category::query()->where('id',$category_id)->first();
        if ($category)
        {
            Helper::delete_file($category->image);
            $category->delete();
            return response()->json(Helper::response_body(true,'با موفقیت حذف شد'));


        }else{
            return response()->json(Helper::response_body(false,'موردی یافت نشد'),404);

        }
    }
}
