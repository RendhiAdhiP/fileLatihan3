<?php

namespace App\Http\Controllers\Api;

use App\Models\Form;
use Illuminate\Http\Request;
use App\Models\AllowedDomain;
use App\Http\Controllers\Controller;

class FormController extends Controller
{
    public function createForm(Request $request)
    {
        $validate = $request->validate([
            "name"=> "required", 
            "slug"=> "required|unique:forms,slug|regex:/^[a-zA-z.-]+/", 
            "allowed_domains"=>  "array" , 
            "description"=> "required", 
            "limit_one_response"=> 'required' 
        ]);

        $user = $request->user();

        $form = Form::create([
            "name"=> $validate['name'], 
            "slug"=> $validate['slug'], 
            "description"=> $validate['description'], 
            "limit_one_response"=> $validate['limit_one_response'] ?? 0,  
            "creator_id"=> $user->id,  
        ]);

        if(!$request->allowedDomain == null){
            foreach($request->allowedDomain as $a){
                AllowedDomain::create(['domain'=>$a]);
            }
        }

        $f = [
            "name"=> $form['name'], 
            "slug"=> $form['slug'], 
            "description"=> $form['description'], 
            "limit_one_response"=> $form['limit_one_response'] ?? 0,  
            "creator_id"=> $form->creator_id,  
            "creator_id"=> $form->id,  
        ];


        return response()->json(['message'=>'create form success','form'=>$f],200);
    }
    public function getAll(Request $request)
    {

        $form = Form::where('creator_id',$request->user()->id)->get();

        // $form = $form->load(['allowedDomain','question']);
        return response()->json(['message'=>'get all form','forms'=>$form],200);

    }
    public function detail($slug,Request $request)
    {
        $form = Form::with('allowedDomain','question')->where('slug',$slug)->first();

        if($form == null){
            return response()->json(['message'=>'form not found'],404);

        }

        // $f = $form->load(['allowedDomain']);

        $f = [
            "id"=> $form->id,
            "name"=> $form->name,
            "slug"=> $form->slug,
            "description"=> $form->description,
            "limit_one_response"=> $form->limit_one_response,
            "creator_id"=> $form->creator->id,
            "allowed_domain"=> $form->allowedDomain->map(function($d){
                                return $d->domain;
                                }),
            
            "question"=> $form->question
        ];

        // $form->allowedDomain()->only('domain');


        return response()->json(['message'=>'form not found','form'=>$f],404);

    }
}
