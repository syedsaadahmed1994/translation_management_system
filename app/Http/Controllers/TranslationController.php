<?php

namespace App\Http\Controllers;

use App\Services\TranslationService;
use Illuminate\Http\Request;
use Validator;

class TranslationController extends Controller
{
    private $translationService;

    public function __construct(TranslationService $translationService){
        $this->translationService = $translationService;
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'key' => 'required|string',
            'content' => 'required|string',
            'language_id' => 'required|exists:languages,id',
            'tags' => 'array'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation Errors',
                'data' => $validator->errors()
            ],400);
        }

        $new_translation = $this->translationService->create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Record created successfully',
            'data' => $new_translation
        ],200);
    }

    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'key' => 'required|string',
            'content' => 'required|string',
            'language_id' => 'required|exists:languages,id',
            'tags' => 'array'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }
    
        $translation = $this->translationService->update($id, $request->all());
    
        return response()->json([
            'status' => true,
            'message' => 'Translation updated successfully',
            'data' => $translation
        ], 200);
    }

    public function search(Request $request){
        $validator = Validator::make($request->all(),[
            'key' => 'string|nullable',
            'content' => 'string|nullable',
            'tag' => 'string|nullable'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation Errors',
                'data' => $validator->errors()
            ],400);
        }

        $results = $this->translationService->search($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Record retrieved successfully',
            'data' => $results
        ],200);
    }

    public function export(){
        $translations = $this->translationService->getTranslations();

        return response()->json([
            'status' => true,
            'message' => 'Translations exported successfully',
            'data' => $translations
        ],200);
    }
}
