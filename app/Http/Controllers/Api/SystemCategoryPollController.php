<?php

namespace App\Http\Controllers\Api;

use App\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SystemCategoryPollRequest;
use App\Models\SystemCategoryPoll;
use DomainException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Exception;
use UnexpectedValueException;

class SystemCategoryPollController extends Controller
{
    public function create(SystemCategoryPollRequest $request)
    {
        $inputs=$request->all();
        Helper::check_poll_start_end($request->input('start_at'),$request->input('expire_at'));
        DB::beginTransaction();
        try {
            $sys_cat_poll=SystemCategoryPoll::query()->create($inputs);
            DB::commit();
           return response()->json(Helper::response_body(true,'نظر سنجی سیستم با موفقیت ایجاد شد',[],$sys_cat_poll));
        }catch (\Exception $exception)
        {
            DB::rollBack();
            return response()->json(Helper::response_body( false,'خطا در ایجاد نظرسنحی سیستم',[],[]));
        }
    }
}
