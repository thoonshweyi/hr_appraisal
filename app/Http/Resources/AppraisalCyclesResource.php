<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Status;
use Illuminate\Http\Resources\Json\JsonResource;

class AppraisalCyclesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
         return [
            "id"=>$this->id,
            "name"=>$this->name,
            "description"=>$this->description,
            "start_date"=> Carbon::parse($this->start_date)->format("M d, Y"),
            "end_date"=> Carbon::parse($this->end_date)->format("M d, Y"),
            "action_start_date"=> Carbon::parse($this->action_start_date)->format("F d, Y"),
            "action_end_date"=> Carbon::parse($this->action_end_date)->format("F d, Y"),
            "action_start_time"=>$this->action_start_time,
            "action_end_time"=>$this->action_end_time,
            "status_id"=>$this->status_id,
            'user_id'=>$this->user_id,
            "created_at"=>$this->created_at->format("M d, Y"),
            "updated_at"=>$this->updated_at->format("d m Y"),

            "user"=>User::where("id",$this->user_id)->select(["id","name"])->first(),
            "status"=>Status::where("id",$this->status_id)->select(["id","name"])->first()
        ];
    }
}
