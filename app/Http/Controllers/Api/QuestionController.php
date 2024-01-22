<?php

namespace App\Http\Controllers\Api;

use App\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\QuestionRequest;
use App\Http\Requests\Api\QuestionUpdateRequest;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public  function index(Request $request)
    {
        return response()->json(Helper::response_body(true,'لیست سوالات',[],Question::query()->with(['question_type'=>function($q){
            $q->select(['id','type']);
        }])->paginate($request->get('perPage')??15)));

    }
    public function create(QuestionRequest $request)
    {
        $inputs=$request->validated();
        $questin=Helper::get_question_type_instance($inputs['question_type_id']);
        $new_question=$questin->create($inputs['title'],$inputs['question_type_id'],details: @$inputs['details']);
        if ($new_question['status'])
        {
            return response()->json(Helper::response_body(true,'سوال با موفقیت ایجاد شد',[],$new_question['data']));

        }
        return response()->json(Helper::response_body(false,$new_question['msg']));
    }
    public function update(QuestionUpdateRequest $request,$question_id)
    {
        $inputs=$request->validated();

        $q=Question::query()->where('id',$question_id)->first();
        if ($q)
        {
            $questin=Helper::get_question_type_instance($q->question_type_id);
            $questin->LoadQuestion($q);
            $new_question=$questin->Update($inputs['title'],details: @$inputs['details']);
            if ($new_question['status'])
            {
                return response()->json(Helper::response_body(true,'سوال با ویرایش  شد',[],$new_question['data']));

            }
            return response()->json(Helper::response_body(false,$new_question['msg']));

        }
        return response()->json(Helper::response_body(false,'موردی یافت نشد'),404);
    }
    public function destroy($question_id)
    {
        $question=Question::query()->where('id',$question_id)->first();
        if ($question)
        {
            try {
                $question->delete();
                DB::commit();
                return response()->json(Helper::response_body(true,'سوال با موفقیت حذف شد'));
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
