<?php

namespace App\Services;

use App\Models\Translation;
use App\Models\Language;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class TranslationService
{
    public function create(array $data){
        $translation = Translation::create([
            'language_id' => $data['language_id'],
            'key' => $data['key'],
            'content' => $data['content']
        ]);

        if(!empty($data['tags'])){
            foreach($data['tags'] as $tagId){
                DB::table('translation_tags')->insert([
                    'translation_id' => $translation->id,
                    'tag_id' => $tagId
                ]);
            }
        }

        return $translation;
    }

    public function update($id, array $data){
        $translation = Translation::findOrFail($id);
    
        $translation->update([
            'key' => $data['key'],
            'content' => $data['content'],
            'language_id' => $data['language_id']
        ]);
    
        if (isset($data['tags'])) {
            DB::table('translation_tags')
                ->where('translation_id', $id)
                ->delete();
    
            foreach ($data['tags'] as $tagId) {
                DB::table('translation_tags')->insert([
                    'translation_id' => $id,
                    'tag_id' => $tagId
                ]);
            }
        }
    
        return $translation;
    }

    public function search(array $filters){
        $query = Translation::query();

        if(!empty($filters['key'])){
            $query->where('key','like',"%{$filters['key']}%");
        }

        if(!empty($filters['content'])){
            $query->where('content','like',"%{$filters['content']}%");
        }

        if(!empty($filters['tag'])){
            $query->join('translation_tags','translation.id','translation_tags.translation_id')
            ->join('tags','translation_tags.tag_id','tags.id')
            ->where('tags.name','like',"%{$filters['tag']}%");
        }

        return $query->paginate(20);
    }

    public function getTranslations(){
        return Translation::select('key', 'content')
        ->orderBy('key')
        ->limit(10000)
        ->get(['key', 'content'])
        ->pluck('content', 'key')
        ->toArray();
    }
}