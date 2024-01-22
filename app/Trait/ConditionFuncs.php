<?php

namespace App\Trait;

use App\Models\QuestionCondition;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

trait ConditionFuncs
{
    public function check_condition_range_overlap($newStart, $newEnd, $existingRanges,$max_range)
    {
        if ($newEnd>$max_range)
        {

            return false;
        }
        if ($newEnd<$newStart)
        {
            return  false;
        }
        if (empty($existingRanges))
        {
            return true;
        }
        foreach ($existingRanges as $key=>$value) {
            if ($newStart < (int)$value && $newEnd > (int)$key) {
                return false;
            }
        }

        return true;
    }
    public function current_conditions($question_id)
    {
        $conditions=QuestionCondition::query()->select()->where('question_id',$question_id)->get();
        if (count($conditions)>0)
        {
            return $conditions->keyBy('start_range')->map(function ($value) {
                return (int)$value->end_range;
            })->toArray();
        }
        return $conditions;
    }
    public function check_continuity($conditions,$max,$min=0){
        $starts=array_keys($conditions);
        $ends=array_values($conditions);
        if (!in_array($max,$ends))
        {
            throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,"حداکثر مقدار امتیاز $max است و شما بازه برای اتمام نداده اید"),403));

        }
        if (!in_array($min,$starts))
        {
            throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,"حداقل مقدار امتیاز $min است و شما بازه برای شروع نداده اید"),403));

        }
        if (($key = array_search(0, $starts)) !== false) {
            unset($starts[$key]);
        }
        foreach ($starts as $start)
        {
            if (!in_array($start,$ends))
            {
                throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false," قبل از $start بازه ای برای حفظ پیوستگی نیامده است "),403));

            }
        }
        return true;
    }
    public function add_condition($start,$end,$creator_frotel_id,$condition_templates)
    {
        $template=$this->question_template->query()->where('question_id',$this->current_question->id)->whereNull('parent_question_answerd_condition_id')->first();
        $can=$this->check_condition_range_overlap($start,$end,$this->current_conditions($this->current_question->id),$template->value);
        if ($can)
        {
            DB::beginTransaction();
            try {

                $condition=\App\Models\QuestionCondition::query()->create(['start_range'=>$start,'end_range'=>$end,'question_id'=>$this->current_question->id,'creator_frotel_id'=>$creator_frotel_id]);
                $templates=[];
                foreach ($condition_templates as $key => $value)
                {
                    $templates[]= $this->AddTemplate($value,$key,$condition->id);
                }
                DB::commit();
                return $this->response(true,'با موفقیت ایجاد شد',['conditin'=>$condition->toArray(),'condition_templates'=>$templates]);
            }catch (Exception $exception)
            {
                DB::rollBack();
                throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,$this->ErrorMassage($exception->getCode())),500));
            }

        }
        throw new HttpResponseException(response()->json(\App\Classes\Helper::response_body(false,'بازه وارد شده با بازه های موجود تداخل دارد'),403));


    }
}
