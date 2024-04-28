<?php

namespace App\Http\Controllers\Api;

use App\Models\Form;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Question;

class QuestionController extends Controller
{
    public function addQuestion($slug,Request $request)
    {
        $form = Form::with('allowedDomain','question')->where('slug',$slug)->first();

        if($form == null){
            return response()->json(['message'=>'form not found'],404);

        }

        $validate = $request->validate([
            "name"=> "required", 
            "type"=> "required|in:short answer,paragraph,date,multiple choice,dropdown,checkboxes", 
            "choices"=> "required_if:type,multiple choice,dropdown,multiple choice,checkboxes|array", 
            "is_required"=> 'required' , 
        ]);

        if(isset($validate['choices'])){
            $validate['choices'] = trim(json_encode($validate['choices']),'[],"');
        }

        $quest = Question::create([
            "name"=> $request->name, 
            "type"=> $request->type, 
            "choices"=> $validate['choices'] ?? null, 
            "is_required"=> $request->is_required , 
            "form_id"=>$form->id , 
        ]);

        $q = [
            "name"=> $quest->name, 
            "type"=> $quest->type, 
            "choices"=> $quest->choices, 
            "is_required"=> $quest->is_required , 
            // "creator_id"=> $form->creator_id ,
            "form_id"=> $quest->form_id ,
            "id"=> $quest->id ,
        ];

        return response()->json(['message'=>'Add question success','question' =>$q],200);

    }

    public function removeQuest($slug,$id){
        $form = Form::with('allowedDomain','question')->where('slug',$slug)->first();

        if($form == null){
            return response()->json(['message'=>'form not found'],404);

        }

        Question::where('id',$id)->first()->delete();

        return response()->json(['message'=>'success delet question'],200);
        
    }
}
