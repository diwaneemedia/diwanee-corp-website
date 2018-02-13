<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Constants\Models;
use Illuminate\Support\Facades\DB;
use App\Constants\FieldTypeCategory;

class Tag extends AppModel {
    use SoftDeletes;
    
    protected $fillable = ['name'];

    protected $allFields = ['id', 'name', 'created_at', 'updated_at', 'deleted_at', 'tag_type_id'];
    
    protected $allFieldsFromPivots = [];

    protected $requiredFields = ['name', 'tagType'];

    protected $attributeType = [
        'parent_id' => Models::AttributeType_Number,
        'tag_id' => Models::AttributeType_Number
    ];
    
    protected $defaultDropdownColumn = 'name';

    protected $relationsSettings = [
        'tagType' => [
            'relationType' => 'belongsTo',
            'model' => 'App\\FieldType',
            'foreignKey' => 'tag_type_id',
            'filters' => ['category' => [FieldTypeCategory::Tag]]
        ],
        'parents' => [
            'relationType' => 'belongsToMany',
            'model' => 'App\\Tag',
            'pivot' => 'tag_parent',
            'foreignKey' => 'tag_id',
            'relationKey' => 'parent_id'
        ],
        'children' => [
            'relationType' => 'belongsToMany',
            'model' => 'App\\Tag',
            'pivot' => 'tag_parent',
            'foreignKey' => 'parent_id',
            'relationKey' => 'tag_id'
        ]
    ];
    
    protected $multipleFields = ['parents', 'children'];
    
    protected $dependsOn = [
        'parents' => ['tagType'],
        'children' => ['tagType']
    ];

    public function parentsRelationValues($dependsOnValues = null) {
        $fieldType = $this->getDependsOnValue('tagType', $dependsOnValues);
        return isset($fieldType->id) ? Tag::where('tag_type_id', '=', $fieldType->id)->where('id', '!=', $this->id)->get() : [];
    }

    public function childrenRelationValues($dependsOnValues = null) {
        $fieldType = $this->getDependsOnValue('tagType', $dependsOnValues);
        return isset($fieldType->id) ? Tag::where('tag_type_id', '=', $fieldType->id)->where('id', '!=', $this->id)->get() : [];
    }

    public static function reorder($tagsData) {
        DB::beginTransaction();
        try {
            $tagIds = self::getTagsIds($tagsData);
            DB::table('tag_parent')->whereIn('tag_id', $tagIds)->delete();
            DB::table('tag_parent')->whereIn('parent_id', $tagIds)->delete();

            self::insertTagChildrens(0, $tagsData);

            DB::commit();
            return true;

        } catch(Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    private static function getTagsIds($tagsData) {
        $tagIds = [];
        foreach($tagsData as $tagData) {
            if(isset($tagData['children']) && count($tagData['children']) > 0) {
              $tagIds = array_merge($tagIds, self::getTagsIds($tagData['children']));
            }
            $tagIds[] = $tagData['id'];
        }
        return $tagIds;
    }

    private static function insertTagChildrens($parentId, $childrenData) {
        foreach($childrenData as $tagData) {
            if($parentId > 0) {
                DB::table('tag_parent')->insert([
                    'tag_id' => $tagData['id'],
                    'parent_id' => $parentId
                ]);
            }

            if(isset($tagData['children']) && count($tagData['children']) > 0) {
                self::insertTagChildrens($tagData['id'], $tagData['children']);
            }
        }

    }
}